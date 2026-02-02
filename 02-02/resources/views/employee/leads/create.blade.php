@extends('layouts.client')

@section('title', 'Add Lead')

@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Add Lead</h4>
                            <div class="page-title-right">
                                <a href="{{ route('employee.leads.index') }}"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="live-preview">

                                    <form action="{{ route('employee.leads.store') }}" method="POST">
                                        @csrf
                                        <div class="row gy-4">
                                            @include('employee.leads.form')
                                        </div>
                                        <div class="card-footer mt-2">
                                            <div class="mb-3" style="float: right;">
                                                <button type="submit"
                                                    class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                                <button type="reset"
                                                    class="btn btn-primary float-right mr-3 mb-3 mx-2">Clear</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--  @section('scripts')
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
            const initially_contacted = document.getElementById('initially_contacted');
            const pipeline_status = document.getElementById('pipeline_statusDiv');
            const commentDiv = document.getElementById('commentDiv');
            const followUpBox = document.getElementById('follow_up_dateBox');
            const statusSelect = document.getElementById('pipeline_status');
            const commentSelect = document.getElementById('comment');
            const amountSelect = document.getElementById('Amount');
            const followup_datetimeSelect = document.getElementById('followup_datetime');

            function initiallyContacted() {
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                const selectedText = selectedOption.text;
                const initially_contactedValue = initially_contacted.value;

                if (initially_contactedValue === 'Yes') {
                    pipeline_status.style.display = 'block';
                    statusSelect.setAttribute('required', 'required');
                    commentDiv.style.display = 'block';
                    commentSelect.setAttribute('required', 'required');
                } else {
                    pipeline_status.style.display = 'none';
                    statusSelect.removeAttribute('required');
                    commentDiv.style.display = 'none';
                    commentSelect.removeAttribute('required');
                }

                if (selectedText === 'Deal Done') {
                    cancelReasonBox.style.display = 'none';
                } else if (selectedText === 'Deal Pending') {
                    followUpBox.style.display = 'block';
                } else if (selectedText === 'Deal Cancel') {
                    cancelReasonBox.style.display = 'block';
                } else {
                    cancelReasonBox.style.display = 'none';
                    followUpBox.style.display = 'none';
                }
            }

            const cancelReasonBox = document.getElementById('cancelReasonBox');
            const amountBox = document.getElementById('amountBox');
            const cancel_reason_idBox = document.getElementById('cancel_reason_id');

            function toggleFields() {
                const selectedValue = statusSelect.value;
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                const selectedText = selectedOption.text;
                const followupNeeded = selectedOption.getAttribute('data-followup');

                // Reset required state for amount
                amountSelect.removeAttribute('required');
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
                    cancelReasonBox.style.display = 'block';
                    cancel_reason_idBox.setAttribute('required', 'required');
                    cancelReasonBox.setAttribute('required', 'required');
                } else {
                    amountBox.style.display = 'none';
                    cancelReasonBox.style.display = 'none';
                }

                // Only check followup_needed flag
                //if (followupNeeded === 'yes') {
                if (selectedText === 'Deal Pending' || followupNeeded === 'yes') {
                    followUpBox.style.display = 'block';
                    followup_datetimeSelect.setAttribute('required', 'required');
                } else {
                    followUpBox.style.display = 'none';
                }
            }

            initially_contacted.addEventListener('change', initiallyContacted);
            statusSelect.addEventListener('change', toggleFields);
            toggleFields(); // Run on page load
            initiallyContacted(); // Run on page load
        });
    </script>
@endsection  --}}

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#followup_datetime", {
            enableTime: true,
            dateFormat: "d-m-Y h:i K",
            time_24hr: false
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initially_contacted = document.getElementById('initially_contacted');
            const pipeline_status = document.getElementById('pipeline_statusDiv');
            const commentDiv = document.getElementById('commentDiv');
            const followUpBox = document.getElementById('follow_up_dateBox');
            const statusSelect = document.getElementById('pipeline_status');
            const commentSelect = document.getElementById('comment');
            const amountSelect = document.getElementById('Amount');
            const followup_datetimeSelect = document.getElementById('followup_datetime');
            const cancelReasonBox = document.getElementById('cancelReasonBox');
            const amountBox = document.getElementById('amountBox');
            const cancel_reason_idBox = document.getElementById('cancel_reason_id');

            function initiallyContacted() {
                const initially_contactedValue = initially_contacted.value;

                if (initially_contactedValue === 'Yes') {
                    pipeline_status.style.display = 'block';
                    statusSelect.setAttribute('required', 'required');
                    commentDiv.style.display = 'block';
                    commentSelect.setAttribute('required', 'required');
                } else {
                    pipeline_status.style.display = 'none';
                    statusSelect.removeAttribute('required');
                    commentDiv.style.display = 'none';
                    commentSelect.removeAttribute('required');

                    // Reset dependent fields
                    cancelReasonBox.style.display = 'none';
                    followUpBox.style.display = 'none';
                    amountBox.style.display = 'none';
                }
            }

            function toggleFields() {
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                if (!selectedOption) return;

                const selectedText = selectedOption.text;
                const followupNeeded = selectedOption.getAttribute('data-followup');
                const initially_contactedValue = initially_contacted.value;

                // Reset
                amountBox.style.display = 'none';
                cancelReasonBox.style.display = 'none';
                followUpBox.style.display = 'none';

                amountSelect.removeAttribute('required');
                cancel_reason_idBox.removeAttribute('required');
                followup_datetimeSelect.removeAttribute('required');

                if (initially_contactedValue !== 'Yes') {
                    return; // ⛔ Stop here if 'No'
                }

                if (selectedText === 'Deal Done') {
                    amountBox.style.display = 'block';
                    amountSelect.setAttribute('required', 'required');
                } else if (selectedText === 'Deal Cancel') {
                    cancelReasonBox.style.display = 'block';
                    cancel_reason_idBox.setAttribute('required', 'required');
                }

                if (selectedText === 'Deal Pending' || followupNeeded === 'yes') {
                    followUpBox.style.display = 'block';
                    followup_datetimeSelect.setAttribute('required', 'required');
                }
            }


            // Attach listeners
            initially_contacted.addEventListener('change', function() {
                initiallyContacted();
                toggleFields();
            });

            statusSelect.addEventListener('change', toggleFields);

            // Run once on load
            initiallyContacted();

            // Only run toggleFields if status has a value
            if (statusSelect.value) {
                toggleFields();
            }
        });
    </script>

@endsection
