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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysFollowups as $index => $lead)
                                        <tr class="text-center">
                                            <td>{{ $todaysFollowups->firstItem() + $index }}</td>
                                            <td>{{ $lead->customer_name ?? '-' }}</td>
                                            <td>{{ $lead->company_name ?? '-' }}</td>
                                            <td>{{ $lead->GST_No ?? '-' }}</td>
                                            <td>{{ $lead->email ?? '-' }}</td>
                                            <td>{{ $lead->mobile ?? '-' }}</td>
                                            <td>{{ $lead->service_name ?? '-' }}</td>
                                            <td>{{ $lead->lead_source_name ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No Follow Up Found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $todaysFollowups->links() }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Pipeline Status Update Section --}}
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('clients.followup_update') }}" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 form-group mt-3">
                                        Status <span class="text-danger">*</span>
                                        <select class="form-control" name="status" id="pipeline_status" required>
                                            <option value="">Select Status</option>
                                            @foreach ($leadPipeline as $pipeline)
                                            <option value="{{ $pipeline->pipeline_id }}"
                                                data-followup="{{ $pipeline->followup_needed }}">
                                                {{ $pipeline->pipeline_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-6 form-group mt-3" id="cancelReasonBox"
                                        style="display: none;">
                                        Cancel Reason List <span class="text-danger">*</span>
                                        <select class="form-control" name="cancel_reason_id" id="">
                                            <option value="">Select Cancel Reason List</option>
                                            @foreach ($leadCancelList as $list)
                                            <option value="{{ $list->lead_cancel_reason_id }}">
                                                {{ $list->reason }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-6 form-group mt-3" id="follow_up_dateBox"
                                        style="display: none;">
                                        Date <span class="text-danger">*</span>
                                        <input type="text" id="followup_datetime" name="followup_datetime"
                                            class="form-control" palaceholder="Select Date and Time">
                                    </div>

                                    <div class="col-lg-4 col-md-6 form-group mt-3" id="amountBox"
                                        style="display: none;">
                                        Amount <span class="text-danger">*</span>
                                        <input type="text" class="form-control" name="amount"
                                            placeholder="Enter Amount" maxlength="100" autocomplete="off" autofocus>
                                    </div>

                                    <div class="col-lg-4 col-md-6 form-group mt-3">
                                        Comment <span class="text-danger">*</span>
                                        <input type="text" class="form-control" name="comment"
                                            placeholder="Enter Comment" maxlength="100" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <h5>Lead History</h5>
                                <table class="table table-bordered table-striped table-hover datatable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Sr No</th>
                                            <th>Contact Person Name</th>
                                            <th>Status</th>
                                            <th>GST No</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Service / Product</th>
                                            <th>Lead Source</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysFollowups as $index => $lead)
                                        <tr class="text-center">
                                            <td>{{ $todaysFollowups->firstItem() + $index }}</td>
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
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No Follow Up Found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $todaysFollowups->links() }}
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

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#followup_datetime", {
        placeholder: "Select Date and Time",
        enableTime: true,
        dateFormat: "d-m-Y h:i K", // h = 12-hour, K = AM/PM
        time_24hr: false
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('pipeline_status');
        const cancelReasonBox = document.getElementById('cancelReasonBox');
        const amountBox = document.getElementById('amountBox');
        const followUpBox = document.getElementById('follow_up_dateBox');

        function toggleFields() {
            const selectedValue = statusSelect.value;
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            const followupNeeded = selectedOption.getAttribute('data-followup');

            // Dynamic logic for amount (Deal Done) and cancel reason (Deal Cancel)
            if (selectedValue === '2') {
                amountBox.style.display = 'block';
                cancelReasonBox.style.display = 'none';
            } else if (selectedValue === '4') {
                amountBox.style.display = 'none';
                cancelReasonBox.style.display = 'block';
            } else {
                amountBox.style.display = 'none';
                cancelReasonBox.style.display = 'none';
            }

            // Only check followup_needed flag
            if (followupNeeded === 'yes') {
                followUpBox.style.display = 'block';
            } else {
                followUpBox.style.display = 'none';
            }
        }

        statusSelect.addEventListener('change', toggleFields);
        toggleFields(); // Run on page load
    });
</script>


@endsection