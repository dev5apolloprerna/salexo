<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\CompanyClient;
use App\Models\Year;
use App\Models\Party;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\TermCondition;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

//use PDF;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{

    public function index(Request $request)
    {
        
         $CompanyName = $request->companyName;
         $ProductName = $request->productName;
         $PartyName = $request->partyName;
        
        $Product = Service::orderBy('service_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['iStatus' => 1, 'isDeleted' => 0])->get();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();
        $Quotation = Quotation::orderBy('quotationId', 'DESC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0])
            
            ->when($request->companyName, fn ($query, $CompanyName) => $query
                ->where('quotation.iCompanyId', '=', $CompanyName))
            ->when($ProductName, fn ($query, $productName) => $query->WhereIn(
                'quotation.quotationId',
                function ($query) use ($productName) {
                    $query->select('quotationdetails.quotationID')
                        ->from(with(new QuotationDetail)->getTable())
                        ->whereIn('productID', $productName);
                }
            ))
            ->when($request->partyName, fn ($query, $PartyName) => $query
                ->where('quotation.iPartyId', '=', $PartyName))
                
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->paginate(25);
        //dd($Party);
        return view('company_client.quotation.index', compact('Year', 'Company', 'Party', 'Quotation','Product','CompanyName','ProductName','PartyName'));
    }
    
    public function getNextQuotationNo($companyId)
    {
        $nextQuotationNo = Quotation::getNextQuotationNo($companyId);
        $nextQuotationNo = $nextQuotationNo . "/24-25";
        
        return response()->json($nextQuotationNo);
    }

    public function createview()
    {
                $user = Auth::user();

        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['company_id'=>$user->company_id,'iStatus' => 1, 'isDeleted' => 0])->first();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();
        $Quotation = Quotation::orderBy('quotationId', 'DESC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->get();

        return view('company_client.quotation.add', compact('Year', 'Company', 'Party', 'Quotation'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        //dd($request);
        $Data = array(
            'iYearId' => $request->iYearId,
            'iQuotationNo' => $request->iQuotationNo,
            'iPartyId' => $request->iPartyId,
            'iCompanyId' => $user->company_id,
            'quotationValidity' => $request->quotationValidity,
            'modeOfDespatch' => $request->modeOfDespatch,
            'deliveryTerm' => $request->deliveryTerm,
            'paymentTerms' => $request->paymentTerms,
            'entryDate' => date('Y-m-d', strtotime($request->entryDate)),
            'iGstType' => $request->iGstType,
            'strTermsCondition' => $request->strTermsCondition
        );
        // dd($Data);
        $getId=DB::table('quotation')->insertGetId($Data);
//dd($getId);
        return redirect()->route('quotationdetails.index',$getId)->with('success', 'Quotation Created Successfully.');
    }

    public function editview(Request $request, $Id)
    {
                        $user = Auth::user();

        $Data = Quotation::where([
            'iStatus' => 1, 'isDelete' => 0, 'quotationId' => $Id
        ])->firstOrFail();
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['company_id'=>$user->company_id,'iStatus' => 1, 'isDeleted' => 0])->first();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();


        return view('company_client.quotation.edit',compact('Data','Company','Party','Year'));
       /* return response()->json([
            'iYearId'           => $Data->iYearId,
            'iQuotationNo'      => $Data->iQuotationNo,
            'iPartyId'          => $Data->iPartyId,
            'iCompanyId'        => $Data->iCompanyId,
            'quotationValidity' => $Data->quotationValidity,
            'modeOfDespatch'    => $Data->modeOfDespatch,
            'deliveryTerm'      => $Data->deliveryTerm,
            'paymentTerms'      => $Data->paymentTerms,
            // nullsafe operator in case it's null
            'entryDate'         => $Data->entryDate?->format('d-m-Y'),
            'iGstType'          => $Data->iGstType,
            'strTermsCondition' => $Data->strTermsCondition,
        ]);*/
    }



    public function update(Request $request, $Id)
    {
        //dd($Id);
        $quotationId = $request->quotationId;
        $Company = DB::table('quotation')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $request->quotationId])
            ->update([
                'iYearId' => $request->iYearId,
                'iQuotationNo' => $request->iQuotationNo,
                'iPartyId' => $request->iPartyId,
                'iCompanyId' => $request->iCompanyId,
                'quotationValidity' => $request->quotationValidity,
                'modeOfDespatch' => $request->modeOfDespatch,
                'deliveryTerm' => $request->deliveryTerm,
                'paymentTerms' => $request->paymentTerms,
                'entryDate' => date('Y-m-d', strtotime($request->entryDate)),
                'iGstType' => $request->iGstType,
                'strTermsCondition' => $request->strTermsCondition
            ]);
        //dd($Company);
        return redirect()->route('quotationdetails.index',$quotationId)->with('success', 'Quotation Updated Successfully.');
    }

    public function delete(Request $request, $Id)
    {
        DB::table('quotation')->where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $Id])->delete();

        return redirect()->route('quotation.index')->with('success', 'Quotation Deleted Successfully!.');
    }

    public function showdetail(Request $request, $id)
    {
        $popupQuotation = Quotation::select('party.address1','company_client_master.company_name','company_client_master.Address','company_client_master.email','company_client_master.mobile','company_client_master.GST','company_client_master.plan_id','party.strPartyName','party.address2','party.address3','party.iMobile','party.strEmail','quotation.iQuotationNo','quotation.entryDate','quotation.iCompanyId','quotation.quotationValidity','quotation.modeOfDespatch','quotation.deliveryTerm','quotation.paymentTerms','quotation.iGstType','quotation.strTermsCondition')
            ->orderBy('quotationId', 'ASC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->first();
            // $path = ("https://quotation.sanjay-sales.com/CompanyLogo/" . $popupQuotation->strLogo);
            $path = ("https://salexo.in/assets/images/logo.png");
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $pic = 'data:CompanyLogo/' . $type . ';base64,' . base64_encode(($data));


        $QuotationDetail = QuotationDetail::orderBy('quotationdetailsId', 'ASC')->where(['quotationdetails.iStatus' => 1, 'quotationdetails.isDelete' => 0, 'quotationdetails.quotationID' => $id])->get();
        $TermCondition = TermCondition::orderBy('termconditionId', 'ASC')->where(['termcondition.iStatus' => 1, 'termcondition.isDelete' => 0, 'termcondition.companyID'=>$popupQuotation->iCompanyId])
            ->get();
        
        return view('company_client.quotation.showdetails', compact('popupQuotation', 'QuotationDetail', 'pic','TermCondition'));
    }

    public function detailPDF(Request $request, $id)
    {
        //dd($id);
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['iStatus' => 1, 'isDeleted' => 0])->get();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();

        $Quotation = Quotation::select('party.address1','company_client_master.company_name','company_client_master.Address','company_client_master.email','company_client_master.mobile','company_client_master.plan_id','company_client_master.GST','party.strPartyName','party.address2','party.address3','party.iMobile','party.strEmail','quotation.iQuotationNo','quotation.entryDate','quotation.iCompanyId','quotation.quotationValidity','quotation.modeOfDespatch','quotation.deliveryTerm','quotation.paymentTerms','quotation.iGstType','quotation.strTermsCondition')
        
        ->orderBy('quotation.quotationId', 'ASC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->first();
            // $path = ("https://quotation.sanjay-sales.com/CompanyLogo/" . $Quotation->strLogo);
            $path = ("https://salexo.in/assets/images/logo.png");
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $pic = 'data:CompanyLogo/' . $type . ';base64,' . base64_encode(($data));


        $QuotationDetail = QuotationDetail::orderBy('quotationdetailsId', 'ASC')->where(['quotationdetails.iStatus' => 1, 'quotationdetails.isDelete' => 0, 'quotationdetails.quotationID' => $id])->get();

        //$Quotation->iCompanyId;
        $TermCondition = TermCondition::orderBy('termconditionId', 'ASC')->where(['termcondition.iStatus' => 1, 'termcondition.isDelete' => 0, 'termcondition.companyID'=>$Quotation->iCompanyId])
            //  ->join('quotation', 'termcondition.companyID', '=', 'quotation.iCompanyId')
            ->get();
        //dd($TermCondition);

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadview('company_client.quotation.detailPDF', ['Quotation' => $Quotation, 'QuotationDetail' => $QuotationDetail, 'TermCondition' => $TermCondition, 'pic' => $pic]);
        
        return $pdf->download($Quotation->strPartyName . $Quotation->iQuotationNo . '.' . 'pdf');
        //return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('reports.invoiceSell')->stream();
    }

    public function search(Request $request)
    {
        $CompanyName = $request->companyName;
        //dd($CompanyName);
        $ProductNameArr[] = $request->productName;
        $PartyName = $request->partyName;
        //dd($PartyName);

        $Product = Product::orderBy('productId', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['iStatus' => 1, 'isDeleted' => 0])->get();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();
        $Quotation = Quotation::orderBy('quotationId', 'DESC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0])
            //->whereIn('quotation.iPartyId', array(90, 91, 92, 93))
            ->when($request->partyName, fn ($query, $PartyName) => $query->where('party.strPartyName', 'like', '%' . $PartyName . '%'))
            ->when($request->companyName, fn ($query, $CompanyName) => $query->where('company_client_master.company_id',  $CompanyName ))
            ->when($ProductNameArr, fn ($query, $productName) => $query->WhereIn(
                'quotation.quotationId',
                function ($query) use ($productName) {
                    $query->select('quotationdetails.quotationID')
                        ->from(with(new QuotationDetail)->getTable())
                        ->whereIn('productID', $productName);
                }
            ))
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->paginate(25);
        //($Quotation);
        return view('company_client.quotation.index', compact('Year', 'Company', 'Party','Product', 'Quotation'));
    }
    
    public function Mapping(Request $request)
    {
        //dd('hello');
        $Mapping = Party::where(['iStatus' => 1, 'isDelete' => 0])->whereIn( 'iCompanyId',[$request->company])->get();

        //dd($Mapping);
        $html = "";

        foreach ($Mapping as $mapping) {
            $html .= "<option value='" . $mapping->partyId . "'>" . $mapping->strPartyName . "</option>";
        }

        return $html;
    }
    
    public function termconditionFetch(Request $request)
    {
        $company = TermCondition::where(['iStatus' => 1, 'isDelete' => 0,  'companyID' => $request->fetchcompany])->get();
        return  json_encode($company);
    }

    public function copyQuotation(Request $request, $Id)
    {
        //dd($Id);
        $Quotation = Quotation::where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $Id])->first();

        //dd($Quotation);

        $Data = array(
            'iYearId' => $Quotation->iYearId,
            'iQuotationNo' => $Quotation->iQuotationNo,
            'iPartyId' => $Quotation->iPartyId,
            'iCompanyId' => $Quotation->iCompanyId,
            'quotationValidity' => $Quotation->quotationValidity,
            'modeOfDespatch' => $Quotation->modeOfDespatch,
            'deliveryTerm' => $Quotation->deliveryTerm,
            'paymentTerms' => $Quotation->paymentTerms,
            'entryDate' => date('Y-m-d', strtotime($Quotation->entryDate)),
            'iGstType' => $Quotation->iGstType,
            'strTermsCondition' => $Quotation->strTermsCondition
        );
        //dd($Data);
        $getId = DB::table('quotation')->insertGetId($Data);
        //dd($getId);

        $QuotationDetail = QuotationDetail::orderBy('quotationdetailsId', 'ASC')->where(['quotationdetails.iStatus' => 1, 'quotationdetails.isDelete' => 0, 'quotationdetails.quotationID' => $Id])->get();
        //dd($QuotationDetail);

        foreach ($QuotationDetail as $detailcopy) {

            $Data = array(
                'productID' => $detailcopy->productID,
                'quotationID' => $getId,
                'description' => $detailcopy->description,
                'uom' => $detailcopy->uom,
                'quantity' => $detailcopy->quantity,
                'rate' => $detailcopy->rate,
                'amount' => $detailcopy->amount,
                'discount' => $detailcopy->discount,
                'netAmount' => $detailcopy->netAmount,
                'iGstPercentage' => $detailcopy->iGstPercentage
            );

            DB::table('quotationdetails')->insert($Data);
        }


        return back()->with('success', 'Quotation Copied Successfully.');
    }
     public function sendWhatsApp(Request $request, $id)
    {
        // 1) Validate phone (WhatsApp requires country code, no "+")
        $phone = preg_replace('/\D/', '', $request->input('phone'));
        if (!$phone) {
            return back()->with('error', 'Invalid phone number.');
        }
        $pdfUrl = route('quotation.DetailPDF', $id, true);

        // 3) Prepare payload for Cloud API
        $token         = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'document',
            'document' => [
                // You can send by URL directly (no media upload step needed for documents)
                'link'     => $pdfUrl,
                'filename' => "Quotation-{$id}.pdf",
            ],
        ];

        $resp = Http::withToken($token)
            ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", $payload);

        if (!$resp->ok()) {
            // Helpful error reporting
            $err = $resp->json();
            report(new \Exception('WhatsApp send failed: ' . json_encode($err)));
            return back()->with('error', $err['error']['message'] ?? 'Failed to send WhatsApp message.');
        }

        return back()->with('success', 'Quotation sent on WhatsApp!');
    }
   
}