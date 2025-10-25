@extends('layouts.app')
@section('title', 'Bucket List')
@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                             <div class="card-header" style="display: flex;justify-content: space-between;">
                                <h5 class="card-title mb-0">Bucket List</h5>
                                <form action="{{route('bucket.clearBucket')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="employeeId" value="{{$userId}}">
                                <button title="clear bucket" type="submit" class="btn btn-sm btn-primary" id="addButton">Clear Bucket</button>
                                </form>
                            </div>

                        </div>

                       
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                 <div class="col-md-6  mb-2">
                                        <div class="input-group d-flex justify-content-right">
                                            <button onclick="genrateToexcel()" type="button" class="btn btn-primary mx-2">
                                                Export to Excel
                                            </button>
                                        </div>
                                    </div>
                                <div class="row">

                                    <div class="col-lg-12">
                                        
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="1%">No</th>
                                                    <th width="2%">First Name</th>
                                                    <th width="2%">Last Name</th>
                                                    <th width="2%">Gender</th>
                                                    <th width="2%">DOB</th>
                                                    <th width="2%">Email</th>
                                                    <th width="2%">Mobile</th>
                                                    <th width="1%">Action</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($bucketList as $data)
                                                    <tr class="text-center">
                                                        <td>{{ $i + $bucketList->perPage() * ($bucketList->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $data->firstName }}</td>
                                                        <td>{{ $data->lastName }}</td>
                                                        <td>{{ $data->Gender }}</td>
                                                        <td>{{ date('d-m-Y',strtotime($data->DOB)) }}</td>
                                                        <td>{{ $data->Email }}</td>
                                                        <td>{{ $data->Mobile }}</td>
                                                        <td>

                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $data->bucketId ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>

                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $bucketList->links() }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
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
                                    <form id="user-delete-form" method="POST" action="{{ route('bucket.deleteBucket') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="bucketId" id="deleteid" value="">

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
        function deleteData(id) {
            $("#deleteid").val(id);
        }
          function genrateToexcel()
    {
        var Url = "{{route('bucket.exportCandidate')}}";
        window.location.href = Url;
    }

    </script>

@endsection
