<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\QuotationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Carbon\Carbon;

class QuotationTemplateController extends Controller
{
   public function setDefault(QuotationTemplate $template)
    {
        $this->authorizeCompany($template);

        $companyId = $template->company_id;

        // Update in company_master instead of templates table
        \DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->update(['companyTemplate' => $template->version]);

        return back()->with('success', 'Default template updated successfully.');
    }


    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $templates = QuotationTemplate::where('company_id', $companyId)
            ->orderBy('version')
            ->get();

        return view('company_client.quotation_template.designs_index', compact('templates'));
    }

    public function create()
    {
        return view('company_client.quotation_template.designs_create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'name'       => ['required','string','max:200'],
            'version'    => ['required','regex:/^v[1-9]\d*$/'], // v1, v2, ...
            'engine'     => ['required','in:blade,html'],
            'inline_html'=> ['nullable','string'],
            'file'       => ['nullable','file','mimetypes:text/html,text/plain,text/x-php,text/x-c']
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            // save to storage/app/quotation_templates/{company_id}/{version}.blade.php (or .html)
            $ext = $data['engine'] === 'blade' ? 'blade.php' : 'html';
            $filePath = "quotation_templates/company_{$companyId}/{$data['version']}.{$ext}";
            Storage::put($filePath, file_get_contents($request->file('file')->getRealPath()));
        }

        QuotationTemplate::updateOrCreate(
            ['company_id'=>$companyId, 'version'=>$data['version']],
            [
                'name'        => $data['name'],
                'engine'      => $data['engine'],
                'file_path'   => $filePath,
                'inline_html' => $data['inline_html'] ?? null,
                'is_active'   => true,
            ]
        );

        return redirect()->route('company.quotations.designs')
            ->with('success','Template saved.');
    }

    public function toggle(QuotationTemplate $template)
    {
        $this->authorizeCompany($template);
        $template->is_active = ! $template->is_active;
        $template->save();

        return back()->with('success', 'Template status updated.');
    }

    public function destroy(QuotationTemplate $template)
    {
        $this->authorizeCompany($template);
        if ($template->file_path) Storage::delete($template->file_path);
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }

    public function previewLatest(Request $request, string $version)
    {
        $companyId = auth()->user()->company_id;

        $tpl = QuotationTemplate::where([
                'company_id' => $companyId,
                'version'    => $version,
                'is_active'  => true,
            ])->firstOrFail();

        // find "latest" quotation for this company (adjust to your schema)
        $quotation = \DB::table('quotation')
            ->where(['iCompanyId'=>$companyId, 'isDelete'=>0])
            ->orderByDesc('quotationId')
            ->first();

        $data = $this->previewData($quotation, $companyId);

        return $this->renderTemplate($tpl, $data);
    }

    public function previewByTemplate(QuotationTemplate $template)
    {
        $this->authorizeCompany($template);
        $companyId = auth()->user()->company_id;

        // same fake/real data
        $quotation = \DB::table('quotation')
            ->where(['iCompanyId'=>$companyId, 'isDelete'=>0])
            ->orderByDesc('quotationId')
            ->first();

        $data = $this->previewData($quotation, $companyId);

        return $this->renderTemplate($template, $data);
    }

    /* ----------------- helpers ----------------- */

    protected function authorizeCompany(QuotationTemplate $tpl): void
    {
        if ($tpl->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

   /* protected function previewData($quotation, int $companyId): array
    {
        // Build the dataset your template expects; include company/party/items, etc.
        // Minimal example:
        return [
            'company' => \DB::table('company_client_master')->where('company_id',$companyId)->first(),
            'quotation' => $quotation,
            'items' => \DB::table('quotationdetails')
                ->where(['quotationID'=>$quotation->quotationId ?? 0,'isDelete'=>0])
                ->get(),
            // any extra computed amounts here...
        ];
    }*/
protected function renderTemplate(QuotationTemplate $tpl, array $data)
{
    if ($tpl->engine === 'blade') {
        // If stored on disk: render from file
        if ($tpl->file_path && Storage::exists($tpl->file_path)) {
            $full = Storage::path($tpl->file_path);

            // Render a blade/php file by absolute path
            // Option A: return the View instance
            return View::file($full, $data);

            // Option B: if you prefer an explicit Response:
            // return response(View::file($full, $data)->render(), 200)
            //          ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        abort(422, 'Blade template not found.');
    } else {
        // Raw HTML engine
        $html = $tpl->inline_html;
        if (!$html && $tpl->file_path && Storage::exists($tpl->file_path)) {
            $html = Storage::get($tpl->file_path);
        }
        if (!$html) {
            abort(422, 'HTML template not found.');
        }

        // If you do any placeholder replacement, do it here...
        // $html = $this->simpleReplace($html, $data);

        return response($html, 200)
                ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}

    // Super-simple placeholder replacement: {{ key.path }}
    protected function simpleReplace(string $html, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', function($m) use ($data) {
            $path = explode('.', $m[1]);
            $val = $data;
            foreach ($path as $seg) {
                if (is_object($val) && isset($val->{$seg})) $val = $val->{$seg};
                elseif (is_array($val) && array_key_exists($seg, $val)) $val = $val[$seg];
                else { $val = ''; break; }
            }
            return e((string)$val);
        }, $html);
    }
      
    protected function previewData($quotation, int $companyId): array
    {
        // --- if not provided, use latest quotation for company ---
        if (!$quotation) {
            $quotation = DB::table('quotation')
                ->where(['iCompanyId' => $companyId, 'isDelete' => 0])
                ->orderByDesc('quotationId')
                ->first();
        }

        // If $quotation is an Eloquent model with relations, prefer those; else fetch.
        $qId = $quotation?->quotationId ?? $quotation?->id ?? null;

        // Fetch company / party robustly
        // 1) try Eloquent relations
        $company = $quotation?->company ?? null;
        $party   = $quotation?->party ?? null;

        // 2) fallback via DB by foreign keys used in your schema
        if (!$company) {
            $company = DB::table('company_client_master')
                ->where('company_id', $quotation?->iCompanyId ?? $quotation?->company_id ?? $companyId)
                ->first();
        }
        if (!$party) {
            $party = DB::table('party')
                ->where('partyId', $quotation?->iPartyId ?? $quotation?->party_id ?? null)
                ->first();
        }

        // -------------------- helpers --------------------
        $clean = function($val) {
            $v = is_string($val) ? trim($val) : (is_null($val) ? null : (string)$val);
            return $v === '' ? null : $v;
        };
        $firstPresent = function($obj, array $keys) use ($clean) {
            foreach ($keys as $k) {
                if (is_object($obj) && isset($obj->{$k})) {
                    $v = $clean($obj->{$k});
                    if ($v !== null) return $v;
                }
            }
            return null;
        };
        $safeDateDMY = function($val, $fallback = null) {
            if (!$val) return $fallback ? Carbon::parse($fallback)->format('d-m-Y') : '';
            try {
                // Support Y-m-d, d-m-Y, timestamps, Carbon, etc.
                $c = $val instanceof Carbon ? $val : Carbon::parse($val);
                return $c->format('d-m-Y');
            } catch (\Throwable $e) {
                return $fallback ? Carbon::parse($fallback)->format('d-m-Y') : '';
            }
        };
        $buildAddress = function($line1 = null, $city = null, $state = null, $pin = null) {
            $parts = array_filter([$line1, $city, $state, $pin], fn($x)=>$x && trim((string)$x) !== '');
            return implode(', ', $parts);
        };

        // -------------------- Company fields --------------------
        $companyState = $firstPresent($company, ['strState','state','stateName','name']);
        $companyCity  = $firstPresent($company, ['strCity','city','cityName']);
        $companyAddr1 = $firstPresent($company, ['strAddress','address','addr1','address1']);
        $companyPin   = $firstPresent($company, ['strPincode','pincode','pin','zip','zipcode']);
        $companyAddress = $buildAddress($companyAddr1, $companyCity, $companyState, $companyPin);

        // Company logo → base64 data URL (fallback to public/assets/images/favicon.png)
        $companyLogoUrl = null;
        $tryPath = null;
        if ($company && $clean($company->strLogo ?? null)) {
            $tryPath = public_path('CompanyLogo/' . $company->strLogo);
        }
        if (!$tryPath || !File::exists($tryPath)) {
            $tryPath = public_path('assets/images/favicon.png');
        }
        if (File::exists($tryPath)) {
            $ext  = pathinfo($tryPath, PATHINFO_EXTENSION) ?: 'png';
            $mime = strtolower($ext) === 'jpg' ? 'jpeg' : strtolower($ext);
            $companyLogoUrl = "data:image/{$mime};base64," . base64_encode(file_get_contents($tryPath));
        }

        // -------------------- Party fields --------------------
        $partyState = $firstPresent($party, ['strState','state','stateName','name']);
        $partyCity  = $firstPresent($party, ['strCity','city','cityName']);
        $partyAddr1 = $firstPresent($party, ['strAddress','address','addr1','address1']);
        $partyPin   = $firstPresent($party, ['strPincode','pincode','pin','zip','zipcode']);
        $partyAddress = $buildAddress($partyAddr1, $partyCity, $partyState, $partyPin);

        // -------------------- Items --------------------
        $details = collect();
        if ($qId) {
            // If you have a model QuotationDetail, you can use it; else DB:
            $details = DB::table('quotationdetails')
                ->where(['quotationID' => $qId, 'isDelete' => 0])
                ->get();
        }

        $items = [];
        foreach ($details as $d) {
            $items[] = [
                'name' => $clean($d->strProductName ?? $d->product_name ?? $d->productName ?? 'Item'),
                'desc' => $clean($d->strDescription ?? $d->description ?? null),
                'hsn'  => $clean($d->hsn_code ?? $d->HSN ?? $d->hsn ?? null),
                'qty'  => (float)($d->qty ?? $d->quantity ?? 0),
                'rate' => (float)($d->rate ?? $d->unit_price ?? 0),
            ];
        }

        // -------------------- Terms & misc --------------------
        $terms = DB::table('termcondition')
            ->where(['iStatus' => 1, 'isDelete' => 0])
            ->orderBy('termconditionId')
            ->pluck('description');
        $extraTerms = $terms->filter()->values()->all();

        // -------------------- Quotation meta --------------------
        $discount     = (float)($quotation?->discount ?? 0);
        $gstRate      = (float)($quotation?->gstRate ?? 18);
        $isInterState = (bool)($quotation?->isInterState ?? false);

        $quotationNumber = $clean($quotation?->strQuotationNo ?? $quotation?->iQuotationNo) ?? ('QTN-' . ($qId ?? 'NA'));
        $quotationDate   = $safeDateDMY($quotation?->quotationDate ?? $quotation?->quotation_date ?? $quotation?->entryDate, now());
        $validTill       = $safeDateDMY($quotation?->valid_till ?? $quotation?->quotationValidity, now()->addDays(7));

        // -------------------- Final payload for the template --------------------
        return [
            // header/company
            'companyLogoUrl' => $companyLogoUrl,
            'companyName'    => $clean($company?->strCompanyName ?? $company?->company_name) ?? 'Your Company Pvt. Ltd.',
            'companyAddress' => $companyAddress,
            'companyGstin'   => $clean($company?->GST ?? $company?->GST),
            'companyPhone'   => $clean($company?->strPhone ?? $company?->mobile),
            'companyEmail'   => $clean($company?->strEmail ?? $company?->email),
            'companyState'   => $companyState ?: '',

            // quotation meta
            'quotationNumber' => $quotationNumber,
            'quotationDate'   => $quotationDate,
            'validTill'       => $validTill,

            // party
            'partyName'    => $clean($party?->strPartyName ?? $party?->party_name) ?? 'Party',
            'partyAddress' => $partyAddress,
            'partyGstin'   => $clean($party?->strGST ?? $party?->strGST),
            'partyPhone'   => $clean($party?->iMobile ?? $party?->iMobile),

            // line items & commerce
            'items'        => $items,
            'discount'     => $discount,
            'gstRate'      => $gstRate,
            'isInterState' => $isInterState,

            // footer bits
            'paymentTerms' => $clean($quotation?->paymentTerms)  ?? '50% advance, balance on delivery',
            'delivery'     => $clean($quotation?->deliveryTerm)       ?? 'Within 7–10 business days from PO',
            'modeOfDespatch'     => $clean($quotation?->modeOfDespatch)       ?? '12 months from invoice date',
            'warranty'       => $quotation->warranty ?? '12 months from invoice date',
            'bankName'     => $clean($company?->bank_account_name ?? $company?->company_name) ?? 'Your Company Pvt. Ltd.',
            'extraTerms'   => $extraTerms,
        ];
    }

}
