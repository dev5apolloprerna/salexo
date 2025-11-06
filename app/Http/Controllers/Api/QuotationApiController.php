<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\CompanyClient;
use App\Models\Year;
use App\Models\Party;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\TermCondition;
use App\Models\Service; // products table in your original code looked like Service
use Illuminate\Support\Carbon;

class QuotationApiController extends Controller
{
    /**
     * GET /api/quotations
     * List quotations (with filters + pagination)
     */
    public function index(Request $request)
    {
        $PartyName = $request->partyName;
        $fromDate  = $request->fromDate;     // dd-mm-YYYY or yyyy-mm-dd (we will convert)
        $toDate    = $request->toDate;  

        $query = Quotation::query()
            ->where(['quotation.iStatus' => 1, 'quotation.isDelete' => 0])
            ->when($PartyName, function($q) use($PartyName) {
                return $q->where('quotation.iPartyId', $PartyName);
            })
            ->when($fromDate, function($q) use($fromDate) {
                $from = date('Y-m-d', strtotime($fromDate));
                return $q->whereDate('quotation.entryDate', '>=', $from);
            })
            ->when($toDate, function($q) use($toDate) {
                $to = date('Y-m-d', strtotime($toDate));
                return $q->whereDate('quotation.entryDate', '<=', $to);
            })
            ->join('company_client_master', 'quotation.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'quotation.iPartyId', '=', 'party.partyId')
            ->join('year', 'quotation.iYearId', '=', 'year.year_id')
            ->orderByDesc('quotation.quotationId')
            ->select([
                'quotation.*',
                'company_client_master.company_name',
                'party.strPartyName',
                'year.year_id'
            ]);

        $paginated = $query->get();

        return response()->json([
            'status'=>'success',
            'message'=>'Quotation List',
            'data' => $paginated,
            
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

        $v = Validator::make($request->all(), [
            'iYearId'            => 'required|integer|exists:year,year_id',
            'iQuotationNo'       => 'required|string|max:50',
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
            'iStatus'           => 1,
            'isDelete'          => 0,
        ];

        $id = DB::table('quotation')->insertGetId($data);

        return response()->json([
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
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $q]);
    }

    /**
     * PUT/PATCH /api/quotations/{id}
     * Update quotation
     */
    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'iYearId'            => 'required|integer|exists:year,year_id',
            'iQuotationNo'       => 'required|string|max:50',
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
            ]);

        if (!$affected) {
            return response()->json(['message' => 'Not found or no changes'], 404);
        }

        return response()->json([
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
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['message' => 'Quotation deleted successfully']);
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
            return response()->json(['message' => 'Not found'], 404);
        }

        // Prefer a static logo URL; avoid embedding base64 in API
        $logoUrl = "https://salexo.in/assets/images/logo.png";

        $items = QuotationDetail::where([
            'quotationdetails.iStatus'  => 1,
            'quotationdetails.isDelete' => 0,
            'quotationdetails.quotationID' => $id
        ])->orderBy('quotationdetailsId')->get();

        $terms = TermCondition::where([
            'termcondition.iStatus'  => 1,
            'termcondition.isDelete' => 0,
            'termcondition.companyID'=> $header->iCompanyId
        ])->orderBy('termconditionId')->get();

        // helpful links
        $pdfUrl = route('api.quotations.pdf', $id);
        $downloadName = $header->strPartyName . $header->iQuotationNo . '.pdf';

        return response()->json([
            'status' => 'success',
            'message' => 'Quotation details',
            'quotation_details' => $header,
            'products'  => $items,
            'terms'  => $terms,
            'logo_url' => $logoUrl,
            'pdf' => [
                'url' => $pdfUrl,
                'filename' => $downloadName
            ]
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
            'status'=>'success',
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
        $q = Quotation::where(['iStatus' => 1, 'isDelete' => 0, 'quotationId' => $id])->first();
        if (!$q) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $copyId = DB::table('quotation')->insertGetId([
            'iYearId'           => $q->iYearId,
            'iQuotationNo'      => $q->iQuotationNo,
            'iPartyId'          => $q->iPartyId,
            'iCompanyId'        => $q->iCompanyId,
            'quotationValidity' => $q->quotationValidity,
            'modeOfDespatch'    => $q->modeOfDespatch,
            'deliveryTerm'      => $q->deliveryTerm,
            'paymentTerms'      => $q->paymentTerms,
            'entryDate'         => Carbon::parse($q->entryDate)->format('Y-m-d'),
            'iGstType'          => $q->iGstType,
            'strTermsCondition' => $q->strTermsCondition,
            'iStatus'           => 1,
            'isDelete'          => 0,
        ]);

        $details = QuotationDetail::where([
            'quotationdetails.iStatus'  => 1,
            'quotationdetails.isDelete' => 0,
            'quotationdetails.quotationID' => $id
        ])->orderBy('quotationdetailsId')->get();

        foreach ($details as $d) {
            DB::table('quotationdetails')->insert([
                'productID'        => $d->productID,
                'quotationID'      => $copyId,
                'description'      => $d->description,
                'uom'              => $d->uom,
                'quantity'         => $d->quantity,
                'rate'             => $d->rate,
                'amount'           => $d->amount,
                'discount'         => $d->discount,
                'netAmount'        => $d->netAmount,
                'iGstPercentage'   => $d->iGstPercentage,
                'iStatus'          => 1,
                'isDelete'         => 0,
            ]);
        }

        return response()->json([
            'message' => 'Quotation copied successfully',
            'new_quotation_id' => $copyId
        ], 201);
    }

    /**
     * POST /api/quotations/{id}/send-whatsapp
     * Body: { "phone": "9198XXXXXXXX" }
     */
    public function sendWhatsApp(Request $request, $id)
    {
        $phone = preg_replace('/\D/', '', (string) $request->input('phone'));
        if (!$phone) {
            return response()->json(['message' => 'Invalid phone number'], 422);
        }

        // Direct link to PDF endpoint in this API
        $pdfUrl = route('api.quotations.pdf', $id);

        $token         = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (!$token || !$phoneNumberId) {
            return response()->json(['message' => 'WhatsApp config missing'], 500);
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'document',
            'document' => [
                'link'     => $pdfUrl,
                'filename' => "Quotation-{$id}.pdf",
            ],
        ];

        $resp = Http::withToken($token)
            ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", $payload);

        if (!$resp->ok()) {
            $err = $resp->json();
            return response()->json([
                'message' => 'Failed to send WhatsApp message',
                'error'   => $err['error']['message'] ?? $resp->body()
            ], 502);
        }

        return response()->json(['message' => 'Quotation sent on WhatsApp']);
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
