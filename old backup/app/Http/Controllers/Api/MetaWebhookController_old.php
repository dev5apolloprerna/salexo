<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\LeadMaster;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    // Facebook verification (GET)
    public function verify(Request $request)
    {
        $mode        = $request->query('hub_mode', $request->query('hub.mode'));
        $verifyToken = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge   = $request->query('hub_challenge', $request->query('hub.challenge'));

        // Get the full URL that was called
        $fullUrl = $request->fullUrl();
        $expectedToken = "mycustom78"; // YOUR VERIFY TOKEN

        // Store the webhook verification details in LeadMaster table
        // LeadMaster::create([
        //     'iCustomerId' => 0,
        //     'iemployeeId' => 0,
        //     'product_service_id' => 0,
        //     'LeadSourceId' => 0,
        //     'lead_history_id' => 0,
        //     'followup_by' => 0,
        //     'status' => 0,
        //     'cancel_reason_id' => 0,
        //     'employee_id' => 0,
        //     'iEnterBy' => 0,
        //     'link' => $fullUrl,
        //     'created_at' => now(),
        // ]);

        // if ($mode === 'subscribe' && $verifyToken === "mycustom78") {
        return response($challenge, 200)->header('Content-Type', 'text/plain');
        // }
        // return response('Invalid verify token', 403);

        // $verifyToken = 'mycustom78'; // set this value yourself
        // \Log::info('Meta Webhook Verification:', $request->all());
        // if ($request->hub_verify_token === $verifyToken) {
        //     \Log::info('Meta Webhook Verification: if ');
        //     return response($request->hub_challenge, 200);
        // }

        // return response('Invalid verify token', 403);
    }

    // Handle webhook POST
    public function receive(Request $request)
    {
        $data = $request->all();

        \Log::info('Meta Webhook Received:', $data);

        LeadMaster::create([
            'iCustomerId' => 0,
            'iemployeeId' => 0,
            'product_service_id' => 0,
            'LeadSourceId' => 0,
            'lead_history_id' => 0,
            'followup_by' => 0,
            'status' => 0,
            'cancel_reason_id' => 0,
            'employee_id' => 0,
            'iEnterBy' => 0,
            'json' => json_encode($data),
            'created_at' => now(),
        ]);

        // Try multiple places where lead id might appear
        $leadgenId = null;
    
        // 1) the common nested page webhook structure
        if (isset($data['entry'][0]['changes'][0]['value']['leadgen_id'])) {
            $leadgenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
        } elseif (isset($data['entry'][0]['changes'][0]['value']['lead_id'])) {
            $leadgenId = $data['entry'][0]['changes'][0]['value']['lead_id'];
        }
        // 2) top-level common keys (test payloads or different app config)
        elseif (isset($data['leadgen_id'])) {
            $leadgenId = $data['leadgen_id'];
        } elseif (isset($data['lead_id'])) {
            $leadgenId = $data['lead_id'];
        } elseif (isset($data['id'])) {
            $leadgenId = $data['id'];
        }
    
        if ($leadgenId) {
            \Log::info("Raw lead id found: " . $leadgenId);
    
            // Normalize: remove any non-digit characters (e.g. "l:8402..." -> "8402...")
            $normalizedId = preg_replace('/\D/', '', $leadgenId);
    
            if (!empty($normalizedId)) {
                \Log::info("Normalized lead id: " . $normalizedId);
                $this->fetchLeadDetails($normalizedId);
            } else {
                \Log::warning("Lead id exists but normalizes to empty after stripping non-digits. Using raw id for fetch.");
                $this->fetchLeadDetails($leadgenId);
            }
        } else {
            \Log::info("No leadgen_id or alternative lead id found in POST payload");
        }


        return response()->json(['status' => 'ok']);
    }

    // Fetch lead details via Graph API
    public function fetchLeadDetails($leadgenId)
    {
        $pageAccessToken = "EAAf1IIY3JmcBP1WC4sLFZCZAXwfUZBqC6qMhD2a4AhS0Agc57wsNXVX7kbdwQ3330szV3ZAcnQLWCfKfhmItsG0OB685yZBR4tZCioLRaOZBr7ZBeYRKzkIBvGMulZCVYGUWcjaVhiGw1iG6MetkBnKMOk0x4W6388h9Uzc1ZA56EdIJKJbzOMN269vwZCUemQSQImZBo1mOvEqe2WNdjMm2IBCO";

        $url = "https://graph.facebook.com/v21.0/{$leadgenId}";

        $response = Http::asForm()->get($url, [
            'access_token' => $pageAccessToken,
            'fields' => 'field_data,created_time,form_id'
        ]);
        \Log::info("Normalized lead id: " . $response);
    
        if ($response->successful()) {
            $leadData = $response->json();
            Log::info("Lead Details Response (direct):", $leadData);
            if (!empty($leadData['field_data'])) {
                $leadInfo = collect($leadData['field_data'])->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['values'][0] ?? null];
                });
    
                LeadMaster::create([
                    'customer_name' => $leadInfo['full_name'] ?? $leadInfo['name'] ?? null,
                    'email' => $leadInfo['email'] ?? null,
                    'mobile' => $leadInfo['phone_number'] ?? null,
                    'address' => $leadInfo['city'] ?? null,
                    'LeadSourceId' => 1,
                    'created_at' => now(),
                    'json' => json_encode($leadData)
                ]);
                return;
            }
        }
    
        // If direct fetch failed, log and try fallback via form_id if available
        $status = $response->status();
        $body = $response->body();
        Log::warning("Direct lead fetch failed: HTTP {$status} - {$body}");
    
        // If form_id is available, try form->leads endpoint
        if (!empty($formId)) {
            $formUrl = "https://graph.facebook.com/v21.0/{$formId}/leads";
            $formResp = Http::asForm()->get($formUrl, [
                'access_token' => $pageAccessToken,
                'fields' => 'field_data,created_time,id'
            ]);
    
            if ($formResp->successful()) {
                $formLeads = $formResp->json();
                Log::info("Form leads fetched:", $formLeads);
    
                if (!empty($formLeads['data'])) {
                    // try locate the lead by id
                    $found = null;
                    foreach ($formLeads['data'] as $l) {
                        if (isset($l['id']) && (string)$l['id'] === (string)$leadgenId) {
                            $found = $l;
                            break;
                        }
                    }
    
                    // If not found, consider saving the latest lead (or all) — here we save the first lead as fallback
                    $toSave = $found ?? $formLeads['data'][0];
    
                    $leadInfo = collect($toSave['field_data'] ?? [])->mapWithKeys(function ($item) {
                        return [$item['name'] => $item['values'][0] ?? null];
                    });
    
                    LeadMaster::create([
                        'customer_name' => $leadInfo['full_name'] ?? $leadInfo['name'] ?? null,
                        'email' => $leadInfo['email'] ?? null,
                        'mobile' => $leadInfo['phone_number'] ?? null,
                        'address' => $leadInfo['city'] ?? null,
                        'LeadSourceId' => 1,
                        'created_at' => now(),
                        'json' => json_encode($toSave)
                    ]);
                    return;
                }
            }
    
            // log form fetch error
            Log::error("Form leads fetch failed: HTTP " . $formResp->status() . ' - ' . $formResp->body());
        }
    
        // Last resort: save pending lead with raw payload
        LeadMaster::create([
            'LeadSourceId' => 1,
            'status' => 98,
            'iEnterBy' => 0,
            'created_at' => now(),
            'json' => json_encode(['leadgen_id' => $leadgenId, 'note' => 'fetch_failed', 'raw' => $rawPayload])
        ]);
    }
}
