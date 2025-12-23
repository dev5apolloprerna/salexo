<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Repositories\State\StateRepository;
use App\Repositories\State\StateRepositoryInterface;

use Illuminate\Validation\Rule;

class StateController extends Controller
{
    protected $stateRepo;

    public function __construct(StateRepositoryInterface $stateRepo)
    {
        $this->stateRepo = $stateRepo;
    }

    public function index(Request $request)
    {
        try{

        $search = $request->input('search');

            $query = $this->stateRepo->query();

            if ($search) {
                $query->where('stateName', 'like', '%' . $search . '%');
            }

            $State = $query->orderBy('stateName', 'asc')->paginate(config('app.per_page'));

        return view('admin.state.index', compact('State','search'));

         } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function create(Request $request)
    {
            $request->validate([
                'stateName' => 'required|unique:state,stateName',
            ], [
                'stateName.unique' => 'This state name already exists.',
            ]);

        try{

            $this->stateRepo->createOrUpdate($request);
            return redirect()->route('state.index')->with('success', 'State updated.');
         } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }

    public function edit($id)
    {
        try
        {

            \Log::info("Edit method hit with ID: " . $id); // Check laravel.log
            return response()->json($this->stateRepo->find($id));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $id=$request->stateId;

         $request->validate([
            'stateName' => 'required|unique:state,stateName,' . $id . ',stateId',
        ]);

        try
        {
            $this->stateRepo->createOrUpdate($request,$id);
            return redirect()->route('state.index')->with('success', 'State updated successfully.');
        } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id=$request->stateId;
            $this->stateRepo->destroy($id);
            return redirect()->route('state.index')->with('success', 'State deleted successfully.');
         } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
         }
    }
}
