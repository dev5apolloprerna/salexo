<?php

namespace App\Http\Controllers\Company;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class CSVUploadController extends Controller
{
    public function index(Request $request)
    {
        return view('company_client.leads.csvupload.index');
    }
    public function dummyexcel(Request $request)
    {
        return Excel::download(new UsersExport, 'DummyExcel.xlsx');
    }
    public function create(Request $request)
    {
        Excel::import(new UsersImport, request()->file('csvfile'));
        return back()->with('success', 'CSV Uploaded Successfully');
    }
}
