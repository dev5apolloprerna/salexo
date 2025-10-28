@extends('layouts.client')

@section('title', 'Profile')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4 border-bottom">
                    <h1 class="h3 mb-0 text-gray-800">Profile</h1>
                </div>

                {{-- Alert Messages --}}
                @include('common.alert')

                {{-- Page Content --}}
                <div class="row">
                    <div class="col-md-3 border-right">
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                            <img class="rounded-circle mt-5" width="150px"
                                src="{{ asset('assets/images/users/undraw_profile.webp') }}">
                            <span class="font-weight-bold">{{ auth()->user()->full_name }}</span>
                            <?php
                            $id = Auth::guard('web_employees')->user()->emp_id;
                            $user = App\Models\Employee::where(['emp_id' => $id])->first();
                            $role = App\Models\Employee::select('employee_master.emp_id', 'roles.name')
                                    ->where('employee_master.emp_id', $id)
                                    ->join('roles', 'employee_master.role_id', '=', 'roles.id')
                                    ->first();
                            ?>
                            <span class="text-black-50">
                                <i>Role:
                                    {{ $role->name }}

                                </i>
                            </span>
                            <span class="text-black-50">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <div class="col-md-9 border-right">
                        {{-- Profile --}}
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Profile</h4>
                            </div>
                            <form action="{{ route('empprofile.update') }}" method="POST">
                                @csrf
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label class="labels">Name</label>
                                        <input type="text" class="form-control @error('emp_name') is-invalid @enderror"
                                            name="emp_name" placeholder="First Name"
                                            value="{{ old('emp_name') ? old('emp_name') : auth()->user()->emp_name }}">

                                        @error('emp_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="labels">Email</label>
                                        <input type="email" class="form-control @error('emp_email') is-invalid @enderror"
                                            name="emp_email" placeholder="First Name"
                                            value="{{ old('emp_email') ? old('emp_email') : auth()->user()->emp_email }}">

                                        @error('emp_email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="labels">Phone</label>
                                        <input type="text"
                                            class="form-control @error('emp_mobile') is-invalid @enderror"
                                            name="emp_mobile" maxlength="10"
                                            value="{{ old('emp_mobile') ? old('emp_mobile') : auth()->user()->emp_mobile }}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                            placeholder="Mobile Number">
                                        @error('emp_mobile')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label class="labels">Login Id</label>
                                        <input type="text"
                                            class="form-control @error('emp_loginId') is-invalid @enderror"
                                            name="emp_loginId" maxlength="20"
                                            value="{{ old('emp_loginId') ? old('emp_loginId') : auth()->user()->emp_loginId }}"  placeholder="Login Id">
                                        @error('emp_loginId')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                     <div class="col-md-6 mt-2">
                                            <label class="labels">Delivery Terms</label>
                                            <input class="form-control" id="basic-form-name" name="delivery_terms" type="text"
                                                placeholder="Enter Delivery Terms" value="{{ old('delivery_terms') ? old('delivery_terms') : $users1->delivery_terms }}">
                                        @error('delivery_terms')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <label class="labels">Payment Terms</label>
                                            <input class="form-control" id="basic-form-name" name="payment_terms" type="text"
                                                placeholder="Enter Payment Terms" value="{{ old('payment_terms') ? old('payment_terms') : $users1->payment_terms }}">
                                            @error('payment_terms')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                                            <span style="color:red;"></span>Terms & Condition</label>
                                            <textarea class="form-control" id="fetchtermcondition" name="terms_condition">{{ old('terms_condition') ? old('terms_condition') : $users1->terms_condition }} </textarea>
                                        </div>

                                </div>
                                <div class="mt-5 text-center">
                                    <button class="btn btn-primary profile-button" type="submit">Update Profile</button>
                                </div>
                            </form>
                        </div>

                        <hr>
                        {{-- Change Password --}}
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Change Password</h4>
                            </div>

                            <form action="{{ route('empprofile.userchangepassword') }}" method="POST">
                                @csrf
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label class="labels">Current Password</label>
                                        <input type="password" name="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            placeholder="Current Password"  required>
                                        @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="labels">New Password</label>
                                        <input type="password" name="new_password"
                                            class="form-control @error('new_password') is-invalid @enderror" required
                                            placeholder="New Password" minlength="6" maxlength="10">
                                        @error('new_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="labels">Confirm Password</label>
                                        <input type="password" name="new_confirm_password"
                                            class="form-control @error('new_confirm_password') is-invalid @enderror"
                                            required placeholder="Confirm Password" minlength="6" maxlength="10">
                                        @error('new_confirm_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-5 text-center">
                                    <button class="btn btn-success profile-button" type="submit">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>



            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('terms_condition');
        
    </script>
    @endsection