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
                                        <h5 class="text-primary">New Password</h5>
                                    </div>
                                   

                                    <div class="p-2 mt-4">
                                        <form id="myForm" action="{{ route('newpasswordsubmit') }}" method="post" class="form">
                                                    @csrf
                                            <input type="hidden" name="token"value="{{ $token }}">
                                            <div class="mb-3">
                                                <label>New Password</label>
                                             <input type="password" name="newpassword" class="form-control"  id="newpassword" placeholder="Enter your Password" id="sfpassword" required>
                                                    @error('newpassword')
                                                        <span class="text-danger">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                            </div>

                                            <div class="mb-3">
                                                 <label>Confirm Password</label>
                                                   <input type="password" name="confirmpassword" placeholder="Confirm Password" class="form-control" id="ccpassword" required>
                                                        @error('confirmpassword')
                                                            <span class="text-danger">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                </div>
                                            </div>
                                            <div class="mb-1"> 
                                                    @error('password')
                                                    <span class="text-danger">
                                                      <strong>{{ $message }}</strong>
                                                  </span>
                                                  @enderror

                                                @if (session('error'))
                                                    <span class="text-danger"> {{ session('error') }}</span>
                                                @endif

                                            </div>
                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Submit</button>
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
