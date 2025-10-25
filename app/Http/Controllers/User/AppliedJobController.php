<?php

namespace App\Http\Controllers\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppliedJob;
use App\Models\Experience;
use App\Models\SalaryRange;
use App\Models\Industry;
use App\Models\Functions;
use App\Models\State;
use App\Models\City;
use App\Models\Jobs;
use App\Models\Bucket;
class AppliedJobController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $userId=auth()->user()->id;

            $Experience=Experience::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
            $SalaryRange=SalaryRange::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
            $Industry=Industry::where(['iStatus'=>1,'isDelete'=>0])->orderBy('IndustryName','asc')->get();
            $Functions=Functions::where(['iStatus'=>1,'isDelete'=>0])->orderBy('functionName','asc')->get();
            $State=State::where(['iStatus'=>1,'isDelete'=>0])->orderBy('stateName','asc')->get();
            $City=City::where(['iStatus'=>1,'isDelete'=>0])->orderBy('cityName','asc')->get();

            $bucketCount=Bucket::where(['employeeId'=>$userId])->count();

            $appliedJob = AppliedJob::select('applied_jobs.*','jobs.created_at as jobDate','jobs.jobTitle','jobs.industryId','jobs.functionId',DB::raw('(select state.stateName from state where state.stateId=applied_jobs.stateId limit 1) as state')
            ,DB::raw('(select city.cityName from city where city.cityId=applied_jobs.cityId limit 1) as city')
             ,DB::raw('(select experience.experienceName from experience where experience.experienceId=applied_jobs.experienceId limit 1) as experience')
             ,DB::raw('(select  industry.IndustryName from  industry where industry.industryId=applied_jobs.industryId limit 1) as industry')
             ,DB::raw('(select functions.functionName from functions where functions.functionId=applied_jobs.functionId limit 1) as functions')
             ,DB::raw('(select education.educationName from education where education.educationId=applied_jobs.educationId limit 1) as education')
             ,DB::raw('(select  industry.IndustryName from  industry where industry.industryId=jobs.industryId limit 1) as jobIndustry')
             ,DB::raw('(select functions.functionName from functions where functions.functionId=jobs.functionId limit 1) as jobFunctions')

             
        )->when($request->jobTitle, fn ($query, $jobTitle) => $query->where('jobTitle','like','%'.$jobTitle.'%'))
            ->when($request->experience, fn ($query, $experience) => $query->where('applied_jobs.experienceId','=',$experience))
            ->when($request->salary, fn ($query, $salary) => $query->where('jobs.salaryId','=',$salary))
            ->when($request->industry, fn ($query, $industry) => $query->where('applied_jobs.industryId','=',$industry))
            ->when($request->functions, fn ($query, $functions) => $query->where('applied_jobs.functionId','=',$functions))
            ->when($request->state, fn ($query, $state) => $query->where('jobs.stateId','=',$state))
            ->when($request->city, fn ($query, $city) => $query->where('jobs.cityId','=',$city))
            ->when($request->date, fn ($query, $ToDate) => $query->where('applied_jobs.created_at', '>=', date('Y-m-d 00:00:00', strtotime($ToDate))))

            ->where(['applied_jobs.isDelete'=>0,'applied_jobs.iStatus'=>1,'enter_by'=>$userId])->orderBy('created_at','desc')
            ->join('jobs', 'jobs.jobId', '=', 'applied_jobs.jobId')
            ->paginate(env('PER_PAGE_COUNT'));
            $jobTitle=$request->jobTitle;
            $experience=$request->experience;
            $industry=$request->industry;
            $functions=$request->functions;
            $state=$request->state;
            $city=$request->city;


        return view('User.applied-job.index', compact('userId','appliedJob','bucketCount','jobTitle','experience','industry','functions','state','city','Experience','SalaryRange','Industry','Functions','State','City'));
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $Experience=Experience::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
        $SalaryRange=SalaryRange::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
        $Industry=Industry::where(['iStatus'=>1,'isDelete'=>0])->orderBy('IndustryName','asc')->get();
        $Functions=Functions::where(['iStatus'=>1,'isDelete'=>0])->orderBy('functionName','asc')->get();
        $State=State::where(['iStatus'=>1,'isDelete'=>0])->orderBy('stateName','asc')->get();
        $City=City::where(['iStatus'=>1,'isDelete'=>0])->orderBy('cityName','asc')->get();


        return view('User.applied-job.add',compact('Experience','SalaryRange','Industry','Functions','State','City'));
    }

    public function store(Request $request)
    {

        try
        {
                $Jobs = new Jobs();
                $Jobs->jobTitle=$request->jobTitle;
                $Jobs->jobDescription=$request->jobDescription;
                $Jobs->experienceId=$request->experience;
                $Jobs->salaryId=$request->salary;
                $Jobs->industryId=$request->industry;
                $Jobs->functionId=$request->function;
                $Jobs->stateId=$request->state;
                $Jobs->cityId=$request->city;
                $Jobs->job_end_date=$request->job_end_date;
                $Jobs->enter_by=auth()->user()->id;
                $Jobs->save();

                if($request->saveAdd == 'saveAdd')
                {
                    return redirect()->back()->with('success','Job Inserted Successfully');
                }else{
                    return redirect()->route('applied-job.index')->with('success','Job Inserted Successfully');
                }


        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
    }

    public function edit(AppliedJob $AppliedJob,$id)
    {
        try
        {

        $Experience=Experience::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
        $SalaryRange=SalaryRange::where(['iStatus'=>1,'isDelete'=>0])->orderBy('sequence','asc')->get();
        $Industry=Industry::where(['iStatus'=>1,'isDelete'=>0])->orderBy('IndustryName','asc')->get();
        $Functions=Functions::where(['iStatus'=>1,'isDelete'=>0])->orderBy('functionName','asc')->get();
        $State=State::where(['iStatus'=>1,'isDelete'=>0])->orderBy('stateName','asc')->get();
        $City=City::where(['iStatus'=>1,'isDelete'=>0])->orderBy('cityName','asc')->get();

        $job = Jobs::where(['jobId' => $id])->first();

        return view('User.applied-job.edit',compact('job','Experience','SalaryRange','Industry','Functions','State','City'));

        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
    }

    public function update(Request $request, $id)
    {
            try{

            $data = array(
                'jobTitle'=>$request->jobTitle,
                'jobDescription'=>$request->jobDescription,
                'experienceId'=>$request->experience,
                'salaryId'=>$request->salary,
                'industryId'=>$request->industry,
                'functionId'=>$request->function,
                'stateId'=>$request->stateId,
                'cityId'=>$request->cityId,
                'job_end_date'=>$request->job_end_date
                );
            AppliedJob::where("jobId","=",$id)->update($data);

                return redirect()->route('applied-job.index')->with('success','Job Updated Successfully');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
    }

    public function delete(Request $request)
    {     
    try{

        $id=$request->jobId;
        $jobs=Jobs::where('jobId','=',$id)->delete();
        
        return back()->with('success','Job Deleted Successfully');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
      public function getCities($stateId)
    {
        $cities = City::where('stateId', $stateId)->get();
        return response()->json($cities);
    }

}
