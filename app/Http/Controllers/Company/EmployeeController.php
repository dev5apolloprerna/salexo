<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyClient;
use Illuminate\Http\Request;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    protected $employeeRepo;

    public function __construct(EmployeeRepositoryInterface $employeeRepo)
    {
        $this->employeeRepo = $employeeRepo;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->emp_name;

            $employees = Employee::orderBy('emp_id', 'desc')
                ->where(['isDelete' => 0,  'company_id' => Auth::user()->company_id])
                ->when($search, function ($query, $search) {
                    $query->where('employee_master.emp_name', 'like', '%' . $search . '%');
                })
                ->paginate(config('app.per_page'));

            // $employees = $this->employeeRepo->query()->paginate(env('PER_PAGE_COUNT'));

            return view('company_client.employee.index', compact('employees', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('company_client.employee.create');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web_employees')->user();
        $company_client = CompanyClient::where(['company_id' => $user->company_id])->first();

        $currentEmployeeCount = Employee::where('company_id', $user->company_id)
            ->where('isDelete', 0)
            ->count();

        // Step 2: Check if the limit is reached
        if ($currentEmployeeCount >= $company_client->no_of_users) {
            return redirect()->back()->with('error', 'You have reached the maximum number of employees allowed for your plan.');
        }

        $data = $request->validate([
            'emp_name' => 'required',
            'emp_mobile' => 'required|unique:employee_master,emp_mobile',
            'emp_email' => 'nullable|email',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        $guid = Str::uuid();
        $data['guid'] = $guid;
        $data['password'] = Hash::make($data['password']);
        $data['company_id'] = $user->company_id;
        $data['isCompanyAdmin'] = $request->role_id == 2 ? 1 : 0;

        $this->employeeRepo->create($data);

        return redirect()->route('employee.index')->with('success', 'Employee created successfully');
    }

    public function edit($id)
    {
        try {
            $employee = $this->employeeRepo->find($id);

            return view('company_client.employee.edit', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'emp_name' => 'required',
            'emp_mobile' => 'required|unique:employee_master,emp_mobile,' . $id . ',emp_id',
            'emp_email' => 'nullable|email',
            'role_id' => 'required',
        ]);
        try {
            // Set admin flag based on selected role
            $data['isCompanyAdmin'] = $request->role_id == 2 ? 1 : 0;

            $this->employeeRepo->update($id, $data);

            return redirect()->route('employee.index')->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->employeeRepo->delete($id);

            return redirect()->route('employee.index')->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function passwordupdate(Request $request)
    {
        $newpassword = $request->newpassword;
        $confirmpassword = $request->confirmpassword;

        if ($newpassword == $confirmpassword) {
            Employee::where(['emp_id' => $request->id])
                ->update([
                    'password' => Hash::make($request->confirmpassword),
                ]);
            return redirect()->route('employee.index')->with('success', 'User Password Updated Successfully.');
        } else {
            return redirect()->route('employee.index')->with('error', 'password and confirm password does not match');
        }
    }
}
