@extends('layouts.client')
@section('title', 'Over Due Follow Up List')
@section('content')

    <?php
    $url1 = Request::segment(2);
    $url2 = Request::segment(3);
    
    ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages   --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title mb-0"> 
                                    Over Due Follow Up List
                                </h5>
                            </div>
                            <div class="card-body border-bottom">
                                    <form action="{{ route('clients.over_due_followup') }}" method="GET" class="row g-3 align-items-end">
                                    @if ($filterEmpId)
                                        <input type="hidden" name="emp_id" value="{{ $filterEmpId }}">
                                    @endif
                                    @if ($fromDate)
                                        <input type="hidden" name="from_date" value="{{ $fromDate }}">
                                    @endif
                                    @if ($toDate)
                                        <input type="hidden" name="to_date" value="{{ $toDate }}">
                                    @endif
                                    <div class="col-md-4">
                                        <label for="search" class="form-label">Search by Company Name or Contact Person Name</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="Enter company name or contact person name" 
                                               value="{{ old('search', request('search')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('clients.over_due_followup', array_filter([
                                                'emp_id' => $filterEmpId,
                                                'from_date' => $fromDate,
                                                'to_date' => $toDate,
                                            ], fn ($value) => filled($value))) }}" class="btn btn-secondary">Reset Search</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                             @if ($filterEmpId || $fromDate || $toDate)
                                <div class="alert alert-info mx-3 mt-3 mb-0">
                                    Showing overdue follow-ups matching the dashboard employee and lead entry date filters.
                                    <a href="{{ route('userhome', array_filter([
                                        'emp_id' => $filterEmpId,
                                        'from_date' => $fromDate,
                                        'to_date' => $toDate,
                                    ], fn ($value) => filled($value))) }}" class="alert-link">Back to dashboard</a>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Sr No</th>
                                                        <th>Company Name</th>
                                                        <th>Contact Person Name</th>
                                                        <th>GST No</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Service / Product</th>
                                                        <th>Followup Date</th>
                                                        <th>Lead Source</th>
                                                        <th>Status</th>
                                                        <th>Created By</th>
                                                        <th>Assigned To</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($paginated as $index => $lead)
                                                        <tr class="text-center">
                                                            <td>{{ $paginated->firstItem() + $index }}
                                                            </td>
                                                            <td>{{ $lead->company_name ?? '-' }}</td>
                                                            <td>{{ $lead->customer_name ?? '-' }}</td>
                                                            <td>{{ $lead->GST_No ?? '-' }}</td>
                                                            <td>{{ $lead->email ?? '-' }}</td>
                                                            <td>{{ $lead->mobile ?? '-' }}</td>
                                                            <td>{{ $lead->service_name ?? '-' }}</td>
                                                            <td>{{ $lead->next_followup_date ?? '-' }}</td>
                                                            <td>
                                                                {{ $lead->lead_source_name ?? '' }}
                                                            </td>
                                                            <td>{{ $lead->status_name ?? '-' }}</td>
                                                            <td>{{ $lead->added_by_name ?? '-' }}</td>
                                                            <td>{{ $lead->assigned_to_name ?? '-' }}</td>
                                                            <td>
                                                                <a href="{{ route('clients.followup_detail', ['over-due', $lead->lead_id]) }}"
                                                                    class="btn btn-sm btn-success" title="Add Followup">
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="13" class="text-center">No Follow Up Found.</td>
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
        </div>
    </div>

@endsection