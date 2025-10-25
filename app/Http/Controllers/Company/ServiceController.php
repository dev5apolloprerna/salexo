<?php

namespace App\Http\Controllers\Company;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\CompanyClient;
use App\Repositories\Service\ServiceRepository;
use App\Repositories\Service\ServiceRepositoryInterface;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class serviceController extends Controller
{
    protected $service;

    public function __construct(ServiceRepositoryInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $services = Service::orderBy('service_id','desc')->where(['company_id' =>$user->company_id ])->paginate(config('app.per_page'));

            return view('company_client.service.index', compact('services'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $companies = CompanyClient::pluck('company_name', 'company_id');

            return view('company_client.service.create', compact('companies'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
        ]);
        try {

            Service::create([
                'company_id' => auth()->user()->company_id,
                'service_name' => $request->service_name,
                'service_description' => $request->service_description,
                'created_at' => now(),
            ]);

            return redirect()->route('service.index')->with('success', 'Service added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {

            $service = $this->service->find($id);

            echo json_encode($service);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string'
        ]);

        try {
            $service = Service::where('service_id', $request->service_id)->first();
            $service->update([
                "service_name" => $request->service_name,
                "service_description" => $request->service_description,
                'updated_at' => now()
            ]);
            return redirect()->route('service.index')->with('success', 'Service updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $service = Service::where('service_id', $request->service_id)->first();
            $service->delete();

            return redirect()->back()->with('success', 'Service deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
