@extends('layouts.client')
@section('title', 'Lead Cancel Detail')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Lead Cancel Detail</h5>
                        <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="{{ route('clients.reports.emp_lead_cancel_analysis') }}">Back</a>
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
                                    <th>Mobile</th>
                                    <th>Lead Source</th>
                                    <th>Service Name</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $index => $lead)
                                    <tr>
                                        <td>{{ $leads->firstItem() + $index }}</td>
                                        <!--<td>{{ $lead->lead_id }}</td>-->
                                        <td>{{ $lead->customer_name }}</td>
                                        <td>{{ $lead->mobile }}</td>
                                        <td>{{ $lead->lead_source_name }}</td>
                                        <td>{{ $lead->service_name }}</td>
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
                            {{ $leads->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
