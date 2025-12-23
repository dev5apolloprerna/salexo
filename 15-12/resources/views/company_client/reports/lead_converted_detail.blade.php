@extends('layouts.client')
@section('title', 'Lead Converted Detail')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center card-header">
                        <h5 class="card-title mb-0">Lead Converted Detail</h5>
                        <a href="{{ route('clients.reports.roi_report') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Back</a>
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
                                    {{--  <th>Lead ID</th>  --}}
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>Lead Source</th>
                                    <th>Service Name</th>
                                    <th>Amount</th>
                                    <th>Done Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $index => $lead)
                                    <tr>
                                        <td>{{ $leads->firstItem() + $index }}</td>
                                        {{--  <td>{{ $lead->lead_id }}</td>  --}}
                                        <td>{{ $lead->customer_name }}</td>
                                        <td>{{ $lead->mobile }}</td>
                                        <td>{{ $lead->lead_source_name }}</td>
                                        <td>{{ $lead->service_name }}</td>
                                        <td>{{ $lead->amount ?? '-' }}</td>
                                        <td>
                                            @if ($lead->deal_done_at == null)
                                                -
                                            @else
                                                {{ \Carbon\Carbon::parse($lead->deal_done_at)->format('d-m-Y H:i') }}
                                            @endif

                                        </td>
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
