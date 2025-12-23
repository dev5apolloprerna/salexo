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
                        <div class="card">
                            <div class="card-body">

                                @include('employee.leads.TabView')

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Lead List
                                        <a href="{{ route('employee.leads.create') }}" style="float: right;"
                                            class="btn btn-sm btn-primary">
                                            <i class="far fa-plus"></i> Add New Lead
                                        </a>
                                    </h5>
                                </div>

                                <div class="tab-content text-muted">
                                    <div class="tab-pane active" id="PendingOrder" role="tabpanel">
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">

                                                        <form method="GET" action="{{ route('employee.leads.cancel') }}"
                                                            id="myForm">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                        <label for="name">Search By Company Name /
                                                                            Contact Person </label>
                                                                        <input type="text" name="search" id="search"
                                                                            class="form-control" value="{{ $search ?? '' }}"
                                                                            autocomplete="off">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input class="btn btn-primary"
                                                                            style="margin-top: 15%;" type="submit"
                                                                            value="Search">
                                                                        <input class="btn btn-secondary"
                                                                            style="margin-top: 15%;" type="button"
                                                                            onclick="resetForm()" value="Reset">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class=" table table-bordered table-striped table-hover datatable">
                                                                            <thead>
                                                                                <tr class="text-center">
                                                                                    <th>Sr No</th>
                                                                                    <th>Company Name</th>
                                                                                    <th>Contact Person Name</th>
                                                                                    <th>Email</th>
                                                                                    <th>Mobile</th>
                                                                                    <th>Lead Source</th>
                                                                                    <th>Lead Cancel Date</th>
                                                                                    <th>Lead Cancel Reason</th>
                                                                                    <th>Actions</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse($leads as $index => $lead)
                                                                                    <tr class="text-center">
                                                                                        <td>{{ $leads->firstItem() + $index }}
                                                                                        </td>
                                                                                        <td>{{ $lead->company_name ?? '-' }}
                                                                                        </td>
                                                                                        <td>{{ $lead->customer_name ?? '-' }}
                                                                                        </td>
                                                                                        <td>{{ $lead->email ?? '-' }}
                                                                                        </td>
                                                                                        <td>{{ $lead->mobile ?? '-' }}
                                                                                        </td>
                                                                                        <td>{{ $lead->leadSource->lead_source_name ?? '' }}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{ $lead->deal_cancel_at ? date('d-m-Y H:i', strtotime($lead->deal_cancel_at)) : '-' }}
                                                                                        </td>
                                                                                        <td>{{ $lead->cancel_reason_name ?? '-' }}
                                                                                        </td>
                                                                                        <td>
                                                                                          
                                                                                            <a href="{{ route('employee.leads.lead_history',[ 'status' => 'lead-cancel' , 'lead_id' => $lead->lead_id]) }}" title="History">
                                                                                                <i class="fa fa-history" aria-hidden="true"></i>
                                                                                            </a>
                                                                                            
                                                                                            @if(Auth::user()->isCompanyAdmin == 1)
                                                                                                <a href="{{ route('employee.followup_detail', ['lead-cancel', $lead->lead_id]) }}"
                                                                                                    title="Add Followup">
                                                                                                    <i class="fa fa-plus"></i>
                                                                                                </a>
                                                                                            @endif
                                                                                        </td>

                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="5"
                                                                                            class="text-center">No Leads
                                                                                            Found.</td>
                                                                                    </tr>
                                                                                @endforelse
                                                                        </table>
                                                                        <div class="d-flex justify-content-center mt-3">
                                                                            {{ $leads->links() }}
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
                        <form action="{{ route('employee.leads.destroy') }}" id="user-delete-form" method="POST">
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
@endsection
