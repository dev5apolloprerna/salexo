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

class MetaWebhookController extends Controller
{
    // Facebook verification (GET)
    /*public function verify(Request $request)
    {
        $mode        = $request->query('hub_mode', $request->query('hub.mode'));
        $verifyToken = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge   = $request->query('hub_challenge', $request->query('hub.challenge'));

        $expectedToken = "mycustom78"; // your verify token

        //if ($mode === 'subscribe' && $verifyToken === $expectedToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        //}

        return response('Invalid verify token', 403);
    }*/

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
        $userData = UserData::where('company_id', $employee->company_id ?? 0)
            ->where('api_id', 3)
            ->first();
    
        // If you store verify token per company in user_data, use it here
        $expectedToken = $userData->verify_token ?? 'mycustom78';
    
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

        // (Optional) store raw payload â€“ remove if you don't want extra row
        // LeadMaster::create([
        //     'iCustomerId'       => 0,
        //     'iemployeeId'       => 0,
        //     'product_service_id'=> 0,
        //     'LeadSourceId'      => 0,
        //     'lead_history_id'   => 0,
        //     'followup_by'       => 0,
        //     'status'            => 0,
        //     'cancel_reason_id'  => 0,
        //     'employee_id'       => 0,
        //     'iEnterBy'          => 0,
        //     'json'              => json_encode($data),
        //     'created_at'        => now(),
        // ]);

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

    // Fetch lead details via Graph API
    public function fetchLeadDetails($leadgenId, $guid)
    {
         $employee = Employee::with('company')->where('isDelete', 0)
            ->where('guid', $guid)
            ->first();

            $userData = UserData::where('company_id', $employee->company_id ?? 0)
            ->where('api_id', 3)
            ->first();

        $pageAccessToken = $userData->access_token;

        $url = "https://graph.facebook.com/v21.0/{$leadgenId}";

        // 1) From GUID get employee
       
        if (!$employee) {
            Log::warning("Meta fetchLeadDetails: Employee not found for guid={$guid}");
            return;
        }

            // lead pipeline for lead status
            $leadPipeline = LeadPipeline::where([
                'company_id' => $employee->company_id,
                'pipeline_name' => "New Lead"
            ])->first();


            //lead source for facebook lead
            $lead_source = LeadSource::where(['company_id' => $employee->company_id, 'lead_source_name' => 'Facebook'])->first();
            if ($lead_source) {
                $lead_source = $lead_source->lead_source_id;
            } else {
                $lead_source = LeadSource::create([
                    'company_id' => $employee->company_id,
                    'lead_source_name' => 'Facebook',
                ]);
                $lead_source = $lead_source->lead_source_id;
            }

                

        $mappedEmpId    = $userData->emp_id    ?? $employee->emp_id ?? 0;
        $mappedSourceId = $userData->source_id ?? 44;

        $response = Http::asForm()->get($url, [
            'access_token' => $pageAccessToken,
            'fields'       => 'field_data,created_time,form_id,ad_id'
        ]);

        Log::info("Lead fetch response: status={$response->status()}, body=" . $response->body());

        if ($response->successful()) 
        {
            $leadData = $response->json();
             $flat = $leadData;
            Log::info("Lead Details Response (direct):", $leadData);

            if (!empty($leadData['field_data'])) 
            {
                
                $leadInfo = collect($leadData['field_data'])->mapWithKeys(function ($item) 
                {
                    return [$item['name'] => $item['values'][0] ?? null];
                });
                $keysToSkip = [
                    'email',
                    'full_name',
                    'name',
                    'phone_number',
                    'city',
                    'created_time'
                ];
            
                $lines = [];
                foreach ($flat as $key => $value) 
                {
                    if (in_array($key, $keysToSkip, true)) {
                        continue;
                    }
                    if (is_array($value)) {
                        // skip arrays or convert to json if you want
                        continue;
                    }
            
                    // make nicer label: "campaign_id" -> "Campaign id"
                    $label = ucwords(str_replace('_', ' ', $key));
                    $lines[] = "{$label}: {$value}";
                }
            
                $commentText = implode("\n", $lines);


                LeadMaster::create([
                    'iCustomerId'       => $employee->company_id ?? 0,
                    'company_name'       => $employee->company->company_name ?? null,
                    'iemployeeId'       => $mappedEmpId,
                    'product_service_id'=> 0,
                    'LeadSourceId'      => $lead_source,
                    'lead_history_id'   => 0,
                    'followup_by'       => 0,
                    'status'            => $leadPipeline->pipeline_id,
                    'cancel_reason_id'  => 0,
                    'employee_id'       => $mappedEmpId,
                    'iEnterBy'          => 0,
                    'customer_name'     => $leadInfo['full_name'] ?? $leadInfo['name'] ?? null,
                    'email'             => $leadInfo['email'] ?? null,
                    'mobile'            => $leadInfo['phone_number'] ?? null,
                    'address'           => $leadInfo['city'] ?? null,
                    'comments'          => $commentText,
                    'created_at'        => !empty($leadData['created_time']) ? date('Y-m-d H:i:s', strtotime($leadData['created_time'])) : now(),
                    'json'              => json_encode($leadData),
                    
                ]);
                
                $userdata = UserData::where(['company_id' => $employee->company_id,'api_id'=> 3])->first();

                if ($userdata) {
                    $userdata->update([
                        'ad_id' => $leadData['ad_id']   // FIXED HERE
                    ]);
                
                    $userDataId = $userdata->data_id;
                
                } else {
                
                    $userdata = UserData::create([
                        'company_id' => $employee->company_id,
                        'api_id'     => 3,
                        'ad_id'      => $leadData['ad_id']   // FIXED HERE
                    ]);
                
                    $userDataId = $userdata->data_id;
                }

                


                return;
            }
        }

        // If direct fetch failed, just log â€“ you can also create a "failed" lead if you want
        $status = $response->status();
        Log::warning("Direct lead fetch failed: HTTP {$status}");
    }
}
