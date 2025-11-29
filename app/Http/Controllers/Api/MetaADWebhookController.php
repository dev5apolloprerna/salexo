<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use App\Models\LeadMaster;
use App\Models\UserData;
use App\Models\Employee;
use App\Models\CompanyClient;
use App\Helpers\WhatsAppHelper;

class MetaADWebhookController extends Controller
{
    /*public function verify(Request $request)
    {
        $expectedToken = config('app.meta_verify_token', 'mycustom78');

        $mode        = $request->query('hub_mode', $request->query('hub.mode'));
        $verifyToken = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge   = $request->query('hub_challenge', $request->query('hub.challenge'));

        if ($mode === 'subscribe' && $verifyToken === $expectedToken) {
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Invalid verify token', 403);
    }*/

    public function verify_token(Request $request)
    {
        $mode        = $request->query('hub_mode', $request->query('hub.mode'));
        $verifyToken = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge   = $request->query('hub_challenge', $request->query('hub.challenge'));

        Log::info("META VERIFY CALLBACK", [
            'mode' => $mode,
            'verify_token' => $verifyToken,
            'challenge' => $challenge
        ]);

        if (!$verifyToken) {
            return response("Missing verify token", 400);
        }

        // 🔍 1) FIND COMPANY BY verify_token
        $company = CompanyClient::where('verify_token', $verifyToken)->first();

        if (!$company) {
            Log::warning("Verify Token NOT FOUND in any company", ['token' => $verifyToken]);
            return response("Invalid verify token", 403);
        }

        Log::info("META Verified for company_id=" . $company->company_id);

        // 🔥 2) Meta verification success
        if ($mode === 'subscribe') {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response("Invalid mode", 403);
    }


   public function receive_data(Request $request)
    {
        $data = $request->all();
        Log::info("Meta Webhook Received:", $data);

        // Extract leadgen ID from all possible patterns
        $leadgenId =
            $data['entry'][0]['changes'][0]['value']['leadgen_id']
            ?? $data['entry'][0]['changes'][0]['value']['lead_id']
            ?? $data['leadgen_id']
            ?? $data['lead_id']
            ?? $data['id']                      // <-- THIS IS YOUR CASE
            ?? null;

        // Extract ad_id from all patterns
        $incomingAdId =
            $data['entry'][0]['changes'][0]['value']['ad_id']
            ?? $data['ad_id']
            ?? null;

        if (!$leadgenId) {
            Log::warning("No lead id found in request", $data);
            return response()->json(['status' => 'leadgen_missing']);
        }

        // Normalize "l:" prefix
        $leadgenId = preg_replace('/[^0-9]/', '', $leadgenId);

        Log::info("Extracted Lead ID = $leadgenId");
        Log::info("Extracted AD-ID = " . ($incomingAdId ?? 'NULL'));

        if (!$incomingAdId) {
            Log::warning("Webhook received but NO ad_id found");
            return response()->json(['status' => 'ad_id_missing']);
        }

        // Continue...
        $this->fetchLeadDetailsUsingAd($leadgenId, $incomingAdId);

        return response()->json(['status' => 'ok']);
    }


    // ============================
    // FETCH USING AD-ID MAPPING
    // ============================
    public function fetchLeadDetailsUsingAd($leadgenId, $incomingAdId)
    {   
        $incomingad_id=preg_replace('/[^0-9]/', '', $incomingAdId);

        // STEP 1 — Get user_data mapping for this AD-ID
        $mapping = UserData::with('company')
            ->where('api_id', 3)
            ->where('ad_id', $incomingad_id)
            ->first();

        if (!$mapping) {
            Log::error("❌ No mapping found for AD-ID = $incomingad_id");
            return;
        }

        $company = $mapping->company;

        $employee = Employee::where('emp_id', $mapping->emp_id)->first();
        if (!$employee) {
            Log::error("❌ Employee not found for emp_id = ".$mapping->emp_id);
            return;
        }

        $pageAccessToken = $company->access_token;
        $productId = $mapping->product_id ?? 0;
        $sourceId = $mapping->source_id ?? null;

        // STEP 2 — Fetch lead from Facebook
        $url = "https://graph.facebook.com/v21.0/{$leadgenId}";

        /*$response = Http::asForm()->get($url, [
            'access_token' => $pageAccessToken,
            'fields'       => 'field_data,created_time,form_id,ad_id,platform'
        ]);
*/
         $response = Http::withOptions([
            'verify' => false, // ⛔ turn off SSL verification only on local
        ])->asForm()->get($url, [
            'access_token' => $pageAccessToken,
            'fields'       => 'field_data,created_time,form_id,ad_id,platform'
        ]);

        Log::info("Lead Fetch Response: ".$response->status());

        if (!$response->successful()) return;

        $leadData = $response->json();


        if (empty($leadData['field_data'])) return;

        // Extract lead fields
        $leadInfo = collect($leadData['field_data'])
            ->mapWithKeys(fn($item) => [$item['name'] => $item['values'][0] ?? null]);

        $cleanMobile = preg_replace('/^\+?91[-\s]?/', '', $leadInfo['phone_number'] ?? '');

        // STEP 3 — Lead Pipeline
        $pipeline = LeadPipeline::where('company_id', $company->company_id)
            ->where('pipeline_name', "New Lead")
            ->first();

        // STEP 4 — Create Lead in CRM
        LeadMaster::create([
            'iCustomerId'        => $company->company_id,
            'company_name'       => $company->company_name,
            'iemployeeId'        => $employee->emp_id,
            'product_service_id' => $productId,
            'LeadSourceId'       => $sourceId,
            'status'             => $pipeline->pipeline_id ?? 0,
            'employee_id'        => $mapping->emp_id,
            'customer_name'      => $leadInfo['full_name'] ?? $leadInfo['name'],
            'email'              => $leadInfo['email'] ?? '',
            'mobile'             => $cleanMobile,
            'address'            => $leadInfo['city'] ?? '',
            'comments'           => json_encode($leadData),
            'json'               => json_encode($leadData),
            'created_at'         => now(),
        ]);

        Log::info("Lead created successfully for AD-ID {$incomingad_id}");

        // STEP 5 — Send WhatsApp Notification
        if ($company->isNotifyApi == 1) 
        {
            $sourceName = LeadSource::find($sourceId)->lead_source_name ?? 'Facebook';

            WhatsAppHelper::sendTemplateMessage(
                $employee->emp_mobile,
                "utility_message",
                [
                    $employee->emp_name,  // {{1}}
                    $sourceName           // {{2}}
                ]
            );
        }
    }
}
