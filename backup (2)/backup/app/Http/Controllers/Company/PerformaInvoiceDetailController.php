<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\PerformaInvoiceDetail;
use App\Models\PerformaInvoice;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PerformaInvoiceDetailController extends Controller
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
                'rate',
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

    public function checkDuplicate(Request $request)
        {
    
            $request->validate([
                'performainvoiceID' => 'required|integer',
                'productID'   => 'required',
            ]);
    
            $detailId    = $request->performainvoicedetailsId;       // dd-mm-YYYY or yyyy-mm-dd
    
            $exists = PerformaInvoiceDetail::query()
                        ->join('invoice as q', 'performa_invoicedetails.performainvoiceID', '=', 'q.performainvoiceID')
                        ->where('performa_invoicedetails.performainvoiceID', $request->performainvoiceID)   // from details
                        ->where('q.iCompanyId', $request->company_id)                    // from quotation
                        ->where('performa_invoicedetails.productID', $request->productID) 
                        ->when($detailId, function($q) use($detailId) {
                                    return $q->where('performa_invoicedetails.performainvoicedetailsId','!=', $detailId);
                                })
                                      // from details
                        ->where([
                            'performa_invoicedetails.isDelete' => 0,
                            'performa_invoicedetails.iStatus'  => 1,
                        ])
                        ->exists();
    
    
            return response()->json(['exists' => $exists]);
        }

    public function index(Request $request, $id)
    {
        // Services for dropdown
        $Product = Service::where(['iStatus' => 1, 'isDelete' => 0])
            ->orderBy('service_id', 'DESC')
            ->get();

        // Paginated rows for the table
        $InvoiceDetail = PerformaInvoiceDetail::query()
            ->from('performa_invoicedetails')
            ->join('service_master', 'performa_invoicedetails.productID', '=', 'service_master.service_id')
            ->where([
                'performa_invoicedetails.iStatus'     => 1,
                'performa_invoicedetails.isDelete'    => 0,
                'performa_invoicedetails.performainvoiceID' => $id,
            ])
            ->select([
                'performa_invoicedetails.performainvoicedetailsId',
                'performa_invoicedetails.productID',
                'performa_invoicedetails.description',
                'performa_invoicedetails.uom',
                'performa_invoicedetails.quantity',
                'performa_invoicedetails.rate',
                'performa_invoicedetails.iGstPercentage',
                'performa_invoicedetails.discount',
                'performa_invoicedetails.amount',
                'performa_invoicedetails.netAmount',
                'performa_invoicedetails.totalAmount',
                DB::raw('service_master.service_name as productName'),
            ])
            ->orderBy('performa_invoicedetails.performainvoicedetailsId', 'ASC')
            ->paginate(10);

        // Header row
        $CompanyName = DB::table('performa_invoice')
            ->leftJoin('company_client_master', 'performa_invoice.iCompanyId', '=', 'company_client_master.company_id')
            ->leftJoin('party', 'performa_invoice.iPartyId', '=', 'party.partyId')
            ->leftJoin('year', 'performa_invoice.iYearId', '=', 'year.year_id')
            ->where([
                'performa_invoice.iStatus'     => 1,
                'performa_invoice.isDelete'    => 0,
                'performa_invoice.performainvoiceID' => $id,
            ])
            ->select([
                'performa_invoice.performainvoiceID',
                'performa_invoice.entryDate',
                'performa_invoice.iPerformaInvoiceNo',
                'company_client_master.company_name',
                'company_client_master.GST',
                'party.strPartyName',
                'year.strYear',
            ])
            ->first();

        /* ===== Totals over ALL items (not just current page) ===== */
        $allRows = DB::table('performa_invoicedetails')
            ->where([
                'performainvoiceID' => $id,
                'iStatus'     => 1,
                'isDelete'    => 0,
            ])
            ->orderBy('performainvoicedetailsId')
            ->get(['quantity', 'rate', 'discount', 'iGstPercentage']);

        $subTotal = 0.0;         // sum of qty*rate (before discount)
        $totalDiscount = 0.0;    // sum of per-line discount
        $gstTotal = 0.0;         // sum of GST on (base - discount)
        $pcts = [];

        foreach ($allRows as $r) {
            $qty   = (float)($r->quantity ?? 0);
            $rate  = (float)($r->rate ?? 0);
            $base  = $qty * $rate;

            $disc  = (float)($r->discount ?? 0);
            if ($disc > $base) { $disc = $base; }           // guard
            $net   = max($base - $disc, 0.0);

            $gstp  = (float)($r->iGstPercentage ?? 0);
            $gst   = round($net * ($gstp / 100.0), 2);

            $subTotal      += $base;
            $totalDiscount += $disc;
            $gstTotal      += $gst;
            $pcts[]         = round($gstp, 2);
        }

        $taxableAfterDiscount = max($subTotal - $totalDiscount, 0.0);
        $grandTotal           = $taxableAfterDiscount + $gstTotal;

        $summary = [
            'sub_total'              => round($subTotal, 2),
            'total_discount'         => round($totalDiscount, 2),
            'taxable_after_discount' => round($taxableAfterDiscount, 2),
            'gst_total'              => round($gstTotal, 2),
            'grand_total'            => round($grandTotal, 2),
            'gst_uniform'            => count(array_unique($pcts)) <= 1,
        ];
        /* ========================================================= */

        return view('company_client.performa_invoicedetails.index', compact(
            'InvoiceDetail', 'id', 'CompanyName', 'Product', 'summary'
        ));
    }

    public function createview()
    {
        return view('performa_invoicedetails.add');
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'performainvoiceID'     => ['required', 'integer'],
            'productID'       => ['required'],                    // numeric id OR '__new__:name' OR 'other'
            'service_name'    => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'uom'             => ['nullable', 'string', 'max:50'],// keep as string
            'quantity'        => ['required', 'numeric'],
            'rate'            => ['required', 'numeric'],
            // client-side amount/discount/net are ignored (server recalculates)
            'iGstPercentage'  => ['nullable', 'numeric'],
        ]);

        $serviceId = $this->resolveServiceIdForRequest($request);

        // --- Server-truth for THIS new row ---
        $qty   = (float) $data['quantity'];
        $rate  = (float) $data['rate'];
        $base  = $qty * $rate;                                    // qty * rate (before discount)
        $gstp  = (float) ($data['iGstPercentage'] ?? 0);

        // --- Read header discount settings ---
        $header = DB::table('performa_invoice')
            ->where('performainvoiceID', (int) $data['performainvoiceID'])
            ->select('discount_type', 'discount')
            ->first();

        // Compute a single effective % to apply to THIS line
        $effectivePercent = 0.0;
        if ($header && (float)$header->discount > 0) {
            if (($header->discount_type ?? '') === 'percent') {
                $effectivePercent = (float) $header->discount;
            } elseif (($header->discount_type ?? '') === 'amount') {
                // Use current total base INCLUDING this new line
                $existingBase = (float) DB::table('performa_invoicedetails')
                    ->where([
                        'performainvoiceID' => (int) $data['performainvoiceID'],
                        'iStatus'     => 1,
                        'isDelete'    => 0,
                    ])
                    ->sum(DB::raw('quantity * rate'));

                $totalBaseWithNew = $existingBase + $base;

                if ($totalBaseWithNew > 0) {
                    $effectivePercent = ((float) $header->discount / $totalBaseWithNew) * 100.0;
                    //echo $header->discount . '/' .$totalBaseWithNew .'*'. 100;
                }
            }
        }

        // --- Apply discount % to THIS line only ---
        $lineDiscount = round($base * ($effectivePercent / 100.0), 2);
            //echo $base .'*'.$effectivePercent .'/'. 100;
    //dd($lineDiscount);
        if ($lineDiscount > $base) $lineDiscount = $base;

        $net   = max($base - $lineDiscount, 0.0);                 // after discount, before GST
        $gst   = round($net * ($gstp / 100.0), 2);                // GST on NET
        $total = round($net + $gst, 2);

        DB::table('performa_invoicedetails')->insert([
            'productID'      => $serviceId,
            'performainvoiceID'    => (int) $data['performainvoiceID'],
            'description'    => $data['description'] ?? null,
            'uom'            => $data['uom'] ?? null,             // keep string
            'quantity'       => $qty,
            'rate'           => $rate,
            'amount'         => round($base, 2),                  // base
            'discount'       => $lineDiscount,                    // computed here
            'netAmount'      => round($net, 2),                   // base - discount
            'totalAmount'    => $total,                           // net + GST
            'iGstPercentage' => $gstp,
            'created_by'     => $user->emp_id ?? null,
            'iStatus'        => 1,
            'isDelete'       => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if (($header->discount_type ?? '') === 'amount' && (float)$header->discount > 0) {
            $this->reallocateAmountDiscount((int)$data['performainvoiceID'], (float)$header->discount);
        }

        return redirect()
            ->route('performainvoicedetails.index', [$data['performainvoiceID']])
            ->with('success', 'Quotation Details Created Successfully.');
    }

    // QuotationDetailController.php
        public function editview($id)
        {
            $row = PerformaInvoiceDetail::where('performainvoicedetailsId', $id)->firstOrFail();

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
        $user = Auth::user();

        $data = $request->validate([
            'performainvoicedetailsId'=> ['required', 'integer'],
            'performainvoiceID'       => ['required', 'integer'],
            'productID'         => ['required'],                   // numeric or '__new__:' or 'other'
            'service_name'      => ['nullable', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'uom'               => ['required', 'string', 'max:50'],
            'quantity'          => ['required', 'numeric'],
            'rate'              => ['required', 'numeric'],
            'iGstPercentage'    => ['required', 'numeric'],
            // amount/discount/netAmount from client are ignored (server recalculates)
        ]);

        // Ensure row exists
        $row = DB::table('performa_invoicedetails')->where([
            'performainvoicedetailsId' => (int)$data['performainvoicedetailsId'],
            'isDelete'           => 0,
        ])->first();

        if (!$row) {
            return back()->with('error', 'Not found');
        }

        // Resolve product id (handles '__new__:' / 'other')
        $serviceId = $this->resolveServiceIdForRequest($request);

        // --- Base for THIS row ---
        $qty   = (float) $data['quantity'];
        $rate  = (float) $data['rate'];
        $base  = $qty * $rate;                            // qty * rate (before discount)
        $gstp  = (float) $data['iGstPercentage'];

        // --- Read header discount settings ---
        $header = DB::table('performa_invoice')
            ->where('performainvoiceID', (int) $data['performainvoiceID'])
            ->select('discount_type', 'discount')
            ->first();

        // Compute a single effective % to apply to THIS row
        $effectivePercent = 0.0;
        if ($header && (float)$header->discount > 0) {
            if (($header->discount_type ?? '') === 'percent') {
                $effectivePercent = (float) $header->discount;
            } elseif (($header->discount_type ?? '') === 'amount') {
                // Uniform % derived from current total base (simple approach)
                $totalBase = (float) DB::table('performa_invoicedetails')
                    ->where([
                        'performainvoiceID' => (int) $data['performainvoiceID'],
                        'iStatus'     => 1,
                        'isDelete'    => 0,
                    ])
                    ->sum(DB::raw('quantity * rate'));
                if ($totalBase > 0) {
                    $effectivePercent = ((float) $header->discount / $totalBase) * 100.0;
                }
            }
        }

        // --- Apply discount % to THIS row only ---
        $lineDiscount = round($base * ($effectivePercent / 100.0), 2);
        if ($lineDiscount > $base) $lineDiscount = $base;

        $net   = max($base - $lineDiscount, 0.0);         // after discount, before GST
        $gst   = round($net * ($gstp / 100.0), 2);        // GST on NET
        $total = round($net + $gst, 2);

        // --- Save ---
        DB::table('performa_invoicedetails')
            ->where([
                'performainvoicedetailsId' => (int)$data['performainvoicedetailsId'],
                'isDelete'           => 0,
            ])
            ->update([
                'productID'      => $serviceId,
                'performainvoiceID'    => (int)$data['performainvoiceID'],
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
            $this->reallocateAmountDiscount((int)$data['performainvoiceID'], (float)$header->discount);
        }


        return redirect()
            ->route('performainvoicedetails.index', [$data['performainvoiceID']])
            ->with('success', 'Quotation Details Updated Successfully.');
    }



    private function reallocateAmountDiscount(int $performainvoiceID, float $headerAmount): void
    {
        DB::transaction(function () use ($performainvoiceID, $headerAmount) {
            // 1) Load lines
            $lines = DB::table('performa_invoicedetails')
                ->where(['performainvoiceID'=>$performainvoiceID,'iStatus'=>1,'isDelete'=>0])
                ->orderBy('performainvoicedetailsId')
                ->get(['performainvoicedetailsId','quantity','rate','iGstPercentage']);

            if ($lines->isEmpty()) return;

            // 2) Bases
            $work = [];
            $totalBase = 0.0;
            foreach ($lines as $r) {
                $b = (float)$r->quantity * (float)$r->rate;
                $work[] = ['id'=>$r->performainvoicedetailsId,'base'=>$b,'gstp'=>(float)$r->iGstPercentage];
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
                  ->where('performainvoicedetailsId', $row['id'])
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


    public function delete(Request $request, $Id)
    {
        // 1) Find the row (need performainvoiceID)
        $row = DB::table('performa_invoicedetails')
            ->where(['performainvoicedetailsId' => (int)$Id, 'isDelete' => 0])
            ->select('performainvoiceID')
            ->first();

        if (!$row) {
            return back()->with('error', 'Record not found or already deleted.');
        }

        // 2) Hard delete (keep as-is if you really want hard delete)
        $deleted = DB::table('performa_invoicedetails')
            ->where(['performainvoicedetailsId' => (int)$Id, 'isDelete' => 0])
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Delete failed.');
        }

        // 3) If header has flat amount discount, reallocate across remaining lines
        $header = DB::table('quotation')
            ->where('performainvoiceID', (int)$row->performainvoiceID)
            ->select('discount_type', 'discount')
            ->first();

        if (($header->discount_type ?? '') === 'amount' && (float)($header->discount ?? 0) > 0) {
            $this->reallocateAmountDiscount((int)$row->performainvoiceID, (float)$header->discount);
        }

        // 4) Done
        return back()->with('success', 'Quotation detail deleted successfully.');
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


    public function applyDiscount(Request $request, $performainvoiceID)
    {
        $data = $request->validate([
            'mode'  => ['required', 'in:percent,amount'],
            'value' => ['required', 'numeric', 'min:0'],
        ]);

        $mode  = $data['mode'];
        $value = (float) $data['value'];

        $items = DB::table('performa_invoicedetails')
            ->where(['performainvoiceID' => $performainvoiceID, 'iStatus' => 1, 'isDelete' => 0])
            ->orderBy('performainvoicedetailsId')
            ->get(['performainvoicedetailsId','quantity','rate','iGstPercentage']);

        if ($items->isEmpty()) {
            return back()->with('error', 'No items found for this invoice.');
        }

        // Build lines
        $lines = $items->map(function ($r) {
            $qty  = (float)($r->quantity ?? 0);
            $rate = (float)($r->rate ?? 0);
            return [
                'id'   => $r->performainvoicedetailsId,
                'base' => $qty * $rate,                 // before discount
                'gstp' => (float)($r->iGstPercentage ?? 0),
            ];
        });

        $totalBase = $lines->sum('base');
        if ($totalBase <= 0) {
            return back()->with('error', 'Total base amount is zero; cannot apply discount.');
        }

        // Decide effective percent + per-line discount (amount -> convert to percent)
        $effectivePercent = $mode === 'percent'
            ? $value
            : (($value / $totalBase) * 100.0);

        // First pass — truncate to 2 decimals and track fractional parts for paise-fix
        $raws = [];
        $sumRounded = 0.0;
        foreach ($lines as $ln) {
            $raw = $ln['base'] * ($effectivePercent / 100.0);
            $trunc = floor($raw * 100) / 100;
            $raws[] = [
                'id'   => $ln['id'],
                'raw'  => $raw,
                'round'=> $trunc,
                'frac' => $raw - $trunc,
            ];
            $sumRounded += $trunc;
        }

        // Target total discount
        $targetTotal = ($mode === 'percent')
            ? round($totalBase * ($effectivePercent / 100.0), 2)
            : round($value, 2);

        // Distribute leftover paise to largest fractional parts
        $left = round($targetTotal - $sumRounded, 2);
        usort($raws, fn($a,$b) => $b['frac'] <=> $a['frac']);

        $discounts = [];
        foreach ($raws as &$row) {
            $alloc = $row['round'];
            if ($left > 0) {
                $bump = min($left, 0.01);
                $alloc = round($alloc + $bump, 2);
                $left  = round($left - $bump, 2);
            }
            $discounts[$row['id']] = $alloc;
        }
        unset($row);

        // Write back: amount (base), discount, netAmount, totalAmount
        $subTotal      = 0.0;
        $totalDiscount = 0.0;
        $gstTotal      = 0.0;
        $grandTotal    = 0.0;

        DB::beginTransaction();
        try {
            foreach ($lines as $ln) {
                $disc = min((float)($discounts[$ln['id']] ?? 0), $ln['base']);
                $net  = max($ln['base'] - $disc, 0.0);
                $gst  = round($net * ($ln['gstp'] / 100.0), 2);
                $tot  = round($net + $gst, 2);

                DB::table('performa_invoicedetails')
                    ->where('performainvoicedetailsId', $ln['id'])
                    ->update([
                        'amount'      => round($ln['base'], 2),
                        'discount'    => round($disc, 2),
                        'netAmount'   => round($net, 2),
                        'totalAmount' => round($tot, 2),
                        'updated_at'  => now(),
                    ]);

                $subTotal      += $ln['base'];
                $totalDiscount += $disc;
                $gstTotal      += $gst;
                $grandTotal    += $tot;
            }
            DB::table('performa_invoice')
            ->where('performainvoiceID', $performainvoiceID)
            ->update([
                'discount_type' => $mode,         // "percent" or "amount"
                'discount'      => round($value, 2),  // the raw value user entered
                'updated_at'    => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to apply discount: '.$e->getMessage());
        }

        return back()->with([
            'success'            => 'Discount applied successfully.',
            'effective_percent'  => round($effectivePercent, 6),
            'sub_total'          => round($subTotal, 2),
            'total_discount'     => round($totalDiscount, 2),
            'taxable_after_disc' => round($subTotal - $totalDiscount, 2),
            'gst_total'          => round($gstTotal, 2),
            'grand_total'        => round($grandTotal, 2),
        ]);
    }

}