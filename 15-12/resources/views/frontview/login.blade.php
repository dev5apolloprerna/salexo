@extends('layouts.front')
@section('title', 'Home')
@section('content')

    <body class="login-page">
        <div class="container auth-wrap">
            <div class="row g-0 gap-3">
                <!-- Left: Carousel -->
                <div class="col-lg-5  align-content-center" style="height: 550px;border-radius: 50px;overflow: hidden;">
                    <div id="loginCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                        data-bs-interval="3500">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('assets/front/images/bar-chart.png') }}" alt="Salexo CRM — Dashboard"
                                    style="height: 350px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('assets/front/images/salexo-about.png') }}"
                                    alt="Salexo CRM — Sales Pipeline" style="height: 350px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('assets/front/images/app.jpg') }}" alt="Salexo CRM — Reports"
                                    style="height: 350px; object-fit: cover;">
                            </div>
                        </div>
                        <!-- Optional controls (hide if you prefer super clean) -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#loginCarousel"
                            data-bs-slide="prev" aria-label="Previous slide">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#loginCarousel"
                            data-bs-slide="next" aria-label="Next slide">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>

                <!-- Right: Login form -->
                <div class="col-lg-6 auth-right">
                    <div class="login-card">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <img src="{{ asset('assets/front/images/logo (1).png') }}" alt="Salexo"
                                class="brand-mark text-center">
                        </div>
                        <h1>Login</h1>
                        <div class="login-sub">Welcome back! Sign in to continue.</div>

                        @include('common.alert')

                        <form id="loginForm" method="POST" action="{{ route('userLogin') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="mobile">Mobile Number</label>
                                <input type="text" id="mobile" name="mobile" class="form-control" maxlength="10"
                                    minlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    placeholder="Enter mobile number" required autocomplete="off" />

                            </div>

                            <div class="mb-2">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Enter password" minlength="6" required autocomplete="off" />
                                    <button type="button" class="btn btn-outline-secondary" id="togglePwd" tabindex="-1"
                                        aria-label="Show/Hide password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50 w-lg-25">Sign In</button>
                            </div>

                            <span class="rule"></span>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>

@endsection

@section('scripts')
    <script>
        (function() {
            const form = document.getElementById('loginForm');
            const success = document.getElementById('loginSuccess');
            const togglePwd = document.getElementById('togglePwd');
            const pwd = document.getElementById('password');

            // Toggle password visibility
            togglePwd.addEventListener('click', () => {
                const isPW = pwd.getAttribute('type') === 'password';
                pwd.setAttribute('type', isPW ? 'text' : 'password');
                togglePwd.querySelector('i').className = isPW ? 'bi bi-eye-slash' : 'bi bi-eye';
            });

        })();
    </script>
@endsection
