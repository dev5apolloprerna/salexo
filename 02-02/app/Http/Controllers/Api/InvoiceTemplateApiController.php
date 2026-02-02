<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceTemplate;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\CompanyClient;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Validation\ValidationException;
 
class InvoiceTemplateApiController extends Controller
{
    /**
     * GET /api/Invoice-templates
     * List active templates + the company’s current default (guid)
     */
    public function index(Request $request)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $companyId = $user->company_id;

        $templates = InvoiceTemplate::where('is_active', 1)
            ->orderByDesc('id')
            ->get(['id','guid','name','file_path','is_active','created_at']);

        $currentDefaultGuid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('invoice_template');


        return response()->json([
            'success' => true,
            'data'    => [
                'templates'          => $templates,
                'currentDefaultGuid' => $currentDefaultGuid,
            ],
        ]);
    }

    
    public function toggle(InvoiceTemplate $template)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        $template->is_active = !$template->is_active;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Template status changed.',
            'data'    => ['id'=>$template->id, 'is_active'=>$template->is_active],
        ]);
    }

    public function setDefault(InvoiceTemplate $template)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        $companyId = $user->company_id;
        $valueToStore = (string) $template->guid; // keep using GUID

        DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->update(['companyTemplate' => $valueToStore]);

        return response()->json([
            'success' => true,
            'message' => 'Default template set for your company.',
            'data'    => ['guid' => $valueToStore],
        ]);
    }

    public function destroy(InvoiceTemplate $template)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        if ($template->file_path) {
            $abs = public_path($template->file_path);
            if (File::exists($abs)) File::delete($abs);
            // delete folder if present
            $folder = dirname(public_path($template->file_path));
            if (File::exists($folder)) @File::deleteDirectory($folder);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted.',
        ]);
    }

    /**
     * GET /api/Invoice-templates/{id}/preview?Invoice_id=123&as=json|html
     * Returns rendered HTML (default) or JSON { html: "<...>" }
     */
    public function preview(InvoiceTemplate $template, Request $request)
{
    try {
        $Invoice = Invoice::with(['company','party'])
            ->where(['iStatus'=>1,'isDelete'=>0])->orderByDesc('InvoiceId')
            ->first();

            $InvoiceId=$Invoice->InvoiceId;

        if (!file_exists(public_path($template->file_path))) {
            return response()->json([
                'success'=>false,
                'message'=>'Template file not found'
            ],404);
        }

        // ✅ Build data → HTML
        $data = $this->previewData($Invoice);
        $html = $this->renderTemplateToHtml($template, $data);

        // ✅ Generate PDF
        $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled'=>true,
                'isRemoteEnabled'=>true
            ])
            ->loadHTML($html)
            ->setPaper('a4');

        // ✅ Save under public_html/uploads/invoice_pdf/
        $root = base_path('../public_html/uploads/invoice_pdf/');
        if (!File::isDirectory($root)) {
            File::makeDirectory($root, 0775, true);
        }

        $fileName = 'preview_'.$InvoiceId.'_'.time().'.pdf';
        $absPath  = $root.$fileName;

        file_put_contents($absPath, $pdf->output());

        // ✅ Public URL
        $url = url('uploads/invoice_pdf/'.$fileName);

        return response()->json([
            'success'=>true,
            'message'=>'Preview PDF generated.',
            'pdf_url'=>$url,
            'template_guid'=>$template->guid
        ],200);

    } catch (\Throwable $th) {
        return response()->json([
            'success'=>false,
            'message'=>$th->getMessage()
        ],500);
    }
}


    /**
     * GET /api/Invoice-templates/preview-default?Invoice_id=123&as=json|html
     */
   public function previewDefault(Request $request)
{
    try {
        $InvoiceId = $request->Invoice_id;
        if (!$InvoiceId) {
            return response()->json([
                'success'=>false,
                'message'=>'Invoice_id is required'
            ],422);
        }

        $Invoice = Invoice::where(['iStatus'=>1,'isDelete'=>0,'InvoiceId'=>$InvoiceId])
            ->firstOrFail();

        $guid = DB::table('company_client_master')
            ->where('company_id',$Invoice->iCompanyId)
            ->value('companyTemplate');

        if (!$guid) {
            return response()->json([
                'success'=>false,
                'message'=>'Default template not set for your company.'
            ],404);
        }

        $template = InvoiceTemplate::where('guid',$guid)->first();
        if (!$template || !file_exists(public_path($template->file_path))) {
            return response()->json([
                'success'=>false,
                'message'=>'Default template file missing.'
            ],404);
        }

        $data = $this->previewData($Invoice);
        $html = $this->renderTemplateToHtml($template, $data);

        $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled'=>true,
                'isRemoteEnabled'=>true
            ])
            ->loadHTML($html)
            ->setPaper('a4');

        $root = base_path('../public_html/uploads/invoice_pdf/');
        if (!File::isDirectory($root)) {
            File::makeDirectory($root, 0775, true);
        }

        $fileName = 'default_preview_'.$InvoiceId.'_'.time().'.pdf';
        $absPath  = $root.$fileName;

        file_put_contents($absPath, $pdf->output());
        $url = url('uploads/invoice_pdf/'.$fileName);

        return response()->json([
            'success'=>true,
            'message'=>'Default template preview generated.',
            'pdf_url'=>$url,
            'template_guid'=>$guid
        ],200);

    } catch (\Throwable $th) {
        return response()->json([
            'success'=>false,
            'message'=>$th->getMessage()
        ],500);
    }
}

    /**
     * Internal: render template file to HTML string.
     * If $returnString = true, always returns [$html, true].
     */

