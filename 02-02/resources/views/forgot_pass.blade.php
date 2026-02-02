<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background:  linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .card {
            background-color: #fff;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .card img.logo {
            width: 200px;
            margin-bottom: 25px;
        }

        .card h2 {
            margin-bottom: 25px;
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
            text-align: left;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #246fdb;
        }

        .btn-secondary {
            width: 100%;
            margin-top: 10px;
            padding: 11px;
            border: 1px solid #ccc;
            background-color: #f5f5f5;
            color: #333;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background-color: #eaeaea;
        }

        .alert {
            max-width: 420px;
            margin: 10px auto;
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
        }

        .text-danger {
            color: #d9534f;
            font-size: 13px;
            display: block;
            text-align: left;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        footer {
            position: absolute;
            bottom: 20px;
            font-size: 13px;
            color: #f0f0f0;
        }
    </style>
</head>

<body>

    @if ($errors->any())
        <div class="alert">
            <strong>Oops! Something went wrong:</strong>
            <ul class="mb-0" style="text-align:left;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <div class="card">
            {{-- Logo --}}
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">

            <h2>Forgot Password</h2>

            {{-- Alerts --}}
            @include('common.alert')

            <form method="POST" action="{{ route('password_forgot_submit') }}">
                @csrf

                <div class="form-group">
                    <label for="mobile">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" id="mobile"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Enter your mobile number"
                        maxlength="10" value="{{ old('mobile') }}" required autocomplete="off">

                    @if ($errors->has('mobile'))
                        <span class="text-danger">{{ $errors->first('mobile') }}</span>
                    @endif
                </div>

                <button type="submit" class="btn-primary">Send Reset Link on WhatsApp</button>
                <button type="reset" class="btn-secondary">Clear</button>
                <a href="{{ route('user_login') }}"
                    style="display:block; margin-top:15px; color:#246fdb; font-weight:500; text-decoration:underline;">
                    ← Return to Login Page
                </a>

            </form>

            <!-- Footer -->
            <footer>
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </footer>
        </div>
    </div>

</body>

</html>
