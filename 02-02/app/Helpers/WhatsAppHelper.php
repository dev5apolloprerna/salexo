<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    /**
     * Send WhatsApp Template Message
     *
     * @param string $mobile - recipient 10-digit mobile
     * @param string $templateName - template name created in Meta
     * @param array $templateParams - array of BODY parameters
     * @return bool
     */
    public static function sendTemplateMessage($mobile, $templateName, $templateParams = [])
    {
        $token = config('app.whatsapp_token');
        $phoneId = config('app.whatsapp_phone_id');

        if (!$token || !$phoneId) {
            Log::error("WhatsApp config missing: token or phone_id not found");
            return false;
        }

        $recipient = "91" . preg_replace('/\D/', '', $mobile); // only digits

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v21.0/{$phoneId}/messages", [
                    "messaging_product" => "whatsapp",
                    "to"   => $recipient,
                    "type" => "template",
                    "template" => [
                        "name" => $templateName,
                        "language" => ["code" => "en"],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => array_map(function ($value) {
                                    return [
                                        "type" => "text",
                                        "text" => (string) $value
                                    ];
                                }, $templateParams)
                            ]
                        ]
                    ]
                ]);

            Log::info("WhatsApp Response: ", [
                'recipient' => $recipient,
                'template'  => $templateName,
                'status'    => $response->status(),
                'body'      => $response->json()
            ]);

            return $response->successful();
        } 
        catch (\Exception $e) {
            Log::error("WhatsApp Exception: " . $e->getMessage());
            return false;
        }
    }
}
