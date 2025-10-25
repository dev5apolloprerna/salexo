@extends('layouts.app')
@section('title', 'Job List')
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
                                <h5 class="card-title mb-0">Job List</h5>
                                <a href="{{route('user-job.create')}}" class="btn btn-sm btn-primary">
                                    <i data-feather="plus"></i> Add New
                                </a>
                            </div>
                             <div class="card-body">
                              <form method="post" action="{{ route('user-job.index') }}" id="myForm">
                                    @csrf
                                     <div class="row"> 
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Search By Name</label>
                                                <input type="text" name="jobTitle" id="jobTitle" class="form-control" value="{{ old('jobTitle', isset($jobTitle) ? $jobTitle : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">Experience</label>
                                            <select class="form-control" name="experience" id="experience" >
                                                <option value="">Select Experience</option>
                                                @foreach($Experience as $evalue)
                                                    <option value="{{$evalue->experienceId}}" {{ old('experience', isset($experience) && $experience == $evalue->experienceId  ? 'selected' : "" ) }}>{{$evalue->experienceName}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">Salary</label>
                                            <select class="form-control" name="salary" id="salary" >
                                                <option value="">Select Salary</option>
                                                @foreach($SalaryRange as $svalue)
                                                 <option value="{{$svalue->salaryId}}" {{ old('salary', isset($salary) && $salary  == $svalue->salaryId  ? 'selected' : "") }}>{{$svalue->name}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">Industry</label>
                                            <select class="form-control" name="industry" id="industry" >
                                                <option value="">Select Industry</option>
                                                @foreach($Industry as $ivalue)
                                                 <option value="{{$ivalue->industryId}}" {{ old('industry', isset($industry) && $industry == $ivalue->industryId  ?  'selected' : "" ) }}>{{$ivalue->IndustryName}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">Function</label>
                                            <select class="form-control" name="functions" id="functions" >
                                                <option value="">Select Function</option>
                                                @foreach($Functions as $fvalue)
                                                 <option value="{{$fvalue->functionId}}" {{ old('industry', isset($functions) && $industry  == $fvalue->functionId ? 'selected' : "") }}>{{$fvalue->functionName}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">State</label>
                                            <select class="form-control" name="state" id="state" >
                                                <option value="">Select State</option>
                                                @foreach($State as $svalue)
                                                 <option value="{{$svalue->stateId}}" {{ old('state', isset($state) && $state == $svalue->stateId ? 'selected' :"") }}>{{$svalue->stateName}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label for="name">City</label>
                                            <select class="form-control" name="city" id="city" >
                                                <option value="">Select City</option>
                                                @foreach($City as $cvalue)
                                                 <option value="{{$cvalue->cityId}}" {{ old('city', isset($city) && $city  == $cvalue->cityId ? 'selected' : "") }}>{{$cvalue->cityName}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <input class="btn btn-primary" style="margin-top: 15%;" type="submit" value="{{'Search'}}">
                                            <input class="btn btn-primary" style="margin-top: 15%;" type="submit" onclick="myFunction()" value="{{'Reset'}}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                                     @if(sizeof($jobs) != 0)
                                        <table id="scroll-horizontal" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr class="text-center">
                                                    <th width="1%">No</th>
                                                    <th width="2%">Job Title</th>
                                                    <th width="2%">Experience</th>
                                                    <th width="2%">Education</th>
                                                    <th width="2%">Salary Range</th>
                                                    <th width="2%">Industry</th>
                                                    <th width="2%">Function</th>
                                                    <th width="2%">State</th>
                                                    <th width="2%">City</th>
                                                    <th width="1%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($jobs as $data)
                                                    <tr class="text-center">
                                                        <td>{{ $i + $jobs->perPage() * ($jobs->currentPage() - 1) }}
                                                        </td>
                                                        <td>{{ $data->jobTitle }}</td>
                                                        <td>{{ $data->experience }}</td>
                                                        <td>{{ $data->education }}</td>
                                                        <td>{{ $data->salary }}</td>
                                                        <td>{{ $data->industry }}</td>
                                                        <td>{{ $data->functions }}</td>
                                                        <td>{{ $data->state }}</td>
                                                        <td>{{ $data->city }}</td>
                                                        <td>
                                                            <div class="gap-2">
                                                                 <a class="mx-1"  href="#" data-bs-toggle="modal" data-bs-target="#viewModal_{{$data->jobId}}" title="View">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>

                                                                <a class="mx-1" title="View applied jobs" href="{{ route('user-job.appliedJob', $data->jobId) }}">
                                                                    <i class="fa fa-tasks "></i>
                                                                    </a>
                                                                <a class="mx-1" title="Edit" href="{{ route('user-job.edit', $data->jobId) }}">
                                                                    <i class="far fa-edit"></i>
                                                                </a>

                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $data->jobId ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>


                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!------model for view description ------------------->

                                                        <div class="modal fade flip" id="viewModal_{{$data->jobId}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-light p-3">
                                                                    <h5 class="modal-title" id="exampleModalLabel">{{ $data->jobTitle }} </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                                                        id="close-modal"></button>
                                                                </div>

                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <p >{!! $data->jobDescription !!}</p>
                                                                            
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                            @else
                                            <div>
                                                <tr><td colspan="10">
                                                <h5 style="text-align: center;">No data found</h5></td></tr>
                                            </div>
                                            @endif
                                        </table>
                                        <div class="d-flex justify-content-center mt-3">
                                            <!-- {{ $jobs->links() }} -->
                                            {{ $jobs->appends(request()->except('page'))->links() }}
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
                                    <form id="user-delete-form" method="POST" action="{{ route('user-job.destroy') }}">
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
        $(document).ready(function() {
            $('#state').change(function() {
                var stateID = $(this).val();
                if(stateID) {
                    $.ajax({
                        url: './get-cities/' + stateID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            $('#city').empty();
                            $('#city').append('<option value="">Select City</option>');
                            $.each(data, function(key, value) {
                                $('#city').append('<option value="'+ value.cityId +'">'+ value.cityName +'</option>');
                            });
                        }
                    });
                } else {
                    $('#city').empty();
                    $('#city').append('<option value="">Select City</option>');
                }
            });
        });


    </script>

@endsection
