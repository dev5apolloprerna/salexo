@extends('layouts.client')
@section('title', 'New Lead List')
@section('content')

    <?php
    $profileId = Request::segment(2);
    $leadPipeline = App\Models\LeadPipeline::where(['slugname' => $profileId, 'company_id' => Auth::user()->company_id])->first();
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
                                <form action="{{ route('employee.status', $profileId) }}" method="POST" class="row g-3 align-items-end">
                                    @csrf
                                    <div class="col-md-4">
                                        <label for="search" class="form-label">Search by Company Name or Contact Person Name</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="Enter company name or contact person name" 
                                               value="{{ old('search', request('search')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('employee.status', $profileId) }}" class="btn btn-secondary">Reset</a>
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
                                                    <tr class="text-center">
                                                        <th>Sr No</th>
                                                        <th>Contact Person Name</th>
                                                        <th>Company Name</th>
                                                        <th>GST No</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Service / Product</th>
                                                        <th>Followup Date</th>
                                                        <th>Lead Source</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 1; ?>

                                                    @forelse($leads as $lead)
                                                        <tr class="text-center">
                                                            <td>{{ $i }}
                                                            </td>
                                                            <td>{{ $lead->customer_name ?? '-' }}</td>
                                                            <td>{{ $lead->company_name ?? '-' }}</td>
                                                            <td>{{ $lead->GST_No ?? '-' }}</td>
                                                            <td>{{ $lead->email ?? '-' }}</td>
                                                            <td>{{ $lead->mobile ?? '-' }}</td>
                                                            <td>{{ $lead->service_name ?? '-' }}</td>
                                                            <td>{{ $lead->next_followup_date ?? '-' }}</td>
                                                            <td>
                                                                {{ $lead->lead_source_name ?? '' }}
                                                            </td>

                                                            @if ($profileId === 'new-lead')
                                                                <td>
                                                                    <a href="{{ route('employee.followup_detail',[$status, $lead->lead_id]) }}"
                                                                        class="btn btn-sm btn-success" title="Add Followup">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </td>
                                                            @else
                                                                <!-- @if ($leadPipeline->followup_needed == 'yes') -->
                                                                    <td>
                                                                        <a href="{{ route('employee.followup_detail', [$status, $lead->lead_id]) }}"
                                                                            class="btn btn-sm btn-success"
                                                                            title="Add Followup">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </td>
                                                                <!-- @endif -->
                                                            @endif

                                                        </tr>
                                                        <?php $i++; ?>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No Follow Up Found.</td>
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