protected function renderTemplateToHtml($template, array $data): string
{
    $full = public_path($template->file_path);
    if (!is_file($full)) {
        throw new \RuntimeException("Template file not found: {$template->file_path}");
    }

    $ext  = strtolower(pathinfo($full, PATHINFO_EXTENSION));
    $html = file_get_contents($full);

    // .html /.htm files
    if (in_array($ext, ['html', 'htm'])) {
        // If the HTML contains Blade syntax, compile & render it
        if (preg_match('/@php|@foreach|@if|@switch|@for|@while|{!!|{{/', $html)) {
            return Blade::render($html, $data);
        }
        // Otherwise do a light {{ key }} replacement
        return $this->simpleReplace($html, $data);
    }

    // .php or .blade.php on disk → render via View::file
    return View::file($full, $data)->render();
}

/**
 * Lightweight {{ key }} replacement for plain HTML templates (no Blade logic).
 * Supports dot paths like {{ items.0.name }}.
 */
protected function simpleReplace(string $html, array $data): string
{
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', function ($m) use ($data) {
        $val = $data;
        foreach (explode('.', $m[1]) as $p) {
            if (is_array($val) && array_key_exists($p, $val)) {
                $val = $val[$p];
            } else {
                return '';
            }
        }
        return e(is_scalar($val) ? (string)$val : json_encode($val));
    }, $html);
}

    /**
     * Build the preview data array (same logic you had, trimmed a bit).
     */
    protected function previewData($Invoice): array
    {
        if (!is_object($Invoice)) {
            $Invoice = Invoice::findOrFail($Invoice);
        }

        $qId     = $Invoice->InvoiceId ?? $Invoice->id;
        $company = CompanyClient::with('state')->where('company_id', $Invoice->iCompanyId)->first();
        $party   = Party::with('state')->where('partyId', $Invoice->iPartyId)->first();

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

        // --- Company fields
        $companyName  = $company->company_name ?? 'Your Company Pvt. Ltd.';
        $companyPhone = $company->mobile ?? null;
        $companyEmail = $company->email ?? null;
        $companyGST   = $company->GST ?? '-';
        $companyState = $company->state->stateName ?? null;
        $companyCity  = $company->city ?? null;
        $companyAddr1 = $company->Address ?? null;
        $companyPin   = $company->pincode ?? null;
        $companyAddr  = $address($companyAddr1, $companyCity, $companyState, $companyPin);

        // Inline logo (base64)
        $root = base_path('../public_html/'); // aligns with your hosting
        $rel  = data_get($company, 'company_logo');
        $rel  = $rel ? (str_contains($rel, '/') ? $rel : "uploads/company/$rel") : 'assets/images/favicon.png';
        $path = $root . ltrim($rel, '/');
        if (!file_exists($path)) $path = $root . 'assets/images/favicon.png';
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'png');
        $mime = $ext === 'jpg' ? 'image/jpeg' : "image/$ext";
        $companyLogoUrl = 'data:' . $mime . ';base64,' . base64_encode(@file_get_contents($path) ?: '');

        // --- Party
        $partyName  = $party->strPartyName ?? 'Party';
        $partyPhone = $party->iMobile ?? null;
        $partyGST   = $party->strGST ?? null;
        $partyCity  = $party->city ?? null;
        $partyAddr1 = $party->address1 ?? null;
        $partyStateName = $party->state->stateName ?? $party->state->name ?? null;
        $partyAddr = implode(', ', array_filter([$partyAddr1, $partyCity, $partyStateName], fn($x)=>$x!==null && trim($x)!==''));

        // --- Line items
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
                'hsn'  => $clean($d->uom ?? ''),
                'gst'  => $clean($d->iGstPercentage ?? ''),
                'qty'  => $qty,
                'rate' => $rate,
                'amount' => $amount,
                'netAmount' => $netAmount,
                'discount' => $discount,

            ];
        }

        // --- Terms
        $extraTerms = DB::table('termcondition')
            ->where(['iStatus'=>1,'isDelete'=>0])
            ->orderBy('termconditionId')
            ->pluck('description')
            ->filter()
            ->values()
            ->all();

        // --- Invoice meta
        $discount       = (float)($Invoice->discount ?? 0);
        $gstRate        = (float)($Invoice->gstRate ?? 18);
        $isInterState   = (bool)($Invoice->isInterState ?? 0);
        $InvoiceNumber= $clean($Invoice->iInvoiceNo) ?? ('Invoice-'.$qId);
        $InvoiceDate  = $fmtDate($Invoice->InvoiceDate ?? $Invoice->entryDate, now());
        $validTill      = $fmtDate($Invoice->valid_till ?? $Invoice->InvoiceValidity, now()->addDays(7));

        // --- Footer
        $paymentTerms = $clean($Invoice->paymentTerms) ?? '50% advance, balance on delivery';
        $delivery     = $clean($Invoice->deliveryTerm) ?? 'Within 7–10 business days from PO';
        $modeOfDespatch = $clean($Invoice->modeOfDespatch) ?? '';
        $warranty     = $clean($Invoice->warranty) ?? '12 months from invoice date';

        $bankName   = $get($company, ['bank_account_name','company_name']) ?? $companyName;
        $bankAcc    = $get($company, ['bank_account_no','account_no','acno']);
        $bankIfsc   = $get($company, ['bank_ifsc','ifsc']);
        $bankBranch = $get($company, ['bank_branch','branch']);

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

            'extraTerms' => $extraTerms,
        ];
    }
}
