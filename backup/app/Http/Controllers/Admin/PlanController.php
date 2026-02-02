<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\DB;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Category;

use App\Models\Plan;

use App\Repositories\Plan\PlanRepositoryInterface;

use App\Repositories\Plan\PlanRepository;

use Illuminate\Validation\Rule;



class PlanController extends Controller

{
    protected $plan;



    public function __construct(PlanRepositoryInterface $plan)

    {

        $this->plan = $plan;

    }

   public function index(Request $request)
    {
         try
        {
            $plan = Plan::select('plan_master.*')->when($request->search, fn ($query, $search) => $query->where('plan_name', 'LIKE', "%{$search}%"))
                ->where(['isDelete'=>0])
            ->orderBy('plan_id','desc')->paginate(config('app.per_page'));

            $search=$request->search;
            
        return view('admin.plan.index', compact('plan','search'));

        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        return view('admin.plan.add');

    }

    public function store(Request $request)
    {
            $validated = $request->validate([

                'plan_name' => [
                    'required',
                    Rule::unique('plan_master', 'plan_name')
                        ->where('isDelete', 0), // Unique per category
                ],

                'plan_days' => 'required', // Ensure description is provided
                'plan_amount' => 'required', // Ensure description is provided
            ]);


        try
        {
                $this->plan->createOrUpdate($request);
                return redirect()->route('plan.index')->with('success', 'Plan created successfully!');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
    }



    public function edit(plan $plan,$id)

    {

        try{
            $data = $this->plan->find($id);
        return view('admin.plan.edit',compact('data'));

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());

         }
    }
    public function update(Request $request, plan $plan,$id)
    {
         $request->validate([

            'plan_name' => [

                'required',

                Rule::unique('plan_master', 'plan_name')
                    ->where('isDelete', 0)
                    ->ignore($id, 'plan_id'),

            ],
            'plan_days' => 'required', // Ensure description is provided
            'plan_amount' => 'required', // Ensure description is provided
        ]);

        try
        {

            $data = $request->all();

            $this->plan->createOrUpdate($data, $id);







             return redirect()->route('plan.index')->with('success','Plan updated successfully');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());

        }

    }

    public function delete(Request $request)

    {     

         try

        {

            $id=$request->plan_id;

            

            $delete = Plan::find($id);

            $destinationPath = public_path('plan_image'); // Correct path

            if (!empty($delete->plan_image)) { // Ensure image name exists
                $imagePath = $destinationPath . '/' . $delete->plan_image; // Construct path

                if (file_exists($imagePath)) { // Check file existence
                    unlink($imagePath); // Delete file
                }
            }


        

            $data['isDelete']=1;
            $this->plan->changeStatus($data,$id);

            

            return back()->with('success','Plan deleted successfully');

        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());

        }

    }

}

