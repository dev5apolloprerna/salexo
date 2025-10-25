@extends('auth.layouts.app')

@section('title', 'User Login')

@section('content')

    <div class="row justify-content-center">

        <div class="auth-page-wrapper">
                            {{-- Alert Messages --}}
                @include('common.alert')

            <!-- auth page bg -->
            <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
                  
                <!--Edit Modal End -->
            </div>

            <!-- auth page content -->
            <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mt-sm-5 mb-4 text-white-50">
                                <div>
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="" height="70">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-5">
                            <div class="card mt-4">

                                <div class="card-body p-4">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary">Welcome !</h5>
                                    </div>
                                    @if (session('error'))
                                        <span class="text-danger"> {{ session('error') }}</span>
                                    @endif

                                    <div class="p-2 mt-4">
                                        <form method="POST" action="{{ route('userLogin') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="username" class="form-label">LoginId</label>

                                                <input id="loginId" type="loginId"
                                                    class="form-control  @error('loginId') is-invalid @enderror"
                                                    name="loginId" value="{{ old('loginId') }}" required autocomplete="loginId"
                                                    autofocus placeholder="Enter loginId.">

                                                @error('loginId')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password-input">Password</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password"
                                                        class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                        name="password" required autocomplete="current-password"
                                                        placeholder="Enter Password" id="password-input">
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                </div>
                                            </div>
                                            <a class="mx-1" title="Edit" href="#"
                                                data-bs-toggle="modal" data-bs-target="#userpwdModal">
                                                Forgot Password?
                                            </a>
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Sign In</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>

                            <!-- end card -->
                         </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end auth page content -->
            <div class="modal fade flip" id="userpwdModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalLabel">Forgot Password Email</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="close-modal"></button>
                            </div>
                            <form method="POST" action="{{ route('password_forgot') }}" autocomplete="off"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <span style="color:red;">*</span>Email
                                         <input type="email" id="user_email" name="user_email" class="form-control" maxlength="50" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary mx-2"
                                            id="add-btn">Submit</button>
                                        <button type="button" class="btn btn-primary mx-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <!-- footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy;
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{env('APP_NAME')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- end auth-page-wrapper -->
            
    </div>
@endsection
