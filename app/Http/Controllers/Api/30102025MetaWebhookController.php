<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\LeadMaster;

class MetaWebhookController extends Controller
{
    // Facebook verification (GET)
    public function verify(Request $request)
    {
        $verifyToken = 'mycustom78'; // set this value yourself

        if ($request->hub_verify_token === $verifyToken) {
            return response($request->hub_challenge, 200);
        }

        return response('Invalid verify token', 403);
    }

    // Handle webhook POST
    public function receive(Request $request)
    {
        $data = $request->all();
        \Log::info('Meta Webhook Received:', $data);

        $leadgenId = $data['entry'][0]['changes'][0]['value']['leadgen_id'] ?? null;
        
        if ($leadgenId) {
            
            $this->fetchLeadDetails($leadgenId);
        }

        return response()->json(['status' => 'ok']);
    }

    // Fetch lead details via Graph API
    public function fetchLeadDetails($leadgenId)
    {
        $accessToken = env('META_GRAPH_TOKEN');
        
        $url = "https://graph.facebook.com/v18.0/{$leadgenId}?access_token={$accessToken}";
        $response = Http::get($url);
        $leadData = $response->json();
        
        if (!empty($leadData['field_data'])) {
            $leadInfo = collect($leadData['field_data'])->pluck('values', 'name')->map(fn($v) => $v[0]);

            LeadMaster::create([
                'customer_name' => $leadInfo['full_name'] ?? null,
                'email' => $leadInfo['email'] ?? null,
                'mobile' => $leadInfo['phone_number'] ?? null,
                'address' => $leadInfo['city'] ?? null,
                'LeadSourceId' => 1, // Facebook
                'created_at' => now(),
            ]);
        }
    }
}
