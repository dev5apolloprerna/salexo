<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .thank-you-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .thank-you-card h1 {
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="thank-you-card">
        <h1>🎉 Thank You!</h1>
        <p>Your company client request has been submitted successfully.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
    </div>
</body>

</html>
