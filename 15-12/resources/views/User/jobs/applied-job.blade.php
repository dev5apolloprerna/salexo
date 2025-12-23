@extends('layouts.app')
@section('title', 'Applied Job List')
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
                                <h5 class="card-title mb-0">Applied Job List</h5>
                                <a href="{{route('user-job.index')}}" class="btn btn-sm btn-primary">
                                    <i data-feather="plus"></i> Back
                                </a>
                            </div>
                             <div class="card-body">
                                <h6>Job Information</h6>
                                <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="2%">Job Title</th>
                                            <th width="2%">Industry</th>
                                            <th width="2%">Function</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr >
                                            <td>{{ $jobs->jobTitle }}</td>
                                            <td>{{ $jobs->industry }}</td>
                                            <td>{{ $jobs->functions }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                       
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                           

                            <div class="card-body">
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
                                                    <th width="2%">Resume</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($appliedJob as $data)
                                                    <tr class="text-center">
                                                        <td>{{ $i + $appliedJob->perPage() * ($appliedJob->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $data->firstName }}</td>
                                                        <td>{{ $data->lastName }}</td>
                                                        <td>{{ $data->Gender }}</td>
                                                        <td>{{ date('d-m-Y',strtotime($data->DOB)) }}</td>
                                                        <td>{{ $data->Email }}</td>
                                                        <td>{{ $data->Mobile }}</td>
                                                        <td>
                                                            <?php
                                                                $jobDate=date('Y/m/d', strtotime($data->jobDate))
                                                            ?>
                                                        <a class="mx-2" target="_blank"
                                                            href="{{ asset($jobDate) . '/' . $data->resume }}"
                                                            title="Pdf Details">
                                                            <i
                                                                class="fa-solid fa-file-pdf fa-lg"></i>
                                                        </a>
                                                    </td>
                                                        
                                                    </tr>
                                                    <!------model for view description ------------------->

                                                        <div class="modal fade flip" id="viewModal_{{$data->jobId}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-light p-3">
                                                                    <h5 class="modal-title" id="exampleModalLabel">{{ $data->jobTitle }} </h5>

                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"id="close-modal"></button>
                                                                </div>

                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <p>Name : {{ $data->firstName }}{{ $data->lastName }}</p>
                                                                            <p>Gender : {{ $data->Gender }}</p>
                                                                            <p>DOB : {{ date('d-m-Y', strtotime($data->DOB)) }}</p>
                                                                            <p>Email : {{ $data->Email }}</p>
                                                                            <p>Mobile : {{ $data->Mobile }}</p>
                                                                            <p>State : {{ $data->state }}</p>
                                                                            <p>City : {{ $data->city }}</p>
                                                                            <p>Company : {{ $data->Company }}</p>
                                                                            <p>Designation : {{ $data->Designation }}</p>
                                                                            <p>Experience : {{ $data->experience }}</p>
                                                                            <p>Current CTC : {{ $data->current_CTC }}</p>
                                                                            <p>Education : {{ $data->education }}</p>
                                                                            <p>Institute : {{ $data->Institute }}</p>
                                                                            <p>Funcation : {{ $data->functions }}</p>
                                                                            <p>Industry : {{ $data->industry }}</p>
                                                                            
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $appliedJob->links() }}
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
                                    <form id="user-delete-form" method="POST" action="{{ route('applied-job.destroy') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="jobId" id="deleteid" value="">

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
                 function myFunction() 
        {
            $('#jobTitle').removeAttr('value');
            $('#experience').val('');
            $('#industry').val('');
            $('#salary').val('');
            $('#fuction').val('');
            $('#state').val('');
            $('#city').val('');

        }

    </script>

@endsection
