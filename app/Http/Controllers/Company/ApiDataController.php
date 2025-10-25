<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ApiDataController extends Controller
{
    public function index(Request $request)
    {
        return view('company_client.api_data.index');
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
}
