<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

// Your models (adjust if namespaces differ)
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\CompanyClient;
use App\Models\Party;
use App\Models\TermCondition;

class QuotationPdfController extends Controller
{
    /**
     * HTML preview in browser (no PDF) – handy for debugging layout.
     */
    public function preview($quotationId)
    {
        $data = $this->buildViewData($quotationId);
        return view('pdf.pdf_design1', $data);
    }

    /**
     * Stream PDF in browser.
     */
    public function stream($quotationId)
    {
        $data = $this->buildViewData($quotationId);

        $pdf = PDF::loadView('quotation.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled'     => true,     // allow external images if you ever use them
                'isHtml5ParserEnabled'=> true,
            ]);

        $filename = 'Quotation-' . ($data['quotationNumber'] ?? $quotationId) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Download PDF.
     */
    public function download($quotationId)
    {
        $data = $this->buildViewData($quotationId);

        $pdf = PDF::loadView('quotation.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled'     => true,
                'isHtml5ParserEnabled'=> true,
            ]);

        $filename = 'Quotation-' . ($data['quotationNumber'] ?? $quotationId) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Build and normalize data for the Blade view.
     * Central place to fix logo fallback, totals, terms, etc.
     */
    private function buildViewData($quotationId): array
    {
        $q = Quotation::with(['company', 'party'])
            ->where('quotationId', $quotationId)
            ->firstOrFail();

        $details = QuotationDetail::where('quotationId', $quotationId)->get();

        // ---- Company & Party ----
        /** @var CompanyClient|null $company */
        $company = $q->company;
        /** @var Party|null $party */
        $party = $q->party;

        // ---- Logo fallback & base64 embed (fast, DomPDF-safe) ----
        $companyLogoUrl = null;
        if ($company && !empty($company->strLogo)) {
            $tryPath = public_path('CompanyLogo/' . $company->strLogo);
        } else {
            $tryPath = public_path('assets/images/favicon.png'); // your fallback
        }
        if (!File::exists($tryPath)) {
            $tryPath = public_path('assets/images/favicon.png');
        }
        if (File::exists($tryPath)) {
            $ext  = pathinfo($tryPath, PATHINFO_EXTENSION) ?: 'png';
            $mime = $ext === 'jpg' ? 'jpeg' : $ext;
            $data = base64_encode(file_get_contents($tryPath));
            $companyLogoUrl = "data:image/{$mime};base64,{$data}";
        }

        // ---- Items array for the template ----
        $items = [];
        foreach ($details as $d) {
            $items[] = [
                'name'  => $d->strProductName ?? $d->product_name ?? 'Item',
                'desc'  => $d->strDescription ?? $d->description ?? null,
                'hsn'   => $d->hsn_code ?? $d->hsn ?? null,
                'qty'   => (float)($d->qty ?? $d->quantity ?? 0),
                'rate'  => (float)($d->rate ?? $d->unit_price ?? 0),
            ];
        }

        // ---- GST logic & other financials (override from DB if you already store them) ----
        $discount      = (float)($q->discount ?? 0);      // absolute amount discount
        $gstRate       = (float)($q->gstRate ?? 18);      // 0..28 typical
        $isInterState  = (bool)($q->isInterState ?? false);

        // If you store state codes, you can compute isInterState here:
        // $isInterState = ($company?->state_code ?? null) !== ($party?->state_code ?? null);

        // ---- Terms from DB (optional) ----
        $extraTerms = [];
        $terms = TermCondition::where('iStatus', 1)->where('isDelete', 0)->orderBy('termconditionId')->get();
        foreach ($terms as $t) {
            $extraTerms[] = $t->strTerm ?? $t->term ?? '';
        }

        // Map to the Blade placeholders used in the provided HTML
        return [
            // Header meta
            'companyLogoUrl' => $companyLogoUrl,
            'companyName'    => $company->strCompanyName ?? $company->company_name ?? 'Your Company Pvt. Ltd.',
            'companyAddress' => trim(($company->strAddress ?? $company->address ?? '') . ' ' . ($company->strCity ?? $company->city ?? '') . ' ' . ($company->strState ?? $company->state ?? '') . ' ' . ($company->strPincode ?? $company->pincode ?? '')),
            'companyGstin'   => $company->strGSTNO ?? $company->gstin ?? null,
            'companyPhone'   => $company->strPhone ?? $company->phone ?? null,
            'companyEmail'   => $company->strEmail ?? $company->email ?? null,
            'companyState'   => $company->strState ?? $company->state ?? null,

            'quotationNumber'=> $q->strQuotationNo ?? $q->quotation_no ?? ('QTN-'.$quotationId),
            'quotationDate'  => optional($q->quotationDate ?? $q->quotation_date ?? now())->format('d-m-Y'),
            'validTill'      => optional($q->valid_till ?? now()->addDays(7))->format('d-m-Y'),

            // Party
            'partyName'      => $party->strPartyName ?? $party->party_name ?? 'Party',
            'partyAddress'   => trim(($party->strAddress ?? $party->address ?? '') . ' ' . ($party->strCity ?? $party->city ?? '') . ' ' . ($party->strState ?? $party->state ?? '') . ' ' . ($party->strPincode ?? $party->pincode ?? '')),
            'partyGstin'     => $party->strGSTNO ?? $party->gstin ?? null,
            'partyPhone'     => $party->strPhone ?? $party->phone ?? null,

            // Items + tax inputs
            'items'          => $items,
            'discount'       => $discount,
            'gstRate'        => $gstRate,
            'isInterState'   => $isInterState,

            // Optional notes
            'paymentTerms'   => $q->payment_terms ?? '50% advance, balance on delivery',
            'delivery'       => $q->delivery ?? 'Within 7–10 business days from PO',
            'warranty'       => $q->warranty ?? '12 months from invoice date',

            // Bank details (adjust to your fields)
            'bankName'       => $company->bank_account_name ?? $company->strCompanyName ?? 'Your Company Pvt. Ltd.',
            'bankAccount'    => $company->bank_account_no ?? null,
            'bankIfsc'       => $company->bank_ifsc ?? null,
            'bankBranch'     => $company->bank_branch ?? null,

            // Extra terms list (appended to template default terms)
            'extraTerms'     => $extraTerms,
        ];
    }
}
