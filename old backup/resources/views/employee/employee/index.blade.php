@extends('layouts.client')
@section('title', 'Employee List')
@section('content')

    <?php $profileId = Request::segment(3); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Employee List
                                    <a href="{{ route('employee.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Employee
                                    </a>

                                </h5>

                            </div>
                            <div class="card-body">

                                <form method="get" action="{{ route('employee.index') }}">

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="name">Search By Employee Name </label>
                                                <input type="text" name="emp_name" id="search" class="form-control"
                                                    value="{{ $search ?? '' }}" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="btn btn-primary" style="margin-top: 15%;" type="submit"
                                                    value="{{ 'Search' }}">
                                                <a href="{{ route('employee.index') }}" class="btn btn-secondary"
                                                    style="margin-top: 15%;">Reset</a>

                                            </div>
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
                                                    <tr class="text-center">
                                                        <th>Sr.No</th>
                                                        <th>Employee Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($employees as $index => $emp)
                                                        <tr class="text-center">
                                                            <td>{{ $employees->firstItem() + $index }}
                                                            </td>
                                                            <td>{{ $emp->emp_name }}</td>
                                                            <td>{{ $emp->emp_mobile }}</td>
                                                            <td>{{ $emp->emp_email ?? '-' }}</td>
                                                            <td>
                                                                <a href="{{ route('employee.edit', $emp->emp_id) }}"><i
                                                                        class="fa fa-edit"></i></a>
                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $emp->emp_id ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>
                                                                <a class="mx-1" href="#" data-bs-toggle="modal"
                                                                    title="Change Password" data-bs-target="#changepassword"
                                                                    onclick="editpassword(<?= $emp->emp_id ?>);">
                                                                    <i class="fa fa-key" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No Employees Found.</td>
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
