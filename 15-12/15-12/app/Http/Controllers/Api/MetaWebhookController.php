<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use App\Models\LeadMaster;
use App\Models\UserData;
use App\Models\Employee; // ðŸ‘ˆ make sure this exists
use Illuminate\Support\Facades\Log;
    use App\Helpers\WhatsAppHelper;

class MetaWebhookController extends Controller
{
    public function verify(Request $request, $guid)
    {
        // 1) Find employee by guid
        $employee = Employee::where('isDelete', 0)
            ->where('guid', $guid)
            ->first();
            
            
        if (!$employee) {
            // guid invalid
            return response('Invalid guid', 404);
        }
    
        // 2) From user_data get Meta API settings for this company (api_id = 3)
        $userData = UserData::with('company')->where('company_id', $employee->company_id ?? 0)
            ->where('api_id', 3)
            ->first();
    
        // If you store verify token per company in user_data, use it here
        $expectedToken = $userData->company->verify_token ?? 'mycustom78';
    
        // 3) Standard Meta verify logic
        $mode        = $request->query('hub_mode', $request->query('hub.mode'));
        $verifyToken = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge   = $request->query('hub_challenge', $request->query('hub.challenge'));
    
        if ($mode === 'subscribe' && $verifyToken === $expectedToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }
    
        return response('Invalid verify token', 403);
    }


