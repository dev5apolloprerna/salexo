@extends('layouts.client')
@section('title', 'Lead List')
@section('content')

    <?php $profileId = Request::segment(3); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
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

                <div class="row">
                    <div class="col-xxl-12">
                        <h5 class="mb-3"></h5>


                        @include('company_client.leads.TabView')


                        <div class="card-header pt-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-0">Lead List </h5>
                            </div>
                            <div>

                                @php
                                    $company_client_master = App\Models\CompanyClient::where([
                                        'iStatus' => 1,
                                        'isDeleted' => 0,
                                        'company_id' => Auth::user('web_employees')->company_id,
                                    ])->first();
                                @endphp

                                @if ($company_client_master && $company_client_master->plan_id != 1)
                                    <button class="btn btn-sm btn-primary mx-2" type="button" onclick="exportExcel();">
                                        <i class="fa-solid fa-file-excel fa-xl"></i>
                                    </button>
                                @else
                                    <a target="_blank" class="btn btn-sm btn-primary mx-2"
                                        href="{{ route('front.index') }}#cta" style="filter: blur(0.5px); opacity:0.5;">
                                        <i class="fa-solid fa-file-excel fa-xl"></i>
                                    </a>
                                @endif

                                <a href="{{ route('leads.create') }}" style="float: right;" class="btn btn-sm btn-primary">
                                    <i class="far fa-plus"></i> Add New Lead
                                </a>
                            </div>

                        </div>

                    </div>
                    <div class="tab-content text-muted pt-3">
                        <div class="tab-pane active" id="PendingOrder" role="tabpanel">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <form method="GET" action="{{ route('leads.index') }}" id="myForm">
                                                @csrf
                                                <input type="hidden" name="lead_type" id="lead_type" value="active_deal">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="name">Company Name /
                                                                Contact Person </label>
                                                            <input type="text" name="search" id="search"
                                                                class="form-control" value="{{ $search ?? '' }}"
                                                                autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="name"> Employee </label>
                                                            <select name="emp_id" id="emp_id" class="form-control">
                                                                <option value="">----- Select Employee -----</option>
                                                                @foreach ($employees as $employee)
                                                                    <option value="{{ $employee->emp_id }}"
                                                                        {{ $emp_id == $employee->emp_id ? 'selected' : '' }}>
                                                                        {{ $employee->emp_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="name"> Status </label>
                                                            <select name="pipeline_id" id="pipeline_id"
                                                                class="form-control">
                                                                <option value="">----- Select Status -----</option>
                                                                @foreach ($leadPipeline as $status)
                                                                    <option value="{{ $status->pipeline_id }}"
                                                                        {{ $pipeline_id == $status->pipeline_id ? 'selected' : '' }}>
                                                                        {{ $status->pipeline_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="name"> Service </label>
                                                            <select name="service_id" id="service_id" class="form-control">
                                                                <option value="">----- Select Service -----</option>
                                                                @foreach ($services as $service)
                                                                    <option value="{{ $service->service_id }}"
                                                                        {{ $service_id == $service->service_id ? 'selected' : '' }}>
                                                                        {{ $service->service_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pt-3">
                                                        <div class="form-group" style="margin-top:12px">
                                                            <input class="btn btn-primary" type="submit" value="Search">
                                                            <a class="btn btn-secondary" href="{{ route('leads.index') }}">
                                                                Reset
                                                            </a>


                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="card-body">
                                                        <div class="table-responsive table">
                                                            <table
                                                                class=" table table-bordered table-striped table-hover datatable">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="1%">Sr No</th>
                                                                        <th width="2%">Company Name</th>
                                                                        <!--<th>Contact Person Name</th>-->
                                                                        <!--<th>Email</th>-->
                                                                        <!--<th>Mobile</th>-->
                                                                        <th width="2%">Contact Detail</th>
                                                                        <th width="2%">Lead Source</th>
                                                                        <th width="2%">Service / Product</th>
                                                                        <th width="2%">Status</th>
                                                                        <th width="2%">Remarks</th>
                                                                        <th width="1%">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($leads as $index => $lead)
                                                                        <tr>
                                                                            <td>{{ $leads->firstItem() + $index }}
                                                                            </td>
                                                                            <td>{{ $lead->company_name ?? '-' }}
                                                                            </td>
                                                                            <!--<td>{{ $lead->customer_name ?? '-' }}-->
                                                                            <!--</td>-->
                                                                            <!--<td>{{ $lead->email ?? '-' }}-->
                                                                            <!--</td>-->
                                                                            <!--<td>{{ $lead->mobile ?? '-' }}-->
                                                                            <td>
                                                                                {{ $lead->customer_name }} <br>
                                                                                {{ $lead->email }} <br>
                                                                                {{ $lead->mobile }}
                                                                            </td>

                                                                            <td>
                                                                                {{ optional($lead->leadSource)->lead_source_name ?? ($lead->LeadSource_other ?? '-') }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $lead->service_name ? $lead->service_name : $lead->product_service_other }}
                                                                                <!--{{ optional($lead->service)->service_name ?? ($lead->product_service_other ?? '-') }}-->
                                                                            </td>
                                                                            <td>{{ $lead->pipeline_name ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $lead->remarks ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                <a
                                                                                    href="{{ route('leads.edit', $lead->lead_id) }}"><i
                                                                                        class="fa fa-edit"></i></a>
                                                                                <a class="" href="#"
                                                                                    data-bs-toggle="modal" title="Delete"
                                                                                    data-bs-target="#deleteRecordModal"
                                                                                    onclick="deleteData(<?= $lead->lead_id ?>);">
                                                                                    <i class="fa fa-trash"
                                                                                        aria-hidden="true"></i>
                                                                                </a>
                                                                                {{-- History --}}
                                                                                <a href="{{ route('leads.lead_history', ['status' => 'active-lead', 'lead_id' => $lead->lead_id]) }}"
                                                                                    title="History">
                                                                                    <i class="fa fa-eye"
                                                                                        aria-hidden="true"></i>
                                                                                </a>
                                                                                @if ($lead->pipelineSlug != null)
                                                                                    <a href="{{ route('clients.followup_detail', [$lead->pipelineSlug, $lead->lead_id]) }}"
                                                                                        title="Add Followup">
                                                                                        <i class="fa fa-plus"
                                                                                            aria-hidden="true"></i>
                                                                                    </a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">No
                                                                                Leads
                                                                                Found.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                            <div class="d-flex justify-content-center mt-3">
                                                                {{ $leads->appends(request()->except('page'))->links() }}
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


                </div>
            </div>
        </div>
    </div>

    <!--Delete Modal -->
    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you Sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Record
                                ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <a class="btn btn-primary mx-2" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('user-delete-form').submit();">
                            Yes,
                            Delete It!
                        </a>
                        <button type="button" class="btn w-sm btn-primary mx-2" data-bs-dismiss="modal">Close</button>
                        <form action="{{ route('leads.destroy') }}" id="user-delete-form" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="lead_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Modal -->

@endsection

@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }

        function resetForm() {
            document.getElementById('search').value = '';
            document.getElementById('myForm').submit();
        }
    </script>
    <script>
        function exportExcel() {
            var lead_type = $("#lead_type").val() || '';
            var search = $("#search").val() || '';
            var emp_id = $("#emp_id").val() || '';
            var pipeline_id = $("#pipeline_id").val() || '';
            var service_id = $("#service_id").val() || '';

            var strURL = "{{ route('leads.export_to_excel') }}";
            strURL += "?lead_type=" + lead_type + "&search=" + search + "&emp_id=" + emp_id + "&pipeline_id=" +
                pipeline_id + "&service_id=" + service_id;
            window.location.href = strURL;
        }
    </script>
@endsection
