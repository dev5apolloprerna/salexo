<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Student;
use App\Models\School;
use App\Models\Route;
use App\Models\Area;
use App\Models\Year;
use App\Models\User;
use App\Models\Semester;
use App\Models\StudentSubscription;
use App\Models\YearSemesterMapping;
use App\Models\RouteArea;
use App\Models\Company;
use App\Models\BranchMaster;
use File;


class HotelProfileController extends Controller
{

    public function index()
    {
        $id = auth()->user()->id;

        $student = Student::where(['isDelete'=>0,'iStatus'=>1,'userId' => $id])->first();
        $school = School::where(['isDelete'=>0,'iStatus'=>1])->get();
        $year= YearSemesterMapping::where(['year_semester_mapping.isDelete'=>0,'year_semester_mapping.iStatus'=>1])->join('year_master', 'year_master.yearId', '=', 'year_semester_mapping.yearId')->groupBy('year_semester_mapping.yearId')->get();
        $semester = YearSemesterMapping::where(['year_semester_mapping.isDelete'=>0,'year_semester_mapping.iStatus'=>1])->join('semester_master', 'semester_master.semesterId', '=', 'year_semester_mapping.semesterId')->groupBy('year_semester_mapping.semesterId')->get();
        $route = Route::where(['route_master.isDelete'=>0,'route_master.iStatus'=>1])->join('route_area_sequence', 'route_area_sequence.routeId', '=', 'route_master.routeId')->select('route_area_sequence.routeId as rid','route_master.routeNumber','route_master.routeId')->groupBy('route_master.routeId')->get();
        $branchMaster= BranchMaster::where(['isDelete'=>0,'iStatus'=>1])->get();

        return view('student.profile.index',compact('student','school','year','semester','route','branchMaster'));
    }    
    
