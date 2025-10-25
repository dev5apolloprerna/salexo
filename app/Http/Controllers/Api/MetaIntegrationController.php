<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MetaIntegrationController extends Controller
{
    protected $systemUserToken;

    public function __construct()
    {
        // Retrieve your system user token from DB or config
        $this->systemUserToken = config('services.meta.system_user_token');
    }

    /**
     * Entry point: Fetch Pages and Lead Forms
     */
    public function fetchPagesAndForms()
    {
        // 1️⃣ Fetch Pages
        $pagesResponse = Http::get("https://graph.facebook.com/v19.0/me/accounts", [
            'access_token' => $this->systemUserToken
        ]);

        $pages = $pagesResponse->json()['data'] ?? [];

        foreach ($pages as $page) {
            // Store or update page in DB
            $pageId = $page['id'];
            $pageName = $page['name'];

            DB::table('pages')->updateOrInsert(
                ['page_id' => $pageId],
                ['page_name' => $pageName, 'client_id' => 1] // replace client_id as needed
            );

            // 2️⃣ Fetch Lead Forms for each page
            $formsResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/leadgen_forms", [
                'access_token' => $this->systemUserToken
            ]);

            $forms = $formsResponse->json()['data'] ?? [];

            foreach ($forms as $form) {
                DB::table('lead_forms')->updateOrInsert(
                    ['form_id' => $form['id']],
                    ['form_name' => $form['name'], 'page_id' => $pageId, 'client_id' => 1]
                );
            }
        }

        return response()->json([
            'status' => 'success',
            'pages_count' => count($pages),
            'forms_count' => collect($pages)->sum(fn($p) => count($forms))
        ]);
    }

    /**
     * Fetch lead details using lead_id
     */
    public function fetchLeadDetails($leadId)
    {
        $response = Http::get("https://graph.facebook.com/{$leadId}", [
            'access_token' => $this->systemUserToken
        ]);

        $lead = $response->json();

        if (isset($lead['field_data'])) {
            $leadFields = [];
            foreach ($lead['field_data'] as $field) {
                $leadFields[$field['name']] = $field['values'][0] ?? null;
            }
            return response()->json($leadFields);
        }

        return response()->json(['error' => 'No field data found'], 404);
    }
}
