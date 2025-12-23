@extends('layouts.client')
@section('title', 'Lead Generate Detail List')
@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    Lead Generate Detail List – <span class="text-primary">{{ $employee->emp_name }}</span>
                                </h5>
                                <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="{{ route('clients.reports.emp_performance') }}">Back</a>
                            </div>
                            
                            @if($fromDate && $toDate)
                                <div class="alert alert-info">
                                    Showing results from <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}</strong> to 
                                    <strong>{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</strong>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Sr.No</th>
                                                        <th>Contact Person Name</th>
                                                        <th>Company Name</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Lead Source</th>
                                                        <th>Service / Product</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @forelse($leads as $index => $emp)
                                                        <tr class="text-center">
                                                            <td>{{ $leads->firstItem() + $index }}</td>
                                                            <td>{{ $emp->customer_name ?? '-' }}</td>
                                                            <td>{{ $emp->company_name ?? '-' }}</td>
                                                            <td>{{ $emp->email ?? '-' }}</td>
                                                            <td>{{ $emp->mobile ?? '-' }}</td>
                                                            <td>{{ $emp->lead_source_name ?? '-' }}</td>
                                                            <td>{{ $emp->service_name ?? '-' }}</td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No Leads Found.</td>
                                                        </tr>
                                                    @endforelse
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $leads->links() }}
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
