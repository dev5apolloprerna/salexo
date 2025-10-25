@extends('layouts.client')
@section('title', 'Lead Analysis Detail')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center card-header">
                        <h5 class="card-title mb-0">Lead Analysis Detail</h5>
                        <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="{{ route('clients.reports.emp_lead_analysis') }}">Back</a>
                    </div>
                    @if($fromDate && $toDate)
                        <div class="alert alert-info">
                            Showing results from <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</strong> to 
                            <strong>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</strong>
                        </div>
                    @endif
                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!--<th>Lead ID</th>-->
                                    <th>Customer Name</th>
                                    <th>Lead Source</th>
                                    <th>Pipeline</th>
                                    <th>Assigned To</th>
                                    <th>Amount</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData as $index => $lead)
                                    <tr>
                                        <td>{{ $reportData->firstItem() + $index }}</td>
                                        <!--<td>{{ $lead->lead_id }}</td>-->
                                        <td>{{ $lead->customer_name }}</td>
                                        <td>{{ $lead->lead_source_name }}</td>
                                        <td>{{ $lead->pipeline_name }}</td>
                                        <td>{{ $lead->employee_name ?? '—' }}</td>
                                        <td>₹{{ number_format($lead->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $reportData->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
