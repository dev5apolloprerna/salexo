@extends('layouts.client')

@section('title', 'Lead History')

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

                <div class="row">
                    <div class="col-lg-12 mb-5">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <h5 class="mb-3 ">Lead Details</h5>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <strong class="w-50">Company name:</strong>
                                            <span>{{ $lead->company_name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">GST no:</strong>
                                            <span>{{ empty($lead->GST_No) || $lead->GST_No === 'NULL' || $lead->GST_No == 0 ? '-' : $lead->GST_No }}</span>

                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Customer name:</strong>
                                            <span>{{ $lead->customer_name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Email:</strong>
                                            <span>{{ $lead->email ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Mobile:</strong>
                                            <span>{{ $lead->mobile ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Alternative No:</strong>
                                            <span>{{ $lead->alternative_no ?? '-' }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <strong class="w-50">Address:</strong>
                                            <span>{{ $lead->address ?? '-' }}</span>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="d-flex">
                                            <strong class="w-50">Follow Up for:</strong>
                                            <span>{{ $lead->customer_name ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <strong class="w-50">Lead Source:</strong>
                                            <span>{{ $lead->lead_source_name ?? '-' }}</span>
                                        </div>


                                        <div class="d-flex">
                                            <strong class="w-50">Service / Product:</strong>
                                            <span>{{ $lead->service_name ?? '-' }}</span>
                                        </div>

                                        @if ($leadUdfData->count() > 0)
                                            @foreach ($leadUdfData as $data)
                                                <div class="d-flex">
                                                    <strong class="w-50">{{ $data->label }}:</strong>
                                                    <span>{{ $data->value ?? '-' }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="d-flex justify-content-between align-items-center card-header">
                                <h5>Lead History</h5>
                                @if ($status == 'active-lead')
                                    <a href="{{ route('leads.index') }}" style="float: right;"
                                        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                        Back
                                    </a>
                                @elseif($status == 'lead-done')
                                    <a href="{{ route('leads.done') }}" style="float: right;"
                                        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                        Back
                                    </a>
                                @else
                                    <a href="{{ route('leads.cancel') }}" style="float: right;"
                                        class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                        Back
                                    </a>
                                @endif
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
                                                @if (!in_array($status, ['active-lead', 'lead-done']))
                                                    <th>Reason</th>
                                                @endif
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
                                                    @if (!in_array($status, ['active-lead', 'lead-done']))
                                                        <td>{{ $lead->reason ?? '-' }}</td>
                                                    @endif
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
