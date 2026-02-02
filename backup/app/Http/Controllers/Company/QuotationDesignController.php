<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\CompanyClient;
use App\Models\Party;
use App\Models\TermCondition;

class QuotationDesignController extends Controller
{
    // ------- Public design keys we support -------
    private array $designs = ['v1','v2','v3','v4','v5'];

    // ------- Main picker UI -------
    public function picker(Request $request)
    {
        $latest = Quotation::orderByDesc('quotationId')->first();
        return view('company_client.quotation.designs.picker', [
            'designs' => $this->designs,
            'latestId' => $latest?->quotationId,
        ]);
    }

    // ------- Latest Quotation (auto-select) -------
    public function previewLatest($design)
    {
        $q = Quotation::orderByDesc('quotationId')->firstOrFail();
        return $this->renderHtml($q->quotationId, $design);
    }

    public function pdfLatest($design)
    {
        $q = Quotation::orderByDesc('quotationId')->firstOrFail();
        return $this->renderPdf($q->quotationId, $design);
    }

    // ------- Specific Quotation -------
    public function preview($id, $design)
    {
        return $this->renderHtml($id, $design);
    }

    public function pdf($id, $design)
    {
        return $this->renderPdf($id, $design);
    }

    // ================== Internals ==================
   private function renderHtml($quotationId, $design)
    {
        $this->guardDesign($design);
        $data = $this->buildViewData($quotationId);
        // Map design -> view file
        $view = match ($design) {
            'v1' => 'company_client.quotation.templates.v1',
            'v2' => 'company_client.quotation.templates.v2',
            'v3' => 'company_client.quotation.templates.v3',
            'v4' => 'company_client.quotation.templates.v4',
            'v5' => 'company_client.quotation.templates.v5',
            default => 'company_client.quotation.templates.v1',
        };
        return view($view, $data);
    }

