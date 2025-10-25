<?php

namespace App\Http\Controllers\Company;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadPipeline;
use App\Repositories\LeadPipeline\LeadPipelineRepository;
use App\Repositories\LeadPipeline\LeadPipelineRepositoryInterface;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class LeadPipelineController extends Controller
{
    protected $leadPipeline;

    public function __construct(LeadPipelineRepositoryInterface $leadPipeline)
    {
        $this->leadPipeline = $leadPipeline;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $leadPipeline = LeadPipeline::orderBy('pipeline_id','desc')->where(['company_id' =>$user->company_id ])->paginate(config('app.per_page'));

            return view('company_client.lead_pipeline.index', compact('leadPipeline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            $this->leadPipeline->createOrUpdate($request);

            return redirect()->route('lead-pipeline.index')->with('success', 'Lead Pipeline created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {

            \Log::info("Edit method hit with ID: " . $id); // Check laravel.log
            return response()->json($this->leadPipeline->find($id));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

        //return view('company_client.lead_pipeline.edit', compact('state'));
    }

    public function update(Request $request)
    {
        try {

            $id = $request->pipeline_id;
            $this->leadPipeline->createOrUpdate($request, $id);
            return redirect()->route('lead-pipeline.index')->with('success', 'Lead Pipeline updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {

            $id = $request->pipeline_id;
            $this->leadPipeline->destroy($id);
            return redirect()->route('lead-pipeline.index')->with('success', 'Lead Pipeline deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
