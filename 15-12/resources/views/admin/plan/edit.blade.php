@extends('layouts.app')

@section('title', 'Edit Plan')

@section('content')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Edit Plan</h4>
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
                            <div class="card-body">
                                    <form action="{{ route('plan.update', $data['plan_id']) }}" method="POST" enctype="multipart/form-data" id="editplanForm">

                                    @csrf
                                    <input type="hidden" name="plan_id" id="plan_id" value="{{ $data['plan_id'] }}">
                                     <div class="row">
                                        <div class="col-md-6 mt-4">
                                            <div class="form-group {{ $errors->has('plan_name') ? 'has-error' : '' }}">
                                                <label for="plan_name">Plan Name <span style="color:red">*</span></label>
                                                <input type="text" id="plan_name" name="plan_name" class="form-control"  maxlength="50" value="{{$data['plan_name']}}" placeholder="Enter Plan Name" required>
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
                                                <input type="text" id="plan_amount" name="plan_amount" class="form-control"  maxlength="10" value="{{$data['plan_amount']}}" placeholder="Enter Plan Amount" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
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
                                                <input type="text" id="plan_days" name="plan_days" class="form-control"  maxlength="11" placeholder="Enter Plan Session" value="{{$data['plan_days']}}"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required>
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
                                                class="btn btn-success btn-user float-right" >Update</button>
                                                <a class="btn btn-primary float-right mr-3"
                                                    href="{{ route('plan.index') }}">Cancel</a>
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
         $("#editplanForm").validate({
    ignore: "", // Include all form elements, even hidden ones

            rules: {
            category_id: {
                required: true,
            },
            plan_name: {
                required: true,
            },
            plan_amount: {
                required: true,
            },
            plan_days: {
                required: true,
            },
            plan_description: {
                required: true,
            },detail_description: {
                required: true,
            },
           
        },
        messages: {
            category_id: {
                required: "Please Select Category",
            },
            plan_name: {
                required: "Please Enter Plan Name",
            },
            
             plan_days: {
                required: "Please Enter Plan Session",
            },plan_amount: {
                required: "Please Enter Plan Amount",
            },
            plan_description: {
                required: "Please Enter Plan Description",
            }, detail_description: {
                required: "Please Enter Detail Description",
            },
            
        },

        errorPlacement: function (error, element) {
            error.insertAfter(element);
            error.css("color", "red"); // Set error message color to red
        },
        submitHandler: function (form) {
            form.submit();
            $('section').addClass('blurred'); // Blur the page
            $('#loader-overlay').show();   // Show overlay
            $('#loader').show();           // Show spinner
            
        }
    });
</script>
@endsection
