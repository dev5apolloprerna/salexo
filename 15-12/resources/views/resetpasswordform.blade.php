<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #dfe9f3, #ffffff);
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
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .card img.logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .card h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #333;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
            text-align: left;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #78c046, #26a9cd);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.4s;
        }

        .btn:hover {
            background: #26a9cd;
        }

        .invalid-feedback {
            color: red;
            font-size: 13px;
            margin-top: -12px;
            margin-bottom: 10px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #78c046, #26a9cd);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.4s;
        }

        .btn-primary:hover {
            background: #26a9cd;
        }

        .btn-secondary {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
            color: #333;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
    </style>
</head>

<body>


    <div class="container">

        <div class="card">
            {{-- ✅ Logo --}}
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">

            {{-- ✅ Session error (like token expired) --}}
            @if (session('error'))
                <div style="color: red;" class="alert alert-danger text-start">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ✅ Validation errors --}}
            @if ($errors->any())
                <div style="color: red;" class="alert alert-danger text-start">
                    <strong>Oops! Something went wrong:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('set_new_password_submit') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="mobile" value="{{ $mobile }}">

                <h2>Reset Your Password</h2>

                <div>
                    <label for="password">New Password</label>
                    <input id="password" type="password" class="@error('password') is-invalid @enderror"
                        name="password" required placeholder="Enter new password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        placeholder="Confirm new password">
                </div>

                <button type="submit" class="btn-primary">Reset Password</button>
                <button type="reset" class="btn-secondary">Clear</button>
            </form>
        </div>
    </div>

</body>

</html>
