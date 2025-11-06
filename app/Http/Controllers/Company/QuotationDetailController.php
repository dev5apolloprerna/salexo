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
    public function serviceLookup(Request $request)
    {
        $q = trim($request->get('q', ''));

        $rows = Service::query()
            ->when($q !== '', fn($x) => $x->where('service_name', 'like', "%{$q}%"))
            ->where('isDelete', 0) // if you have this flag
            ->orderBy('service_name')
            ->limit(20)
            ->get([
                'service_id as id',
                'service_name as text',
                'HSN',
                'service_description',
            ]);

        // Select2 wants { results: [{id, text, ...}] }
        return response()->json([
            'results' => $rows,
        ]);
    }

    public function serviceById(int $id)
    {
        $row = Service::query()
            ->where('service_id', $id)
            ->first(['service_id as id', 'service_name as text', 'HSN', 'service_description']);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($row);
    }


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
         $data = $request->validate([
        'quotationID'      => ['required', 'integer'],
        'productID'        => ['required'], // can be numeric id OR '__new__:name' OR 'other'
        'service_name'     => ['nullable', 'string', 'max:255'], // used when new
        'description'      => ['nullable', 'string'],
        'uom'              => ['required', 'string', 'max:50'],  // you’re using this as HSN too
        'quantity'         => ['required', 'numeric'],
        'rate'             => ['required', 'numeric'],
        'amount'           => ['nullable', 'numeric'],
        'discount'         => ['nullable', 'numeric'],
        'netAmount'        => ['required', 'numeric'],
        'iGstPercentage'   => ['required', 'numeric'],
    ]);
        $serviceId = $this->resolveServiceIdForRequest($request);

    // ---- Compute amounts safely (server truth) ----
    $qty       = (float)$data['quantity'];
    $rate      = (float)$data['rate'];
    $amountSrv = $qty * $rate; // server truth
    $discount  = isset($data['discount']) ? (float)$data['discount'] : 0.0;

    // If you want to trust UI netAmount, keep it; else recompute your own here.
    $netAmount = (float)$data['netAmount'];

    DB::table('quotationdetails')->insert([
        'productID'       => $serviceId,
        'quotationID'     => (int)$data['quotationID'],
        'description'     => $data['description'],
        'uom'             => $data['uom'],
        'quantity'        => $qty,
        'rate'            => $rate,
        'amount'          => $amountSrv,           // prefer server calc to request
        'discount'        => $discount,
        'netAmount'       => $netAmount,
        'iGstPercentage'  => (float)$data['iGstPercentage'],
        'iStatus'         => 1,                    // set defaults if your table uses them
        'isDelete'        => 0,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    return redirect()
        ->route('quotationdetails.index', [$data['quotationID']])
        ->with('success', 'Quotation Details Created Successfully.');


/*        return redirect()->route('quotationdetails.index', [$request->quotationID])->with('success', 'Quotation Details Created Successfully.');
*/    }

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
         $data = $request->validate([
        'quotationdetailsId'=> ['required', 'integer'],
        'quotationID'       => ['required', 'integer'],
        'productID'         => ['required'],                   // numeric or '__new__:' or 'other'
        'service_name'      => ['nullable', 'string', 'max:255'],
        'description'       => ['nullable', 'string'],
        'uom'               => ['required', 'string', 'max:50'],
        'quantity'          => ['required', 'numeric'],
        'rate'              => ['required', 'numeric'],
        'amount'            => ['nullable', 'numeric'],
        'discount'          => ['nullable', 'numeric'],
        'netAmount'         => ['required', 'numeric'],
        'iGstPercentage'    => ['required', 'numeric'],
    ]);

        $serviceId = $this->resolveServiceIdForRequest($request);

    // ---- Compute amounts safely (server truth) ----
    $qty       = (float)$data['quantity'];
    $rate      = (float)$data['rate'];
    $amountSrv = $qty * $rate;
    $discount  = isset($data['discount']) ? (float)$data['discount'] : 0.0;
    $netAmount = (float)$data['netAmount'];

    DB::table('quotationdetails')
        ->where([
            'quotationdetailsId' => (int)$data['quotationdetailsId'],
            'iStatus'            => 1,
            'isDelete'           => 0,
        ])
        ->update([
            'productID'       => $serviceId,
            'quotationID'     => (int)$data['quotationID'],
            'description'     => $data['description'],
            'uom'             => $data['uom'],
            'quantity'        => $qty,
            'rate'            => $rate,
            'amount'          => $amountSrv,
            'discount'        => $discount,
            'netAmount'       => $netAmount,
            'iGstPercentage'  => (float)$data['iGstPercentage'],
            'updated_at'      => now(),
        ]);

    return redirect()
        ->route('quotationdetails.index', [$data['quotationID']])
        ->with('success', 'Quotation Details Updated Successfully.');
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

    private function resolveServiceIdForRequest(Request $request): int
{
    $raw = $request->input('productID');

    // Case 1: Select2 "tags": "__new__:Some Service Name"
    if (is_string($raw) && str_starts_with($raw, '__new__:')) {
        $typed = trim(substr($raw, 8));
        $name  = $request->string('service_name')->trim()->value() ?: $typed;

        $svc = Service::create([
            'company_id'          => auth()->user()->company_id ?? null,
            'service_name'        => $name,
            'HSN'                 => $request->input('uom', ''),                // or dedicate a separate HSN field
            'service_description' => $request->input('description', ''),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
        return (int)$svc->service_id;
    }

    // Case 2: Old "other" option
    if ($raw === 'other') {
        $name = $request->string('service_name')->trim()->value();
        if (!$name) {
            abort(422, 'Please enter Service Name for "Other".');
        }

        $svc = Service::create([
            'company_id'          => auth()->user()->company_id ?? null,
            'service_name'        => $name,
            'HSN'                 => $request->input('uom', ''),
            'service_description' => $request->input('description', ''),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
        return (int)$svc->service_id;
    }

    // Case 3: Existing numeric id
    if (is_numeric($raw)) {
        return (int)$raw;
    }

    // Invalid payload
    abort(422, 'Invalid product/service selection.');
}


}