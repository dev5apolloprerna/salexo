<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

use App\Models\Employee;
use App\Models\LeadSource;
use App\Models\Service;
use App\Models\UserData;

use Illuminate\Support\Facades\Auth;



class ApiDataController extends Controller
{
     public function index(Request $request)
    {
        $user = Auth::user();
        $employees=Employee::where(['iStatus'=>1,'isDelete'=>0,'company_id'=>$user->company_id])->get();
        $product=Service::where(['iStatus'=>1,'isDelete'=>0])->get();
        $leadSources=LeadSource::where(['company_id'=>$user->company_id])->get();
        
         $apiSettings = UserData::where('company_id', $user->company_id)
            ->get()
            ->keyBy('api_id');   // so you can do $apiSettings[1], [2], [3]

        $indiamartSettings = $apiSettings->get(1); // may be null if not set
        $generalSettings   = $apiSettings->get(2);
        $metaSettings      = $apiSettings->get(3);

        return view('company_client.api_data.index',compact('employees','product','leadSources','indiamartSettings','generalSettings','metaSettings'));
    }
    public function indiamart()
    {
        $params = [
            'API Name' => 'IndiaMart WebHook API',
            'Method'   => 'POST',
            'Params'   => [
                "SENDER_NAME"        => "Prabhat",
                "SENDER_MOBILE"      => "+91-9999999999",
                "SENDER_EMAIL"       => "abcdeprabhat@gmail.com",
                "SENDER_COMPANY"     => "ABC Pvt Ltd.",
                "SENDER_ADDRESS"     => "Sec 135, Noida, Uttar Pradesh",
                "SENDER_MOBILE_ALT"  => "+91-8888888888",
                "QUERY_PRODUCT_NAME" => "Mineral Water Bottle",
                "QUERY_MESSAGE"      => "I want to purchase an Empty Mineral Water Bottle. Kindly send me price and other details. 
                                          Quantity: 100000 Piece. 
                                          Probable Order Value: Rs. 10 to 20 Lakh. 
                                          Probable Requirement Type: Business Use"
            ]
        ];

        $pdf = Pdf::loadView('company_client.api_data.api_doc', ['data' => $params]);

        return $pdf->stream('indiamart_api.pdf'); // opens in browser
    }

    public function general()
    {
        $params = [
            'API Name' => 'General API',
            'Method'   => 'POST',
            'Params'   => [
                "company_name"        => "Future Office Solutions",
                "gst_no"              => "29AABCF2345E1Z7",
                "contact_person_name" => "Neha Sharma",
                "email"               => "neha.sharma@futureoffice.com",
                "mobile"              => "+91-9812345678",
                "alternative_mobile"  => "+91-9765432189",
                "address"             => "Koramangala, Bangalore, Karnataka, India",
                "remarks"             => "Looking for bulk purchase of ergonomic office chairs. Expected quantity: 500 units. Requirement Type: Corporate Office Setup.",
                "product_service"     => "Ergonomic Office Chairs",
                "lead_source"         => "JustDial"
            ]
        ];

        $pdf = Pdf::loadView('company_client.api_data.api_doc', ['data' => $params]);
        return $pdf->stream('general_api.pdf'); // opens in browser
    }
    //store api setting data 
        public function store(Request $request)
        {

            if($request->api_name == 'indiamart')
            {
                $api_id=1;
            }
            else if($request->api_name == 'general')
            {
                $api_id=2;
            }
            else{
                $api_id=3;
            }

            $request->validate([
                'employee_id' => ['nullable', 'integer'],
                'source_id'   => ['nullable', 'integer'],
                'api_name'    => ['nullable', 'string'], // optional, in case you want to use it later
            ]);

            $companyId = auth()->user()->company_id;

            // upsert row in user_data table for this company
            DB::table('user_data')->updateOrInsert(
                ['company_id' => $companyId,'api_id'=>$api_id], // unique key
                [
                    'emp_id'     => $request->input('employee_id') ?: null,
                    'source_id'  => $request->input('source_id') ?: null,
                    'api_id'     => $api_id,
                    'updated_at' => now(),
                    'created_at' => now(), // will only be used on insert
                ]
            );

            return response()->json([
                'success'    => true,
                'message'    => 'API settings saved successfully.',
            ]);
        }
            public function storeMetaTokens(Request $request)
        {
            $request->validate([
                'access_token' => ['nullable', 'string'],
                'verify_token' => ['nullable', 'string'],
            ]);
    
            $companyId = auth()->user()->company_id;
    
            DB::table('user_data')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'api_id'     => 3, // 3 = Meta API
                ],
                [
                    'access_token' => $request->input('access_token') ?: null,
                    'verify_token' => $request->input('verify_token') ?: null,
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );
    
            return response()->json([
                'success' => true,
                'message' => 'Meta API tokens saved successfully.',
            ]);
        }

}
