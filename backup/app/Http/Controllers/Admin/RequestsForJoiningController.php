<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RequestForJoining;
use App\Models\State;
use Illuminate\Http\Request;

class RequestsForJoiningController extends Controller
{
    public function index(Request $request)
    {
        try {

            $datas = RequestForJoining::where(['iStatus' => 1, 'isDeleted' => 0])->paginate(env('PER_PAGE_COUNT'));
            $states=State::where(['iStatus' => 1, 'isDeleted' => 0])->get();
            return view('request_for_joining.create', compact('datas','states'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
