<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use App\Models\InvoiceTemplate;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\CompanyClient;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

    use Illuminate\Support\Facades\Blade;

class InvoiceTemplateController extends Controller
{
   public function index(Request $request)
    {
        // Logged-in company id (guard: web_employees)
        $companyId = auth()->user()->company_id;

        // All templates (global list). If you keep per-company templates, filter here instead.
        $templates = InvoiceTemplate::where(['is_active'=>1])->orderByDesc('id')->get();

        // Read this company’s default (we store the template GUID here; switch to 'id' if you prefer)
        $currentDefaultGuid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('invoice_template');

        return view('company_client.invoice_template.designs_index', compact('templates','currentDefaultGuid'));
    }
    public function toggle(InvoiceTemplate $template)
    {
        $template->is_active = !$template->is_active;
        $template->save();
        return back()->with('success', 'Template status changed.');
    }

    public function setDefault(InvoiceTemplate $template)
    {
        $companyId = auth()->user()->company_id;

        // Value you want to store as default for this company:
        // If you prefer ID, change to (string)$template->id and keep it consistent everywhere.
        $valueToStore = (string) $template->guid;

        DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->update(['invoice_template' => $valueToStore]);

        return back()->with('success', 'Default invoice template set for your company.');
    }


    public function destroy(InvoiceTemplate $template)
    {
        if ($template->file_path) {
            $abs = public_path($template->file_path);
            if (File::exists($abs)) File::delete($abs);
        }

        // delete folder if empty
        $folder = dirname(public_path($template->file_path));
        if (File::exists($folder)) @File::deleteDirectory($folder);

        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    // ✅ Preview specific template
    public function preview(InvoiceTemplate $template, $InvoiceId)
    {
        $Invoice = Invoice::with('party','company')
            ->findOrFail($InvoiceId);
        $data = $this->previewData($Invoice);
        return $this->renderTemplate($template, $data);
    }

        public function previewDefault()
        {
            $companyId = auth()->user()->company_id;

            $guid = DB::table('company_client_master')
                ->where('company_id', $companyId)
                ->value('companyTemplate');

            abort_if(!$guid, 404, 'No default template set for your company.');

            // Reuse your existing previewLatest($guid)
            return $this->previewLatest(request(), $guid);
        }


    protected function renderTemplate(InvoiceTemplate $tpl, array $data)
    {
        $full = public_path($tpl->file_path);
        if (!file_exists($full)) abort(422, 'Template file not found.');

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $html = file_get_contents($full);

        // If the HTML contains Blade directives, render it through Blade:
        if ($ext === 'html' || $ext === 'htm') {
            if (preg_match('/@php|@foreach|@if|{{/', $html)) {
                // Laravel 9+: Blade::render() compiles + renders a string
                $out = Blade::render($html, $data);
                return response($out, 200)->header('Content-Type', 'text/html; charset=UTF-8');
            } else {
                // Plain placeholders: {{ key }} only (optional)
                $out = $this->simpleReplace($html, $data);
                return response($out, 200)->header('Content-Type','text/html; charset=UTF-8');
            }
        }

        // Fallback for php/blade files
        return View::file($full, $data);
    }


    protected function previewData($Invoice): array
{
   
    // If only ID passed instead of full object
    if (!is_object($Invoice)) {
        $Invoice = Invoice::findOrFail($Invoice);
    }

    $qId = $Invoice->InvoiceId ?? $Invoice->id;

    // Safely load company & party (no login, no company filter)
    $company =CompanyClient::with('state')->where('company_id', $Invoice->iCompanyId)->first();

    $party = Party::with('state')->where('partyId', $Invoice->iPartyId)->first();



    /* -----------------  Helper closures  ----------------- */
    $clean = function($v) {
        if (is_null($v)) return null;
        $v = trim((string)$v);
        return $v === '' ? null : $v;
    };

    $get = function($obj, $keys) use ($clean) {
        foreach ($keys as $k) {
            if (is_object($obj) && isset($obj->{$k})) {
                $val = $clean($obj->{$k});
                if ($val !== null) return $val;
            }
        }
        return null;
    };

    $fmtDate = function($val,$fallback=null) {
        if (!$val) return $fallback ? \Carbon\Carbon::parse($fallback)->format('d-m-Y') : '';
        try {
            return \Carbon\Carbon::parse($val)->format('d-m-Y');
        } catch (\Throwable $e) {
            return $fallback ? \Carbon\Carbon::parse($fallback)->format('d-m-Y') : '';
        }
    };

    $address = function(...$parts) {
        $good = [];
        foreach ($parts as $p) {
            $p = trim((string)$p);
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

    // Company logo → base64 inline
    $companyLogoUrl = null;
        /*$root = base_path('../public_html/');

        // 1) pick relative path (from DB or fallback)
        $rel = data_get($company, 'company_logo'); // e.g. 'uploads/company/logo.png' or 'logo.png'
        $rel = $rel ? (str_contains($rel, '/') ? $rel : "uploads/company/$rel")
                    : 'assets/images/favicon.png';
        
        // 2) build absolute path and fallback if missing
        $path = $root . ltrim($rel, '/');
        if (!file_exists($path)) {
            $path = $root . 'assets/images/favicon.png';
        }
        
        // 3) make data URI
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
        $mime = $ext === 'jpg' ? 'image/jpeg' : "image/$ext";
        $companyLogoUrl = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        */

    /* -----------------  Party fields  ----------------- */
   $partyName  = $party->strPartyName ?? 'Party';
    $partyPhone = $party->iMobile ?? null;
    $partyGST   = $party->strGST ?? null;
    $partyCity  = $party->city ?? null;
    $partyAddr1 = $party->address1 ?? null;
    $partyStateName = $party->state->stateName ?? $party->state->name ?? null;

    $partyAddr  = implode(', ', array_filter([$partyAddr1, $partyCity, $partyStateName], fn($x)=>$x!==null && trim($x)!==''));

    /* -----------------  Line items  ----------------- */
     $details = InvoiceDetail::with('service')
            ->where(['InvoiceID'=>$qId,'isDelete'=>0])
            ->get();

    $items = [];
    foreach ($details as $d) {
        $qty  = (float)($d->quantity ?? $d->qty ?? 0);
        $rate = (float)($d->rate ?? 0);
        $netAmount = (float)($d->netAmount ?? 0);
        $discount = (float)($d->discount ?? 0);
        $amount = (float)($d->totalAmount ?? 0);
        
        $items[] = [
            'name' => $clean($d->service->service_name ?? $d->service->service_name ?? $d->service->service_name ?? ''),
            'desc' => $clean($d->strDescription ?? $d->description ?? ''),
            'hsn'  => $clean($d->HSN ?? $d->hsn ?? ''),
            'gst'  => $clean($d->iGstPercentage ?? $d->iGstPercentage ?? ''),
            'qty'  => $qty,
            'rate' => $rate,
            'amount' => $amount,
            'netAmount' => $netAmount,
            'discount' => $discount,

        ];
    }

    /* -----------------  Terms  ----------------- */
   

    /* -----------------  Invoice meta  ----------------- */
    $discount     = (float)($Invoice->discount ?? 0);
    $gstRate      = (float)($Invoice->gstRate ?? 18);
    $isInterState = (bool)($Invoice->isInterState ?? 0);

    $InvoiceNumber = $clean($Invoice->iInvoiceNo ?? $Invoice->iInvoiceNo) ?? ('QTN-'.$qId);
  
    $InvoiceDate   = $fmtDate($Invoice->InvoiceDate ?? $Invoice->entryDate, now());
    $validTill       = $fmtDate($Invoice->valid_till ?? $Invoice->InvoiceValidity, now()->addDays(7));

    /* -----------------  Footer  ----------------- */
    $paymentTerms = $clean($Invoice->paymentTerms) ?? '-';
    $delivery     = $clean($Invoice->deliveryTerm) ?? '-';
    $modeOfDespatch = $clean($Invoice->modeOfDespatch) ?? '';
    $warranty     = $clean($Invoice->warranty) ?? '-';
        $extraTerms     = $clean($Invoice->strTermsCondition) ?? '';

    $bankName   = $get($company, ['bank_account_name','company_name']) ?? $companyName;
    $bankAcc    = $get($company, ['bank_account_no','account_no','acno']);
    $bankIfsc   = $get($company, ['bank_ifsc','ifsc']);
    $bankBranch = $get($company, ['bank_branch','branch']);



    /* -----------------  FINAL RETURN  ----------------- */
    return [
        'companyLogoUrl' => $companyLogoUrl,
        'companyName'    => $companyName,
        'companyAddress' => $companyAddr,
        'companyGstin'   => $companyGST,
        'companyPhone'   => $companyPhone,
        'companyEmail'   => $companyEmail,
        'companyState'   => $companyState,

        'InvoiceNumber'=> $InvoiceNumber,
        'InvoiceDate'  => $InvoiceDate,
        'validTill'      => $validTill,

        'partyName'    => $partyName,
        'partyAddress' => $partyAddr,
        'partyGstin'   => $partyGST,
        'partyPhone'   => $partyPhone,

        'items'        => $items,
        'discount'     => $discount,
        'gstRate'      => $gstRate,
        'isInterState' => $isInterState,

        'paymentTerms' => $paymentTerms,
        'delivery'     => $delivery,
        'modeOfDespatch' => $modeOfDespatch,
        'warranty'     => $warranty,

        'bankName'   => $bankName,
        'bankAccount'=> $bankAcc,
        'bankIfsc'   => $bankIfsc,
        'bankBranch' => $bankBranch,

        'termCondition' => $extraTerms,
    ];
}

}
