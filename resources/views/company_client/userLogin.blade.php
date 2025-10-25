@extends('auth.layouts.app')
@section('title', 'Employee Login')

@section('content')


    <style>
        body {
            /*background: linear-gradient(to right, #3ccf90, #246fdb);*/
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

            body{
            background:linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
        }

        .login-container {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background-color: white;
            padding: 40px 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-box img {
            width: 200px;
            margin-bottom: 25px;
        }

        .login-box h3 {
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            height: 45px;
        }

        .form-label {
            float: left;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .btn-primary {
            background: linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
            border: none;
            height: 45px;
            font-weight: 600;
            margin-top: 15px;
            width: 100%;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: #246fdb;
        }

        .forgot-link {
            display: block;
            text-align: right;
            margin-top: 8px;
            font-size: 14px;
        }

        .text-danger {
            font-size: 14px;
        }

        .input-group {
            position: relative;
            /* keep your button in-context */
        }

        .input-group input.form-control {
            padding-right: 48px;
            /* room for a slightly wider button */
            position: relative;
            /* establish new stacking context */
            z-index: 1;
            /* sit below the button */
        }

        .input-group button {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 48px;
            /* match the padding-right */
            border: none;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 16px;

            z-index: 2;
            /* float above the input */
            cursor: pointer;
        }
    </style>

    <div class="login-container">
        <div class="login-box">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            <h3>Welcome</h3>

            @if ($errors->any())
                <div class="alert alert-danger text-start">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <span class="text-danger">{{ session('error') }}</span>
            @endif

            <form method="POST" action="{{ route('userLogin') }}">
                @csrf
                <div class="mb-3 text-start">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}"
                        class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter your mobile number"
                        required autofocus maxlength="10" autocomplete="off">
                    @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password-input">Password</label>
                    <div class="position-relative auth-pass-inputgroup mb-3">
                        <input type="password"
                            class="form-control pe-5 password-input @error('password') is-invalid @enderror" name="password"
                            required autocomplete="current-password" placeholder="Enter Password" id="password-input">
                        <button style="margin-top: 28px;"
                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                            type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    </div>
                </div>


                <a href="{{ route('password_forgot') }}" class="forgot-link">Forgot Password?</a>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

        </div>
    </div>

    <footer class="text-center mt-4 text-white-50">
        <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
    </footer>

@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('password-toggle');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    this.innerHTML = type === 'password' ?
                        '<i class="ri-eye-fill text-xl"></i>' :
                        '<i class="ri-eye-off-fill text-xl"></i>';
                });
            }
        });
    </script>

@endsection
