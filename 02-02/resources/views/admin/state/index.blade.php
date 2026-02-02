@extends('layouts.app')
@section('title', 'State List')
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
                                            <h5 class="card-title mb-0">Add State </h5>
                                        </div>

                                        <div class="live-preview">
                                            <form method="POST" action="{{ route('state.create') }}" autocomplete="off"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    <div class="mt-4 mb-3">
                                                        Name <span style="color:red;">*</span>
                                                        <input type="text" class="form-control" name="stateName"
                                                            placeholder="Enter Name" maxlength="100" autocomplete="off"
                                                            required autofocus>
                                                            @if($errors->has('stateName'))
                                                                 <span class="text-danger">
                                                                    {{ $errors->first('stateName') }}
                                                                </span>
                                                            @endif
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="submit" class="btn btn-primary mx-2"
                                                            id="add-btn">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>  
                                    <div class="col-lg-1">
                                        
                                    </div>
                                    <div class="col-lg-5">
                                        <form method="post" action="{{ route('state.index') }}" id="myForm">
                                                @csrf
                                            <div class="mt-4 mb-3"> Search By State Name
                                                 <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <input class="btn btn-primary mx-2" type="submit" value="{{'Search'}}">
                                                <input class="btn btn-primary" type="submit" onclick="myFunction()" value="{{'Reset'}}">
                                            </div>
                                        </form>
                                        <div class="d-flex justify-content-between card-header">
                                            <h5 class="card-title mb-0">State List</h5>
                                        </div>
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="50%">No</th>
                                                    <th width="50%"> Name</th>
                                                    <!-- <th width="1%">Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($State as $state)
                                                    <tr class="text-center">
                                                        <td>{{ $i + $State->perPage() * ($State->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $state->stateName }}</td>

                                                        <td>
                                                            <div class="gap-2">
                                                               <!--  <a class="mx-1" title="Edit" href="#"
                                                                    onclick="getEditData(<?= $state->stateId ?>)"
                                                                    data-bs-toggle="modal" data-bs-target="#showModal">
                                                                    <i class="far fa-edit"></i>
                                                                </a>

                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $state->stateId ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a> -->

                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $State->links() }}
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
                                <h5 class="modal-title" id="exampleModalLabel">Edit State</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-modal"></button>
                            </div>
                            <form method="POST" action="{{ route('state.update') }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="stateId" id="stateId" value="">

                                <div class="modal-body">

                                    <div class="mb-3">
                                        Name <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="stateName" id="EditstateName"
                                            placeholder="Enter Name" maxlength="100" autocomplete="off" required>
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
                                    <form id="user-delete-form" method="POST" action="{{ route('state.delete') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="stateId" id="deleteid" value="">

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
          function myFunction() 
        {
            $('#search').val('');

        }
       function getEditData(id) {
    var url = "{{ route('state.edit', ':id') }}";
    url = url.replace(":id", id);

    if (id) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                console.log(data); // fixed typo from `console,log`
                $("#EditstateName").val(data.stateName); // check property name matches response
                $('#stateId').val(id);
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