    private function renderPdf($quotationId, $design)
    {
        $this->guardDesign($design);
        $data = $this->buildViewData($quotationId);
        $view = match ($design) {
            'v1' => 'quotation.templates.v1',
            'v2' => 'quotation.templates.v2',
            'v3' => 'quotation.templates.v3',
            'v4' => 'quotation.templates.v4',
            'v5' => 'quotation.templates.v5',
            default => 'quotation.templates.v1',
        };
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)
            ->setPaper('a4','portrait')
            ->setOptions(['isRemoteEnabled'=>true,'isHtml5ParserEnabled'=>true]);
        $filename = 'Quotation-' . ($data['quotationNumber'] ?? $quotationId) . '-' . strtoupper($design) . '.pdf';
        return $pdf->download($filename);
    }
    // QuotationDesignController.php (private helpers)

        private function toArrayOrNull($v): ?array {
            if ($v === null) return null;

            // already array/object?
            if (is_array($v)) return $v;
            if (is_object($v)) return (array)$v;

            // JSON string?
            if (is_string($v)) {
                $s = trim($v);
                if ($s !== '' && ($s[0] === '{' || $s[0] === '[')) {
                    $decoded = json_decode($s, true);
                    if (json_last_error() === JSON_ERROR_NONE) return $decoded;
                }
                // plain string
                return null;
            }
            return null;
        }

        /**
         * Return a clean string from mixed value.
         * - If it's an array/object/JSON, try the preferred keys (stateName, name, title, label, value…).
         * - Otherwise return the trimmed string.
         */
        private function cleanStr($v, array $preferredKeys = []): ?string {
            if ($v === null) return null;

            // if array/object/json
            $arr = $this->toArrayOrNull($v);
            if ($arr !== null) {
                $keys = array_merge($preferredKeys, ['stateName','cityName','name','title','label','value']);
                foreach ($keys as $k) {
                    if (isset($arr[$k]) && $arr[$k] !== null && $arr[$k] !== '') {
                        return trim((string)$arr[$k]);
                    }
                }
                // fall back to first scalar-ish value
                foreach ($arr as $val) {
                    if (is_scalar($val)) return trim((string)$val);
                }
                return null;
            }

            // plain string
            if (is_string($v)) return trim($v);

            // number/bool
            if (is_scalar($v)) return trim((string)$v);

            return null;
        }

        /** Build a human address safely from parts (skips blanks/JSON) */
        private function buildAddress(...$parts): string {
            $out = [];
            foreach ($parts as $p) {
                $txt = $this->cleanStr($p);
                if ($txt) $out[] = $txt;
            }
            return implode(', ', $out);
        }



    private function guardDesign(string $design): void
    {
        if (!in_array($design, $this->designs, true)) {
            abort(404, 'Invalid quotation design.');
        }
    }

    /**
     * Same builder you already use (adapted from prior message).
     */
    private function buildViewData($quotationId): array
    {
        $q = Quotation::with(['company','party'])
            ->where('quotationId', $quotationId)
            ->firstOrFail();
        $details = QuotationDetail::where('quotationId', $quotationId)->get();

        $company = $q->company;
        $party   = $q->party;

        // --- Clean Company fields ---
        $companyState = $this->cleanStr($company->strState ?? $company->state ?? null, ['stateName','name']);
        $companyCity  = $this->cleanStr($company->strCity  ?? $company->city  ?? null, ['cityName','name']);
        $companyAddr1 = $this->cleanStr($company->strAddress ?? $company->address ?? null);
        $companyPin   = $this->cleanStr($company->strPincode ?? $company->pincode ?? null);

        $companyAddress = $this->buildAddress($companyAddr1, $companyCity, $companyState, $companyPin);

        // --- Clean Party fields ---
        $partyState = $this->cleanStr($party->strState ?? $party->state ?? null, ['stateName','name']);
        $partyCity  = $this->cleanStr($party->strCity  ?? $party->city  ?? null, ['cityName','name']);
        $partyAddr1 = $this->cleanStr($party->strAddress ?? $party->address ?? null);
        $partyPin   = $this->cleanStr($party->strPincode ?? $party->pincode ?? null);

        $partyAddress = $this->buildAddress($partyAddr1, $partyCity, $partyState, $partyPin);

        $companyLogoUrl = null;
        $tryPath = $company && $company->strLogo
            ? public_path('CompanyLogo/' . $company->strLogo)
            : public_path('assets/images/favicon.png');

        if (!File::exists($tryPath)) $tryPath = public_path('assets/images/favicon.png');

        if (File::exists($tryPath)) {
            $ext  = pathinfo($tryPath, PATHINFO_EXTENSION) ?: 'png';
            $mime = $ext === 'jpg' ? 'jpeg' : $ext;
            $companyLogoUrl = "data:image/{$mime};base64," . base64_encode(file_get_contents($tryPath));
        }

        $items = [];
        foreach ($details as $d) {
            $items[] = [
                'name' => $d->strProductName ?? $d->product_name ?? 'Item',
                'desc' => $d->strDescription ?? $d->description ?? null,
                'hsn'  => $d->hsn_code ?? $d->hsn ?? null,
                'qty'  => (float)($d->qty ?? $d->quantity ?? 0),
                'rate' => (float)($d->rate ?? $d->unit_price ?? 0),
            ];
        }

        $discount     = (float)($q->discount ?? 0);
        $gstRate      = (float)($q->gstRate ?? 18);
        $isInterState = (bool)($q->isInterState ?? false);

        $extraTerms = [];
        $terms = TermCondition::where('iStatus', 1)->where('isDelete', 0)->orderBy('termconditionId')->get();
        foreach ($terms as $t) $extraTerms[] = $t->strTerm ?? $t->term ?? '';

        // --- Keep your existing code below (logo, items, gst, etc.) ---
        // ... (logo/base64, items loop, terms, totals inputs) ...

        return [
            // header/company
            'companyLogoUrl' => $companyLogoUrl,
            'companyName'    => $this->cleanStr($company->strCompanyName ?? $company->company_name ?? 'Your Company Pvt. Ltd.'),
            'companyAddress' => $companyAddress,
            'companyGstin'   => $this->cleanStr($company->strGSTNO ?? $company->gstin ?? null),
            'companyPhone'   => $this->cleanStr($company->strPhone ?? $company->phone ?? null),
            'companyEmail'   => $this->cleanStr($company->strEmail ?? $company->email ?? null),
            'companyState'   => $companyState ?: '',

            // quotation meta
            'quotationNumber'=> $q->strQuotationNo ?? $q->quotation_no ?? ('QTN-'.$quotationId),
            'quotationDate'  => optional($q->quotationDate ?? $q->quotation_date ?? now())->format('d-m-Y'),
            'validTill'      => optional($q->valid_till ?? now()->addDays(7))->format('d-m-Y'),

            // party
            'partyName'      => $this->cleanStr($party->strPartyName ?? $party->party_name ?? 'Party'),
            'partyAddress'   => $partyAddress,
            'partyGstin'     => $this->cleanStr($party->strGSTNO ?? $party->gstin ?? null),
            'partyPhone'     => $this->cleanStr($party->strPhone ?? $party->phone ?? null),

            // items / taxes / notes … (unchanged)
            'items'        => $items,
            'discount'     => $discount,
            'gstRate'      => $gstRate,
            'isInterState' => $isInterState,
            'paymentTerms'   => $q->payment_terms ?? '50% advance, balance on delivery',
            'delivery'       => $q->delivery ?? 'Within 7–10 business days from PO',
            'warranty'       => $q->warranty ?? '12 months from invoice date',
            'bankName'       => $company->bank_account_name ?? $company->strCompanyName ?? 'Your Company Pvt. Ltd.',
            'bankAccount'    => $company->bank_account_no ?? null,
            'bankIfsc'       => $company->bank_ifsc ?? null,
            'bankBranch'     => $company->bank_branch ?? null,
            'extraTerms'   => $extraTerms,
        ];
    }

}
