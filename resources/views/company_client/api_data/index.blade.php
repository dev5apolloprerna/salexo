@extends('layouts.client')
@section('title', 'Add Api')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                {{-- ========== IndiaMart WebHook API ========== --}}
                                <p class="d-flex align-items-center justify-content-between">
                                    <span>
                                        <b>IndiaMart WebHook Api</b>
                                        <a href="https://help.indiamart.com/knowledge-base/integration-of-indiamarts-lead-manager-crm-push-api-with-third-party-crms-real-time-push-of-leads/"
                                           target="_blank">
                                            ( Reference )
                                        </a>

                                        <!-- PDF Icon -->
                                        <a href="{{ route('api_data.pdf.indiamart') }}"
                                           target="_blank"
                                           class="ms-2"
                                           title="View Request Parameters">
                                            <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                        </a>
                                    </span>

                                    <!-- Settings icon (NO source dropdown for this one) -->
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Settings"
                                            data-bs-toggle="modal"
                                            data-bs-target="#apiSettingsModal"
                                            data-api-name="indiamart"
                                            data-api-id="1"
                                            data-show-source="0"
                                            data-employee-id="{{ $indiamartSettings->emp_id ?? '' }}"
                                            data-source-id="">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </p>

                                <div class="input-group mb-4">
                                    <input type="text"
                                           id="indiamart_url"
                                           class="form-control"
                                           value="https://salexo.in/api/webhook/{{ Auth::user()->guid }}"
                                           readonly>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="copyToClipboard('indiamart_url')"
                                            title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>

                                <hr>

                                {{-- ========== General API ========== --}}
                                <p class="d-flex align-items-center justify-content-between">
                                    <span>
                                        <b>General Api</b>
                                        <!-- PDF Icon -->
                                        <a href="{{ route('api_data.pdf.general') }}"
                                           target="_blank"
                                           class="ms-2"
                                           title="View Request Parameters">
                                            <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                        </a>
                                    </span>

                                    <!-- Settings icon (show source dropdown) -->
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Settings"
                                            data-bs-toggle="modal"
                                            data-bs-target="#apiSettingsModal"
                                            data-api-name="general"
                                            data-api-id="2"
                                            data-show-source="0"
                                            data-employee-id="{{ $generalSettings->emp_id ?? '' }}"
                                            data-source-id="{{ $generalSettings->source_id ?? '' }}">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </p>

                                <div class="input-group mb-3">
                                    <input type="text"
                                           id="general_url"
                                           class="form-control"
                                           value="https://salexo.in/api/inquiry/{{ Auth::user()->guid }}"
                                           readonly>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="copyToClipboard('general_url')"
                                            title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>

                                <hr>

                                {{-- ========== Meta API ========== --}}
                                <p class="d-flex align-items-center justify-content-between">
                                    <span>
                                        <b>Meta Api</b>
                                    </span>

                                    <span class="d-flex gap-2">
                                        <!-- Settings icon (employee/source mapping) -->

                                        <a href="{{ route('api_data.meta.index') }}"
                                           class="btn btn-sm btn-outline-secondary me-2"
                                           title="Meta API Advanced Settings">
                                            <i class="fas fa-user"></i>
                                        </a>

                                       <!--  <button type="button"
                                                class="btn btn-sm btn-outline-secondary me-2"
                                                title="Settings"
                                                data-bs-toggle="modal"
                                                data-bs-target="#apiSettingsModal"
                                                data-api-name="meta"
                                                data-api-id="3"
                                                data-ad-id="{{ $metaSettings->ad_id ?? '' }}"
                                                data-show-source="1"
                                                data-employee-id="{{ $metaSettings->emp_id ?? '' }}"
                                                data-source-id="{{ $metaSettings->source_id ?? '' }}"
                                                data-product-id="{{ $metaSettings->product_id ?? '' }}"
                                                >
                                            <i class="fas fa-user"></i>
                                        </button> -->

                                        <!-- User icon for access token + verify token -->
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Meta Token Settings"
                                                data-bs-toggle="modal"
                                                data-bs-target="#metaTokenModal"
                                                data-access-token="{{ $metaSettings->company->access_token ?? '' }}"
                                                data-verify-token="{{ $metaSettings->company->verify_token ?? '' }}">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                    </span>
                                </p>

                                <div class="input-group mb-3">
                                    <input type="text"
                                           id="meta_url"
                                           class="form-control"
                                           value="https://salexo.in/api/meta/webhook/{{ Auth::user()->guid }}"
                                           readonly>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="copyToClipboard('meta_url')"
                                            title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>

                            </div> {{-- card-body --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== Common Settings Modal (Employee + Source + Product + API ID + Radio) ========== -->
    <div class="modal fade" id="apiSettingsModal" tabindex="-1" aria-labelledby="apiSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="apiSettingsModalLabel">API Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="settings_api_name">

                    <!-- Assign Type -->
                    <div class="mb-3">
                        <label class="form-label">Assignment Type</label><br>

                        <label class="me-3">
                            <input type="radio" name="assign_type" value="single" checked> Single
                        </label>

                        <label>
                            <input type="radio" name="assign_type" value="multiple"> Multiple
                        </label>
                    </div>

                    <!-- Source (General + Meta only) -->
                    <div class="mb-3" id="settings_source_group">
                        <label for="settings_source_id" class="form-label">Lead Source</label>
                        <select id="settings_source_id" class="form-control">
                            <option value="">Select Source</option>
                            @foreach($leadSources ?? [] as $src)
                                <option value="{{ $src->lead_source_id }}">{{ $src->lead_source_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee -->
                    <div class="mb-3">
                        <label for="settings_employee_id" class="form-label">Employee</label>
                        <select id="settings_employee_id" class="form-control">
                            <option value="">Select Employee</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->emp_id }}">{{ $emp->emp_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product (Meta only) -->
                    <div class="mb-3" id="settings_product_group">
                        <label for="settings_product_id" class="form-label">Product</label>
                        <select id="settings_product_id" class="form-control">
                            <option value="">Select Product</option>
                            @foreach($product ?? [] as $prd)
                                <option value="{{ $prd->service_id }}">{{ $prd->service_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- API ID (Meta only) -->
                    <div class="mb-3" id="settings_apiid_group">
                        <label for="setting_ad_id" class="form-label">AD ID</label>
                        <input type="text" id="setting_ad_id" class="form-control" placeholder="Enter Ad Id">
                    </div>

                    <small class="text-muted">
                        These settings auto-attach employees/sources/products to leads from this API.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveApiSettings()">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- ========== Meta Token Modal (Access + Verify Token) ========== --}}
    <div class="modal fade" id="metaTokenModal" tabindex="-1" aria-labelledby="metaTokenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="metaTokenModalLabel">Meta Token Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Access Token --}}
                    <div class="mb-3">
                        <label for="meta_access_token" class="form-label">Access Token</label>
                        <input type="text"
                               id="meta_access_token"
                               class="form-control"
                               placeholder="Enter page access token">
                    </div>

                    {{-- Verify Token (text input) --}}
                    <div class="mb-3">
                        <label for="meta_verify_token" class="form-label">Verify Token</label>
                        <input type="text"
                               id="meta_verify_token"
                               class="form-control"
                               placeholder="Enter verify token">
                    </div>

                    <small class="text-muted">
                        These tokens will be used for Meta webhook verification and lead fetching.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveMetaTokenSettings()">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
document.addEventListener("DOMContentLoaded", function () {

    const apiSettingsModal = document.getElementById("apiSettingsModal");

    if (apiSettingsModal) {
        apiSettingsModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;

            const apiName = button.getAttribute("data-api-name");
            const showSource = button.getAttribute("data-show-source") === "1";

            const employeeId = button.getAttribute("data-employee-id") || "";
            const sourceId   = button.getAttribute("data-source-id") || "";
            const productId  = button.getAttribute("data-product-id") || "";
            const adId  = button.getAttribute("data-ad-id") || "";

            document.getElementById("settings_api_name").value = apiName;
            document.getElementById("settings_employee_id").value = employeeId;

            const sourceGroup = document.getElementById("settings_source_group");
            const sourceSelect = document.getElementById("settings_source_id");

            const productGroup = document.getElementById("settings_product_group");
            const productSelect = document.getElementById("settings_product_id");

            const apiIdGroup = document.getElementById("settings_apiid_group");
            const apiIdInput = document.getElementById("setting_ad_id");

            /** SHOW / HIDE FIELDS */
            if (apiName === "indiamart") {
                sourceGroup.style.display = "none";
                productGroup.style.display = "none";
                apiIdGroup.style.display = "none";
            } 
            else if (apiName === "general") {
                sourceGroup.style.display = "block";
                productGroup.style.display = "none";
                apiIdGroup.style.display = "none";
            } 
            else { // META
                sourceGroup.style.display = "block";
                productGroup.style.display = "block";
                apiIdGroup.style.display = "block";
            }

            sourceSelect.value = sourceId;
            productSelect.value = productId;
            apiIdInput.value = adId;
        });
    }


    // SAVE FUNCTION
    window.saveApiSettings = function () {

        const apiName = document.getElementById("settings_api_name").value;
        const employee = document.getElementById("settings_employee_id").value;
        const source = document.getElementById("settings_source_id").value;
        const product = document.getElementById("settings_product_id").value;
        const adId = document.getElementById("setting_ad_id").value;

        const assignType = document.querySelector("input[name='assign_type']:checked").value;

        // VALIDATION FOR META
        if (apiName === "meta" && assignType === "multiple" && adId.trim() === "") {
            alert("AD ID is required when Multiple is selected.");
            return;
        }

        const token = document.querySelector("meta[name='csrf-token']").getAttribute("content");

        fetch("{{ route('api_data.api-settings.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                "Accept": "application/json",
            },
            body: JSON.stringify({
                api_name: apiName,
                employee_id: employee || null,
                source_id: source || null,
                product_id: product || null,
                ad_id: adId || null,
                assign_type: assignType,
            }),
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || "Settings saved");
            const modal = bootstrap.Modal.getInstance(apiSettingsModal);
            modal.hide();
        })
        .catch(err => {
            console.error(err);
            alert("Failed to save settings");
        });
    };
});
</script>

@endsection
