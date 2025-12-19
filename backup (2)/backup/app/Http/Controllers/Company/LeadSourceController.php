<?php

namespace App\Http\Controllers\Company;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadSource;
use App\Repositories\LeadSource\LeadSourceRepository;
use App\Repositories\LeadSource\LeadSourceRepositoryInterface;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class LeadSourceController extends Controller
{
    protected $leadSource;

    public function __construct(LeadSourceRepositoryInterface $leadSource)
    {
        $this->leadSource = $leadSource;
    }

    public function index()
    {
        try{
        $user = Auth::user();
        $Leadsource = LeadSource::orderBy('lead_source_id','desc')->where(['company_id' =>$user->company_id ])->paginate(config('app.per_page'));
        return view('company_client.lead_source.index', compact('Leadsource'));
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }
    public function create(Request $request)
    {
        try{

        $this->leadSource->createOrUpdate($request);
        return redirect()->route('lead-source.index')->with('success', 'Lead Source create successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }

    }

    public function edit($id)
    {
        try{

        \Log::info("Edit method hit with ID: " . $id); // Check laravel.log
        return response()->json($this->leadSource->find($id));

        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
        //return view('company_client.lead_source.edit', compact('state'));
    }

    public function update(Request $request)
    {
        try{

        $id=$request->lead_source_id;
        $this->leadSource->createOrUpdate($request,$id);
        return redirect()->route('lead-source.index')->with('success', 'Lead Source updated successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }

    public function delete(Request $request)
    {
        try{

        $id=$request->lead_source_id;
        $this->leadSource->destroy($id);
        return redirect()->route('lead-source.index')->with('success', 'Lead Source deleted successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }
}
