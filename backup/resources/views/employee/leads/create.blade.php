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
@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
  // Flatpickr
  flatpickr("#followup_datetime", {
    enableTime: true,
    dateFormat: "d-m-Y h:i K",
    time_24hr: false
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const initially_contacted = document.getElementById('initially_contacted');

    const pipeline_statusDiv = document.getElementById('pipeline_statusDiv');
    const statusSelect = document.getElementById('pipeline_status');

    const commentDiv = document.getElementById('commentDiv');
    const commentSelect = document.getElementById('comment');

    const followUpBox = document.getElementById('follow_up_dateBox');
    const followup_datetimeSelect = document.getElementById('followup_datetime');

    const cancelReasonBox = document.getElementById('cancelReasonBox');
    const cancel_reason_idBox = document.getElementById('cancel_reason_id');

    const amountBox = document.getElementById('amountBox');
    const amountSelect = document.getElementById('Amount');

    function initiallyContacted() {
      const initiallyVal = (initially_contacted?.value || '').trim();

      if (initiallyVal === 'Yes') {
        pipeline_statusDiv.style.display = 'block';
        statusSelect.setAttribute('required', 'required');

        commentDiv.style.display = 'block';
        commentSelect.setAttribute('required', 'required');
      } else {
        pipeline_statusDiv.style.display = 'none';
        statusSelect.removeAttribute('required');
        statusSelect.value = "";

        commentDiv.style.display = 'none';
        commentSelect.removeAttribute('required');
        commentSelect.value = "";

        // Reset dependent fields when "No"
        cancelReasonBox.style.display = 'none';
        followUpBox.style.display = 'none';
        amountBox.style.display = 'none';

        cancel_reason_idBox.removeAttribute('required');
        followup_datetimeSelect.removeAttribute('required');
        amountSelect.removeAttribute('required');

        cancel_reason_idBox.value = "";
        followup_datetimeSelect.value = "";
        amountSelect.value = "";
      }
    }

    function toggleFields() {
      if (!statusSelect) return;

      const selectedOption = statusSelect.options[statusSelect.selectedIndex];
      if (!selectedOption) return;

      const initiallyVal = (initially_contacted?.value || '').trim();
      if (initiallyVal !== 'Yes') return; // stop if Initially Contacted = No

      // ✅ Use slug instead of pipeline name
      const slug = (selectedOption.getAttribute('data-slug') || '').trim(); // ex: deal-done
      const followupNeeded = (selectedOption.getAttribute('data-followup') || '').trim(); // yes/no

      // Reset UI + required
      amountBox.style.display = 'none';
      cancelReasonBox.style.display = 'none';
      followUpBox.style.display = 'none';

      amountSelect.removeAttribute('required');
      cancel_reason_idBox.removeAttribute('required');
      followup_datetimeSelect.removeAttribute('required');

      // Slug based rules
      if (slug === 'deal-done') {
        amountBox.style.display = 'block';
        amountSelect.setAttribute('required', 'required');
      } else if (slug === 'deal-cancel') {
        cancelReasonBox.style.display = 'block';
        cancel_reason_idBox.setAttribute('required', 'required');
      }

      // Followup rules
      if (slug === 'deal-pending' || followupNeeded === 'yes') {
        followUpBox.style.display = 'block';
        followup_datetimeSelect.setAttribute('required', 'required');
      }
    }

    // Events
    initially_contacted?.addEventListener('change', function () {
      initiallyContacted();
      toggleFields();
    });

    statusSelect?.addEventListener('change', toggleFields);

    // Init
    initiallyContacted();
    if (statusSelect && statusSelect.value) toggleFields();
  });
</script>
@endsection
