<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YearController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim($request->get('q',''));
        $status  = $request->get('status', '');
        $perPage = (int)($request->get('pp', 10));

        $list = Year::where('isDelete', 0)
            ->when($q !== '', fn($w)=>$w->where('strYear', 'like', "%{$q}%"))
            ->when($status !== '', fn($w)=>$w->where('iStatus', (int)$status))
            ->orderByDesc('year_id')
            ->paginate($perPage)
            ->appends($request->query());

        return view('company_client.year.index', compact('list','q','status','perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'strYear' => ['required','max:12'],
            'iStatus' => ['nullable','in:0,1'],
        ]);

        Year::create([
            'strYear'  => $request->strYear,
            'iStatus'  => (int)$request->input('iStatus', 1),
            'isDelete' => 0,
            'strIP'    => $request->ip(),
        ]);

        return back()->with('success', 'Year added.');
    }

    public function update(Request $request, Year $year)
    {
        if ($year->isDelete) return back()->with('error', 'Record deleted.');

        $request->validate([
            'strYear' => ['required','max:12'],
            // 'iStatus' => ['nullable','in:0,1'],
        ]);

        $year->update([
            'strYear' => $request->strYear,
            // 'iStatus' => (int)$request->input('iStatus', 1),
            'strIP'   => $request->ip(),
        ]);

        return back()->with('success', 'Year updated.');
    }

    public function destroy(Year $year)
    {
        if ($year->isDelete) return back()->with('error', 'Already deleted.');
        $year->isDelete = 1;
        $year->save();

        return back()->with('success', 'Year deleted.');
    }

    public function toggleStatus(Year $year)
    {
        if ($year->isDelete) return back()->with('error', 'Record deleted.');
        $year->iStatus = $year->iStatus ? 0 : 1;
        $year->save();

        return back()->with('success', 'Status updated.');
    }
}
