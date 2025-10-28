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
        // Services for dropdown
        $Product = Service::where(['iStatus' => 1, 'isDelete' => 0])
            ->orderBy('service_id', 'DESC')
            ->get();

        // Quotation detail rows
        $QuotationDetail = QuotationDetail::query()
            ->from('quotationdetails')
            ->join('service_master', 'quotationdetails.productID', '=', 'service_master.service_id')
            ->where([
                'quotationdetails.iStatus'     => 1,
                'quotationdetails.isDelete'    => 0,
                'quotationdetails.quotationID' => $id,
            ])
            ->select([
                'quotationdetails.quotationdetailsId',
                'quotationdetails.productID',
                'quotationdetails.description',
                'quotationdetails.uom',
                'quotationdetails.quantity',
                'quotationdetails.rate',
                'quotationdetails.iGstPercentage',
                'quotationdetails.netAmount',
                DB::raw('service_master.service_name as productName'),
            ])
            ->orderBy('quotationdetails.quotationdetailsId', 'ASC')
            ->paginate(10);

        // Header row (LEFT JOIN so missing related rows don’t null the whole thing)
        $CompanyName = DB::table('quotation')
            ->leftJoin('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->leftJoin('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->leftJoin('year', 'quotation.iYearId', '=', 'year.year_id')
            ->where([
                'quotation.iStatus'     => 1,
                'quotation.isDelete'    => 0,
                'quotation.quotationId' => $id,
            ])
            ->select([
                'quotation.quotationId',
                'quotation.entryDate',
                'quotation.iQuotationNo',
                'company_client_master.company_name',
                'party.strPartyName',
                'year.strYear',
            ])
            ->first(); // may be null

        return view('company_client.quotationdetail.index', compact(
            'QuotationDetail', 'id', 'CompanyName', 'Product'
        ));
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
    // JSON product fetch (used by AJAX)
        public function productfetch(Request $request)
        {
            $product = $request->input('product');

            if ($product === 'other') {
                return response()->json([
                    'productDescription' => null,
                    'message' => 'Other product selected.',
                ], 200);
            }

            $id = (int) $product;

            $srv = Service::where([
                'iStatus'    => 1,
                'isDelete'   => 0,
                'service_id' => $id,
            ])->first();

            if (!$srv) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return response()->json([
                'service_id'         => $srv->service_id,
                'service_name'       => $srv->service_name,
                // pick the correct column from your table:
                'productDescription' => $srv->service_description ?? $srv->description ?? '',
                'HSN' => $srv->HSN ?? $srv->HSN ?? '',
            ], 200);
        }

    
    /*public function productfetch(Request $request)
    {
        dd($request);
        $product = Service::where(['iStatus' => 1, 'isDelete' => 0,  'service_id' => $request->product])->first();

        return  json_encode($product);
    }*/

}