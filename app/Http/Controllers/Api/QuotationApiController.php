<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyClient;
use App\Models\Year;
use App\Models\Party;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\TermCondition;
use App\Models\Service; // products table in your original code looked like Service
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class QuotationApiController extends Controller
{
    /**
     * GET /api/quotations
     * List quotations (with filters + pagination)
     */

public function index(Request $request)
{
    $user = Auth::user();

    $PartyName = $request->party_id;
    $fromDate  = $request->fromDate;     // dd-mm-YYYY or yyyy-mm-dd (we will convert)
    $toDate    = $request->toDate;
    $mobile    = $request->mobile;

    // Subquery to count products per quotation
    $detailsSub = DB::table('quotationdetails')
        ->select('quotationID', DB::raw('COUNT(*) as product_count'))
        ->where([
            'isDelete' => 0,
            'iStatus'  => 1,
        ])
        ->groupBy('quotationID');

    $query = Quotation::query()
        ->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0])
        ->when($PartyName, function ($q) use ($PartyName) {
            return $q->where('quotation.iPartyId', $PartyName);
        })
        ->when($mobile, function ($q) use ($mobile) {
            return $q->where('party.iMobile', $mobile);
        })
        ->when($fromDate, function ($q) use ($fromDate) {
            $from = date('Y-m-d', strtotime($fromDate));
            return $q->whereDate('quotation.entryDate', '>=', $from);
        })
        ->when($toDate, function ($q) use ($toDate) {
            $to = date('Y-m-d', strtotime($toDate));
            return $q->whereDate('quotation.entryDate', '<=', $to);
        });

    if ($user->role_id == '3') {
        $query->where(['created_by' => $user->emp_id]);
    }

    $query = $query
        ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
        ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
        ->join('year', 'quotation.iYearId', '=', 'year.year_id')
        // LEFT JOIN subquery for product count
        ->leftJoinSub($detailsSub, 'qd', function ($join) {
            $join->on('qd.quotationID', '=', 'quotation.quotationId');
        })
        ->orderByDesc('quotation.quotationId')
        ->select([
            'quotation.*',
            'company_client_master.company_name',
            'party.strPartyName',
            'party.iMobile',
            'year.year_id',
            DB::raw('COALESCE(qd.product_count, 0) as product_count'),
        ]);

    $rows = $query->get();

    return response()->json([
        'success' => true,
        'message' => 'Quotation List',
        'data'    => $rows,
    ]);
}


    public function getNextQuotationNo()
    {
        // Get last saved value, e.g. "0012/24-25" or "0012"
        $last = Quotation::orderByDesc('quotationId')->value('iQuotationNo');

        // Take only the left part before '/', keep digits only
        $n = 0;
        if (!empty($last)) {
            $left = explode('/', trim($last))[0];     // "0012"
            $n    = (int) preg_replace('/\D/', '', $left); // 12
        }

        // +1 and pad to 4 digits: 0001, 0002, ...
        $next = str_pad((string)($n + 1), 4, '0', STR_PAD_LEFT);
            $year = Year::where(['iStatus'=>1,'isDelete'=>0])->orderByDesc('year_id')->value('strYear');
            if (!$year) $year = now('Asia/Kolkata')->format('y').'-'.now('Asia/Kolkata')->addYear()->format('y');

            $nextQuotationNo=$next . '/' . $year;
            return response()->json(['next_quotation_no' => $nextQuotationNo] );
    }


    /**
     * POST /api/quotations
     * Create/store quotation
     */
    public function store(Request $request)
    {
        $user = $request->user(); // if using Sanctum/Passport (optional)
        $user = Auth::user();

        $v = Validator::make($request->all(), [
            'iYearId'            => 'required|integer|exists:year,year_id',
            'iQuotationNo' => [
                'required', 'string', 'max:50',
                Rule::unique('quotation', 'iQuotationNo')
                    ->where(fn ($q) => $q
                        ->where('iCompanyId', $request->iCompanyId ?? optional($request->user())->company_id)
                        ->where('iYearId', $request->iYearId)
                        ->where('isDelete', 0)
                    ),
            ],
            'iPartyId'           => 'required|integer|exists:party,partyId',
            // If you want to force the company from auth: omit iCompanyId and use $user->company_id
            'iCompanyId'         => 'nullable|integer|exists:company_client_master,company_id',
            'quotationValidity'  => 'nullable|string|max:255',
            'modeOfDespatch'     => 'nullable|string|max:255',
            'deliveryTerm'       => 'nullable|string|max:255',
            'paymentTerms'       => 'nullable|string|max:255',
            'entryDate'          => 'required|string', // d-m-Y or Y-m-d; parse below safely
            'iGstType'           => 'nullable|integer|in:0,1,2,3',
            'strTermsCondition'  => 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $entryDate = $this->parseDate($request->input('entryDate'));

        $data = [
            'iYearId'           => (int) $request->iYearId,
            'iQuotationNo'      => (string) $request->iQuotationNo,
            'iPartyId'          => (int) $request->iPartyId,
            'iCompanyId'        => $request->filled('iCompanyId') ? (int) $request->iCompanyId : optional($user)->company_id,
            'quotationValidity' => $request->input('quotationValidity'),
            'modeOfDespatch'    => $request->input('modeOfDespatch'),
            'deliveryTerm'      => $request->input('deliveryTerm'),
            'paymentTerms'      => $request->input('paymentTerms'),
            'entryDate'         => $entryDate->format('Y-m-d'),
            'iGstType'          => $request->input('iGstType', 0),
            'strTermsCondition' => $request->input('strTermsCondition'),
            'created_by'        => $user->emp_id,
            'iStatus'           => 1,
            'isDelete'          => 0,
        ];

        $id = DB::table('quotation')->insertGetId($data);

        return response()->json([
            'success'=>true,
            'message' => 'Quotation created successfully',
            'quotation_id' => $id,
            'redirect_to_details' => route('api.quotations.details', $id)
        ], 201);
    }

    /**
     * GET /api/quotations/{id}
     * Show a quotation (basic)
     */
    public function show($id)
    {
        $q = Quotation::where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->select([
                'quotation.*',
                'company_client_master.company_name',
                'company_client_master.Address as company_address',
                'company_client_master.email as company_email',
                'company_client_master.mobile as company_mobile',
                'company_client_master.GST as company_gst',
                'party.strPartyName',
                'party.address1 as party_address1',
                'party.address2 as party_address2',
                'party.address3 as party_address3',
                'party.iMobile as party_mobile',
                'party.strEmail as party_email',
            ])
            ->first();

        if (!$q) {
            return response()->json(['success'=>false,'message' => 'Not found'], 404);
        }

        return response()->json(['data' => $q]);
    }

    /**
     * PUT/PATCH /api/quotations/{id}
     * Update quotation
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $v = Validator::make($request->all(), [
            'iYearId'            => 'required|integer|exists:year,year_id',
            'iQuotationNo' => [
                'required', 'string', 'max:50',
                Rule::unique('quotation', 'iQuotationNo')
                    ->ignore($id, 'quotationId')                // ignore current row
                    ->where(fn($q) => $q
                        ->where('iCompanyId', $request->iCompanyId ?? optional($request->user())->company_id)
                        ->where('iYearId',    $request->iYearId)
                        ->where('isDelete',   0)
                    ),
            ],
            'iPartyId'           => 'required|integer|exists:party,partyId',
            'iCompanyId'         => 'required|integer|exists:company_client_master,company_id',
            'quotationValidity'  => 'nullable|string|max:255',
            'modeOfDespatch'     => 'nullable|string|max:255',
            'deliveryTerm'       => 'nullable|string|max:255',
            'paymentTerms'       => 'nullable|string|max:255',
            'entryDate'          => 'required|string',
            'iGstType'           => 'nullable|integer|in:0,1,2,3',
            'strTermsCondition'  => 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $entryDate = $this->parseDate($request->input('entryDate'));

        $affected = DB::table('quotation')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $id])
            ->update([
                'iYearId'           => (int) $request->iYearId,
                'iQuotationNo'      => (string) $request->iQuotationNo,
                'iPartyId'          => (int) $request->iPartyId,
                'iCompanyId'        => (int) $request->iCompanyId,
                'quotationValidity' => $request->input('quotationValidity'),
                'modeOfDespatch'    => $request->input('modeOfDespatch'),
                'deliveryTerm'      => $request->input('deliveryTerm'),
                'paymentTerms'      => $request->input('paymentTerms'),
                'entryDate'         => $entryDate->format('Y-m-d'),
                'iGstType'          => $request->input('iGstType', 0),
                'strTermsCondition' => $request->input('strTermsCondition'),
                'updated_by'        => $user->emp_id,
            ]);

        if (!$affected) {
            return response()->json(['success'=>false,'message' => 'Not found or no changes'], 404);
        }

        return response()->json([
            'success'=>true,
            'message' => 'Quotation updated successfully',
            'quotation_id' => (int) $id
        ]);
    }

    /**
     * DELETE /api/quotations/{id}
     */
    public function destroy($id)
    {
        $deleted = DB::table('quotation')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $id])
            ->delete();

        if (!$deleted) {
            return response()->json(['success'=>false,'message' => 'Not found'], 404);
        }

        return response()->json(['success'=>true,'message' => 'Quotation deleted successfully']);
    }

    /**
     * GET /api/quotations/{id}/details
     * (Full detail: header + items + term conditions + logo URL)
     */
    public function details($id)
    {
        $header = Quotation::select(
                'party.address1',
                'company_client_master.company_name',
                'company_client_master.Address',
                'company_client_master.email',
                'company_client_master.mobile',
                'company_client_master.GST',
                'company_client_master.plan_id',
                'party.strPartyName',
                'party.address2',
                'party.address3',
                'party.iMobile',
                'party.strEmail',
                'quotation.iQuotationNo',
                'quotation.entryDate',
                'quotation.iCompanyId',
                'quotation.quotationValidity',
                'quotation.modeOfDespatch',
                'quotation.deliveryTerm',
                'quotation.paymentTerms',
                'quotation.iGstType',
                'quotation.strTermsCondition',
                'quotation.quotationId'
            )
            ->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->first();
    
        if (!$header) {
            return response()->json(['success'=>false,'message' => 'Not found'], 404);
        }
    
        // Load items and keep needed columns (ensure iGstPercentage is present)
        $items = QuotationDetail::from('quotationdetails')
            ->where([
                'quotationdetails.iStatus'    => 1,
                'quotationdetails.isDelete'   => 0,
                'quotationdetails.quotationID'=> $id,
            ])
            ->join('service_master', 'service_master.service_id', '=', 'quotationdetails.productID')
            ->orderBy('quotationdetailsId')
            ->get([
                'quotationdetails.*',
                'service_master.service_name', // include any descriptive field you need
            ]);
    
        $subTotal   = 0.0; // sum of qty*rate (before discount)
        $discountT  = 0.0; // sum of per-line discount
        $gstTotal   = 0.0; // GST on NET (after discount)
        $gstPercents = [];
    
        $enrichedItems = $items->map(function ($row) use (&$subTotal, &$discountT, &$gstTotal, &$gstPercents) {
            $qty   = (float)($row->quantity ?? $row->qty ?? 0);
            $rate  = (float)($row->rate ?? 0);
            $base  = $qty * $rate;
    
            $disc  = (float)($row->discount ?? 0);
            if ($disc > $base) { $disc = $base; }      // guard
            $net   = max($base - $disc, 0.0);
    
            $gstPct   = (float)($row->iGstPercentage ?? 0);
            $lineGst  = round($net * ($gstPct / 100.0), 2);   // GST on NET
            $lineTot  = round($net + $lineGst, 2);
    
            $subTotal   += $base;
            $discountT  += $disc;
            $gstTotal   += $lineGst;
    
            $gstPercents[] = round($gstPct, 2);
    
            // expose handy computed fields
            $row->line_subtotal = round($base, 2);   // before discount
            $row->line_discount = round($disc, 2);
            $row->line_net      = round($net, 2);    // after discount, before GST
            $row->line_gst      = $lineGst;          // GST on net
            $row->line_total    = $lineTot;          // net + GST
            return $row;
        });
    
        // Flags (unchanged)
        $uniquePcts = array_values(array_unique($gstPercents, SORT_REGULAR));
        $allSamePct = count($uniquePcts) <= 1;
    
        // Totals (API-consistent)
        $taxableAfter = max($subTotal - $discountT, 0.0);
        $grandTotal   = $taxableAfter + $gstTotal;
    
        $quotationDetails = $header->toArray();
        $quotationDetails['sub_total']              = round($subTotal, 2);
        $quotationDetails['total_discount']         = round($discountT, 2);
        $quotationDetails['gst_total']              = round($gstTotal, 2);     // ← correct GST (on net)
        $quotationDetails['taxable_after_discount'] = round($taxableAfter, 2);
        $quotationDetails['total_amount']           = round($grandTotal, 2);

    
        // Build GST flags payload
        $gstFlags = [
            'use_percentage'       => !$allSamePct,            // % option true when different %
            'use_amount'           => $allSamePct,             // amount option true when same %
            'gst_common_percentage'=> $allSamePct ? ($uniquePcts[0] ?? 0.0) : null,
            // 'gst_flat_amount'      => $allSamePct ? round($gstTotal, 2) : null,
        ];
    
        return response()->json([
            'success'           => true,
            'message'           => 'Quotation details',
            'quotation_details' => $quotationDetails,          // includes sub_total, gst_total, total_amount
            'gst_flags'         => $gstFlags,                  // ← flags you asked for
            'products'          => $enrichedItems,             // with line_subtotal/line_gst/line_total
        ]);
    }


    /**
     * GET /api/quotations/{id}/pdf
     * Stream the PDF (browser will download)
     */
    public function pdf($id)
    {
        $q = Quotation::select(
                'party.address1',
                'company_client_master.company_name',
                'company_client_master.Address',
                'company_client_master.email',
                'company_client_master.mobile',
                'company_client_master.plan_id',
                'company_client_master.GST',
                'party.strPartyName',
                'party.address2',
                'party.address3',
                'party.iMobile',
                'party.strEmail',
                'quotation.iQuotationNo',
                'quotation.entryDate',
                'quotation.iCompanyId',
                'quotation.quotationValidity',
                'quotation.modeOfDespatch',
                'quotation.deliveryTerm',
                'quotation.paymentTerms',
                'quotation.iGstType',
                'quotation.strTermsCondition'
            )
            ->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0, 'quotation.quotationId' => $id])
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->first();

        if (!$q) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $logoUrl = "https://salexo.in/assets/images/logo.png";

        $items = QuotationDetail::where([
            'quotationdetails.iStatus'  => 1,
            'quotationdetails.isDelete' => 0,
            'quotationdetails.quotationID' => $id
        ])->orderBy('quotationdetailsId')->get();

        $terms = TermCondition::where([
            'termcondition.iStatus'  => 1,
            'termcondition.isDelete' => 0,
            'termcondition.companyID'=> $q->iCompanyId
        ])->orderBy('termconditionId')->get();

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadview('company_client.quotation.detailPDF', [
                'Quotation'       => $q,
                'QuotationDetail' => $items,
                'TermCondition'   => $terms,
                // if your blade expects $pic, keep a data-uri
                'pic'             => $this->toDataUri($logoUrl)
            ]);

        $downloadName = $q->strPartyName . $q->iQuotationNo . '.pdf';
        return $pdf->download($downloadName);
    }

    /**
     * GET /api/party-mapping?company_ids[]=1&company_ids[]=2
     * Return party options (mapping)
     */
    public function mapping(Request $request)
    {
        $companyIds = (array) $request->input('company_ids', []);
        if (empty($companyIds)) {
            return response()->json(['data' => []]);
        }

        $list = Party::where(['iStatus' => 1, 'isDelete' => 0])
            ->whereIn('iCompanyId', $companyIds)
            ->orderByDesc('partyId')
            ->get(['partyId as id', 'strPartyName as name','iCompanyId as company_id']);

        return response()->json([
            'success'=>true,
            'message'=>"party mapping list",
            'data' => $list]);
    }

    /**
     * GET /api/term-conditions?company_id=123
     */
    public function termConditions(Request $request)
    {
        $companyId = $request->integer('company_id');
        if (!$companyId) {
            return response()->json(['message' => 'company_id is required'], 422);
        }

        $rows = TermCondition::where([
            'iStatus'  => 1,
            'isDelete' => 0,
            'companyID'=> $companyId
        ])->orderBy('termconditionId')->get();

        return response()->json(['data' => $rows]);
    }

    /**
     * POST /api/quotations/{id}/copy
     */
    public function copy($id)
    {
        $user = Auth::user();
    
        $old = Quotation::where([
            'iStatus' => 1,
            'isDelete' => 0,
            'quotationId' => $id
        ])->first();
    
        if (!$old) {
            return response()->json(['success'=>false,'message' => 'Not found'], 404);
        }
    
        // ✅ FETCH LAST QUOTATION NO like: 0012/24-25
        $last = Quotation::where('iCompanyId', $old->iCompanyId)
            ->orderByDesc('quotationId')
            ->value('iQuotationNo');
    
        // ✅ Extract number & create next running no
        $n = 0;
        if (!empty($last)) {
            $left = explode('/', trim($last))[0];  // 0012
            $n = (int) preg_replace('/\D/', '', $left); // 12
        }
    
        $nextLeft = str_pad((string)($n + 1), 4, '0', STR_PAD_LEFT);
    
        // ✅ Get current financial year
        $year = Year::where(['iStatus'=>1,'isDelete'=>0])
            ->orderByDesc('year_id')
            ->value('strYear');
    
        if (!$year) {
            $year = now()->format('y') . '-' . now()->addYear()->format('y');
        }
    
        // ✅ Final quotation no e.g. "0013/24-25"
        $nextQuotationNo = $nextLeft . '/' . $year;
    
        // ✅ INSERT NEW QUOTATION
        $copyId = DB::table('quotation')->insertGetId([
            'iYearId'           => $old->iYearId,
            'iQuotationNo'      => $nextQuotationNo,
            'iPartyId'          => $old->iPartyId,
            'iCompanyId'        => $old->iCompanyId,
            'quotationValidity' => $old->quotationValidity,
            'modeOfDespatch'    => $old->modeOfDespatch,
            'deliveryTerm'      => $old->deliveryTerm,
            'paymentTerms'      => $old->paymentTerms,
            'entryDate'         => now()->format('Y-m-d'),
            'iGstType'          => $old->iGstType,
            'strTermsCondition' => $old->strTermsCondition,
            'created_by'        => $user->emp_id,
            'iStatus'           => 1,
            'isDelete'          => 0,
        ]);
    
        // ✅ COPY ALL ITEMS
        $details = QuotationDetail::where([
            'quotationID' => $id,
            'iStatus'     => 1,
            'isDelete'    => 0
        ])->get();
    
        foreach ($details as $d) {
            DB::table('quotationdetails')->insert([
                'quotationID'      => $copyId,
                'productID'        => $d->productID,
                'description'      => $d->description,
                'uom'              => $d->uom,
                'quantity'         => $d->quantity,
                'rate'             => $d->rate,
                'amount'           => $d->amount,
                'discount'         => $d->discount,
                'netAmount'        => $d->netAmount,
                'iGstPercentage'   => $d->iGstPercentage,
                'created_by'       => $user->emp_id,
                'iStatus'          => 1,
                'isDelete'         => 0,
            ]);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Quotation copied successfully',
            'new_quotation_id' => $copyId,
            'new_quotation_no' => $nextQuotationNo
        ], 201);
    }


    /**
     * POST /api/quotations/{id}/send-whatsapp
     * Body: { "phone": "9198XXXXXXXX" }
     */
   public function sendWhatsApp(Request $request, $id)
{
    // 1) Clean phone
    $phone = preg_replace('/\D/', '', (string) $request->input('phone'));
    if (!$phone) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid phone number',
        ], 422);
    }

    // 2) Direct link to PDF in your system
    $pdfUrl = route('api.employee.quotations.pdf.link', $id);

    // 3) Build WhatsApp deep-link (opens WhatsApp with message ready)
    $text = "Dear customer,\nHere is your quotation PDF:\n{$pdfUrl}";
    $waLink = "https://wa.me/{$phone}?text=" . urlencode($text);

    return response()->json([
        'success' => true,
        'message' => 'WhatsApp link generated successfully',
        'whatsapp_url' => $waLink,
        'pdf_url' => $pdfUrl,
    ]);
}


    /* -------------------- helpers -------------------- */

    private function parseDate(?string $value): Carbon
    {
        if (!$value) return Carbon::now();
        // accepts d-m-Y or Y-m-d
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
            return Carbon::createFromFormat('d-m-Y', $value);
        }
        return Carbon::parse($value);
    }

    private function toDataUri(string $url): string
    {
        try {
            $type = pathinfo($url, PATHINFO_EXTENSION);
            $data = file_get_contents($url);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
