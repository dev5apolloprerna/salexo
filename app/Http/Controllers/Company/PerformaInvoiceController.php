<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\CompanyClient;
use App\Models\Year;
use App\Models\Party;
use App\Models\State;
use App\Models\PerformaInvoice;
use App\Models\PerformaInvoiceDetail;
use App\Models\TermCondition;
use App\Models\InvoiceTemplate;
use App\Models\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Barryvdh\DomPDF\Facade\Pdf as PDF; // barryvdh/laravel-dompdf

use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;

class PerformaInvoiceController extends Controller
{

   public function index(Request $request)
    {
        $user = Auth::user();

        $PartyName = $request->partyName;
        $fromDate  = $request->fromDate;     // dd-mm-YYYY or yyyy-mm-dd (we will convert)
        $toDate    = $request->toDate;       // dd-mm-YYYY or yyyy-mm-dd

        $Year    = Year::orderBy('year_id','DESC')->where(['iStatus'=>1,'isDelete'=>0])->get();
        $Company = CompanyClient::orderBy('company_id','DESC')->where(['iStatus'=>1,'isDeleted'=>0])->get();
        $Party   = Party::orderBy('partyId','DESC')->where(['party.iStatus'=>1,'party.isDelete'=>0])->get();
        $Product = Service::orderBy('service_id','DESC')->where(['iStatus'=>1,'isDelete'=>0])->get();

        $Invoice = PerformaInvoice::orderBy('performainvoiceId','DESC')
            ->where(['performa_invoice.iStatus'=>1,'performa_invoice.isDelete'=>0])

            // ✅ Filter by Party
            ->when($PartyName, function($q) use($PartyName) {
                return $q->where('performa_invoice.iPartyId', $PartyName);
            })

            // ✅ Filter From-Date
            ->when($fromDate, function($q) use($fromDate) {
                $from = date('Y-m-d', strtotime($fromDate));
                return $q->whereDate('performa_invoice.entryDate', '>=', $from);
            })

            // ✅ Filter To-Date
            ->when($toDate, function($q) use($toDate) {
                $to = date('Y-m-d', strtotime($toDate));
                return $q->whereDate('performa_invoice.entryDate', '<=', $to);
            });

            if($user->role_id == '3')
            {
                $Invoice->where(['created_by'=>$user->emp_id]);
            }

            $Invoice = $Invoice->join('company_client_master','performa_invoice.iCompanyId','=','company_client_master.company_id')
            ->join('party','performa_invoice.iPartyId','=','party.partyId')
            ->join('year','performa_invoice.iYearId','=','year.year_id')
            ->paginate(25);

        return view('company_client.performa_invoice.index', compact(
            'Year','Company','Party','Invoice','Product','PartyName','fromDate','toDate'
        ));
    }

    public function getNextInvoiceNo()
    {
        // Get last saved value, e.g. "0012/24-25" or "0012"
        $last = PerformaInvoice::orderByDesc('performainvoiceId')->value('iPerformaInvoiceNo');

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

            return response()->json($next . '/' . $year);
    }



