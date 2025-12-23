@extends('layouts.client')

@section('title', 'Add Lead')

@section('content')


    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                

                <div class="row">
                    <div class="col-lg-12">
                        
                        
                         <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-0">
                            <h5 class="mb-sm-0">Add Lead
                            
                            <a href="{{ route('leads.index') }}" style="float: right;"
                                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    Back
                                </a>
                            </h5>
                          
                               <hr> 
                        
                        
                        
                      
                       
                      
                    
                                <div class="live-preview">
                                    <div class="card-body">
                                    <form action="{{ route('leads.store') }}" method="POST">
                                        @csrf
                                        <div class="row gy-4">
                                            @include('company_client.leads.form')
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
    
    <script>
  function toggleOther(sel, boxId, inputId) {
    const isOther = sel.value === 'other';
    const box = document.getElementById(boxId);
    const input = document.getElementById(inputId);
    box.style.display = isOther ? 'block' : 'none';
    input.toggleAttribute('required', isOther);
    if (!isOther) input.value = '';
  }

  document.addEventListener('DOMContentLoaded', () => {
    const psSel = document.getElementById('product_service_id');
    const lsSel = document.getElementById('LeadSourceId');

    psSel.addEventListener('change', () =>
      toggleOther(psSel, 'product_service_other_box', 'product_service_other'));
    lsSel.addEventListener('change', () =>
      toggleOther(lsSel, 'LeadSource_other_box', 'LeadSource_other'));

    // initialize on load (handles edit + old form state)
    toggleOther(psSel, 'product_service_other_box', 'product_service_other');
    toggleOther(lsSel, 'LeadSource_other_box', 'LeadSource_other');
  });
</script>

@endsection
