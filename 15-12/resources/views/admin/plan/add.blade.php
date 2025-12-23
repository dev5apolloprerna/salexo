@extends('layouts.app')



@section('title', 'Add Plan')



@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">



<div class="main-content">

    <div class="page-content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12">

                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                        <h4 class="mb-sm-0">Add Plan</h4>

                        <div class="page-title-right">

                            <a href="{{ route('plan.index') }}"

                                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">

                                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-lg-12">

                    <div class="card">

                        {{-- Alert Messages --}}

                        @include('common.alert')



                        <div class="card-body">

                            <form action="{{ route('plan.store') }}" method="POST" enctype="multipart/form-data" id="planForm">

                                @csrf

                                    <div class="row">

                                        <div class="col-md-6 mt-4">

                                            <div class="form-group {{ $errors->has('plan_name') ? 'has-error' : '' }}">

                                                <label for="plan_name">Plan Name <span style="color:red">*</span></label>

                                                <input type="text" id="plan_name" name="plan_name" class="form-control" value="{{ old('plan_name') }}" placeholder="Enter Plan Name" maxlength="50" required>

                                                @if($errors->has('plan_name'))

                                                    <span class="text-danger">

                                                        {{ $errors->first('plan_name') }}

                                                    </span>

                                                @endif

                                            </div> 

                                        </div>

                                         <div class="col-md-6 mt-4">

                                            <div class="form-group {{ $errors->has('plan_amount') ? 'has-error' : '' }}">

                                                <label for="plan_amount">Plan Amount <span style="color:red">*</span></label>

                                                <input type="text" id="plan_amount" name="plan_amount" class="form-control" value="{{ old('plan_amount') }}" placeholder="Enter Plan Amount" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>

                                                @if($errors->has('plan_amount'))

                                                    <span class="text-danger">

                                                        {{ $errors->first('plan_amount') }}

                                                    </span>

                                                @endif

                                            </div> 

                                        </div>
                                        <div class="col-md-6 mt-4">

                                            <div class="form-group {{ $errors->has('plan_days') ? 'has-error' : '' }}">

                                                <label for="plan_days">Plan Days <span style="color:red">*</span></label>

                                                <input type="text" id="plan_days" name="plan_days" class="form-control" value="{{ old('plan_days') }}" placeholder="Enter Plan Session" maxlength="11" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>

                                                @if($errors->has('plan_days'))

                                                    <span class="text-danger">

                                                        {{ $errors->first('plan_days') }}

                                                    </span>

                                                @endif

                                            </div> 

                                        </div>

                                    </div> 
                                    

                                <div class="card-footer mt-2">
                                        <div class="mb-3" style="float: right;">
                                        <button type="submit"
                                            class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                        <button type="reset" class="btn btn-primary float-right mr-3 mb-3 mx-2" >Clear</button>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@section('scripts')

@parent

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script type="text/javascript">

      function validateFile() {

            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp'];

            var fileExtension = document.getElementById('plan_image').value.split('.').pop().toLowerCase();

            var isValidFile = false;

            var image = document.getElementById('plan_image').value;



            for (var index in allowedExtension) {



                if (fileExtension === allowedExtension[index]) {

                    isValidFile = true;

                    break;

                }

            }

            if (image != "") {

                if (!isValidFile) {

                    alert('Allowed Extensions are : *.' + allowedExtension.join(', *.'));

                    $('#plan_image').val("")

                }

                return isValidFile;

            }



            return true;

        }


</script>

@endsection