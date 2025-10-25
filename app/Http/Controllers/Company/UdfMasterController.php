<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadPipeline;
use App\Models\UdfMaster;
use Illuminate\Support\Facades\Auth;

class UdfMasterController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            $datas = UdfMaster::orderBy('id', 'desc')
                ->where(['company_id' => $user->company_id])
                ->paginate(config('app.per_page'));

            return view('company_client.udf.index', compact('datas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {

            $request->validate([
                'label' => 'required|string|max:255',
                'required' => 'required',
            ]);

            UdfMaster::create([
                'company_id' => auth()->user()->company_id,
                'label' => $request->label,
                'required' => $request->required,
                'created_at' => now(),
                'strIP' => $request->ip()
            ]);

            return redirect()->route('udf.index')->with('success', 'Udf created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {

            $data = UdfMaster::find($id);

            echo json_encode($data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {

            $request->validate([
                'label' => 'required|string|max:255',
                'required' => 'required',
            ]);

            $udf =  UdfMaster::where('id', $request->id)->first();

            $udf->update([
                'label' => $request->label,
                'required' => $request->required,
                'updated_at' => now(),
                'strIP' => $request->ip()
            ]);

            return redirect()->route('udf.index')->with('success', 'Udf updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $udf =  UdfMaster::where('id', $request->id)->first();
            $udf->delete();

            return redirect()->route('udf.index')->with('success', 'Udf deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
