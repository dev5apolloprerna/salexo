<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $data['API Name'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-height: 80px;
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="https://salexo.in/assets/images/logo.png" alt="Company Logo">
    </div>

    <h2>{{ $data['API Name'] }}</h2>
    <p><strong>Method:</strong> {{ $data['Method'] }}</p>

    <h3>Request Parameters</h3>
    <table>
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['Params'] as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
