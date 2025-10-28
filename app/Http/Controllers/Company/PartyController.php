<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;

use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $companyId = (int)$request->get('company_id', 0);
        $q         = $request->get('q');
        $editId    = (int)$request->get('edit', 0);

        $list = Party::with('company')
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

    public function destroy(int $party)
    {
        Party::findOrFail($party)->delete();
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