    public function update(Request $request, student $student)
    {
         $request->validate([
                'birthDate' => 'required',
                'formNumber' => 'required',
                'studentName' => 'required',
                'mobile' => 'required',
                'email' => 'required|unique:users,email,'. $request->uid . ',id'
            ]);
        $root = $_SERVER['DOCUMENT_ROOT'];
        if($request->hasFile('studentPhoto'))
        {
            $fimage = $request->file('studentPhoto');
            $studentImg = time().'.'.$fimage->getClientOriginalExtension();
            $destinationpath = $root.'/images/student/';
            if(!file_exists($destinationpath)) {

                mkdir($destinationpath, 0755, true);
            }
            $fimage->move($destinationpath,$studentImg);
        }
        else
        {
            $oldFrontImage = $request->input('hiddenstudentPhoto');
            $studentImg = $oldFrontImage;
        }
        $mobile=$request->mobile;
        $email=$request->email;
        $studentId=$request->studentId;
        //\DB::enableQueryLog(); // Enable query log

        $student = Student::where(['mobile'=>$mobile,'isDelete'=>0,'iStatus'=>1])->whereNotIn('studentId', [$studentId])->get();
        $student1 = Student::where(['email'=>$email,'isDelete'=>0,'iStatus'=>1])->whereNotIn('studentId', [$studentId])->get();

        if((sizeof($student) &&  sizeof($student1)) == 0)
        {
            $data = array(
                'formNumber'=>$request->formNumber,
                'studentName'=>$request->studentName,
                'birthDate'=>$request->birthDate,
                'address'=>$request->address,
                'phoneNumber'=>$request->phoneNumber,
                'mobile'=>$request->mobile,
                'schoolIdForStudent'=>$request->schoolIdForStudent,
                'schoolName'=>$request->schoolName,
                'semester'=>$request->semester,
                'semester2'=>$request->semester2,
                'branch'=>$request->branch,
                'bloodGroup'=>$request->bloodGroup,
                'email'=>$request->email,
                'deposite'=>$request->deposite,
                'pickUpAreaId'=>$request->pickUpAreaId,
                'routeId'=>$request->routeId,
                'fee'=>$request->fee,
                'studentPhoto'=>$studentImg
                );
            Student::where("studentId","=",$request->studentId)->update($data);

            $data2 = array(
            'first_name'=> $request->studentName,
            'mobile_number'=> $request->mobile,
            'email'=> $request->email
            );
            User::where("id","=",$request->uid)->update($data2);


            return redirect()->route('student.profile.index')->with('success','Data Inserted Successfully');
        }else if(sizeof($student1) != 0)
        {
            return redirect()->back()->withInput()->with('error','Email alredy exist!');
        }
        else
        {
            return redirect()->back()->withInput()->with('error','Mobile Number alredy exist!');
        }
    }
        public function getEmail(Request $request)
    {
        $email=$request->email;
        $student = Student::where(['email'=>$email,'isDelete'=>0,'iStatus'=>1])->get();
        $student1 = User::where(['email'=>$email,'status'=>0])->get();
        if(sizeof($student) == 0 && sizeof($student1) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    } public function getMobile(Request $request)
    {
        $mobile=$request->mobile;
        $student = Student::where(['mobile'=>$mobile,'isDelete'=>0,'iStatus'=>1])->get();

        if(sizeof($student) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    }
    public function getFatherPhone(Request $request)
    {
        $student2 = Student::where(['fatherMobileNo'=>$request->getFatherPhone,'isDelete'=>0,'iStatus'=>1])->get();
        if(sizeof($student2) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    } public function EditEmail(Request $request)
    {
        $studentId=$request->studentId;
        $email=$request->email;
        $student1 = Student::where(['email'=>$email,'isDelete'=>0,'iStatus'=>1])->whereNotIn('studentId', [$studentId])->get();
        $student11 = User::where(['email'=>$email,'isDelete'=>0,'status'=>0])->whereNotIn('id', [$request->uid])->get();
        if(sizeof($student) == 0 && sizeof($student11) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    } public function EditMobile(Request $request)
    {

        $mobile=$request->mobile;
        $studentId=$request->studentId;

        $student = Student::where(['mobile'=>$mobile,'isDelete'=>0,'iStatus'=>1])->whereNotIn('studentId', [$studentId])->get();

        if(sizeof($student) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    }
    public function EditFatherPhone(Request $request)
    {

        $studentId=$request->studentId;
        $student2 = Student::where(['fatherMobileNo'=>$request->fatherMobileNo,'isDelete'=>0,'iStatus'=>1])->whereNotIn('studentId', [$studentId])->get();
        if(sizeof($student2) == 0)
        {
                echo  0;
        }else{
            echo 1;
        }
    }
    public function getAmount(Request $request)
    {
       $data['amount'] = YearSemesterMapping::where(['yearId'=>$request->year,'semesterId'=>$request->semester])
                    ->get(["Amount","yearSemesterId"]);

        return response()->json($data);
    }
    public function getSemester(Request $request)
    {
        $data['semester'] = YearSemesterMapping::where(['year_semester_mapping.isDelete'=>0,'year_semester_mapping.iStatus'=>1,'yearId'=>$request->year])->join('semester_master', 'semester_master.semesterId', '=', 'year_semester_mapping.semesterId')
            ->select('semester_master.semesterMonth','semester_master.semesterId as sid','year_semester_mapping.semesterId as ysId','year_semester_mapping.yearId')
            ->get('sid','semesterMonth','yearId');        

        return response()->json($data);
    }
    public function getArea(Request $request)
    {
            $id=$request->routeId;

       $data['area'] = RouteArea::where(['route_area_sequence.isDelete'=>0,'route_area_sequence.iStatus'=>1,'route_area_sequence.routeId' => $id])
                        ->join('area_master', 'area_master.areaId', '=', 'route_area_sequence.areaId')->
                        select('route_area_sequence.sequenceNumber','route_area_sequence.routeId','route_area_sequence.sequenceId', 'area_master.areaName', 'area_master.areaId as aid','route_area_sequence.areaId as sid')->orderBy('route_area_sequence.sequenceNumber','ASC')
                        ->get(["areaName","areaId"]);
        return response()->json($data);
    }


}
