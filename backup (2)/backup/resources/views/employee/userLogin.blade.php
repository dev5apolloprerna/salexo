@extends('auth.layouts.app')
@section('title', 'Employee Login')

@section('content')
    <style>
        body {
            background: linear-gradient(to right, #3ccf90, #246fdb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
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
            width: 120px;
            margin-bottom: 15px;
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
            background: linear-gradient(to right, #3ccf90, #246fdb);
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
    </style>

    <div class="login-container">
        <div class="login-box">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            <h3>Welcome Back</h3>

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

                <div class="mb-2 text-start">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter your password" required>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
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
