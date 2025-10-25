@extends('layouts.client')
@section('title', 'Service List')
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
                                <div class="row">

                                    <div class="col-lg-5">

                                        <div class="d-flex justify-content-between card-header">
                                            <h5 class="card-title mb-0">Add Service / Product </h5>
                                        </div>

                                        <div class="live-preview">
                                            <form method="POST" action="{{ route('service.store') }}" autocomplete="off"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    <div class="mt-4 mb-3">
                                                        Service / Product Name <span style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="service_name"
                                                            placeholder="Enter Service / Product Name" maxlength="100"
                                                            autocomplete="off" required autofocus>
                                                    </div>

                                                    <div class="mt-4 mb-3">
                                                        Description Name <span style="color:red;">*</span>
                                                        <textarea class="form-control" name="service_description" id="service_description" cols="30" rows="3"></textarea>
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

                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-between card-header">
                                            <h5 class="card-title mb-0">Service / Product List</h5>
                                        </div>
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Sr No</th>
                                                    <th>Service / Product Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @forelse($services as $index => $service)
                                                    <tr class="text-center">
                                                        <td>{{ $services->firstItem() + $index }}</td>

                                                        <td>{{ $service->service_name }}</td>
                                                        <td>{{ $service->service_description ? Str::limit($service->service_description, 50) : '-' }}
                                                        </td>

                                                        <td>
                                                            <a class="mx-1" title="Edit" href="#"
                                                                onclick="getEditData(<?= $service->service_id ?>)"
                                                                data-bs-toggle="modal" data-bs-target="#showModal">
                                                                <i class="far fa-edit"></i>
                                                            </a>

                                                            <a class="" href="#" data-bs-toggle="modal"
                                                                title="Delete" data-bs-target="#deleteRecordModal"
                                                                onclick="deleteData(<?= $service->service_id ?>);">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No service Found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {!! $services->links() !!}
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!--Edit Modal Start-->
                <div class="modal fade flip" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Service / Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-modal"></button>
                            </div>
                            <form method="POST" action="{{ route('service.update') }}" autocomplete="off">
                                @csrf

                                <input type="hidden" name="service_id" id="service_id" value="">

                                <div class="modal-body">

                                    <div class="mb-3">
                                        Service / Product Name <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="service_name" id="Editservice_name"
                                            placeholder="Enter Service Name" maxlength="100" autocomplete="off" required>
                                    </div>

                                    <div class="mb-3">
                                        Description Name <span style="color:red;">*</span>
                                        <textarea class="form-control" name="service_description" id="Editservice_description" cols="30"
                                            rows="3"></textarea>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary mx-2"
                                            id="add-btn">Update</button>
                                        <button type="button" class="btn btn-primary mx-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--Edit Modal End -->

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
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
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
                                    <button type="button" class="btn w-sm btn-primary mx-2"
                                        data-bs-dismiss="modal">Close</button>
                                    <form action="{{ route('service.destroy', $service->service_id ?? '') }}"
                                        id="user-delete-form" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="service_id" id="deleteid" value="">

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Delete Modal -->

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        function getEditData(id) {

            var url = "{{ route('service.edit', ':id') }}";
            url = url.replace(":id", id);
            if (id) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        var obj = JSON.parse(data);
                        $("#Editservice_name").val(obj.service_name);
                        $("#Editservice_description").val(obj.service_description);
                        $('#service_id').val(id);
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            }
        }
    </script>

    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }
    </script>

@endsection
