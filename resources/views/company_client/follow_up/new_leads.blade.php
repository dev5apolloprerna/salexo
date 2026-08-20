@extends('layouts.client')
@section('title', 'New Lead List')
@section('content')

    <?php
    $profileId = Request::segment(2);
    $leadPipeline = App\Models\LeadPipeline::where([
        'slugname' => $profileId,
        'company_id' => Auth::user()->company_id,
    ])->first();
    ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                
                            
                      
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">

                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    {{ $leadPipeline->pipeline_name }} List
                                </h5>
                            </div>
                            <div class="card-body border-bottom">
<form action="{{ route('clients.new_lead', $profileId) }}" method="GET" class="row g-3 align-items-end">
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
                                            <a href="{{ route('clients.new_lead', array_filter([
                                                'status' => $profileId,
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
                                    Showing leads matching the dashboard filters
                                    @if ($fromDate || $toDate)
                                        ({{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Beginning' }}
                                        to {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Today' }})
                                    @endif.
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
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Service / Product</th>
                                                        @if (!in_array($status, ['deal-done']))
                                                            <th>Followup Date</th>
                                                        @endif
                                                        <th>Lead Source</th>
                                                        @if ($status === 'deal-done')
                                                            <th>Lead Done Date</th>
                                                        @endif
                                                        @if ($status === 'deal-cancel')
                                                            <th>Lead Cancel Date</th>
                                                        @endif
                                                        <th>Entry Date</th>
                                                        <th>Status</th>
                                                        <th>Created By</th>
                                                        <th>Assigned To</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 1; ?>

                                                    @forelse($leads as $index => $lead)
                                                        <tr class="text-center">
                                                            <td>{{ $leads->firstItem() + $index }}
                                                            </td>
                                                            <td>{{ $lead->company_name ?? '-' }}</td>
                                                            <td>{{ $lead->customer_name ?? '-' }}</td>
                                                            <td>{{ $lead->email ?? '-' }}</td>
                                                            <td>{{ $lead->mobile ?? '-' }}</td>
                                                            <td>{{ $lead->service_name ?? '-' }}</td>
                                                            @if (!in_array($status, ['deal-done']))
                                                                <td>{{ $lead->next_followup_date ?? '-' }}</td>
                                                            @endif
                                                            <td>
                                                                {{ $lead->lead_source_name ?? '' }}
                                                            </td>
                                                            @if ($status === 'deal-done')
                                                                <td>
                                                                    {{ $lead->deal_done_at ? date('d-m-Y H:i', strtotime($lead->deal_done_at)) : '-' }}
                                                                </td>
                                                            @endif
                                                            @if ($status === 'deal-cancel')
                                                                <td>
                                                                    {{ $lead->deal_cancel_at ? date('d-m-Y H:i', strtotime($lead->deal_cancel_at)) : '-' }}
                                                                </td>
                                                            @endif
                                                                <td>
                                                                    {{ date('d-m-Y h:i A',strtotime($lead->created_at)) }}
                                                                </td>
                                                                <td>{{ $lead->status_name ?? '-' }}</td>
                                                                <td>{{ $lead->added_by_name ?? '-' }}</td>
                                                                <td>{{ $lead->assigned_to_name ?? '-' }}</td>
                                                            @if ($profileId === 'new-lead')
                                                                <td>
                                                                    <a href="{{ route('clients.followup_detail', [$status, $lead->lead_id]) }}"
                                                                        class="btn btn-sm btn-success" title="Add Followup">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            @else
                                                                {{--  @if ($leadPipeline->followup_needed == 'yes')  --}}
                                                                <td>
                                                                    <a href="{{ route('clients.followup_detail', [$status, $lead->lead_id]) }}"
                                                                        class="btn btn-sm btn-success" title="Add Followup">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                                {{--  @endif  --}}
                                                            @endif

                                                        </tr>
                                                        <?php $i++; ?>
                                                    @empty
                                                        <tr>
                                                            <td colspan="13" class="text-center">No Follow Up Found.</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection