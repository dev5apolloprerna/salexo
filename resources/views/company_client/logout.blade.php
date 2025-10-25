<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> Logged Out</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
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
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
            position:relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .icon {
            font-size: 40px;
            margin: 10px 0 20px;
        }

        h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 8px;
        }

        p {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(to right, #141E30 0%, #243B55  51%, #141E30  100%)!important;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            opacity: 0.9;
        }

        footer {
            position: absolute;
            bottom: 20px;
            font-size: 13px;
            color: #f0f0f0;
            text-align:center;
            width:100%;
        }
    </style>
</head>

<body>


    <div class="container">
        <div class="card">

            <!-- Logo -->
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
            <div class="icon">☕</div>
            <h2>You are Logged Out</h2>
            <p>Thanks for visiting {{ config('app.name') }}. See you soon!</p>
            <a href="{{ route('user_login') }}" class="btn">Sign In Again</a>

            <!-- Footer -->
           
        </div>
         <footer class="text-center">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </footer>
    </div>

</body>

</html>
