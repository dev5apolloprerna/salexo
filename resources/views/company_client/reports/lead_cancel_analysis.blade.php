@extends('layouts.client')
@section('title', 'Lead Cancel Analysis Report List')
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
                                <h5 class="card-title mb-0">Lead Cancel Analysis Report List</h5>
                                <hr>
                            </div>

                            <div class="card-body">
                                <form method="get" action="{{ route('clients.reports.emp_lead_cancel_analysis') }}">
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
                                            <a href="{{ route('clients.reports.emp_lead_cancel_analysis') }}"
                                                class="btn btn-secondary">Reset</a>

                                            @php
                                                $company_client_master = App\Models\CompanyClient::where([
                                                    'iStatus' => 1,
                                                    'isDeleted' => 0,
                                                    'company_id' => Auth::user('web_employees')->company_id,
                                                ])->first();
                                            @endphp

                                            @if ($company_client_master && $company_client_master->plan_id != 1)
                                                <a href="{{ route('clients.reports.emp_lead_cancel_analysis.export', [
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

                            <div class="card-body">
                                <div class="row">
                                    {{-- Table --}}
                                    <div class="col-lg-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr.No</th>
                                                        <th>Cancel Reason</th>
                                                        <th class="text-center">Lead Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($reportData as $index => $row)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $row->reason ?? 'N/A' }}</td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_cancel_analysis_detail', [
                                                                        'cancel_reason_id' => $row->cancel_reason_id,
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $row->lead_count }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">No data found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $reportData->links() }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Pie Chart --}}
                                    <div class="col-lg-6">
                                        <div style="height: 400px; width: 100%;">
                                            <canvas id="cancelPieChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- card -->
                    </div> <!-- col -->
                </div> <!-- row -->

            </div> <!-- container-fluid -->
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('cancelPieChart').getContext('2d');

            const pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        data: @json($chartPercentages),
                        backgroundColor: [
                            '#007bff', '#dc3545', '#ffc107', '#28a745', '#6f42c1', '#fd7e14',
                            '#20c997',
                            '#6610f2', '#17a2b8', '#e83e8c' // Add more if needed
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Lead Cancel Reason (by Percentage)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return label + ': ' + value + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
