<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class CompanyClientLoginController extends Controller
{
    //
    public function loginform()
    {
        if (Auth::guard('web_employees')->check()) {
            $user = Auth::guard('web_employees')->user();

            if ($user->role_id == 2) {
                return redirect()->route('userhome');
            } elseif ($user->role_id == 3) {
                return redirect()->route('employee.home');
            }
        }

        return view('company_client.userLogin');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required',
                'password' => 'required',
            ]);

            $mobile = $request->mobile;
            $password = $request->password;

            $user = Employee::where(['emp_mobile' => $mobile, 'iStatus' => 1])->first();
            if ($user && in_array($user->role_id, [2, 3])) {

                $credentials = [
                    'emp_mobile' => $mobile,
                    'password' => $password
                ];

                if (Auth::guard('web_employees')->attempt($credentials)) {

                   $user = Employee::with(['company.state'])
                        ->select([
                            'emp_id',
                            'emp_name',
                            'emp_email',
                            'company_id',
                            'emp_mobile',
                            'role_id',
                            'isCompanyAdmin',
                        ])
                        ->where('emp_id', $empId)
                        ->first();

                    if (!$user) {
                        return back()->withErrors('Employee not found.');
                    }

                    if ($user) {
                        $request->session()->put([
                            'emp_id'              => $user->emp_id,
                            'emp_name'            => $user->emp_name,
                            'emp_mobile'          => $user->emp_mobile,
                            'emp_email'           => $user->emp_email,
                            'company_id'          => $user->company_id,
                            'company_name'        => data_get($user, 'company.company_name'),
                            'contact_person_name' => data_get($user, 'company.contact_person_name'),
                            'company_logo' => data_get($user, 'company.company_logo'),
                            'GST'                 => data_get($user, 'company.GST'),
                            'city'                => data_get($user, 'company.city'),
                            'state'               => data_get($user, 'company.state.stateName'),
                            'user_role_id'        => $user->role_id,
                            'isCompanyAdmin'      => $user->isCompanyAdmin,
                        ]);

                        // return redirect()->route('userhome');
                        // ✅ Redirect based on role

                        if ($user->role_id == 2) {
                            return redirect()->route('userhome');
                        } elseif ($user->role_id == 3) {
                            return redirect()->route('employee.home');
                        }
                    } else {
                        Log::warning('Login attempt failed: User not found after successful authentication.', [
                            'mobile' => $mobile
                        ]);
                        return redirect()->back()->with('error', 'User Not Found');
                    }
                } else {
                    Log::warning('Login attempt failed: Incorrect credentials.', [
                        'mobile' => $mobile
                    ]);
                    return redirect()->back()->with('error', 'Incorrect Mobile or Password');
                }
            } else {
                Log::warning('Login attempt failed: Inactive or unauthorized user.', [
                    'mobile' => $mobile
                ]);
                return redirect()->back()->with('error', 'Inactive User Cannot Login. Please Contact Admin.');
            }
        } catch (\Exception $e) {
            Log::error('Error during employee login: ' . $e->getMessage(), [
                'mobile' => $request->mobile ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web_employees')->logout();
        $request->session()->invalidate();
        // Regenerate the session token to prevent session fixation attacks
        $request->session()->regenerateToken();


        $request->session()->forget('emp_id');
        $request->session()->forget('company_name');
        $request->session()->forget('company_id');
        $request->session()->forget('emp_mobile');
        $request->session()->forget('emp_email');
        $request->session()->forget('emp_name');
        $request->session()->forget('user_role_id');
        $request->session()->forget('contact_person_name');
        $request->session()->forget('GST');
        $request->session()->forget('city');
        $request->session()->forget('state');
        $request->session()->forget('branch_id');
        $request->session()->forget('company_logo');

        return redirect()->route('user_login');

        // return view('frontview.login');
        // return view('company_client.logout');
    }
}
