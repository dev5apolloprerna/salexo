<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{QuotationDetail, Quotation, Service};

class QuotationDetailApiController extends Controller
{
   
    public function index(Request $request)
{
    // Optional knobs (no pagination)
    $sortBy  = $request->query('sort_by', 'quotationdetails.quotationdetailsId'); // any selected column
    $sortDir = strtolower($request->query('sort_dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $limit   = $request->integer('limit'); // optional hard cap; if null => all rows

    // Header (safe LEFT JOINs)
    $header = DB::table('quotation')
        ->leftJoin('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
        ->leftJoin('party', 'quotation.iPartyId', '=', 'party.partyId')
        ->leftJoin('year', 'quotation.iYearId', '=', 'year.year_id')
        ->where([
            'quotation.iStatus'     => 1,
            'quotation.isDelete'    => 0,
            'quotation.quotationId' => $request->quotation_id,
        ])
        ->select([
            'quotation.quotationId',
            'quotation.entryDate',
            'quotation.iQuotationNo',
            'company_client_master.company_name',
            'party.strPartyName',
            'year.strYear',
        ])
        ->first();

    if (!$header) {
        return response()->json(['success'=>false,'message' => 'Quotation not found'], 404);
    }

    // Items (NO pagination)
    $itemsQuery = QuotationDetail::query()
        ->from('quotationdetails')
        ->join('service_master', 'quotationdetails.productID', '=', 'service_master.service_id')
        ->where([
            'quotationdetails.iStatus'     => 1,
            'quotationdetails.isDelete'    => 0,
            'quotationdetails.quotationID' => $request->quotation_id,
        ])
        ->select([
            'quotationdetails.quotationdetailsId',
            'quotationdetails.quotationID',
            'quotationdetails.productID',
            'quotationdetails.description',
            'quotationdetails.uom',
            'quotationdetails.quantity',
            'quotationdetails.rate',
            'quotationdetails.amount',
            'quotationdetails.discount',
            'quotationdetails.netAmount',
            'quotationdetails.iGstPercentage',
            DB::raw('service_master.service_name as productName'),
        ])
        ->orderBy($sortBy, $sortDir);

    if ($limit && $limit > 0) {
        $itemsQuery->limit($limit);
    }

    $items = $itemsQuery->get();

    return response()->json([
        'success'=>true,
        'message'=>"Quotation details",
        'quotation_detail' => $header,
        'products'  => $items,
        'count'  => $items->count(), // handy meta
    ], 200);
}


    /**
     * GET /api/quotation-details/{id}
     * Show one detail row (edit prefill)
     */
    public function productshow(Request $request)
    {
        $row = QuotationDetail::query()
            ->from('quotationdetails')
            ->where(['quotationdetailsId' => $request->quotationdetailsId, 'isDelete' => 0])
            ->first([
                'quotationdetailsId',
                'quotationID',
                'productID',
                'description',
                'uom',
                'quantity',
                'rate',
                'amount',
                'discount',
                'netAmount',
                'iGstPercentage',
            ]);

        if (!$row) return response()->json(['message' => 'Not found'], 404);

        // payload same as your editview structure
        $payload = [
            'productID'      => (int) ($row->productID ?? 0),
            'description'    => (string) ($row->description ?? ''),
            'uom'            => (string) ($row->uom ?? ''),
            'quantity'       => (float) ($row->quantity ?? 0),
            'rate'           => (float) ($row->rate ?? 0),
            'amount'         => isset($row->amount) ? (float)$row->amount
                                 : (float)(($row->quantity ?? 0) * ($row->rate ?? 0)),
            'discount'       => (float) ($row->discount ?? 0),
            'netAmount'      => isset($row->netAmount) ? (float)$row->netAmount : 0,
            'iGstPercentage' => (float) ($row->iGstPercentage ?? 0),
            'quotationdetailsId' => (int) $row->quotationdetailsId,
            'quotationID'        => (int) $row->quotationID,
        ];

        return response()->json([
            'success'=>true,
            'message'=>'Quotation Product List',
            'quotation_product_list'=>$payload], 200);
    }

    /**
     * POST /api/quotations/{quotation_id}/details
     * Create detail row
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $quotation_id=$request->quotation_id;

        $data = $request->validate([
            'productID'       => ['required'],                    // numeric id OR '__new__:name' OR 'other'
            'service_name'    => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'uom'             => ['required', 'string', 'max:50'],
            'quantity'        => ['required', 'numeric'],
            'rate'            => ['required', 'numeric'],
            'amount'          => ['nullable', 'numeric'],
            'discount'        => ['nullable', 'numeric'],
            'netAmount'       => ['required', 'numeric'],
            'iGstPercentage'  => ['required', 'numeric'],
        ]);

        // ensure quotation exists & is active
        $exists = Quotation::where([
            'quotationId' => $quotation_id,
            'iStatus'     => 1,
            'isDelete'    => 0,
        ])->exists();
        if (!$exists) return response()->json(['success'=>false,'message' => 'Quotation not found'], 404);

        $serviceId = $this->resolveServiceIdForRequest($request);

        // server truth calculations
        $qty       = (float)$data['quantity'];
        $rate      = (float)$data['rate'];
        $amountSrv = $qty * $rate;
        $discount  = isset($data['discount']) ? (float)$data['discount'] : 0.0;
        $netAmount = (float)$data['netAmount'];

        $id = DB::table('quotationdetails')->insertGetId([
            'productID'       => $serviceId,
            'quotationID'     => $quotation_id,
            'description'     => $data['description'] ?? null,
            'uom'             => $data['uom'],
            'quantity'        => $qty,
            'rate'            => $rate,
            'amount'          => $amountSrv,
            'discount'        => $discount,
            'netAmount'       => $netAmount,
            'iGstPercentage'  => (float)$data['iGstPercentage'],
            'created_by'      => $user->emp_id ?? null,
            'iStatus'         => 1,
            'isDelete'        => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json([
            'success'=>true,
            'message' => 'Quotation detail created',
            'id'      => $id,
        ], 201);
    }

    /**
     * PUT /api/quotation-details/{id}
     * Update detail row
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $id=$request->quotationdetailsId;

        $data = $request->validate([
            'productID'       => ['required'],                   // numeric or '__new__:' or 'other'
            'service_name'    => ['nullable', 'string'],
            'description'     => ['nullable', 'string'],
            'uom'             => ['required', 'string'],
            'quantity'        => ['required', 'numeric'],
            'rate'            => ['required', 'numeric'],
            'amount'          => ['nullable', 'numeric'],
            'discount'        => ['nullable', 'numeric'],
            'netAmount'       => ['required', 'numeric'],
            'iGstPercentage'  => ['required', 'numeric'],
        ]);

        $row = DB::table('quotationdetails')->where([
            'quotationdetailsId' => $id,
            'isDelete'           => 0,
        ])->first();

        if (!$row) return response()->json(['success'=>false,'message' => 'Not found'], 404);

        $serviceId = $this->resolveServiceIdForRequest($request);

        $qty       = (float)$data['quantity'];
        $rate      = (float)$data['rate'];
        $amountSrv = $qty * $rate;
        $discount  = isset($data['discount']) ? (float)$data['discount'] : 0.0;
        $netAmount = (float)$data['netAmount'];

        DB::table('quotationdetails')
            ->where(['quotationdetailsId' => $id, 'isDelete' => 0])
            ->update([
                'productID'       => $serviceId,
                'description'     => $data['description'] ?? null,
                'uom'             => $data['uom'],
                'quantity'        => $qty,
                'rate'            => $rate,
                'amount'          => $amountSrv,
                'discount'        => $discount,
                'netAmount'       => $netAmount,
                'iGstPercentage'  => (float)$data['iGstPercentage'],
                'updated_by'      => $user->emp_id ?? null,
                'updated_at'      => now(),
            ]);

        return response()->json(['success'=>true,'message' => 'Quotation detail updated', 'id' => $id], 200);
    }

    /**
     * DELETE /api/quotation-details/{id}
     */
    public function destroy(Request $request)
    {
        $id=$request->quotationdetailsId;
        $deleted = DB::table('quotationdetails')
            ->where(['quotationdetailsId' => $id, 'isDelete' => 0])
            ->delete();

        if (!$deleted) return response()->json(['success'=>false,'message' => 'Not found'], 404);

        return response()->json(['success'=>true,'message' => 'Quotation detail deleted'], 200);
    }

    /**
     * GET /api/services/{id}/meta
     * Your old productfetch (returns description + HSN)
     */
    public function productMeta(int $id)
    {
        $srv = Service::where([
            'iStatus'    => 1,
            'isDelete'   => 0,
            'service_id' => $id,
        ])->first();

        if (!$srv) return response()->json(['success'=>false,'message' => 'Product not found'], 404);

        return response()->json([
            'service_id'         => $srv->service_id,
            'service_name'       => $srv->service_name,
            'productDescription' => $srv->service_description ?? $srv->description ?? '',
            'HSN'                => $srv->HSN ?? '',
        ], 200);
    }

    /* ---------------- helpers ---------------- */

    private function resolveServiceIdForRequest(Request $request): int
    {
        $raw = $request->input('productID');

        if (is_string($raw) && str_starts_with($raw, '__new__:')) {
            $typed = trim(substr($raw, 8));
            $name  = $request->string('service_name')->trim()->value() ?: $typed;

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

        if ($raw === 'other') {
            $name = $request->string('service_name')->trim()->value();
            if (!$name) abort(422, 'Please enter Service Name for "Other".');

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

        if (is_numeric($raw)) return (int)$raw;

        abort(422, 'Invalid product/service selection.');
    }
}
