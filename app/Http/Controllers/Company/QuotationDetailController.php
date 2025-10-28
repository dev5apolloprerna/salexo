<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class QuotationDetailController extends Controller
{
    public function index(Request $request, $id)
    {
        //dd($id);
        $Product = Service::orderBy('service_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $QuotationDetail = QuotationDetail::orderBy('quotationdetailsId', 'ASC')->where(['quotationdetails.iStatus' => 1, 'quotationdetails.isDelete' => 0, 'quotationdetails.quotationID' => $id])->join('service_master', 'quotationdetails.productID', '=', 'service_master.service_id')->paginate(10);
        //dd($QuotationDetail);
        $CompanyName = Quotation::orderBy('quotationId', 'DESC')->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->first();
        
        return view('company_client.quotationdetail.index', compact('QuotationDetail', 'id', 'CompanyName','Product'));
    }

    public function createview()
    {
        return view('quotationdetail.add');
    }

    public function create(Request $request)
    {
        if($request->productID == 'other')
        {
            $service=Service::create([
                'company_id' => auth()->user()->company_id,
                'service_name' => $request->service_name,
                'HSN' => $request->uom,
                'service_description' => $request->description,
                'created_at' => now(),
            ]);
                $productId = $service->service_id; 
            } else {
                // Ensure numeric id
                $productId = $request->productID;
            }

        $Data = array(
            'productID' => $productId,
            'quotationID' => $request->quotationID,
            'description' => $request->description,
            'uom' => $request->uom,
            'quantity' => $request->quantity,
            'rate' => $request->rate,
            'amount' => $request->amount ?? 0,
            'discount' => $request->discount ?? 0,
            'netAmount' => $request->netAmount,
            'iGstPercentage' => $request->iGstPercentage
        );
        //dd($Data);
        DB::table('quotationdetails')->insert($Data);


        return redirect()->route('quotationdetails.index', [$request->quotationID])->with('success', 'Quotation Details Created Successfully.');
    }

    // QuotationDetailController.php
        public function editview($id)
        {
            $row = QuotationDetail::where('quotationdetailsId', $id)->firstOrFail();

            $payload = [
                'productID'      => (int) ($row->productID ?? 0),
                'description'    => (string) ($row->description ?? ''),
                'uom'            => (string) ($row->uom ?? ''),
                'quantity'       => (float) ($row->quantity ?? 0),
                'rate'           => (float) ($row->rate ?? 0),
                'amount'         => isset($row->amount) ? (float)$row->amount
                                     : (float)(($row->quantity ?? 0) * ($row->rate ?? 0)),
                'discount'       => (float) ($row->discount ?? 0),
                'netAmount'      => isset($row->netAmount) ? (float)$row->netAmount
                                     : (float) ($row->totalAmount ?? 0),
                'iGstPercentage' => (float) ($row->iGstPercentage ?? 0),
            ];

            // return TEXT, not application/json
            return response()->make(json_encode($payload), 200, [
                'Content-Type' => 'text/plain; charset=utf-8'
            ]);
        }

    public function update(Request $request, $Id)
    {
        $Company = DB::table('quotationdetails')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'quotationdetailsId' => $request->quotationdetailsId])
            ->update([
                'productID' => $request->productID,
                'quotationID' => $request->quotationID,
                'description' => $request->description,
                'uom' => $request->uom,
                'quantity' => $request->quantity,
                'rate' => $request->rate,
                'amount' => $request->amount ?? 0,
                'discount' => $request->discount ?? 0,
                'netAmount' => $request->netAmount,
                'iGstPercentage' => $request->iGstPercentage
            ]);
        //dd($Company);
        return redirect()->route('quotationdetails.index', [$request->quotationID])->with('success', 'Quotation Details Updated Successfully.');
    }

    public function delete(Request $request, $Id)
    {
        DB::table('quotationdetails')->where(['iStatus' => 1, 'isDelete' => 0, 'quotationdetailsId' => $Id])->delete();

        return back()->with('success', 'Quotation Details Deleted Successfully!.');
    }
    
    public function productfetch(Request $request)
    {
        $product = Service::where(['iStatus' => 1, 'isDelete' => 0,  'service_id' => $request->product])->first();

        return  json_encode($product);
    }

}