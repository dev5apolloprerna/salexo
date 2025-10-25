@extends('layouts.app')

@section('title', 'Add Hotel')

@section('content')

<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Alert Messages --}}
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Change Password</h4>
                        <div class="page-title-right">
                            <a href="{{ route('hotelMaster.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="tab-pane" id="changePassword" role="tabpanel">
                                        <form action="{{ route('hotelMaster.updatepassword',$userid) }}" method="POST">
                                            @csrf
                                            <div class="row g-2" style="align-items: end;">
                                                <!--end col-->
                                                <div class="col-lg-3">
                                                    <div>
                                                        <span style="color:red;">*</span>New
                                                        Password
                                                        <input type="password"
                                                            class="form-control @error('new_password') is-invalid @enderror"
                                                            required name="new_password"id="newpasswordInput"
                                                            placeholder="Enter new password" minlength="4"
                                                            maxlength="20">
                                                        @error('new_password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!--end col-->
                                                <div class="col-lg-3">
                                                    <div>
                                                        <span style="color:red;">*</span>Confirm
                                                        Password
                                                        <input type="password"
                                                            class="form-control @error('new_confirm_password') is-invalid @enderror"
                                                            name="new_confirm_password" required id="confirmpasswordInput"
                                                            minlength="4" maxlength="20" placeholder="Confirm password">
                                                        @error('new_confirm_password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!--end col-->
                                                <div class="col-lg-3">
                                                    <div class="text-end">
                                                        <button type="submit" class="btn btn-success">Change
                                                            Password</button>
                                                    </div>
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection