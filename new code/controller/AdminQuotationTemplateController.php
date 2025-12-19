<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationTemplate;
use App\Models\Quotation;
use App\Models\Party;
use App\Models\QuotationDetail;
use App\Models\CompanyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AdminQuotationTemplateController extends Controller
{
    /**
     * ✅ LIVE storage root (outside Laravel /public)
     * Example: /home/youruser/public_html
     *
     * Change this if your hosting path is different.
     */
    protected function publicHtmlRoot(): string
    {
        // Typical setup where Laravel is in /home/xxx/laravel_app
        // and website public root is /home/xxx/public_html
        return rtrim(base_path('../public_html'), DIRECTORY_SEPARATOR);
    }

    /**
     * Build absolute path inside public_html.
     * e.g. publicHtmlPath('quotation_templates/abc/file.html')
     */
    protected function publicHtmlPath(string $relative = ''): string
    {
        $relative = ltrim($relative, '/\\');
        return $relative === ''
            ? $this->publicHtmlRoot()
            : $this->publicHtmlRoot() . DIRECTORY_SEPARATOR . $relative;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $templates = QuotationTemplate::orderByDesc('id')->get();

        $currentDefaultGuid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');

        return view('admin.quotation_template.designs_index', compact('templates', 'currentDefaultGuid'));
    }

    public function create()
    {
        return view('admin.quotation_template.designs_create');
    }

    /**
     * ✅ CHANGE: store uploaded template in LIVE:
     * public_html/quotation_templates/{guid}/filename.html
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:200',
            'file' => 'required|file|mimes:php,html,htm',
        ]);

        $guid = (string) Str::uuid();

        // store in /public_html/quotation_templates/{guid}
        $relativeDir = "quotation_templates/{$guid}";
        $baseDir = $this->publicHtmlPath($relativeDir);

        if (!File::isDirectory($baseDir)) {
            File::makeDirectory($baseDir, 0775, true);
        }

        $file = $request->file('file');

        // safe filename
        $name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $file->getClientOriginalName());

        // move file to public_html folder
        $file->move($baseDir, $name);

        // store RELATIVE path in DB (so you can render from the same root)
        $relativePath = "{$relativeDir}/{$name}";

        QuotationTemplate::create([
            'guid'       => $guid,
            'name'       => $request->name,
            'file_path'  => $relativePath,
            'is_active'  => 1,
            'is_default' => 0,
        ]);

        return redirect()->route('admin.quotations.templates')
            ->with('success', 'Template uploaded successfully');
    }

    public function toggle(QuotationTemplate $template)
    {
        $template->is_active = !$template->is_active;
        $template->save();

        return back()->with('success', 'Template status changed.');
    }

    public function setDefault(QuotationTemplate $template)
    {
        $companyId = auth()->user()->company_id;

        // store GUID as default (as you already do)
        $valueToStore = (string) $template->guid;

        DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->update(['companyTemplate' => $valueToStore]);

        return back()->with('success', 'Default template set for your company.');
    }

    /**
     * ✅ CHANGE: delete from public_html storage
     */
    public function destroy(QuotationTemplate $template)
    {
        if ($template->file_path) {
            $abs = $this->publicHtmlPath($template->file_path);
            if (File::exists($abs)) {
                File::delete($abs);
            }

            // delete folder /quotation_templates/{guid} if exists
            $folder = dirname($abs);
            if (File::exists($folder)) {
                @File::deleteDirectory($folder);
            }
        }

        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    // ✅ Preview specific template
    public function preview(QuotationTemplate $template, $quotationId)
    {
        $quotation = Quotation::with('party', 'company')->findOrFail($quotationId);
        $data = $this->previewData($quotation);
        return $this->renderTemplate($template, $data);
    }

    public function previewDefault()
    {
        $companyId = auth()->user()->company_id;

        $guid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');

        abort_if(!$guid, 404, 'No default template set for your company.');

        // If you already have previewLatest(), keep this as-is.
        // Otherwise you can implement it similarly to preview() by loading template by guid.
        return $this->previewLatest(request(), $guid);
    }

    /**
     * ✅ CHANGE: render template from public_html path
     */
    protected function renderTemplate(QuotationTemplate $tpl, array $data)
    {
        $full = $this->publicHtmlPath($tpl->file_path);

        if (!file_exists($full)) {
            abort(422, 'Template file not found.');
        }

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $html = file_get_contents($full);

        // If the HTML contains Blade directives, render it through Blade:
        if ($ext === 'html' || $ext === 'htm') {
            if (preg_match('/@php|@foreach|@if|{{/', $html)) {
                $out = Blade::render($html, $data);
                return response($out, 200)->header('Content-Type', 'text/html; charset=UTF-8');
            } else {
                $out = $this->simpleReplace($html, $data); // keep your existing method if you have it
                return response($out, 200)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        // Fallback for php/blade files on disk
        return View::file($full, $data);
    }

    /**
     * If you don't already have it, keep a minimal placeholder replacer for non-blade HTML
     * (Optional - safe to remove if your code already has this method elsewhere)
     */
    protected function simpleReplace(string $html, array $data): string
    {
        // Supports {{ key }} only (no nested/loops)
        foreach ($data as $k => $v) {
            if (is_scalar($v) || is_null($v)) {
                $html = str_replace('{{ ' . $k . ' }}', (string) $v, $html);
                $html = str_replace('{{' . $k . '}}', (string) $v, $html);
            }
        }
        return $html;
    }

    protected function previewData($quotation): array
    {
        if (!is_object($quotation)) {
            $quotation = Quotation::findOrFail($quotation);
        }

        $qId = $quotation->quotationId ?? $quotation->id;

        $company = CompanyClient::with('state')
            ->where('company_id', $quotation->iCompanyId)
            ->first();

        $party = Party::with('state')
            ->where('partyId', $quotation->iPartyId)
            ->first();

        /* -----------------  Helper closures  ----------------- */
        $clean = function ($v) {
            if (is_null($v)) return null;
            $v = trim((string) $v);
            return $v === '' ? null : $v;
        };

        $get = function ($obj, $keys) use ($clean) {
            foreach ($keys as $k) {
                if (is_object($obj) && isset($obj->{$k})) {
                    $val = $clean($obj->{$k});
                    if ($val !== null) return $val;
                }
            }
            return null;
        };

        $fmtDate = function ($val, $fallback = null) {
            if (!$val) return $fallback ? \Carbon\Carbon::parse($fallback)->format('d-m-Y') : '';
            try {
                return \Carbon\Carbon::parse($val)->format('d-m-Y');
            } catch (\Throwable $e) {
                return $fallback ? \Carbon\Carbon::parse($fallback)->format('d-m-Y') : '';
            }
        };

        $address = function (...$parts) {
            $good = [];
            foreach ($parts as $p) {
                $p = trim((string) $p);
                if ($p !== '') $good[] = $p;
            }
            return implode(', ', $good);
        };

        /* -----------------  Company fields  ----------------- */
        $companyName  = $company->company_name ?? 'Your Company Pvt. Ltd.';
        $companyPhone = $company->mobile ?? null;
        $companyEmail = $company->email ?? null;
        $companyGST   = $company->GST ?? '-';
        $companyState = $company->state->stateName ?? null;
        $companyCity  = $company->city ?? null;
        $companyAddr1 = $company->Address ?? null;
        $companyPin   = $company->pincode ?? null;
        $companyAddr  = $address($companyAddr1, $companyCity, $companyState, $companyPin);

        $companyLogoUrl = null;

        /* -----------------  Party fields  ----------------- */
        $partyName  = $party->strPartyName ?? 'Party';
        $partyPhone = $party->iMobile ?? null;
        $partyGST   = $party->strGST ?? null;
        $partyCity  = $party->city ?? null;
        $partyAddr1 = $party->address1 ?? null;
        $partyStateName = $party->state->stateName ?? $party->state->name ?? null;

        $partyAddr = implode(', ', array_filter([$partyAddr1, $partyCity, $partyStateName], fn($x) => $x !== null && trim($x) !== ''));

        /* -----------------  Line items  ----------------- */
        $details = QuotationDetail::with('service')
            ->where(['quotationID' => $qId, 'iStatus' => 1, 'isDelete' => 0])
            ->get();

        $items = [];
        foreach ($details as $d) {
            $qty       = (float) ($d->quantity ?? $d->qty ?? 0);
            $rate      = (float) ($d->rate ?? 0);
            $netAmount = (float) ($d->netAmount ?? 0);
            $discount  = (float) ($d->discount ?? 0);
            $amount    = (float) ($d->totalAmount ?? 0);

            $items[] = [
                'name'      => $clean($d->service->service_name ?? ''),
                'desc'      => $clean($d->strDescription ?? $d->description ?? ''),
                'hsn'       => $clean($d->uom ?? '-') ,
                'gst'       => $clean($d->iGstPercentage ?? ''),
                'qty'       => $qty,
                'rate'      => $rate,
                'amount'    => $amount,
                'netAmount' => $netAmount,
                'discount'  => $discount,
            ];
        }

        /* -----------------  Quotation meta  ----------------- */
        $discount     = (float) ($quotation->discount ?? 0);
        $gstRate      = (float) ($quotation->gstRate ?? 18);
        $isInterState = (bool)  ($quotation->isInterState ?? 0);

        $quotationNumber = $clean($quotation->iQuotationNo ?? null) ?? ('QTN-' . $qId);
        $quotationDate   = $fmtDate($quotation->quotationDate ?? $quotation->entryDate, now());
        $validTill       = $fmtDate($quotation->valid_till ?? $quotation->quotationValidity, now()->addDays(7));

        /* -----------------  Footer  ----------------- */
        $paymentTerms   = $clean($quotation->paymentTerms) ?? '50% advance, balance on delivery';
        $delivery       = $clean($quotation->deliveryTerm) ?? 'Within 7–10 business days from PO';
        $modeOfDespatch = $clean($quotation->modeOfDespatch) ?? '';
        $extraTerms     = $clean($quotation->strTermsCondition) ?? '';

        $bankName   = $get($company, ['bank_account_name', 'company_name']) ?? $companyName;
        $bankAcc    = $get($company, ['bank_account_no', 'account_no', 'acno']);
        $bankIfsc   = $get($company, ['bank_ifsc', 'ifsc']);
        $bankBranch = $get($company, ['bank_branch', 'branch']);

        return [
            'companyLogoUrl' => $companyLogoUrl,
            'companyName'    => $companyName,
            'companyAddress' => $companyAddr,
            'companyGstin'   => $companyGST,
            'companyPhone'   => $companyPhone,
            'companyEmail'   => $companyEmail,
            'companyState'   => $companyState,

            'quotationNumber' => $quotationNumber,
            'quotationDate'   => $quotationDate,
            'validTill'       => $validTill,

            'partyName'    => $partyName,
            'partyAddress' => $partyAddr,
            'partyGstin'   => $partyGST,
            'partyPhone'   => $partyPhone,

            'items'        => $items,
            'discount'     => $discount,
            'gstRate'      => $gstRate,
            'isInterState' => $isInterState,

            'paymentTerms'    => $paymentTerms,
            'delivery'        => $delivery,
            'modeOfDespatch'  => $modeOfDespatch,
            'termCondition'   => $extraTerms,

            'bankName'    => $bankName,
            'bankAccount' => $bankAcc,
            'bankIfsc'    => $bankIfsc,
            'bankBranch'  => $bankBranch,
        ];
    }
}
