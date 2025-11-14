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
            'link' => $fullUrl,
            'created_at' => now(),
        ]);

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

        $leadgenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'] ?? null;

        if ($leadgenId) {
            Log::info("Leadgen ID found: " . $leadgenId);
            $this->fetchLeadDetails($leadgenId);
        }else {
            Log::info("No leadgen_id found in POST payload");
        }


        return response()->json(['status' => 'ok']);
    }

    // Fetch lead details via Graph API
    public function fetchLeadDetails($leadgenId)
    {
        $accessToken = "EAAf1IIY3JmcBPwrtrpWzP9Ll4TnvvjhV0QqEixVcOToATzhgZBN8bdf0vCs2Cg0uicVhZBhlmtO1bJj33FxC3THVCFWn5vhdd0ZCGRlJVvnhCW85o9CbnIKXiHeZCUd9BvwFjZArvXbjZA7Xd6t8ZAgn7EcZB37bsbr0timVSAUe9xZCZBUaYT9yveWbpaJlIH90XdNgdJjKKK1ZCQPrx0f9m5e8P9NcoJDDz6OfReWKuUMoi2C7cO1ZCirFewgZD";

        $url = "https://graph.facebook.com/v21.0/{$leadgenId}?access_token={$accessToken}";
        
        $response = Http::get($url);
        $leadData = $response->json();
        Log::info("Lead Details Response:", $leadData);
        
        if (!isset($leadData['field_data'])) {
            Log::error("No field_data in lead");
            return;
        }

        // Convert field_data → key/value
        $leadInfo = collect($leadData['field_data'])
            ->mapWithKeys(function ($item) {
                return [$item['name'] => $item['values'][0] ?? null];
            });

        Log::info("Lead Parsed Info:", $leadInfo->toArray());

        // Save Lead in DB
        LeadMaster::create([
            'customer_name' => $leadInfo['full_name'] ?? $leadInfo['name'] ?? null,
            'email'         => $leadInfo['email'] ?? null,
            'mobile'        => $leadInfo['phone_number'] ?? null,
            'address'       => $leadInfo['city'] ?? null,
            'LeadSourceId'  => 1,
            'created_at'    => now(),
        ]);


        // if (!empty($leadData['field_data'])) {
        //     $leadInfo = collect($leadData['field_data'])->pluck('values', 'name')->map(fn($v) => $v[0]);
        //     \Log::info('leadInfo: = >>>>>>>>', $leadInfo);

        //     LeadMaster::create([
        //         'customer_name' => $leadInfo['full_name'] ?? null,
        //         'email' => $leadInfo['email'] ?? null,
        //         'mobile' => $leadInfo['phone_number'] ?? null,
        //         'address' => $leadInfo['city'] ?? null,
        //         'LeadSourceId' => 1, // Facebook
        //         'created_at' => now(),
        //     ]);
        // }
    }
}
