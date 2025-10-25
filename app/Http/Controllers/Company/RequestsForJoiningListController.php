<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\RequestForJoining;
use Illuminate\Http\Request;

class RequestsForJoiningListController extends Controller
{
    public function index(Request $request)
    {
        try {

            $datas = RequestForJoining::where(['iStatus' => 1, 'isDeleted' => 0])->paginate(env('PER_PAGE_COUNT'));

            return view('request_for_joining.create', compact('datas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
