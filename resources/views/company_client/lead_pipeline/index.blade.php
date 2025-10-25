@extends('layouts.client')
@section('title', 'Lead Pipeline List')
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
                                            <h5 class="card-title mb-0">Add Lead Pipeline </h5>
                                            <hr>
                                        </div>

                                        <div class="live-preview">
                                            <form method="POST" action="{{ route('lead-pipeline.create') }}"
                                                autocomplete="off">
                                                @csrf

                                                <div class="card-body pt-0">

                                                    <div class="">
                                                        Pipeline Name <span style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="pipeline_name"
                                                            placeholder="Enter Pipeline Name" maxlength="100"
                                                            autocomplete="off" required autofocus>
                                                    </div>

                                                    <div class="">
                                                        Next Followup Required? <span style="color:red;">*</span>
                                                        <select class="form-control" name="followup_needed"
                                                            id="followupRequired" required>
                                                            <option value="">Select option</option>
                                                            <option value="yes">Yes</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                    </div>

                                                    <div class="">
                                                        Colour <span style="color:red;">*</span>
                                                        <input type="color" class="form-control" name="color"
                                                            autocomplete="off" required>
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
                                            <h5 class="card-title mb-0">Lead Pipeline List</h5>
                                        </div>
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr >
                                                    <th width="5%">No</th>
                                                    <th width="30%"> Name</th>
                                                    <th width="35%"> Followup Required?</th>
                                                    <th width="15%"> Color </th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($leadPipeline as $lead)
                                                    <tr >
                                                        <td>{{ $i + $leadPipeline->perPage() * ($leadPipeline->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $lead->pipeline_name }}</td>
                                                        <td>{{ $lead->followup_needed ?? '-' }}</td>
                                                        <td class="d-flex justify-content-center">
                                                            @if (isset($lead->color))
                                                                <div
                                                                    style="width: 100%; height: 20px; background-color: {{ $lead->color }};">
                                                                </div>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>


                                                        <td>
                                                            <div class="gap-2">
                                                                <a class="mx-1" title="Edit" href="#"
                                                                    onclick="getEditData(<?= $lead->pipeline_id ?>)"
                                                                    data-bs-toggle="modal" data-bs-target="#showModal">
                                                                    <i class="far fa-edit"></i>
                                                                </a>
                                                                @if ($lead->admin != 1)
                                                                    <a class="" href="#" data-bs-toggle="modal"
                                                                        title="Delete" data-bs-target="#deleteRecordModal"
                                                                        onclick="deleteData(<?= $lead->pipeline_id ?>);">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </a>
                                                                @else
                                                                    {{ '-' }}
                                                                @endif
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $leadPipeline->links() }}
                                    </div>
                                </div>

                            </div></div>
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
                            <h5 class="modal-title" id="exampleModalLabel">Edit Lead Pipeline</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                id="close-modal"></button>
                        </div>
                        <form method="POST" action="{{ route('lead-pipeline.update') }}" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pipeline_id" id="pipeline_id" value="">

                            <div class="modal-body">

                                <div class="mb-3">
                                    Pipeline Name <span style="color:red;">*</span>
                                    <input type="text" class="form-control" name="pipeline_name"
                                        id="Editpipeline_name" placeholder="Enter Pipeline Name" maxlength="100"
                                        autocomplete="off" required>
                                </div>
                                <div class="mt-4 mb-3">
                                    Next Followup Required? <span style="color:red;">*</span>
                                    <select class="form-control" name="followup_needed" id="editfollowupRequired"
                                        required>
                                        <option value="">Select option</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    Color <span style="color:red;">*</span>
                                    <input type="color" class="form-control" name="color" id="Editcolor"
                                        autocomplete="off" required>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="submit" class="btn btn-primary mx-2" id="add-btn">Update</button>
                                    <button type="button" class="btn btn-primary mx-2"
                                        data-bs-dismiss="modal">Cancel</button>
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
                                <button type="button" class="btn w-sm btn-primary mx-2"
                                    data-bs-dismiss="modal">Close</button>
                                <form id="user-delete-form" method="POST" action="{{ route('lead-pipeline.delete') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="pipeline_id" id="deleteid" value="">

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

            var url = "{{ route('lead-pipeline.edit', ':id') }}";
            url = url.replace(":id", id);
            if (id) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        console.log(data); // fixed typo from `console,log`
                        $("#Editpipeline_name").val(data.pipeline_name); // check property name matches response
                        $('#pipeline_id').val(id);
                        $('#editfollowupRequired').val(data.followup_needed);
                        $('#Editcolor').val(data.color);

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