    public function createview()
    {
        $user = Auth::user();

        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['company_id'=>$user->company_id,'iStatus' => 1, 'isDeleted' => 0])->first();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();
        $Invoice = PerformaInvoice::orderBy('performainvoiceId', 'DESC')->where(['performa_invoice.iStatus' => 1, 'performa_invoice.isDelete' => 0])
            ->join('company_client_master', 'performa_invoice.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'performa_invoice.iPartyId', '=', 'party.partyId')
            ->join('year', 'performa_invoice.iYearId', '=', 'year.year_id')
            ->get();

        return view('company_client.performa_invoice.add', compact('Year', 'Company', 'Party', 'Invoice'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        //dd($request);
        $Data = array(
            'iYearId' => $request->iYearId,
            'iPerformaInvoiceNo' => $request->iPerformaInvoiceNo,
            'iPartyId' => $request->iPartyId,
            'iCompanyId' => $user->company_id,
            'invoiceValidity' => $request->invoiceValidity,
            'modeOfDespatch' => $request->modeOfDespatch,
            'deliveryTerm' => $request->deliveryTerm,
            'paymentTerms' => $request->paymentTerms,
            'entryDate' => date('Y-m-d', strtotime($request->entryDate)),
            'iGstType' => $request->iGstType ?? 0,
            'strTermsCondition' => $request->strTermsCondition,
            'created_by' => $user->emp_id
        );
        // dd($Data);
        $getId=DB::table('performa_invoice')->insertGetId($Data);
//dd($getId);
        return redirect()->route('performainvoicedetails.index',$getId)->with('success', 'Invoice Created Successfully.');
    }

    public function editview(Request $request, $Id)
    {
                        $user = Auth::user();

        $Data = PerformaInvoice::where([
            'iStatus' => 1, 'isDelete' => 0, 'performainvoiceId' => $Id
        ])->firstOrFail();
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['company_id'=>$user->company_id,'iStatus' => 1, 'isDeleted' => 0])->first();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();


        return view('company_client.performa_invoice.edit',compact('Data','Company','Party','Year'));
       
    }



    public function update(Request $request, $Id)
    {
        $user = Auth::user();

        $performainvoiceId = $Id;
        $Company = DB::table('invoice')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'performainvoiceId' => $Id])
            ->update([
                'iYearId' => $request->iYearId,
                'iPerformaInvoiceNo' => $request->iPerformaInvoiceNo,
                'iPartyId' => $request->iPartyId,
                'iCompanyId' => $user->company_id,
                'invoiceValidity' => $request->invoiceValidity,
                'modeOfDespatch' => $request->modeOfDespatch,
                'deliveryTerm' => $request->deliveryTerm,
                'paymentTerms' => $request->paymentTerms,
                'entryDate' => date('Y-m-d', strtotime($request->entryDate)),
                'iGstType' => $request->iGstType ?? 0,
                'strTermsCondition' => $request->strTermsCondition,
                'updated_by' => $user->emp_id
            ]);
    return redirect()
        ->route('performa_invoicedetails.index', ['getId' => $performainvoiceId])
        ->with('success', 'Invoice Updated Successfully.');

    }

    public function delete(Request $request, $Id)
    {
        DB::table('performa_invoice')->where(['iStatus' => 1, 'isDelete' => 0, 'performainvoiceId' => $request->invoice_id])->delete();

        return redirect()->route('performa_invoice.index')->with('success', 'Invoice Deleted Successfully!.');
    }

    public function showdetail(Request $request, $id)
    {
        $popupInvoice = PerformaInvoice::select('party.address1','company_client_master.company_name','company_client_master.Address','company_client_master.email','company_client_master.mobile','company_client_master.GST','company_client_master.plan_id','party.strPartyName','party.address2','party.address3','party.iMobile','party.strEmail','performa_invoice.iPerformaInvoiceNo','performa_invoice.entryDate','performa_invoice.iCompanyId','performa_invoice.invoiceValidity','performa_invoice.modeOfDespatch','performa_invoice.deliveryTerm','performa_invoice.paymentTerms','performa_invoice.iGstType','performa_invoice.strTermsCondition')
            ->orderBy('performainvoiceId', 'ASC')->where(['performa_invoice.iStatus' => 1, 'performa_invoice.isDelete' => 0, 'performa_invoice.performainvoiceId' => $id])
            ->join('company_client_master', 'performa_invoice.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'performa_invoice.iPartyId', '=', 'party.partyId')
            ->join('year', 'performa_invoice.iYearId', '=', 'year.year_id')
            ->first();
            // $path = ("https://performa_invoice.sanjay-sales.com/CompanyLogo/" . $popupInvoice->strLogo);
            $path = ("https://salexo.in/assets/images/logo.png");
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $pic = 'data:CompanyLogo/' . $type . ';base64,' . base64_encode(($data));


        $InvoiceDetail = PerformaInvoiceDetail::orderBy('performa_invoicedetailsId', 'ASC')->where(['performa_invoicedetails.iStatus' => 1, 'performa_invoicedetails.isDelete' => 0, 'performa_invoicedetails.performainvoiceId' => $id])->get();
        $TermCondition = TermCondition::orderBy('termconditionId', 'ASC')->where(['termcondition.iStatus' => 1, 'termcondition.isDelete' => 0, 'termcondition.companyID'=>$popupInvoice->iCompanyId])
            ->get();
        
        return view('company_client.performa_invoice.showdetails', compact('popupInvoice', 'InvoiceDetail', 'pic','TermCondition'));
    }

    public function detailPDF(Request $request, $id)
    {
        // 1) Load invoice with relations
        $invoice = PerformaInvoice::with(['company','party'])
            ->where(['iStatus' => 1, 'isDelete' => 0, 'performainvoiceId' => $id])
            ->firstOrFail();

        // 2) Resolve the default template for THIS invoice's company
        $tpl = $this->getDefaultTemplateForCompany($invoice->iCompanyId);

        // 3) Build payload for the template (your existing function)
        $data = $this->previewData($invoice);

        // (Optional) If you still need TermCondition per company in the HTML:
        $data['extraTerms'] = DB::table('termcondition')
            ->where(['iStatus' => 1, 'isDelete' => 0, 'companyID' => $invoice->iCompanyId])
            ->orderBy('termconditionId')
            ->pluck('description')
            ->filter()
            ->values()
            ->all();

        // 4) Render template to HTML (from /public path)
        $html = $this->renderTemplateToHtml($tpl, $data);

        // 5) Make the PDF and download
        $fileName = trim(($data['partyName'] ?? 'Party') . ' ' . ($data['invoiceNumber'] ?? 'QTN')) . '.pdf';

        $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ])->loadHTML($html);

        $pdf->setPaper('a4');

        /*return $pdf->download($fileName);*/
        return $pdf->stream($fileName);
    }
    protected function getDefaultTemplateForCompany(int $companyId): InvoiceTemplate
    {
        // Read GUID from company table
        $guid = DB::table('company_client_master')
            ->where('company_id', $companyId)
            ->value('companyTemplate');

        // Try: active template by GUID
        if ($guid) {
            $tpl = InvoiceTemplate::where('guid', $guid)
                ->where('is_active', 1)
                ->first();
            if ($tpl) return $tpl;
        }

        // Fallback: first active template marked default, else any active template
        $tpl = InvoiceTemplate::where('is_active', 1)
            ->where('is_default', 1 ?? 0)
            ->first();


        if (!$tpl) {
            $tpl = InvoiceTemplate::where('is_active', 1)->first();
        }

        if (!$tpl) {
            abort(422, 'No active invoice template found. Please upload or activate a template.');
        }

        return $tpl;
    }

    protected function renderTemplateToHtml(InvoiceTemplate $tpl, array $data): string
    {
        $full = public_path($tpl->file_path);
        if (!File::exists($full)) {
            abort(422, 'Template file not found: ' . $tpl->file_path);
        }

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));

        // If the template is a Blade/PHP file stored under public
        if ($ext === 'php' || str_ends_with($full, '.blade.php')) {
            return View::file($full, $data)->render();
        }

        // If it's an HTML file: render through Blade if it contains directives/placeholders
        if ($ext === 'html' || $ext === 'htm') {
            $raw = file_get_contents($full);
            // If HTML includes any Blade syntax, run it through Blade
            if (preg_match('/@php|@foreach|@if|{{\s*[\w\[\]\'"\.\-\>]+\s*}}/m', $raw)) {
                return Blade::render($raw, $data);
            }
            // Plain HTML (no Blade) – optionally do a minimal {{ key }} replace:
            // return $this->simpleReplace($raw, $data);
            return $raw;
        }

        abort(422, 'Unsupported template format: ' . $ext);
    }


    public function search(Request $request)
    {
        $CompanyName = $request->companyName;
        //dd($CompanyName);
        $ProductNameArr[] = $request->productName;
        $PartyName = $request->partyName;
        //dd($PartyName);

        $Product = Product::orderBy('productId', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Year = Year::orderBy('year_id', 'DESC')->where(['iStatus' => 1, 'isDelete' => 0])->get();
        $Company = CompanyClient::orderBy('company_id', 'DESC')->where(['iStatus' => 1, 'isDeleted' => 0])->get();
        $Party = Party::orderBy('partyId', 'DESC')->where(['party.iStatus' => 1, 'party.isDelete' => 0])->get();
        $Invoice = PerformaInvoice::orderBy('performainvoiceId', 'DESC')->where(['performa_invoice.iStatus' => 1, 'performa_invoice.isDelete' => 0])
            //->whereIn('performa_invoice.iPartyId', array(90, 91, 92, 93))
            ->when($request->partyName, fn ($query, $PartyName) => $query->where('party.strPartyName', 'like', '%' . $PartyName . '%'))
            ->when($request->companyName, fn ($query, $CompanyName) => $query->where('company_client_master.company_id',  $CompanyName ))
            ->when($ProductNameArr, fn ($query, $productName) => $query->WhereIn(
                'performa_invoice.performainvoiceId',
                function ($query) use ($productName) {
                    $query->select('performa_invoicedetails.performainvoiceId')
                        ->from(with(new InvoiceDetail)->getTable())
                        ->whereIn('productID', $productName);
                }
            ))
            ->join('company_client_master', 'performa_invoice.iCompanyId', '=', 'company_client_master.company_id')
            ->join('party', 'performa_invoice.iPartyId', '=', 'party.partyId')
            ->join('year', 'performa_invoice.iYearId', '=', 'year.year_id')
            ->paginate(25);
        //($Invoice);
        return view('company_client.performa_invoice.index', compact('Year', 'Company', 'Party','Product', 'Invoice'));
    }
    
    public function Mapping(Request $request)
    {
        //dd('hello');
        $Mapping = Party::where(['iStatus' => 1, 'isDelete' => 0])->whereIn( 'iCompanyId',[$request->company])->get();

        //dd($Mapping);
        $html = "";

        foreach ($Mapping as $mapping) {
            $html .= "<option value='" . $mapping->partyId . "'>" . $mapping->strPartyName . "</option>";
        }

        return $html;
    }
    
    public function termconditionFetch(Request $request)
    {
        $company = TermCondition::where(['iStatus' => 1, 'isDelete' => 0,  'companyID' => $request->fetchcompany])->get();
        return  json_encode($company);
    }

    public function copyInvoice(Request $request, $Id)
    {

        $user = Auth::user();


        $Invoice = PerformaInvoice::where(['iStatus' => 1, 'isDelete' => 0, 'performainvoiceId' => $Id])->first();


         // ✅ FETCH LAST Invoice NO like: 0012/24-25
        $last = PerformaInvoice::where('iCompanyId', $Invoice->iCompanyId)
            ->orderByDesc('performainvoiceId')
            ->value('iPerformaInvoiceNo');
    
        // ✅ Extract number & create next running no
        $n = 0;
        if (!empty($last)) {
            $left = explode('/', trim($last))[0];  // 0012
            $n = (int) preg_replace('/\D/', '', $left); // 12
        }
    
        $nextLeft = str_pad((string)($n + 1), 4, '0', STR_PAD_LEFT);
    
        // ✅ Get current financial year
        $year = Year::where(['iStatus'=>1,'isDelete'=>0])
            ->orderByDesc('year_id')
            ->value('strYear');
    
        if (!$year) {
            $year = now()->format('y') . '-' . now()->addYear()->format('y');
        }
    
        // ✅ Final Invoice no e.g. "0013/24-25"
        $nextInvoiceNo = $nextLeft . '/' . $year;
        

        //dd($Invoice);

        $Data = array(
            'iYearId' => $Invoice->iYearId,
            'iPerformaInvoiceNo' => $nextInvoiceNo,
            'iPartyId' => $Invoice->iPartyId,
            'iCompanyId' => $Invoice->iCompanyId,
            'invoiceValidity' => $Invoice->invoiceValidity,
            'modeOfDespatch' => $Invoice->modeOfDespatch,
            'deliveryTerm' => $Invoice->deliveryTerm,
            'paymentTerms' => $Invoice->paymentTerms,
            'entryDate' => date('Y-m-d', strtotime($Invoice->entryDate)),
            'iGstType' => $Invoice->iGstType ?? 0,
            'strTermsCondition' => $Invoice->strTermsCondition,
            'created_by' => $user->emp_id
        );
        //dd($Data);
        $getId = DB::table('performa_invoice')->insertGetId($Data);
        //dd($getId);

        $InvoiceDetail = PerformaInvoiceDetail::orderBy('performa_invoicedetailsId', 'ASC')->where(['performa_invoicedetails.iStatus' => 1, 'performa_invoicedetails.isDelete' => 0, 'performa_invoicedetails.performainvoiceId' => $Id])->get();
        //dd($InvoiceDetail);

        foreach ($InvoiceDetail as $detailcopy) {

            $Data = array(
                'productID' => $detailcopy->productID,
                'performainvoiceId' => $getId,
                'description' => $detailcopy->description,
                'uom' => $detailcopy->uom,
                'quantity' => $detailcopy->quantity,
                'rate' => $detailcopy->rate,
                'amount' => $detailcopy->amount,
                'discount' => $detailcopy->discount,
                'netAmount' => $detailcopy->netAmount,
                'iGstPercentage' => $detailcopy->iGstPercentage,
                'created_by' => $user->emp_id
            );

            DB::table('performa_invoicedetails')->insert($Data);
        }


        return back()->with('success', 'Invoice Copied Successfully.');
    }
     public function sendWhatsApp(Request $request, $id)
    {
        // 1) Validate phone (WhatsApp requires country code, no "+")
        $phone = preg_replace('/\D/', '', $request->input('phone'));
        if (!$phone) {
            return back()->with('error', 'Invalid phone number.');
        }
        $pdfUrl = route('performa_invoice.DetailPDF', $id, true);

        // 3) Prepare payload for Cloud API
        $token         = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'   => $phone,
            'type' => 'document',
            'document' => [
                // You can send by URL directly (no media upload step needed for documents)
                'link'     => $pdfUrl,
                'filename' => "Invoice-{$id}.pdf",
            ],
        ];

        $resp = Http::withToken($token)
            ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", $payload);

        if (!$resp->ok()) {
            // Helpful error reporting
            $err = $resp->json();
            report(new \Exception('WhatsApp send failed: ' . json_encode($err)));
            return back()->with('error', $err['error']['message'] ?? 'Failed to send WhatsApp message.');
        }

        return back()->with('success', 'Invoice sent on WhatsApp!');
    }
   protected function previewData($invoice): array
    {
       
        // If only ID passed instead of full object
        if (!is_object($invoice)) {
            $invoice = PerformaInvoice::findOrFail($invoice);
        }

        $qId = $invoice->performainvoiceId ?? $invoice->id;

        // Safely load company & party (no login, no company filter)
        $company =CompanyClient::with('state')->where('company_id', $invoice->iCompanyId)->first();

        $party = Party::with('state')->where('partyId', $invoice->iPartyId)->first();



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
        //$extraTerms  = $company->terms_condition ?? null;

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
         $details = PerformaInvoiceDetail::with('service')
            ->where(['performainvoiceId'=>$qId,'iStatus'=>1,'isDelete'=>0])
            ->get();

        $items = [];
        foreach ($details as $d) 
        {
            $qty  = (float)($d->quantity ?? $d->qty ?? 0);
            $rate = (float)($d->rate ?? 0);
             $netAmount = (float)($d->netAmount ?? 0);
            $discount = (float)($d->discount ?? 0);
            $amount = (float)($d->totalAmount ?? 0);
            
            $items[] = [
                'name' => $clean($d->service->service_name ?? $d->service->service_name ?? $d->service->service_name ?? ''),
                'desc' => $clean($d->strDescription ?? $d->description ?? ''),
                'hsn'  => $clean($d->uom ?? $d->uom ?? '-'),
                'gst'  => $clean($d->iGstPercentage ?? $d->iGstPercentage ?? ''),
                'qty'  => $qty,
                'rate' => $rate,
                'amount' => $amount,
                'netAmount' => $netAmount,
                'discount' => $discount,

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
        $discount     = (float)($invoice->discount ?? 0);
        $gstRate      = (float)($invoice->gstRate ?? 18);
        $isInterState = (bool)($invoice->isInterState ?? 0);

        $invoiceNumber = $clean($invoice->iPerformaInvoiceNo ?? $invoice->iPerformaInvoiceNo) ?? ('QTN-'.$qId);
      
        $invoiceDate   = $fmtDate($invoice->invoiceDate ?? $invoice->entryDate, now());
        $validTill       = $fmtDate($invoice->valid_till ?? $invoice->invoiceValidity, now()->addDays(7));

        /* -----------------  Footer  ----------------- */
        $paymentTerms = $clean($invoice->paymentTerms) ?? '-';
        $delivery     = $clean($invoice->deliveryTerm) ?? '-';
        $modeOfDespatch = $clean($invoice->modeOfDespatch) ?? '-';
        $warranty     = $clean($invoice->warranty) ?? '-';
        $extraTerms     = $clean($invoice->strTermsCondition) ?? '-';

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

            'invoiceNumber'=> $invoiceNumber,
            'invoiceDate'  => $invoiceDate,
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