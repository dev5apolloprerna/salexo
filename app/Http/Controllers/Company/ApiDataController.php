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
        
         $apiSettings = UserData::with('company')->where('company_id', $user->company_id)
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
    
            DB::table('company_client_master')->updateOrInsert(
                [
                    'company_id' => $companyId,
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

        public function metaIndex()
        {
            $companyId = auth()->user()->company_id;

            // Fetch all rows for API 3 (Meta)
            $metaSettings = UserData::with('employee','product','source')->where(['company_id'=> $companyId,'api_id'=>3])->orderBy('data_id', 'DESC')->get();

            // Employees, Sources & Products for Dropdown
            $employees=Employee::where(['iStatus'=>1,'isDelete'=>0,'company_id'=>$companyId])->get();
            $products=Service::where(['iStatus'=>1,'isDelete'=>0])->get();
            $sources=LeadSource::where(['company_id'=>$companyId])->get();


            return view('company_client.api_data.meta-settings', compact('metaSettings','employees','sources','products'));
        }
        public function metaStore(Request $request)
        {
            $companyId = auth()->user()->company_id;

            // Base validation (ad_id validated below depending on type)
            $request->validate([
                'employee_id' => ['nullable', 'integer'],
                'source_id'   => ['nullable', 'integer'],
                'product_id'  => ['nullable', 'integer'],
                'assign_type' => ['required', 'string', 'in:single,multiple'],
            ]);

            // Get existing meta rows for company
            $existing = UserData::where('company_id', $companyId)
                ->where('api_id', 3)
                ->get();

            /**
             * ------------------------------------------------------
             * CASE 1: assign_type = SINGLE
             * ------------------------------------------------------
             */
            if ($request->assign_type === 'single') 
            {
                // If ANY row exists → block single
                if ($existing->count() > 0) {
                    return back()->with('error', 'You cannot create "Single" entry because entries already exist for this company.');
                }

                // Single entries MUST NOT have ad_id
                $request->merge(['ad_id' => null]);
            }

            /**
             * ------------------------------------------------------
             * CASE 2: assign_type = MULTIPLE
             * ------------------------------------------------------
             */
            if ($request->assign_type === 'multiple') 
            {
                // If any existing entry is SINGLE → block multiple
                $hasSingle = $existing->where('assign_type', 'single')->count();
                if ($hasSingle > 0) {
                    return back()->with('error', 'Cannot add "Multiple" entry because a "Single" assignment exists for this company.');
                }

                // ad_id MUST be provided
                if (!$request->ad_id) {
                    return back()->with('error', 'Meta Advertisement Id is required for Multiple assignment.');
                }

                // AD ID must be unique for this company only
                $existsAd = UserData::where('company_id', $companyId)
                    ->where('api_id', 3)
                    ->where('ad_id', $request->ad_id)
                    ->exists();

                if ($existsAd) {
                    return back()->with('error', 'This AD ID is already assigned. AD ID must be unique.');
                }
            }

            // ------------------------------------------------------
            // Insert new row (validated above)
            // ------------------------------------------------------
            UserData::create([
                'company_id'   => $companyId,
                'api_id'       => 3,
                'emp_id'       => $request->employee_id,
                'source_id'    => $request->source_id,
                'product_id'   => $request->product_id,
                'ad_id'        => $request->ad_id,
                'assign_type'  => $request->assign_type,
            ]);

            return back()->with('success', 'Meta API setting added successfully.');
        }

        public function metaDelete($id)
        {
            UserData::where('data_id', $id)->delete();

            return back()->with('success', 'Record deleted successfully.');
        }




}
