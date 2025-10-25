@extends('layouts.client')

@section('title', "Today's Follow Up List")

@section('content')
    @php
        $profileId = Request::segment(3);
    @endphp

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title mb-0">Today's Follow Up List</h5>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <h5>Lead Details</h5>
                                    <table class="table table-bordered table-striped table-hover datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Sr No</th>
                                                <th>Contact Person Name</th>
                                                <th>Company Name</th>
                                                <th>GST No</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Service / Product</th>
                                                <th>Lead Source</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp
                                            @forelse($paginated as $index => $lead)
                                                <tr class="text-center">
                                                    <td>{{ $i }}</td>
                                                    <td>{{ $lead->customer_name ?? '-' }}</td>
                                                    <td>{{ $lead->company_name ?? '-' }}</td>
                                                    <td>{{ $lead->GST_No ?? '-' }}</td>
                                                    <td>{{ $lead->email ?? '-' }}</td>
                                                    <td>{{ $lead->mobile ?? '-' }}</td>
                                                    <td>{{ $lead->service_name ?? '-' }}</td>
                                                    <td>{{ $lead->lead_source_name ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('clients.followup_detail', $lead->lead_id) }}"
                                                            class="btn btn-sm btn-success" title="Add Followup">
                                                            <i class="fa fa-plus"></i> {{-- You can change to another icon if needed --}}
                                                        </a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $i++;
                                                @endphp
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No Follow Up Found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $paginated->links() }}
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