    // Handle webhook POST: /api/meta/webhook/{guid}
    public function receive(Request $request, $guid)
    {
        $data = $request->all();

        Log::info('Meta Webhook Received:', $data);

        // Try multiple places where lead id might appear
        $leadgenId = null;

        // 1) common nested page webhook structure
        if (isset($data['entry'][0]['changes'][0]['value']['leadgen_id'])) {
            $leadgenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
        } elseif (isset($data['entry'][0]['changes'][0]['value']['lead_id'])) {
            $leadgenId = $data['entry'][0]['changes'][0]['value']['lead_id'];
        }
        // 2) top-level keys
        elseif (isset($data['leadgen_id'])) {
            $leadgenId = $data['leadgen_id'];
        } elseif (isset($data['lead_id'])) {
            $leadgenId = $data['lead_id'];
        } elseif (isset($data['id'])) {
            $leadgenId = $data['id'];
        }

        if ($leadgenId) {
            Log::info("Raw lead id found: " . $leadgenId);

            // Normalize: remove non-digits (e.g. "l:8402..." -> "8402...")
            $normalizedId = preg_replace('/\D/', '', $leadgenId);

            if (!empty($normalizedId)) {
                Log::info("Normalized lead id: " . $normalizedId);
                // ðŸ‘‡ pass guid here
                $this->fetchLeadDetails($normalizedId, $guid);
            } else {
                Log::warning("Lead id exists but normalizes to empty. Using raw id for fetch.");
                // ðŸ‘‡ and here
                $this->fetchLeadDetails($leadgenId, $guid);
            }
        } else {
            Log::info("No leadgen_id or alternative lead id found in POST payload");
        }

        return response()->json(['status' => 'ok']);
    }
    public function fetchLeadDetails($leadgenId, $guid)
    {
        // 1) Identify employee by GUID
        $employee = Employee::with('company')->where('isDelete', 0)->where('guid', $guid)->first();

        if (!$employee) {
            Log::warning("Meta fetchLeadDetails: Employee not found for guid={$guid}");
            return;
        }

        // 2) Get default Meta API mapping (api_id = 3)
        $defaultUserData = UserData::with('company')->where('company_id', $employee->company_id)->where('api_id', 3)->first();


        if (!$defaultUserData || !$defaultUserData->company->access_token) {
            Log::warning("Meta Page Access Token missing for company={$employee->company_id}");
            return;
        }

        $pageAccessToken = $defaultUserData->company->access_token;

        // 3) URL call
        $url = "https://graph.facebook.com/v21.0/{$leadgenId}";


        // 4) Lead pipeline
        $leadPipeline = LeadPipeline::where([
            'company_id'    => $employee->company_id,
            'pipeline_name' => "New Lead"
        ])->first();


        // 5) Call Meta Lead API
       $response = Http::withOptions([
            'verify' => true, // ⛔ turn off SSL verification only on local
        ])->asForm()->get($url, [
            'access_token' => $pageAccessToken,
            'fields'       => 'field_data,created_time,form_id,ad_id'
        ]);


        Log::info("Lead fetch response: status={$response->status()}, body=" . $response->body());

        if (!$response->successful()) {
            Log::warning("Meta lead fetch FAILED. HTTP Status: " . $response->status());
            return;
        }

        $leadData = $response->json();
        Log::info("Lead Details:", $leadData);

        if (empty($leadData['field_data'])) {
            Log::warning("Meta lead has empty field_data. ID={$leadgenId}");
            return;
        }

        // 6) Extract Field Data
        $leadInfo = collect($leadData['field_data'])->mapWithKeys(fn($item) =>
            [$item['name'] => $item['values'][0] ?? null]
        );

        $incomingAdId = $leadData['ad_id'] ?? null;

        // PRIORITY 1 — Exact AD ID match
        $mapping = UserData::where('company_id', $employee->company_id)
            ->where('api_id', 3)
            ->where('ad_id', $incomingAdId)
            ->first();



        // PRIORITY 2 — Fallback where AD ID is null
        if (!$mapping) {
            $mapping = UserData::where('company_id', $employee->company_id)
                ->where('api_id', 3)
                ->whereNull('ad_id')
                ->first();
        }

        // PRIORITY 3 — Final fallback to default Meta config
        if (!$mapping) {
            $mapping = $defaultUserData;
        }

        Log::info("AD-ID Routing → incoming_ad_id={$incomingAdId}, chosen mapping record", $mapping->toArray());

        $sourceId = $mapping->source_id;          // From user_data table
        $productId = $mapping->product_id ?? 0;   // From user_data table

        $mappedEmpId = $mapping->emp_id ?? $employee->emp_id;

        $keysToSkip = ['email', 'full_name', 'name', 'phone_number', 'city', 'created_time'];

        $lines = [];
        foreach ($leadData as $key => $value) {
            if (in_array($key, $keysToSkip)) continue;
            if (is_array($value)) continue;

            $label = ucwords(str_replace('_', ' ', $key));
            $lines[] = "{$label}: {$value}";
        }

        $commentText = implode("\n", $lines);

        // Clean phone
        $cleanMobile = null;
        if (!empty($leadInfo['phone_number'])) {
            $cleanMobile = preg_replace('/^\+?91[-\s]?/', '', $leadInfo['phone_number']);
        }

        
        LeadMaster::create([
            'iCustomerId'        => $employee->company_id,
            'company_name'       => $employee->company->company_name ?? null,
            'iemployeeId'        => $employee->emp_id,
            'product_service_id' => $productId ?? 0,            
            'LeadSourceId'       => $sourceId,                  
            'lead_history_id'    => 0,
            'followup_by'        => 0,
            'status'             => $leadPipeline->pipeline_id ?? 0,
            'cancel_reason_id'   => 0,
            'employee_id'        => $mappedEmpId,               
            'iEnterBy'           => 0,
            'customer_name'      => $leadInfo['full_name'] ?? $leadInfo['name'] ?? null,
            'email'              => $leadInfo['email'] ?? null,
            'mobile'             => $cleanMobile,
            'address'            => $leadInfo['city'] ?? null,
            'comments'           => $commentText,
            'created_at'         => !empty($leadData['created_time'])
                                    ? date('Y-m-d H:i:s', strtotime($leadData['created_time']))
                                    : now(),
            'json'               => json_encode($leadData),
        ]);

        // -----------------------------------------------------------------
        // WhatsApp Notification
        // -----------------------------------------------------------------

        $mapemp = Employee::with('company')->where(['emp_id' => $mappedEmpId])->first();

        if ($employee->company->isNotifyApi == 1) 
        {
            $name = $mapemp->emp_name;

            $leadSourceRow = LeadSource::select('lead_source_name')->find($sourceId);
            $sourceName = $leadSourceRow->lead_source_name ?? 'Facebook';

            WhatsAppHelper::sendTemplateMessage(
                $mapemp->emp_mobile,
                "utility_message",
                [
                    $name,        // {{1}}
                    $sourceName   // {{2}} → dynamically inserted!
                ]
            );
        }


        return;
    }

}
