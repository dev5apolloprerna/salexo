@extends('layouts.client')
@section('title', 'Lead Analysis Report List')
@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Lead Analysis Report List

                                </h5>
                                <hr>

                            </div>
                            <div class="card-body">

                                <form method="get" action="{{ route('clients.reports.emp_lead_analysis') }}">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" value="{{ $fromDate }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" value="{{ $toDate }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                                            <a href="{{ route('clients.reports.emp_lead_analysis') }}"
                                                class="btn btn-secondary">Reset</a>

                                            @php
                                                $company_client_master = App\Models\CompanyClient::where([
                                                    'iStatus' => 1,
                                                    'isDeleted' => 0,
                                                    'company_id' => Auth::user('web_employees')->company_id,
                                                ])->first();
                                            @endphp

                                            @if ($company_client_master && $company_client_master->plan_id != 1)
                                                <a href="{{ route('clients.reports.emp_lead_analysis.export', [
                                                    'from_date' => request('from_date'),
                                                    'to_date' => request('to_date'),
                                                    'emp_name' => request('emp_name'),
                                                ]) }}"
                                                    class="btn btn-success ms-2">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @else
                                                <a target="_blank" class="btn btn-success ms-2"
                                                    href="{{ route('front.index') }}#cta"
                                                    style="filter: blur(0.5px); opacity:0.5;">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr.No</th>
                                                        <th>Lead Received</th>
                                                        <th>Pipeline</th>
                                                        <th class="text-center">Count</th>
                                                        <th style="text-align:right">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($reportData as $index => $row)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $row->lead_source_name }}</td>
                                                            <td>{{ $row->pipeline_name }}</td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_analysis_detail', [
                                                                        'lead_source_id' => $row->LeadSourceId,
                                                                        'pipeline_id' => $row->pipeline_id,
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $row->lead_count }}
                                                                </a>
                                                            </td>
                                                            <td style="text-align:right">₹
                                                                {{ number_format($row->total_amount, 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No data found.</td>
                                                        </tr>
                                                    @endforelse
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $reportData->links() }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
