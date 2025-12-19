@extends('layouts.app')

@section('title', 'Add Job')

@section('content')


<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Alert Messages --}}
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Add Job</h4>
                        <div class="page-title-right">
                            <a href="{{ route('user-job.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="live-preview">
                                <form method="POST"  action="{{ route('user-job.store') }}" enctype="multipart/form-data" id="myForm">
                                @csrf
                                    <div class="row gy-4">
                                        <div class="col-lg-6 col-md-6">
                                                <span style="color:red;">*</span><label>Job Title</label>
                                                <input type="text" class="form-control"
                                                placeholder="Enter Name" name="jobTitle" id="jobTitle"
                                                autocomplete="off" value="{{ old('jobTitle') }}" maxlength="50" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>Education</label>
                                            <select class="form-control" name="education" {{ old('education') }} required>
                                                <option value="">Select Education</option>
                                                @foreach($Education as $evalue)
                                                    <option value="{{$evalue->educationId}}">{{$evalue->educationName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>Experience</label>
                                            <select class="form-control" name="experience" {{ old('experience') }} required>
                                                <option value="">Select Experience</option>
                                                @foreach($Experience as $evalue)
                                                    <option value="{{$evalue->experienceId}}">{{$evalue->experienceName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>Salary</label>
                                            <select class="form-control" name="salary" {{ old('salary') }} required>
                                                <option value="">Select Salary</option>
                                                @foreach($SalaryRange as $svalue)
                                                    <option value="{{$svalue->salaryId}}">{{$svalue->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>Industry</label>
                                            <select class="form-control" name="industry" {{ old('industry') }} required>
                                                <option value="">Select Industry</option>
                                                @foreach($Industry as $ivalue)
                                                    <option value="{{$ivalue->industryId}}">{{$ivalue->IndustryName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>Function</label>
                                            <select class="form-control" name="function" {{ old('function') }} required>
                                                <option value="">Select Function</option>
                                                @foreach($Functions as $fvalue)
                                                    <option value="{{$fvalue->functionId}}">{{$fvalue->functionName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>State</label>
                                            <select class="form-control" name="state" id="state" {{ old('state') }} required>
                                                <option value="">Select State</option>
                                                @foreach($State as $svalue)
                                                    <option value="{{$svalue->stateId}}">{{$svalue->stateName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <span style="color:red;">*</span><label>City</label>
                                            <select class="form-control" name="city"  id="city" {{ old('city') }} required>
                                                <option value="">Select City</option>
                                               <!--  @foreach($City as $cvalue)
                                                    <option value="{{$cvalue->cityId}}">{{$cvalue->cityName}}</option>
                                                @endforeach -->
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-4">
                                            <div>
                                                <span style="color:red;">*</span><label>Job End Date</label>
                                                <input type="date" class="form-control" name="job_end_date" min="<?php echo  date('Y-m-d')?>" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-6">
                                            <label>Job Description</label>
                                            <textarea  class="form-control ckeditor" name="jobDescription" id="jobDescription" cols="4">{{ old('jobDescription') }}</textarea>
                                        </div>
                                    <div class="card-footer mt-5" style="float: right;">
                                        <button type="submit" name="save" value="save" class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                        <button type="submit" name="saveAdd" value="saveAdd" class="btn btn-primary btn-user float-right mb-3 mx-2">Save & Add New</button>
                                        <a class="btn btn-primary float-right mr-3 mb-3 mx-2" href="{{ route('user-job.index') }}">Cancel </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

@section('scripts')
<script>
    function validateFile() 
    {
        var allowedExtension = ['jpeg', 'jpg', 'png', 'webp'];
        var fileExtension = document.getElementById('strPhoto').value.split('.').pop().toLowerCase();
        var isValidFile = false;
        var image = document.getElementById('strPhoto').value;

        for (var index in allowedExtension) {

            if (fileExtension === allowedExtension[index]) {
                isValidFile = true;
                break;
            }
        }
        if (image != "") {
            if (!isValidFile) {
                alert('Allowed Extensions are : *.' + allowedExtension.join(', *.'));
                $('#strPhoto').val("");
            }
            return isValidFile;
        }

        return true;
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
