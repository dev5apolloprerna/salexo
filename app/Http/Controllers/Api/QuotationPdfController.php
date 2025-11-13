<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Quotation;
use App\Models\QuotationTemplate;
use App\Models\QuotationDetail;
use App\Models\CompanyClient;
use App\Models\Party;


use PDF; // Barryvdh\DomPDF

class QuotationPdfController extends Controller
{

    public function quotationPdfLink(Request $request, int $id)
    {
        try {
            // 1) Load Quotation + relations
            $quotation = Quotation::with(['company', 'party'])
                ->where([
                    'iStatus'     => 1,
                    'isDelete'    => 0,
                    'quotationId' => $id,
                ])
                ->firstOrFail();

            // 2) Resolve default template for this company
            $template = $this->getDefaultTemplateForCompany($quotation->iCompanyId);
            if (!$template || !$template->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Default template not configured or file missing.',
                ], 404);
            }

            // 3) Build payload (reuse your existing previewData)
            $data = $this->previewData($quotation);

            // Optional: company-specific terms if your template shows them
            $data['extraTerms'] = DB::table('termcondition')
                ->where([
                    'iStatus'   => 1,
                    'isDelete'  => 0,
                    'companyID' => $quotation->iCompanyId,
                ])
                ->orderBy('termconditionId')
                ->pluck('description')
                ->filter()
                ->values()
                ->all();

            // 4) Render the template file to HTML (supports Blade-in-HTML)
            $html = $this->renderTemplateToHtml($template, $data);

            // 5) Generate the PDF
            $pdf = PDF::setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => true,
                ])
                ->loadHTML($html)
                ->setPaper('a4');

            // 6) Save under /public/uploads/quotation_pdf/
            $safeParty = Str::slug($data['partyName'] ?? 'party');
            $safeNo    = Str::slug($data['quotationNumber'] ?? ('QTN-' . $id));
            $fileName  = "{$safeParty}-{$safeNo}.pdf";

            // use standard public_path so URL and path match
            $dir = public_path('uploads/quotation_pdf');

            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true);
            }

            $absPath = $dir . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($absPath, $pdf->output());

            // 7) Full public URL to the PDF
            // e.g. https://your-domain.com/uploads/quotation_pdf/party-qtn-123.pdf
            $url = url('uploads/quotation_pdf/' . $fileName);

            return response()->json([
                'success' => true,
                'message' => 'Quotation PDF generated.',
                'pdfurl'  => $url, // full PDF URL
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation not found.',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


/**
 * Fetch default template for a company using company_client_master.companyTemplate (GUID).
 */
protected function getDefaultTemplateForCompany(int $companyId): ?QuotationTemplate
{
    $guid = DB::table('company_client_master')
        ->where('company_id', $companyId)
        ->value('companyTemplate');

    if (!$guid) return null;

    $tpl = QuotationTemplate::where('guid', $guid)->first();
    if (!$tpl) return null;

    // Ensure file exists under public/
    $full = public_path($tpl->file_path);
    return file_exists($full) ? $tpl : null;
}

/**
 * Render a template file from /public to HTML string (Blade-in-HTML supported).
 */
protected function renderTemplateToHtml(QuotationTemplate $tpl, array $data): string
{
    $full = public_path($tpl->file_path);
    if (!file_exists($full)) {
        throw new \RuntimeException('Template file not found on disk.');
    }

    $ext  = strtolower(pathinfo($full, PATHINFO_EXTENSION));
    $html = file_get_contents($full);

    if (in_array($ext, ['html','htm'])) {
        // If HTML contains Blade directives, compile + render
        if (preg_match('/@php|@foreach|@if|{{/', $html)) {
            return \Blade::render($html, $data);
        }
        // Simple replacement for {{ key }} tokens
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', function ($m) use ($data) {
            $val = $data;
            foreach (explode('.', $m[1]) as $p) {
                if (is_array($val) && array_key_exists($p, $val)) $val = $val[$p]; else return '';
            }
            return e(is_scalar($val) ? (string)$val : json_encode($val));
        }, $html);
    }

    // php/blade files on disk → render via View::file
    return \View::file($full, $data)->render();
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
        $root = base_path('../public_html/');

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
        

       /* if ($get($company, ['company_logo'])) {
            $path = public_path('CompanyLogo/'.$company->company_logo);
            if (!file_exists($path)) $path = public_path('assets/images/favicon.png');
        } else {
            $path = public_path('assets/images/favicon.png');
        }
        if (file_exists($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: 'png';
            $mime = $ext === 'jpg' ? 'jpeg' : $ext;
            $companyLogoUrl = "data:image/{$mime};base64,".base64_encode(file_get_contents($path));
        }*/

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
                'hsn'  => $clean($d->uom ?? $d->uom ?? '-'),
                'gst'  => $clean($d->iGstPercentage ?? $d->iGstPercentage ?? ''),
                'qty'  => $qty,
                'rate' => $rate,
            ];
        }

        /* -----------------  Terms  ----------------- */
        

        /*$extraTerms = \DB::table('termcondition')
            ->where(['iStatus'=>1,'isDelete'=>0])
            ->orderBy('termconditionId')
            ->pluck('description')
            ->filter()
            ->values()
            ->all();*/

        /* -----------------  Quotation meta  ----------------- */
        $discount     = (float)($quotation->discount ?? 0);
        $gstRate      = (float)($quotation->gstRate ?? 18);
        $isInterState = (bool)($quotation->isInterState ?? 0);

        $quotationNumber = $clean($quotation->iQuotationNo ?? $quotation->iQuotationNo) ?? ('QTN-'.$qId);
      
        $quotationDate   = $fmtDate($quotation->quotationDate ?? $quotation->entryDate, now());
        $validTill       = $fmtDate($quotation->valid_till ?? $quotation->quotationValidity, now()->addDays(7));

        /* -----------------  Footer  ----------------- */
        $paymentTerms = $clean($quotation->paymentTerms) ?? '50% advance, balance on delivery';
        $delivery     = $clean($quotation->deliveryTerm) ?? 'Within 7–10 business days from PO';
        $modeOfDespatch = $clean($quotation->modeOfDespatch) ?? '';
        $warranty     = $clean($quotation->warranty) ?? '12 months from invoice date';
        $warranty     = $clean($quotation->strTermsCondition) ?? '';

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