<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\RequestForJoining;
use App\Models\State;
use App\Models\Plan;

use Illuminate\Http\Request;

class RequestsForJoiningListController extends Controller
{
    public function index(Request $request)
    {
        try {

            $datas = RequestForJoining::where(['iStatus' => 1, 'isDeleted' => 0])->paginate(env('PER_PAGE_COUNT'));
            $states=State::where(['iStatus' => 1, 'isDelete' => 0])->get();
            $plans=Plan::where(['iStatus' => 1, 'isDelete' => 0])->get();
            
             $planDetails = [];
                foreach ($plans as $plan) {
                    $planDetails[$plan->id] = [
                        'amount' => $plan->amount,
                        'days'   => $plan->days,
                    ];
                }

            return view('request_for_joining.create', compact('datas','states','plans','planDetails'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
