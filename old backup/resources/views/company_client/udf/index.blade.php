@extends('layouts.client')
@section('title', 'UDF List')
@section('content')

    <style type="text/css">
        #followupDateContainer {
            display: none;
        }
    </style>
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="card pb-3">

                                    <div class="">
                                        <div class=" justify-content-between card-header pb-0">
                                            <h5 class="card-title mb-0">Add UDF </h5>
                                            <hr>
                                        </div>

                                        <div class="live-preview">
                                            <form method="POST" action="{{ route('udf.store') }}" autocomplete="off">
                                                @csrf

                                                <div class="card-body pt-0">

                                                    <div class="">
                                                        Label Name <span style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="label"
                                                            placeholder="Enter Label Name" maxlength="255"
                                                            autocomplete="off" required autofocus>
                                                    </div>

                                                    <div class="">
                                                        Required ? <span style="color:red;">*</span>
                                                        <select class="form-control" name="required" id="required"
                                                            required>
                                                            <option value="">Select option</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" class="btn btn-primary mx-2"
                                                            id="add-btn">Submit</button>
                                                        <button type="reset" class="btn btn-primary mx-2"
                                                            id="add-btn">Reset</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">

                                    <div class="">
                                        <div class="d-flex justify-content-between card-header">
                                            <h5 class="card-title mb-0">UDF List</h5>
                                        </div>
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="30%"> Label Name</th>
                                                    <th width="35%"> Required ?</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($datas as $data)
                                                    <tr>
                                                        <td>{{ $i + $datas->perPage() * ($datas->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $data->label }}</td>
                                                        <td>{{ $data->required }}</td>
                                                        <td>
                                                            <div class="gap-2">
                                                                <a class="mx-1" title="Edit" href="#"
                                                                    onclick="getEditData(<?= $data->id ?>)"
                                                                    data-bs-toggle="modal" data-bs-target="#showModal">
                                                                    <i class="far fa-edit"></i>
                                                                </a>
                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $data->id ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $datas->links() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!--Edit Modal Start-->
    <div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Edit UDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="{{ route('udf.update') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="udfid" value="">

                    <div class="modal-body">

                        <div class="mb-3">
                            Label Name <span style="color:red;">*</span>
                            <input type="text" class="form-control" name="label" id="Editlabel"
                                placeholder="Enter Label Name" maxlength="100" autocomplete="off" required>
                        </div>
                        <div class="mt-4 mb-3">
                            Required ? <span style="color:red;">*</span>
                            <select class="form-control" name="required" id="Editrequired" required>
                                <option value="">Select Required ?</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary mx-2" id="add-btn">Update</button>
                            <button type="button" class="btn btn-primary mx-2" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--Edit Modal End -->

    <!--Delete Modal Start -->
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
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                        </lord-icon>
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
                        <form id="user-delete-form" method="POST" action="{{ route('udf.delete') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Delete modal End -->

    </div>
    </div>
    </div>
@endsection

@section('scripts')

    <script>
        function getEditData(id) {

            var url = "{{ route('udf.edit', ':id') }}";
            url = url.replace(":id", id);
            if (id) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(obj) {
                        $("#Editrequired").val(obj.required);
                        $("#Editlabel").val(obj.label);
                        $('#udfid').val(id);
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            }
        }

        function deleteData(id) {
            $("#deleteid").val(id);
        }
    </script>

@endsection
