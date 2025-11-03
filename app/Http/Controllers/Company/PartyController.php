<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;

use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\State;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $companyId = (int)$request->get('company_id', 0);
        $q         = $request->get('q');
        $editId    = (int)$request->get('edit', 0);

        $list = Party::with('company','state')
            ->when($companyId > 0, fn($x) => $x->where('iCompanyId', $companyId))
            ->where('isDelete', 0)
            ->when($q, function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('strPartyName', 'like', "%$q%")
                      ->orWhere('strGST', 'like', "%$q%")
                      ->orWhere('strEmail', 'like', "%$q%")
                      ->orWhere('iMobile', 'like', "%$q%");
                });
            })
            ->orderBy('strPartyName')
            ->paginate(15)
            ->withQueryString();

        $editing = $editId ? Party::where('isDelete',0)->findOrFail($editId) : null;
        return view('company_client.party.index', [
            'list'       => $list,
            'company_id' => $companyId,
            'q'          => $q,
            'editing'    => $editing,
        ]);
    }
    public function lookupByMobile(Request $request)
    {
        // sanitize: keep digits only
        $mobile = preg_replace('/\D+/', '', (string) $request->query('mobile', ''));

        if (strlen($mobile) < 6) {
            return response()->json([
                'ok' => false,
                'message' => 'Please provide a valid mobile number.',
            ], 422);
        }

        // Optional: if your employee belongs to a company and you want to scope by iCustomerId
        $companyId = (int) ($request->query('company_id', 0)); // or derive from Auth if needed

        $query = DB::table('lead_master')
            ->where('isDelete', 0)
            ->where('mobile', $mobile);

        if ($companyId > 0) {
            $query->where('iCustomerId', $companyId);
        }

        // Get the latest matching lead
        $lead = $query->orderByDesc('lead_id')->first();

        if (!$lead) {
            return response()->json([
                'ok' => false,
                'message' => 'No lead found for this mobile.',
            ], 404);
        }

        // Map lead fields -> your Party create form fields.
        // Adjust keys BELOW to match your actual Party columns / input names.
        $prefill = [
            // common Party fields (rename if yours differ)
            'strPartyName'      => $lead->company_name ?: ($lead->customer_name ?: ''), // company or person
            'strContactPerson'  => $lead->customer_name ?: '',
            'iMobile'      => $lead->mobile ?: '',
            'strEmail'        => $lead->email ?: '',
            'address1'        => $lead->address ?: '',
            'strGST'          => $lead->GST_No ?: '',
            'remarks'           => $lead->remarks ?: '',
            // add more mappings if you keep them in Party:
            // 'product_service_id' => $lead->product_service_id,
            // 'amount'             => $lead->amount,
        ];

        return response()->json([
            'ok'   => true,
            'lead' => $lead,
            'data' => $prefill,
        ]);
    }
    public function create()
    {
          $state=State::where(['iStatus'=>1,'isDelete'=>0])->get();
      
        return view('company_client.party.add',compact('state'));
    }

    public function edit(Party $party) // or findOrFail($id) if not using model binding
    {
         $state=State::where(['iStatus'=>1,'isDelete'=>0])->get();
       
        return view('company_client.party.edit', compact('party','state'));
    }


    public function store(StorePartyRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['iCompanyId']    = $user->company_id;
        $data['strIP']    = $request->ip();

        Party::create($data);

        return redirect()->route('party.index')->with('success', 'Party created successfully.');
    }

    public function update(UpdatePartyRequest $request, int $party)
    {
        $user = Auth::user();

        $row = Party::findOrFail($party);
        $data = $request->validated();
        $data['strIP'] = $request->ip();
        $data['iCompanyId']    = $user->company_id;

        $row->update($data);

        // Return to the same page in add mode (no ?edit)
        return redirect()->route('party.index')->with('success', 'Party updated successfully.');
    }

    public function destroy(Request $request)
    {
        Party::findOrFail($id)->delete();
        return back()->with('success', 'Party deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = collect($request->input('ids', []))->map('intval')->filter()->all();
        if (!$ids) return back()->with('error', 'No records selected.');
        Party::whereIn('partyId', $ids)->delete();
        return back()->with('success', 'Selected parties deleted.');
    }

    public function toggleStatus(int $party)
    {
        $row = Party::findOrFail($party);
        $row->update(['iStatus' => $row->iStatus ? 0 : 1]);
        return back()->with('success', 'Status updated.');
    }
}
