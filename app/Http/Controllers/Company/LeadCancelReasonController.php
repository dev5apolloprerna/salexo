<?php

namespace App\Http\Controllers\Company;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadCancelReason;
use App\Repositories\LeadCancelReason\LeadCancelReasonRepository;
use App\Repositories\LeadCancelReason\LeadCancelReasonRepositoryInterface;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class LeadCancelReasonController extends Controller
{
    protected $leadCReason;

    public function __construct(LeadCancelReasonRepositoryInterface $leadCReason)
    {
        $this->leadCReason = $leadCReason;
    }

    public function index()
    {
        try{
        $user = Auth::user();
        $leadCReason = LeadCancelReason::orderBy('lead_cancel_reason_id','desc')->where(['company_id' =>$user->company_id ])->paginate(config('app.per_page'));
        
        return view('company_client.lead_cancel_reason.index', compact('leadCReason'));
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }
    public function create(Request $request)
    {
        try{

        $this->leadCReason->createOrUpdate($request);
        return redirect()->route('lead-cancel-reason.index')->with('success', 'Lead Cancle Reason created successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }

    }

    public function edit($id)
    {
        \Log::info("Edit method hit with ID: " . $id); // Check laravel.log
        return response()->json($this->leadCReason->find($id));

        //return view('company_client.lead_cancel_reason.edit', compact('state'));
    }

    public function update(Request $request)
    {
        try{

        $id=$request->lead_cancel_reason_id;
        $this->leadCReason->createOrUpdate($request,$id);
        return redirect()->route('lead-cancel-reason.index')->with('success', 'Lead Cancle Reason updated successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }

    public function delete(Request $request)
    {
        try{

        $id=$request->lead_cancel_reason_id;
        $this->leadCReason->destroy($id);
        return redirect()->route('lead-cancel-reason.index')->with('success', 'Lead Cancle Reason deleted successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }
}
