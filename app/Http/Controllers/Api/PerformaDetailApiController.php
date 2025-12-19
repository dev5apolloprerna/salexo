<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{InvoiceDetail, PerformaInvoice, Service};
use Illuminate\Support\Facades\Validator;

class PerformaDetailApiController extends Controller
{
   
    public function index(Request $request)
{
    // Optional knobs (no pagination)
    $sortBy  = $request->query('sort_by', 'performa_invoicedetails.performainvoicedetailsId '); // any selected column
    $sortDir = strtolower($request->query('sort_dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';
    $limit   = $request->integer('limit'); // optional hard cap; if null => all rows

    // Header (safe LEFT JOINs)
    $header = DB::table('invoice')
        ->leftJoin('company_client_master', 'performa_invoice.iCompanyId', '=', 'company_client_master.company_id')
        ->leftJoin('party', 'performa_invoice.iPartyId', '=', 'party.partyId')
        ->leftJoin('year', 'performa_invoice.iYearId', '=', 'year.year_id')
        ->where([
            'performa_invoice.iStatus'     => 1,
            'performa_invoice.isDelete'    => 0,
            'performa_invoice.performainvoiceID' => $request->performainvoice_id,
        ])
        ->select([
            'performa_invoice.performainvoiceID',
            'performa_invoice.entryDate',
            'performa_invoice.iInvoiceNo',
            'company_client_master.company_name',
            'party.strPartyName',
            'year.strYear',
        ])
        ->first();

    if (!$header) {
        return response()->json(['success'=>false,'message' => 'Performa Invoice not found'], 404);
    }

    // Items (NO pagination)
    $itemsQuery = InvoiceDetail::query()
        ->from('performa_invoicedetails')
        ->join('service_master', 'performa_invoicedetails.productID', '=', 'service_master.service_id')
        ->where([
            'performa_invoicedetails.iStatus'     => 1,
            'performa_invoicedetails.isDelete'    => 0,
            'performa_invoicedetails.performainvoiceID' => $request->performainvoice_id,
        ])
        ->select([
            'performa_invoicedetails.performainvoicedetailsId ',
            'performa_invoicedetails.performainvoiceID',
            'performa_invoicedetails.productID',
            'performa_invoicedetails.description',
            'performa_invoicedetails.uom',
            'performa_invoicedetails.quantity',
            'performa_invoicedetails.rate',
            'performa_invoicedetails.amount',
            'performa_invoicedetails.discount',
            'performa_invoicedetails.netAmount',
            'performa_invoicedetails.iGstPercentage',
            DB::raw('service_master.service_name as productName'),
        ])
        ->orderBy($sortBy, $sortDir);

    if ($limit && $limit > 0) {
        $itemsQuery->limit($limit);
    }

    $items = $itemsQuery->get();

    return response()->json([
        'success'=>true,
        'message'=>"Invoice details",
        'invoice_detail' => $header,
        'products'  => $items,
        'count'  => $items->count(), // handy meta
    ], 200);
}


    /**
     * GET /api/invoice-details/{id}
     * Show one detail row (edit prefill)
     */
    public function productshow(Request $request)
    {
        $row = InvoiceDetail::query()
            ->from('performa_invoicedetails')
            ->where(['performainvoicedetailsId ' => $request->performainvoicedetailsId , 'isDelete' => 0])
            ->first([
                'performainvoicedetailsId ',
                'performainvoiceID',
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
            'performainvoicedetailsId ' => (int) $row->performainvoicedetailsId ,
            'performainvoiceID'        => (int) $row->performainvoiceID,
        ];

        return response()->json([
            'success'=>true,
            'message'=>'Invoice Product List',
            'invoice_product_list'=>$payload], 200);
    }

    /**
     * POST /api/quotations/{performainvoice_id}/details
     * Create detail row
     */
   public function store(Request $request)
{
    $performainvoice_id = (int) $request->performainvoice_id;
    $user = Auth::user();
    $validator = Validator::make($request->all(), [
        'productID'       => ['required'],                 // numeric id OR '__new__:name' OR 'other'
        'service_name'    => ['nullable', 'string', 'max:255'],
        'description'     => ['nullable', 'string'],
        'uom'             => ['nullable', 'string', 'max:50'],
        'quantity'        => ['required', 'numeric'],
        'rate'            => ['required', 'numeric'],
        // client-side amount/discount/net are ignored; server recalculates
        'iGstPercentage'  => ['required', 'numeric'],
    ]);

    $validator->after(function ($v) use ($request) {
            $pid = (string)$request->productID;
            $user = Auth::user();
            // Skip if "Other" or newly added product
            if ($pid === 'other' || str_starts_with($pid, '__new__:')) {
                return;
            }

            $exists = InvoiceDetail::query()
                ->from('performa_invoicedetails')
                ->join('quotation as q', 'performa_invoicedetails.performainvoiceID', '=', 'q.performainvoiceID')
                ->where('performa_invoicedetails.performainvoiceID', $request->performainvoice_id)
                ->where('performa_invoicedetails.productID', $request->productID)
                ->where('q.iCompanyId', $user->company_id)
                ->where([
                    'performa_invoicedetails.isDelete' => 0,
                    'performa_invoicedetails.iStatus'  => 1,
                ])
                ->exists();

            if ($exists) {
                $v->errors()->add('productID', 'This product is already added for this performa_invoice.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();



    // ensure quotation exists & is active
    $exists = Invoice::where([
        'performainvoiceID' => $performainvoice_id,
        'iStatus'     => 1,
        'isDelete'    => 0,
    ])->exists();
    if (!$exists) {
        return response()->json(['success'=>false,'message'=>'Invoice not found'], 404);
    }

    // Resolve product id (handles '__new__:' / 'other')
    $serviceId = $this->resolveServiceIdForRequest($request);

    // --- Server truth for THIS new row ---
    $qty   = (float) $data['quantity'];
    $rate  = (float) $data['rate'];
    $base  = $qty * $rate;                                // qty * rate (before discount)
    $gstp  = (float) ($data['iGstPercentage'] ?? 0);

    // --- Read header discount settings ---
    $header = DB::table('invoice')
        ->where('performainvoiceID', $performainvoice_id)
        ->select('discount_type', 'discount')
        ->first();

    // Compute a single effective % to apply to THIS line
    $effectivePercent = 0.0;
    if ($header && (float) $header->discount > 0) {
        if (($header->discount_type ?? '') === 'percent') {
            $effectivePercent = (float) $header->discount;
        } elseif (($header->discount_type ?? '') === 'amount') {
            // Uniform % of base across all items (simple approach):
            // use current total base INCLUDING this new line so math stays proportional.
            $existingBase = (float) DB::table('performa_invoicedetails')
                ->where([
                    'performainvoiceID' => $performainvoice_id,
                    'iStatus'     => 1,
                    'isDelete'    => 0,
                ])
                ->sum(DB::raw('quantity * rate'));
            $totalBaseWithNew = $existingBase + $base;
            if ($totalBaseWithNew > 0) {
                $effectivePercent = ((float) $header->discount / $totalBaseWithNew) * 100.0;
            }
        }
    }

    // --- Apply discount % to THIS line only ---
    $lineDiscount = round($base * ($effectivePercent / 100.0), 2);
    if ($lineDiscount > $base) $lineDiscount = $base;

    $net   = max($base - $lineDiscount, 0.0);             // after discount, before GST
    $gst   = round($net * ($gstp / 100.0), 2);            // GST on NET
    $total = round($net + $gst, 2);

    // --- Insert row ---
    $id = DB::table('performa_invoicedetails')->insertGetId([
        'productID'      => $serviceId,
        'performainvoiceID'    => $performainvoice_id,
        'description'    => $data['description'] ?? null,
        'uom'            => $data['uom'],
        'quantity'       => $qty,
        'rate'           => $rate,
        'amount'         => round($base, 2),               // base (qty*rate)
        'discount'       => $lineDiscount,                 // computed here
        'netAmount'      => round($net, 2),                // base - discount
        'totalAmount'    => $total,                        // net + GST
        'iGstPercentage' => $gstp,
        'created_by'     => $user->emp_id ?? null,
        'iStatus'        => 1,
        'isDelete'       => 0,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
     if (($header->discount_type ?? '') === 'amount' && (float)$header->discount > 0) {
                $this->reallocateAmountDiscount((int)$performainvoice_id, (float)$header->discount);
            }

    // Optional: return computed values so UI can refresh immediately
    return response()->json([
        'success' => true,
        'message' => 'Invoice detail created',
        'id'      => $id,
        'calc'    => [
            'amount'            => round($base, 2),
            'discount'          => $lineDiscount,
            'netAmount'         => round($net, 2),
            'gst'               => $gst,
            'totalAmount'       => $total,
            'effective_percent' => round($effectivePercent, 6),
        ],
    ], 201);
}


    /**
     * PUT /api/invoice-details/{id}
     * Update detail row
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $id   = (int) $request->performainvoicedetailsId ;
    
        $validator = Validator::make($request->all(), [
            'productID'       => ['required'],         // numeric or '__new__:' or 'other'
            'service_name'    => ['nullable', 'string'],
            'description'     => ['nullable', 'string'],
            'uom'             => ['nullable', 'string'],
            'quantity'        => ['required', 'numeric'],
            'rate'            => ['required', 'numeric'],
            'iGstPercentage'  => ['required', 'numeric'],
            // amount/discount/netAmount from client are ignored (server recalculates)
        ]);
    
    $validator->after(function ($v) use ($request) {
            $pid = (string)$request->productID;
            $user = Auth::user();
            $id   = (int) $request->performainvoicedetailsId ;

        $row = DB::table('performa_invoicedetails')->where([
            'performainvoicedetailsId ' => $id,
            'isDelete'           => 0,
        ])->first();
            // Skip if "Other" or newly added product
            if ($pid === 'other' || str_starts_with($pid, '__new__:')) {
                return;
            }

            $exists = InvoiceDetail::query()
                ->from('performa_invoicedetails')
                ->join('invoice as q', 'performa_invoicedetails.performainvoiceID', '=', 'q.performainvoiceID')
                    ->where('performa_invoicedetails.performainvoiceID', $row->performainvoiceID)   // from details
                ->where('performa_invoicedetails.productID', $request->productID)
                ->where('q.iCompanyId', $user->company_id)
                ->when($id, function($q) use($id) {
                        return $q->where('performa_invoicedetails.performainvoicedetailsId ','!=', $id);
                    })
                ->where([
                    'performa_invoicedetails.isDelete' => 0,
                    'performa_invoicedetails.iStatus'  => 1,
                ])
                ->exists();

            if ($exists) {
                $v->errors()->add('productID', 'This product is already added for this performa_invoice.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();


        // Ensure row exists
        $row = DB::table('performa_invoicedetails')->where([
            'performainvoicedetailsId ' => $id,
            'isDelete'           => 0,
        ])->first();
        if (!$row) {
            return response()->json(['success'=>false,'message'=>'Not found'], 404);
        }
    
        // Resolve product id (handles '__new__:' / 'other')
        $serviceId = $this->resolveServiceIdForRequest($request);
    
        // --- Base for THIS row ---
        $qty   = (float) $data['quantity'];
        $rate  = (float) $data['rate'];
        $base  = $qty * $rate;                            // qty * rate (before discount)
        $gstp  = (float) ($data['iGstPercentage'] ?? 0);
    
        // --- Read header discount settings ---
        $header = DB::table('invoice')
            ->where('performainvoiceID', (int) $row->performainvoiceID)
            ->select('discount_type', 'discount')
            ->first();
    
        $effectivePercent = 0.0;
        if ($header && (float)$header->discount > 0) {
            if (($header->discount_type ?? '') === 'percent') {
                $effectivePercent = (float) $header->discount;
            } elseif (($header->discount_type ?? '') === 'amount') {
                // convert header flat amount to a single uniform % of current total base
                $totalBase = (float) DB::table('performa_invoicedetails')
                    ->where([
                        'performainvoiceID' => (int) $row->performainvoiceID,
                        'iStatus'     => 1,
                        'isDelete'    => 0,
                    ])
                    ->sum(DB::raw('quantity * rate'));
                if ($totalBase > 0) {
                    $effectivePercent = ((float) $header->discount / $totalBase) * 100.0;
                }
            }
        }
    
        // --- Apply discount % to THIS row only (simple approach) ---
        $lineDiscount = round($base * ($effectivePercent / 100.0), 2);
        if ($lineDiscount > $base) $lineDiscount = $base;
    
        $net   = max($base - $lineDiscount, 0.0);         // after discount, before GST
        $gst   = round($net * ($gstp / 100.0), 2);        // GST on NET
        $total = round($net + $gst, 2);
    
        // --- Update row ---
        DB::table('performa_invoicedetails')
            ->where(['performainvoicedetailsId ' => $id, 'isDelete' => 0])
            ->update([
                'productID'      => $serviceId,
                'description'    => $data['description'] ?? null,
                'uom'            => $data['uom'],
                'quantity'       => $qty,
                'rate'           => $rate,
                'amount'         => round($base, 2),       // base
                'discount'       => $lineDiscount,         // computed here
                'netAmount'      => round($net, 2),        // base - discount
                'totalAmount'    => $total,                // net + GST
                'iGstPercentage' => $gstp,
                'updated_by'     => $user->emp_id ?? null,
                'updated_at'     => now(),
            ]);
    
         if (($header->discount_type ?? '') === 'amount' && (float)$header->discount > 0) {
                $this->reallocateAmountDiscount((int)$row->performainvoiceID, (float)$header->discount);
            }
        // Optional: return computed values so UI can refresh immediately
        return response()->json([
            'success' => true,
            'message' => 'Invoice detail updated',
            'id'      => $id,
            'calc'    => [
                'amount'      => round($base, 2),
                'discount'    => $lineDiscount,
                'netAmount'   => round($net, 2),
                'gst'         => $gst,
                'totalAmount' => $total,
                'effective_percent' => round($effectivePercent, 6),
            ],
        ], 200);
    }

    /**
     * DELETE /api/invoice-details/{id}
     */
        public function destroy(Request $request)
        {
            // Validate incoming ID
            $data = $request->validate([
                'performainvoicedetailsId ' => ['required', 'integer'],
            ]);

            $id = (int) $data['performainvoicedetailsId '];

            // Get the row first (to know performainvoiceID)
            $row = DB::table('performa_invoicedetails')
                ->where(['performainvoicedetailsId ' => $id, 'isDelete' => 0])
                ->select('performainvoiceID')
                ->first();

            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found or already deleted',
                ], 404);
            }

            DB::beginTransaction();
            try {
                $deleted = DB::table('performa_invoicedetails')
                    ->where(['performainvoicedetailsId ' => $id, 'isDelete' => 0])
                    ->update([
                        'isDelete'   => 1,
                        'updated_at' => now(),
                    ]);

                if (!$deleted) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Delete failed',
                    ], 500);
                }

                // ---- Handle header discount reallocation ----
                $performainvoiceID = (int) $row->performainvoiceID;

                $header = DB::table('invoice')
                    ->where(['performainvoiceID' => $performainvoiceID, 'isDelete' => 0])
                    ->select('discount_type', 'discount')
                    ->first();

                if ($header && ($header->discount_type === 'amount') && (float) $header->discount > 0) {
                    // Reallocate flat discount across remaining lines
                    $this->reallocateAmountDiscount($performainvoiceID, (float) $header->discount);
                }

                // ---- If no remaining products, clear discount fields on header ----
                $remaining = DB::table('performa_invoicedetails')
                    ->where([
                        'performainvoiceID' => $performainvoiceID,
                        'isDelete'    => 0,
                    ])
                    ->count();

                if ($remaining === 0) {
                    DB::table('invoice')
                        ->where(['performainvoiceID' => $performainvoiceID, 'isDelete' => 0])
                        ->update([
                            'discount'      => 0,
                            'discount_type' => null,
                            'updated_at'    => now(),
                        ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice detail deleted',
                ], 200);
            } catch (\Throwable $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Unexpected error',
                    'error'   => $e->getMessage(), // remove in production if you don’t want to expose details
                ], 500);
            }
        }
        
        private function reallocateAmountDiscount(int $performainvoiceID, float $headerAmount): void
        {
            DB::transaction(function () use ($performainvoiceID, $headerAmount) {
                // 1) Load lines
                $lines = DB::table('performa_invoicedetails')
                    ->where(['performainvoiceID'=>$performainvoiceID,'iStatus'=>1,'isDelete'=>0])
                    ->orderBy('performainvoicedetailsId ')
                    ->get(['performainvoicedetailsId ','quantity','rate','iGstPercentage']);
    
                if ($lines->isEmpty()) return;
    
                // 2) Bases
                $work = [];
                $totalBase = 0.0;
                foreach ($lines as $r) {
                    $b = (float)$r->quantity * (float)$r->rate;
                    $work[] = ['id'=>$r->performainvoicedetailsId ,'base'=>$b,'gstp'=>(float)$r->iGstPercentage];
                    $totalBase += $b;
                }
                if ($totalBase <= 0) return;
    
                // 3) Proportional raw + truncate to paise
                $raws = []; $sumRounded = 0.0;
                foreach ($work as $w) {
                    $raw = $headerAmount * ($w['base'] / $totalBase);
                    $tr  = floor($raw * 100) / 100;
                    $raws[] = ['id'=>$w['id'],'raw'=>$raw,'round'=>$tr,'frac'=>$raw-$tr,'base'=>$w['base'],'gstp'=>$w['gstp']];
                    $sumRounded += $tr;
                }
    
                // 4) Distribute leftover paise to largest fractional parts
                $left = round($headerAmount - $sumRounded, 2);
                usort($raws, fn($a,$b) => $b['frac'] <=> $a['frac']);
    
                foreach ($raws as &$row) {
                    $disc = $row['round'];
                    if ($left > 0) {
                        $bump = min($left, 0.01);
                        $disc = round($disc + $bump, 2);
                        $left = round($left - $bump, 2);
                    }
                    // clamp to base
                    if ($disc > $row['base']) $disc = $row['base'];
    
                    $net = max($row['base'] - $disc, 0.0);
                    $gst = round($net * ($row['gstp'] / 100.0), 2);
                    $tot = round($net + $gst, 2);
    
                    DB::table('performa_invoicedetails')
                      ->where('performainvoicedetailsId ', $row['id'])
                      ->update([
                          'amount'      => round($row['base'], 2),
                          'discount'    => round($disc, 2),
                          'netAmount'   => round($net, 2),
                          'totalAmount' => round($tot, 2),
                          'updated_at'  => now(),
                      ]);
                }
                unset($row);
            });
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
