<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class PushNotificationController extends Controller
{
    public function notification($request)
    {
        $title = $request['title'];
        $body = $request['body'];
        $guid = $request['guid'];
        $type = $request['type'];


        $FcmToken = Employee::select('firebaseDeviceToken', 'emp_name as name')->where("emp_id", $request['id'])->first();

        $data = [
            'message' => [
                'token' => $FcmToken->firebaseDeviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => [
                    'guid' => (string)$guid,
                    'type' => (string)$type,
                ]
            ],
        ];

        $json_data = json_encode($data);
        $serviceAccountPath = __DIR__ . '/../../../lms-smaart-5c4138f54008.json';
        $client = new Client();
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // Save the notification to the database before sending
        $notification = Notification::create([
            'getId' => $request['id'],
            'title' => $title,
            'name' => $FcmToken->name,
            'body' => $body,
            'guid' => $guid,
            'type' => $type,
            'service' => $service ?? 0,
            'iTripId' => $iTripId ?? 0,
            'fcm_token' => $FcmToken->firebaseDeviceToken,
            'status' => 'pending',
            'created_at' => now()
        ]);

        try {

            $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
            $accessToken = $credentials->fetchAuthToken()['access_token'];
            $url = 'https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send';

            $client = new Client();
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'body' => $json_data,
            ]);
            $result = $response->getBody()->getContents();

            // Update Notification Status and Response
            $notification->update([
                'status' => 'sent',
                'response' => $result,
                'updated_at' => now()
            ]);

            return response()->json(['success' => 'Notification sent successfully', 'response' => $result]);
            // Log successful response or handle as needed
        } catch (\Exception $e) {

            // Update Notification Status on Failure
            $notification->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
                'updated_at' => now()
            ]);

            // Log or handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // }
    }
}
