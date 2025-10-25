@extends('layouts.client')

@section('title', 'Follow Up Detail')

@section('content')
    <style>
    .card-header {
        /*background-color: #f8f9fa;*/
        padding: 20px 25px;
    }

    .card-header .d-flex {
        margin-bottom: 8px;
    }

    .card-header strong {
        min-width: 140px;
    }

    .text-primary {
        font-weight: 600;
    }

    .card {
        border: 1px solid #c1c1c1 !important;
        border-radius: 1rem !important;
        box-shadow: 10px 14px 20px rgb(8 8 8 / 9%) !important;
        position: relative !important;
        height: 100% !important;
        margin-bottom: 0 !important;
        overflow: hidden;
    }
</style>
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row mb-4">
                    
                    @include('common.alert')

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    @if($status == "over-due")
                        <div class="col-12 mb-3 d-flex justify-content-end">
                            <a href="{{ route('employee.over_due_followup') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                Back
                            </a>
                        </div>
                    @elseif($status == "todays-followup")
                        <div class="col-12 mb-3 d-flex justify-content-end">
                            <a href="{{ route('employee.todays_followup') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                Back
                            </a>
                        </div>
                    @else
                        <div class="col-12 mb-3 d-flex justify-content-end">
                            <a href="{{ route('employee.status',$status) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                Back
                            </a>
                        </div>
                    @endif
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <h5 class="mb-3 ">Lead Details</h5>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="d-flex">
                                            <strong class="w-50">Follow Up for:</strong>
                                            <span>{{ $lead->customer_name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Email:</strong>
                                            <span>{{ $lead->email ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Lead Source:</strong>
                                            <span>{{ $lead->lead_source_name ?? '-' }}</span>
                                        </div>
    
                                        <div class="d-flex">
                                            <strong class="w-50">Company Name:</strong>
                                            <span>{{ $lead->company_name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Mobile:</strong>
                                            <span>{{ $lead->mobile ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Service / Product:</strong>
                                            <span>{{ $lead->service_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="col-lg-12">-->
                    <!--    <div class="card">-->
                    <!--        <div class="card-header">-->
                    <!--            <h5 class="mb-0">Follow Up for {{ $lead->customer_name }}</h5>-->
                    <!--        </div>-->
                    <!--        <div class="card-body">-->
                    <!--            {{-- Paste pipeline form here (as-is from the original page) --}}-->
                    <!--            {{-- Ensure form passes hidden field: --}}-->
                    <!--            <form method="POST" action="{{ route('clients.followup_update') }}" autocomplete="off">-->
                    <!--                @csrf-->
                    <!--                <input type="hidden" name="lead_id" value="{{ $id }}">-->
                    <!--                <div class="row mb-3">-->
                    <!--                    <div class="col-lg-4 col-md-6 form-group mt-3">-->
                    <!--                        Status <span class="text-danger">*</span>-->
                    <!--                        <select class="form-control" name="status" id="pipeline_status" required>-->
                    <!--                            <option value="">Select Status</option>-->
                    <!--                            @foreach ($leadPipeline as $pipeline)-->
                    <!--                                <option value="{{ $pipeline->pipeline_id }}"-->
                    <!--                                    data-followup="{{ $pipeline->followup_needed }}">-->
                    <!--                                    {{ $pipeline->pipeline_name }}-->
                    <!--                                </option>-->
                    <!--                            @endforeach-->
                    <!--                        </select>-->
                    <!--                    </div>-->

                    <!--                    <div class="col-lg-4 col-md-6 form-group mt-3" id="cancelReasonBox"-->
                    <!--                        style="display: none;">-->
                    <!--                        Cancel Reason List <span class="text-danger">*</span>-->
                    <!--                        <select class="form-control" name="cancel_reason_id" id="cancel_reason_id">-->
                    <!--                            <option value="">Select Cancel Reason List</option>-->
                    <!--                            @foreach ($leadCancelList as $list)-->
                    <!--                                <option value="{{ $list->lead_cancel_reason_id }}">-->
                    <!--                                    {{ $list->reason }}-->
                    <!--                                </option>-->
                    <!--                            @endforeach-->
                    <!--                        </select>-->
                    <!--                    </div>-->

                    <!--                    <div class="col-lg-4 col-md-6 form-group mt-3" id="follow_up_dateBox"-->
                    <!--                        style="display: none;">-->
                    <!--                        Date <span class="text-danger">*</span>-->
                    <!--                        <input type="text" id="followup_datetime" name="followup_datetime"-->
                    <!--                            class="form-control" palaceholder="Select Date and Time">-->
                    <!--                    </div>-->

                    <!--                    <div class="col-lg-4 col-md-6 form-group mt-3" id="amountBox"-->
                    <!--                        style="display: none;">-->
                    <!--                        Amount <span class="text-danger">*</span>-->
                    <!--                        <input type="text" class="form-control" name="amount" id="amount"-->
                    <!--                            placeholder="Enter Amount" maxlength="100" autocomplete="off" autofocus>-->
                    <!--                    </div>-->

                    <!--                    <div class="col-lg-4 col-md-6 form-group mt-3">-->
                    <!--                        Comment <span class="text-danger">*</span>-->
                    <!--                        <input type="text" class="form-control" name="comment"-->
                    <!--                            placeholder="Enter Comment" maxlength="100" autocomplete="off" required>-->
                    <!--                    </div>-->
                    <!--                </div>-->

                    <!--                <div class="modal-footer">-->
                    <!--                    <div class="hstack gap-2 justify-content-end">-->
                    <!--                        <button type="submit" class="btn btn-primary">Submit</button>-->
                    <!--                        <button type="reset" class="btn btn-secondary">Reset</button>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--            </form>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    @if ($status != 'deal-done' && $status != 'deal-cancel')
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Follow-Up</h5>
                            <hr>
                            <form method="POST" action="{{ route('employee.followup_update') }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="lead_status" value="{{ $status }}">
                                <input type="hidden" name="lead_id" value="{{ $id }}">
                                <div class="row mb-3">
                                    <div class="col-lg-12 form-group mt-3">
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

                                    <div class="col-lg-12 form-group mt-3" id="cancelReasonBox"
                                        style="display: none;">
                                        Cancel Reason List <span class="text-danger">*</span>
                                        <select class="form-control" name="cancel_reason_id" id="cancel_reason_id">
                                            <option value="">Select Cancel Reason List</option>
                                            @foreach ($leadCancelList as $list)
                                            <option value="{{ $list->lead_cancel_reason_id }}">
                                                {{ $list->reason }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-12 form-group mt-3" id="follow_up_dateBox"
                                        style="display: none;">
                                        Date <span class="text-danger">*</span>
                                        <input type="text" id="followup_datetime" name="followup_datetime"
                                            class="form-control" palaceholder="Select Date and Time">
                                    </div>

                                    <div class="col-lg-12 form-group mt-3" id="amountBox"
                                        style="display: none;">
                                        Amount <span class="text-danger">*</span>
                                        <input type="text" class="form-control" name="amount" id="amount"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            placeholder="Enter Amount" maxlength="7" autocomplete="off" autofocus>
                                    </div>

                                    <div class="col-lg-12 form-group mt-3">
                                        Comment <span class="text-danger">*</span>
                                        <textarea class="form-control" name="comment" id="comment" cols="30" rows="5"></textarea>

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
                @endif
                    
                </div>


                {{-- Pipeline Status Update Section --}}
                {{--  <div class="row mt-4">
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
                </div>  --}}

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Follow-Up History</h5>
                            </div>
                            <div class="">
                                <div class="table-responsive">
                                    
                                    <table class="table table-bordered table-striped table-hover datatable">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Sr No</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Comment</th>
                                                <th>Reason</th>
                                                <th>Amount</th>
                                                <th>Follow Up Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($lead_history as $index => $lead)
                                                <tr class="text-center">
                                                    <td>{{ $lead_history->firstItem() + $index }}</td>
                                                    <td>{{ date('d-m-Y H:i:s', strtotime($lead->created_at)) }}</td>
                                                    <td>{{ $lead->pipeline_name ?? '-' }}</td>
                                                    <td>{{ $lead->Comments ?? '-' }}</td>
                                                    <td>{{ $lead->reason ?? '-' }}</td>
                                                    <td>{{ $lead->amount == '0' ? '-' : $lead->amount }}</td>
                                                    <td>{{ $lead->next_followup_date ?? '-' }}</td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No Follow Up Found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $lead_history->links() }}
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
            const amountSelect = document.getElementById('amount');
            const followup_datetimeSelect = document.getElementById('followup_datetime');
            const cancel_reason_idBox = document.getElementById('cancel_reason_id');


            function toggleFields() {
                const selectedValue = statusSelect.value;
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                const selectedText = selectedOption.text;
                const followupNeeded = selectedOption.getAttribute('data-followup');

                amountSelect.removeAttribute('required');
                followup_datetimeSelect.removeAttribute('required');
                cancel_reason_idBox.removeAttribute('required');


                // Dynamic logic for amount (Deal Done) and cancel reason (Deal Cancel)
                if (selectedText === 'Deal Done') {
                    amountBox.style.display = 'block';
                    amountSelect.setAttribute('required', 'required');
                    cancelReasonBox.style.display = 'none';
                } else if (selectedText === 'Deal Cancel') {
                    amountBox.style.display = 'none';
                    cancel_reason_idBox.setAttribute('required', 'required');
                    cancelReasonBox.style.display = 'block';
                } else {
                    amountBox.style.display = 'none';
                    cancelReasonBox.style.display = 'none';
                }

                // Only check followup_needed flag
                if (selectedText === 'Deal Pending' || followupNeeded === 'yes') {
                    followUpBox.style.display = 'block';
                    followup_datetimeSelect.setAttribute('required', 'required');
                } else {
                    followUpBox.style.display = 'none';
                }
            }

            statusSelect.addEventListener('change', toggleFields);
            toggleFields(); // Run on page load
        });
    </script>
@endsection
