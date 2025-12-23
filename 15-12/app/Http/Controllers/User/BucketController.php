<?php

namespace App\Http\Controllers\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppliedJob;
use App\Models\Jobs;
use App\Models\Bucket;
class BucketController extends Controller
{
    public function addMember(Request $request)
    {
        try
        {
            $members = $request->input('memberId');
            $employeId=$request->input('employeeId');

            $bucketList=Bucket::whereIn('candidateId',[$members])->where(['employeeId'=>$employeId])->get();

            if(sizeof($bucketList) == 0)
            {
                if (!empty($members)) 
                {
                    foreach ($members as $item) 
                    {
                        // Assuming your Item model has a `name` field
                        Bucket::create(['candidateId' => $item,'employeeId'=>$employeId]);
                    }
                }
                return response()->json(['success' => 'Member added successfully!']);
            }else{
            return response()->json(['success' => 'Member already added to bucket!']);

            }
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function bucketList(Request $request)
    {
        try
        {
            $userId=auth()->user()->id;
            $bucketList=Bucket::where(['employeeId'=>$userId])->join('applied_jobs', 'applied_jobs.candidateId', '=', 'bucket.candidateId')->join('jobs', 'jobs.jobId', '=', 'applied_jobs.jobId')->paginate(env('PER_PAGE_COUNT'));


        return view('User.bucket.bucket-list',compact('bucketList','userId'));


        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
     public function deleteBucket(Request $request)
    {     
    try{

        $id=$request->bucketId;
        $Bucket=Bucket::where('bucketId','=',$id)->delete();
        
        return back()->with('success','Member Deleted Successfully');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function clearBucket(Request $request)
    {     
    try{

        $id=$request->employeeId;
        $Bucket=Bucket::where('employeeId','=',$id)->delete();
        
        return back()->with('success','Bucket clear Successfully');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
     public function exportCandidate()
    {

    try{
        $userId=auth()->user()->id;
        $data=Bucket::select('bucket.*','applied_jobs.*',DB::raw('(select state.stateName from state where state.stateId=applied_jobs.stateId limit 1) as state'),DB::raw('(select city.cityName from city where city.cityId=applied_jobs.cityId limit 1) as city')
             ,DB::raw('(select experience.experienceName from experience where experience.experienceId=applied_jobs.experienceId limit 1) as experience')
             ,DB::raw('(select  industry.IndustryName from  industry where industry.industryId=applied_jobs.industryId limit 1) as industry')
             ,DB::raw('(select functions.functionName from functions where functions.functionId=applied_jobs.functionId limit 1) as functions')
             ,DB::raw('(select education.educationName from education where education.educationId=applied_jobs.educationId limit 1) as education')
            )->where(['employeeId'=>$userId])->join('applied_jobs', 'applied_jobs.candidateId', '=', 'bucket.candidateId')->join('jobs', 'jobs.jobId', '=', 'applied_jobs.jobId')->get();

        return view('User.bucket.exportCandidate', compact('data'));
    } catch (\Exception $e) {

        report($e);
 
        return false;
    }
    }
  }