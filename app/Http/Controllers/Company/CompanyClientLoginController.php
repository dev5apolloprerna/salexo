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

                    $user = Employee::select(
                        'emp_name',
                        'emp_id',
                        'emp_email',
                        'company_id',
                        'emp_mobile',
                        'role_id',
                        'isCompanyAdmin'
                    )
                        ->where('emp_id', $user->emp_id)
                        ->first();

                    if ($user) {
                        $request->session()->put('emp_id', $user->emp_id);
                        $request->session()->put('emp_name', $user->emp_name);
                        $request->session()->put('emp_mobile', $user->emp_mobile);
                        $request->session()->put('emp_email', $user->emp_email);
                        $request->session()->put('company_id', $user->company_id);
                        $request->session()->put('user_role_id', $user->role_id);

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
        $request->session()->forget('emp_name');
        $request->session()->forget('emp_phone');
        $request->session()->forget('emp_email');
        $request->session()->forget('user_role_id');
        $request->session()->forget('branch_id');

        return redirect()->route('user_login');
        // return view('frontview.login');
        // return view('company_client.logout');
    }
}
