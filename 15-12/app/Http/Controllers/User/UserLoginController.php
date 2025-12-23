<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\HotelMaster;
use Session;
use Illuminate\Support\Facades\Auth;
use Mail;

class UserLoginController extends Controller
{
    //
    public function loginform()
    {
        return view('User.userLogin');
    }
    public function login(Request $request)
    {
        $request->validate([
            'loginId' => 'required',
            'password' => 'required',
        ]);

       $loginId=$request->loginId;
       $password=$request->password;
       $user = User::where(['loginId'=>$loginId,'status'=>1])->first();
       $credentials = array(
            "loginId" => $loginId,
            "password" => $password
       );

        if ($user && $user->role_id == 2) 
        {
            if (Auth::attempt($credentials)) 
            {
                $user=User::select('name','id','loginId')->where(['id'=>$user->id])->first();
                if(!empty($user))
                {
                    $request->session()->put('userId', $user->id);
                    $request->session()->put('name',$user->name);
                    $request->session()->put('user_role_id','2');
                }else {
                    
                    return redirect()
                        ->back()
                        ->with('error', 'User Not Found');
                }
                // $request->session()->put('CustomerImagePath',$customer->image_path);
                
                return redirect()->route('userhome');
                
            } else {
                
                return redirect()
                    ->back()
                    ->with('error', 'Enter Loginid or Password is Incorrect');
            }
        } else {

            return redirect()
                ->back()
                ->with('error', 'Inactive  User Can not Login. Please Contact to admin.');
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('name');
        $request->session()->forget('user_role_id');
        $request->session()->forget('userId');
        return view('User.logout');
    }

}
