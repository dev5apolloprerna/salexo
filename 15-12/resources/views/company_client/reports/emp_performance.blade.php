@extends('layouts.client')
@section('title', 'Employee Performance Report List')
@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Employee Performance Report List
                                </h5>
                                <hr>
                            </div>

                            <div class="card-body">

                                <form method="get" action="{{ route('clients.reports.emp_performance') }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" value="{{ $fromDate }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" value="{{ $toDate }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Employee</label>
                                            <select class="form-control" name="emp_name" id="emp_name">
                                                <option value="">Select Employee</option>
                                                @foreach ($allemployees as $employee)
                                                    <option value="{{ $employee->emp_id }}"
                                                        {{ $search && $search == $employee->emp_id ? 'selected' : '' }}>
                                                        {{ $employee->emp_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-12 d-flex justify-content-end align-items-end mt-3">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('clients.reports.emp_performance') }}"
                                                class="btn btn-secondary ms-2">Reset</a>

                                            @php
                                                $company_client_master = App\Models\CompanyClient::where([
                                                    'iStatus' => 1,
                                                    'isDeleted' => 0,
                                                    'company_id' => Auth::user('web_employees')->company_id,
                                                ])->first();
                                            @endphp

                                            @if ($company_client_master && $company_client_master->plan_id != 1)
                                                <a href="{{ route('clients.reports.emp_performance.export', [
                                                    'from_date' => request('from_date'),
                                                    'to_date' => request('to_date'),
                                                    'emp_name' => request('emp_name'),
                                                ]) }}"
                                                    class="btn btn-success ms-2">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @else
                                                <a target="_blank" class="btn btn-success ms-2"
                                                    href="{{ route('front.index') }}#cta"
                                                    style="filter: blur(0.5px); opacity:0.5;">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @endif


                                        </div>

                                    </div>
                                </form>

                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr.No</th>
                                                        <th>Employee Name</th>
                                                        <th class="text-center">Total Received</th>
                                                        <th class="text-center">Leads Generated</th>
                                                        <th class="text-center">Lead Given</th>
                                                        <th style="text-align:right">Lead Conveted Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @forelse($employees as $index => $emp)
                                                        <tr>
                                                            <td>{{ $employees->firstItem() + $index }}</td>
                                                            <td>{{ $emp->emp_name }}</td>
                                                            <td class="text-center">
                                                                {{ $emp->leads_generated }}
                                                            </td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_generated_detail', [
                                                                        'emp_id' => $emp->emp_id,
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $emp->leads_generated }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_given_detail', [
                                                                        'emp_id' => $emp->emp_id,
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $emp->leads_assigned }}
                                                                </a>
                                                            </td>
                                                            <td style="text-align:right">₹
                                                                {{ number_format($emp->converted_amount, 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No Employees Found.</td>
                                                        </tr>
                                                    @endforelse
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $employees->links() }}
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
                        <form action="{{ route('employee.destroy', $emp->emp_id ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="emp_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Modal -->

    <div class="modal fade flip" id="changepassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="post" action="{{ route('employee.passwordupdate') }}" autocomplete="off">
                    @csrf
                    @method('post')

                    <input type="hidden" name="id" id="GetId" value="">

                    <div class="modal-body">
                        <div class="mb-3" id="modal-id" style="display: none;">
                            <label for="id-field" class="form-label">ID</label>
                            <input type="text" id="id-field" class="form-control" placeholder="ID" readonly />
                        </div>

                        <div class="mb-3">
                            <span style="color:red;">*</span>New Password
                            <input type="password" name="newpassword" id="newpassword" class="form-control"
                                placeholder="Enter New Password" required />
                        </div>

                        <div class="mb-3">
                            <span style="color:red;">*</span>Confirm Password
                            <input type="password" name="confirmpassword" id="confirmpassword" class="form-control"
                                placeholder="Enter Confirm Password" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary" id="add-btn">Update</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }

        function editpassword(id) {
            $("#GetId").val(id);
        }
    </script>
@endsection
