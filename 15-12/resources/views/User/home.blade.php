@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        {{--  <div class="auth-one-bg-position auth-one-bg" id="auth-particles" style="height: 600px">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>  --}}

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col">

                        <div class="h-100">
                            <div class="row mb-3 pb-1">
                                <div class="col-12">
                                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                        <div class="flex-grow-1">
                                            {{--  <h4 class="fs-16 mb-1">Admin Login</h4>  --}}
                                        </div>

                                    </div><!-- end card header -->
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate bg-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-bold text-white-50 text-truncate mb-0">
                                                        Today's Job</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-bold ff-secondary text-white mb-4">
                                                        <span class="counter-value"
                                                            data-target="{{ $todaysJob }}">{{ $todaysJob }}</span>
                                                    </h4>
                                                    <?php 
                                                    $date=date('Y-m-d');
                                                    ?>
                                                    <a href="{{ route('user-job.index',['date' => $date]) }}"
                                                        class="text-decoration-underline text-white-50">View
                                                        Today's Job</a>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-light rounded fs-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate bg-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-bold text-white-50 text-truncate mb-0">
                                                        Jobs</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-bold ff-secondary text-white mb-4">
                                                        <span class="counter-value"
                                                            data-target="{{ $Jobs }}">{{ $Jobs }}</span>
                                                    </h4>
                                                    <a href="{{ route('user-job.index') }}"
                                                        class="text-decoration-underline text-white-50">View
                                                        Jobs</a>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-light rounded fs-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate bg-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-bold text-white-50 text-truncate mb-0">
                                                        Applied Jobs</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-bold ff-secondary text-white mb-4">
                                                        <span class="counter-value"
                                                            data-target="{{ $appliedJob }}">{{ $appliedJob }}</span>
                                                    </h4>
                                                    
                                                    <a href="{{ route('applied-job.index') }}"
                                                        class="text-decoration-underline text-white-50">View
                                                       Applied Jobs</a>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-light rounded fs-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <!-- card -->
                                    <div class="card card-animate bg-secondary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="text-uppercase fw-bold text-white-50 text-truncate mb-0">
                                                       Today's Applied Jobs</p>
                                                </div>
                                            </div>
                                            <?php 
                                                    $date=date('Y-m-d');
                                                    ?>
                                            <div class="d-flex align-items-end justify-content-between mt-4">
                                                <div>
                                                    <h4 class="fs-22 fw-bold ff-secondary text-white mb-4">
                                                        <span class="counter-value"
                                                            data-target="{{ $todayappliedJob }}">{{ $todayappliedJob }}</span>
                                                    </h4>
                                                    <a href="{{ route('applied-job.index',['date'=>$date]) }}"
                                                        class="text-decoration-underline text-white-50">View
                                                       Today's Applied Jobs</a>
                                                </div>
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-soft-light rounded fs-3">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © {{ env('APP_NAME') }}
                    </div>

                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->


@endsection
