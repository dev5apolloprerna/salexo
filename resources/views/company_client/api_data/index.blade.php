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
                                            data-show-source="1"
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
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary me-2"
                                                title="Settings"
                                                data-bs-toggle="modal"
                                                data-bs-target="#apiSettingsModal"
                                                data-api-name="meta"
                                                data-api-id="3"
                                                data-show-source="1"
                                                data-employee-id="{{ $metaSettings->emp_id ?? '' }}"
                                                data-source-id="{{ $metaSettings->source_id ?? '' }}">
                                            <i class="fas fa-user"></i>
                                        </button>

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

    {{-- ========== Common Settings Modal (Employee + Source) ========== --}}
    <div class="modal fade" id="apiSettingsModal" tabindex="-1" aria-labelledby="apiSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="apiSettingsModalLabel">API Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Which API is being edited --}}
                    <input type="hidden" id="settings_api_name">

                    {{-- Employee --}}
                    <div class="mb-3">
                        <label for="settings_employee_id" class="form-label">Employee</label>
                        <select id="settings_employee_id" class="form-control">
                            <option value="">Select Employee</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->emp_id }}">{{ $emp->emp_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lead Source (hidden for IndiaMart, visible for General & Meta) --}}
                    <!--<div class="mb-3" id="settings_source_group">
                        <label for="settings_source_id" class="form-label">Lead Source</label>
                        <select id="settings_source_id" class="form-control">
                            <option value="">Select Source</option>
                            @foreach($leadSources ?? [] as $src)
                                <option value="{{ $src->lead_source_id }}">{{ $src->lead_source_name }}</option>
                            @endforeach
                        </select>
                    </div>-->

                    <small class="text-muted">
                        These selections will be used to auto-attach employee/source to leads coming from this API.
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
        document.addEventListener('DOMContentLoaded', function () {
            // ========== Copy to clipboard ==========
            function copyToClipboard(elementId) {
                var copyText = document.getElementById(elementId);
                if (!copyText) return;

                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                alert("Copied to clipboard: " + copyText.value);
            }
            window.copyToClipboard = copyToClipboard;

            // ========== API Settings Modal (Employee + Source) ==========
            const apiSettingsModal = document.getElementById('apiSettingsModal');
            if (apiSettingsModal) {
                apiSettingsModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const apiName    = button.getAttribute('data-api-name');
                    const showSource = button.getAttribute('data-show-source') === '1';
                    const employeeId = button.getAttribute('data-employee-id') || '';
                    const sourceId   = button.getAttribute('data-source-id') || '';

                    const apiNameInput = document.getElementById('settings_api_name');
                    if (apiNameInput) apiNameInput.value = apiName;

                    const empSelect = document.getElementById('settings_employee_id');
                    if (empSelect) empSelect.value = employeeId;

                    const sourceGroup  = document.getElementById('settings_source_group');
                    const sourceSelect = document.getElementById('settings_source_id');

                    if (sourceGroup) {
                        sourceGroup.style.display = showSource ? 'block' : 'none';
                    }
                    if (sourceSelect) {
                        sourceSelect.value = sourceId;
                    }
                });
            }

            function saveApiSettings() {
                const apiNameEl   = document.getElementById('settings_api_name');
                const empSelect   = document.getElementById('settings_employee_id');
                const sourceSelect = document.getElementById('settings_source_id');

                const apiName  = apiNameEl ? apiNameEl.value : '';
                const employee = empSelect ? empSelect.value : '';
                const source   = sourceSelect ? sourceSelect.value : '';

                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const token     = tokenMeta ? tokenMeta.getAttribute('content') : '';

                fetch("{{ route('api_data.api-settings.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        api_name: apiName,
                        employee_id: employee || null,
                        source_id: source || null,
                    }),
                })
                .then(async (response) => {
                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        throw new Error(err.message || 'Something went wrong');
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log('Saved:', data);
                    alert(data.message || 'Settings saved');

                    if (apiSettingsModal) {
                        const modalInst = bootstrap.Modal.getInstance(apiSettingsModal)
                                          || new bootstrap.Modal(apiSettingsModal);
                        modalInst.hide();
                    }
                })
                .catch((error) => {
                    console.error(error);
                    alert(error.message || 'Failed to save settings');
                });
            }
            window.saveApiSettings = saveApiSettings;

            // ========== Meta Token Modal Logic ==========
            const metaTokenModal = document.getElementById('metaTokenModal');
            if (metaTokenModal) {
                metaTokenModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const accessToken = button.getAttribute('data-access-token') || '';
                    const verifyToken = button.getAttribute('data-verify-token') || '';

                    const accessInput = document.getElementById('meta_access_token');
                    const verifyInput = document.getElementById('meta_verify_token');

                    if (accessInput) accessInput.value = accessToken;
                    if (verifyInput) verifyInput.value = verifyToken;
                });
            }

            function saveMetaTokenSettings() {
                const accessInput = document.getElementById('meta_access_token');
                const verifyInput = document.getElementById('meta_verify_token');

                const accessToken = accessInput ? accessInput.value.trim() : '';
                const verifyToken = verifyInput ? verifyInput.value.trim() : '';

                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const token     = tokenMeta ? tokenMeta.getAttribute('content') : '';

                fetch("{{ route('api_data.meta-token.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        access_token: accessToken || null,
                        verify_token: verifyToken || null,
                    }),
                })
                .then(async (response) => {
                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        throw new Error(err.message || 'Something went wrong');
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log('Meta tokens saved:', data);
                    alert(data.message || 'Meta tokens saved');

                    if (metaTokenModal) {
                        const modalInst = bootstrap.Modal.getInstance(metaTokenModal)
                                          || new bootstrap.Modal(metaTokenModal);
                        modalInst.hide();
                    }

                    // Update data-* attributes so next open uses fresh values
                    const metaUserButton = document.querySelector('button[title="Meta Token Settings"]');
                    if (metaUserButton) {
                        metaUserButton.setAttribute('data-access-token', accessToken || '');
                        metaUserButton.setAttribute('data-verify-token', verifyToken || '');
                    }
                })
                .catch((error) => {
                    console.error(error);
                    alert(error.message || 'Failed to save Meta tokens');
                });
            }
            window.saveMetaTokenSettings = saveMetaTokenSettings;
        });
    </script>
@endsection
