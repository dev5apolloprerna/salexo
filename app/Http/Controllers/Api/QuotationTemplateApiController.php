<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuotationTemplate;
use App\Models\Quotation;
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

class QuotationTemplateApiController extends Controller
{
    /**
     * GET /api/quotation-templates
     * List active templates + the company’s current default (guid)
     */
    public function index(Request $request)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $companyId = $user->company_id;

        $templates = QuotationTemplate::where('is_active', 1)
            ->orderByDesc('id')
            ->get(['id','guid','name','file_path','is_active','created_at']);

        $currentDefaultGuid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');


        return response()->json([
            'success' => true,
            'data'    => [
                'templates'          => $templates,
                'currentDefaultGuid' => $currentDefaultGuid,
            ],
        ]);
    }

    
    public function toggle(QuotationTemplate $template)
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

    public function setDefault(QuotationTemplate $template)
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

    public function destroy(QuotationTemplate $template)
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
     * GET /api/quotation-templates/{id}/preview?quotation_id=123&as=json|html
     * Returns rendered HTML (default) or JSON { html: "<...>" }
     */
    public function preview(Request $request, QuotationTemplate $template)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        $quotationId = $request->query('quotation_id');
        if (!$quotationId) {
            return response()->json([
                'success' => false,
                'message' => 'quotation_id is required'
            ], 422);
        }

        $quotation = Quotation::with('party','company')->findOrFail($quotationId);
        $data = $this->previewData($quotation);

        [$html, $isHtml] = $this->renderTemplate($template, $data, true);

        $as = strtolower((string) $request->query('as', 'html'));
        if ($as === 'json') {
            return response()->json(['success'=>true, 'html'=>$html]);
        }
        // default: raw HTML (for direct preview in browser/iframe)
        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * GET /api/quotation-templates/preview-default?quotation_id=123&as=json|html
     */
    public function previewDefault(Request $request)
    {
        $user = Auth::guard('employee_api')->user();
        if (!$user) return response()->json(['message'=>'Unauthorized'], 401);

        $companyId    = $user->company_id;
        $quotationId  = $request->query('quotation_id');

        if (!$quotationId) {
            return response()->json([
                'success' => false,
                'message' => 'quotation_id is required'
            ], 422);
        }

        $guid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');

        if (!$guid) {
            return response()->json([
                'success' => false,
                'message' => 'No default template set for your company.',
            ], 404);
        }

        $template = QuotationTemplate::where('guid', $guid)->firstOrFail();

        $quotation = Quotation::with('party','company')->findOrFail($quotationId);
        $data = $this->previewData($quotation);

        [$html, $isHtml] = $this->renderTemplate($template, $data, true);

        $as = strtolower((string) $request->query('as', 'html'));
        if ($as === 'json') {
            return response()->json(['success'=>true, 'html'=>$html]);
        }
        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Internal: render template file to HTML string.
     * If $returnString = true, always returns [$html, true].
     */
    protected function renderTemplate(QuotationTemplate $tpl, array $data, bool $returnString = false): array
    {
        $full = public_path($tpl->file_path);
        if (!file_exists($full)) {
            abort(422, 'Template file not found.');
        }

        $ext  = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $html = file_get_contents($full);

        // HTML/HTM files → either Blade or simple replace
        if ($ext === 'html' || $ext === 'htm') {
            if (preg_match('/@php|@foreach|@if|{{/', $html)) {
                $out = Blade::render($html, $data);
            } else {
                $out = $this->simpleReplace($html, $data);
            }
            return [$out, true];
        }

        // Fallback: php/blade file on disk
        // View::file returns a Response normally; we need the string:
        $rendered = View::file($full, $data)->render();
        return [$rendered, true];
    }

    /**
     * Simple {{ key }} replacement for plain HTML templates.
     */
    protected function simpleReplace(string $html, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', function ($m) use ($data) {
            $path = $m[1];
            $parts = explode('.', $path);
            $val = $data;
            foreach ($parts as $p) {
                if (is_array($val) && array_key_exists($p, $val)) {
                    $val = $val[$p];
                } else {
                    return ''; // missing → blank
                }
            }
            return e(is_scalar($val) ? (string)$val : json_encode($val));
        }, $html);
    }

    /**
     * Build the preview data array (same logic you had, trimmed a bit).
     */
    protected function previewData($quotation): array
    {
        if (!is_object($quotation)) {
            $quotation = Quotation::findOrFail($quotation);
        }

        $qId     = $quotation->quotationId ?? $quotation->id;
        $company = CompanyClient::with('state')->where('company_id', $quotation->iCompanyId)->first();
        $party   = Party::with('state')->where('partyId', $quotation->iPartyId)->first();

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
        $details = DB::table('quotationdetails')->where(['quotationID'=>$qId,'isDelete'=>0])->get();
        $items = [];
        foreach ($details as $d) {
            $qty  = (float)($d->quantity ?? $d->qty ?? 0);
            $rate = (float)($d->rate ?? 0);
            $items[] = [
                'name' => $clean($d->strProductName ?? $d->productName ?? $d->service_name ?? 'Item'),
                'desc' => $clean($d->strDescription ?? $d->description ?? ''),
                'hsn'  => $clean($d->uom ?? ''),
                'gst'  => $clean($d->iGstPercentage ?? ''),
                'qty'  => $qty,
                'rate' => $rate,
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

        // --- Quotation meta
        $discount       = (float)($quotation->discount ?? 0);
        $gstRate        = (float)($quotation->gstRate ?? 18);
        $isInterState   = (bool)($quotation->isInterState ?? 0);
        $quotationNumber= $clean($quotation->iQuotationNo) ?? ('QTN-'.$qId);
        $quotationDate  = $fmtDate($quotation->quotationDate ?? $quotation->entryDate, now());
        $validTill      = $fmtDate($quotation->valid_till ?? $quotation->quotationValidity, now()->addDays(7));

        // --- Footer
        $paymentTerms = $clean($quotation->paymentTerms) ?? '50% advance, balance on delivery';
        $delivery     = $clean($quotation->deliveryTerm) ?? 'Within 7–10 business days from PO';
        $modeOfDespatch = $clean($quotation->modeOfDespatch) ?? '';
        $warranty     = $clean($quotation->warranty) ?? '12 months from invoice date';

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

            'extraTerms' => $extraTerms,
        ];
    }
}
