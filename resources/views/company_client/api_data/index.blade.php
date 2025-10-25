@extends('layouts.client')
@section('title', 'Add CSV')
@section('content') <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            {{--  <div class="d-flex justify-content-between card-body">
                                <h5 class="card-title mb-0">Upload CSV
                                    <a href="{{ route('lead.csvupload.dummyexcel') }}"
                                        class="btn btn-sm btn-primary text-white">
                                        Sample Excel
                                    </a>

                                </h5>
                                <hr>
                            </div>  --}}
                            <div class="card-body">

                                <p>
                                    <b> IndiaMart WebHook Api </b>
                                    <a href="https://help.indiamart.com/knowledge-base/integration-of-indiamarts-lead-manager-crm-push-api-with-third-party-crms-real-time-push-of-leads/"
                                        target="_blank">( Reference )
                                    </a>

                                    <!-- PDF Icon -->
                                    <a href="{{ route('api_data.pdf.indiamart') }}" target="_blank" class="ms-2"
                                        title="View Request Parameters">
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                    </a>
                                </p>
                                <div class="input-group mb-4">
                                    <input type="text" id="indiamart_url" class="form-control"
                                        value="https://salexo.in/api/webhook/{{ Auth::user()->guid }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard('indiamart_url')" title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>

                                <hr>

                                <p>
                                    <b> General Api </b>
                                    <!-- PDF Icon -->
                                    <a href="{{ route('api_data.pdf.general') }}" target="_blank" class="ms-2"
                                        title="View Request Parameters">
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                    </a>
                                </p>
                                <div class="input-group mb-3">
                                    <input type="text" id="general_url" class="form-control"
                                        value="https://salexo.in/api/inquiry/{{ Auth::user()->guid }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard('general_url')" title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Copy to Clipboard Script --}}
    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");

            // Optional: show a tooltip or alert
            alert("Copied to clipboard: " + copyText.value);
        }
    </script>
@endsection
