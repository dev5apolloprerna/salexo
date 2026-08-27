@extends('layouts.client')
@section('title', 'EMP Analysis Report')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    @include('common.alert')
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">EMP Analysis Report</h5></div>
        <div class="card-body border-bottom">
            @include('company_client.reports._analysis_filters', ['filterRoute' => route('clients.reports.employee_analysis')])
        </div>
        <div class="card-body">
            @forelse ($reportEmployees as $employee)
                @php($counts = $statusCounts->get($employee->emp_id, collect()))
                <div class="accordion mb-2" id="employee-analysis-{{ $employee->emp_id }}">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employee-analysis-body-{{ $employee->emp_id }}">
                                <span class="fw-semibold">{{ $employee->emp_name }}</span>
                                <span class="badge bg-primary ms-2">{{ $counts->sum() }} Total Leads</span>
                            </button>
                        </h2>
                        <div id="employee-analysis-body-{{ $employee->emp_id }}" class="accordion-collapse collapse">
                            <div class="accordion-body table-responsive">
                                @include('company_client.reports._status_table', ['counts' => $counts])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mb-0">No employees found for the selected filters.</div>
            @endforelse
        </div>
    </div>
</div></div></div>
@endsection
