<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use App\Models\QuotationTemplate;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\CompanyClient;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

    use Illuminate\Support\Facades\Blade;

class QuotationTemplateController extends Controller
{
   public function index(Request $request)
    {
        // Logged-in company id (guard: web_employees)
        $companyId = auth()->user()->company_id;

        // All templates (global list). If you keep per-company templates, filter here instead.
        $templates = QuotationTemplate::where(['is_active'=>1])->orderByDesc('id')->get();

        // Read this company’s default (we store the template GUID here; switch to 'id' if you prefer)
        $currentDefaultGuid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');

        return view('company_client.quotation_template.designs_index', compact('templates','currentDefaultGuid'));
    }

    public function create()
    {
        return view('company_client.quotation_template.designs_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:200',
            'file' => 'required|file|mimes:php,html,htm'
        ]);

        $guid = (string) Str::uuid();

        $baseDir = public_path("quotation_templates/{$guid}");
        if (!File::isDirectory($baseDir)) {
            File::makeDirectory($baseDir, 0775, true);
        }

        $file    = $request->file('file');
        $name    = preg_replace('/[^a-zA-Z0-9\.\-_]/','_', $file->getClientOriginalName());
        $path    = $baseDir . '/' . $name;

        $file->move($baseDir, $name);

        $relativePath = "quotation_templates/{$guid}/{$name}";

        QuotationTemplate::create([
            'guid'       => $guid,
            'name'       => $request->name,
            'file_path'  => $relativePath,
            'is_active'  => 1,
            'is_default' => 0,
        ]);

        return redirect()->route('quotations.templates')
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

        // Value you want to store as default for this company:
        // If you prefer ID, change to (string)$template->id and keep it consistent everywhere.
        $valueToStore = (string) $template->guid;

        DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->update(['companyTemplate' => $valueToStore]);

        return back()->with('success', 'Default template set for your company.');
    }


    public function destroy(QuotationTemplate $template)
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
    public function preview(QuotationTemplate $template, $quotationId)
    {
        $quotation = Quotation::with('party','company')
            ->findOrFail($quotationId);
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

            // Reuse your existing previewLatest($guid)
            return $this->previewLatest(request(), $guid);
        }


    protected function renderTemplate(QuotationTemplate $tpl, array $data)
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


    protected function previewData($quotation): array
{
   
    // If only ID passed instead of full object
    if (!is_object($quotation)) {
        $quotation = Quotation::findOrFail($quotation);
    }

    $qId = $quotation->quotationId ?? $quotation->id;

    // Safely load company & party (no login, no company filter)
    $company =CompanyClient::with('state')->where('company_id', $quotation->iCompanyId)->first();

    $party = Party::with('state')->where('partyId', $quotation->iPartyId)->first();



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
     $details = QuotationDetail::with('service')
            ->where(['quotationID'=>$qId,'isDelete'=>0])
            ->get();

    $items = [];
    foreach ($details as $d) {
        $qty  = (float)($d->quantity ?? $d->qty ?? 0);
        $rate = (float)($d->rate ?? 0);
        $items[] = [
            'name' => $clean($d->service->service_name ?? $d->service->service_name ?? $d->service->service_name ?? ''),
            'desc' => $clean($d->strDescription ?? $d->description ?? ''),
            'hsn'  => $clean($d->HSN ?? $d->hsn ?? ''),
            'gst'  => $clean($d->iGstPercentage ?? $d->iGstPercentage ?? ''),
            'qty'  => $qty,
            'rate' => $rate,
        ];
    }

    /* -----------------  Terms  ----------------- */
   

    /* -----------------  Quotation meta  ----------------- */
    $discount     = (float)($quotation->discount ?? 0);
    $gstRate      = (float)($quotation->gstRate ?? 18);
    $isInterState = (bool)($quotation->isInterState ?? 0);

    $quotationNumber = $clean($quotation->iQuotationNo ?? $quotation->iQuotationNo) ?? ('QTN-'.$qId);
  
    $quotationDate   = $fmtDate($quotation->quotationDate ?? $quotation->entryDate, now());
    $validTill       = $fmtDate($quotation->valid_till ?? $quotation->quotationValidity, now()->addDays(7));

    /* -----------------  Footer  ----------------- */
    $paymentTerms = $clean($quotation->paymentTerms) ?? '-';
    $delivery     = $clean($quotation->deliveryTerm) ?? '-';
    $modeOfDespatch = $clean($quotation->modeOfDespatch) ?? '';
    $warranty     = $clean($quotation->warranty) ?? '-';
        $extraTerms     = $clean($quotation->strTermsCondition) ?? '';

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

        'quotationNumber'=> $quotationNumber,
        'quotationDate'  => $quotationDate,
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
