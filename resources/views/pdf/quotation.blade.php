<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Quotation #{{ $quotation->quotationId ?? '' }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .logo { width:140px; height:auto; }
  </style>
</head>
<body>
  @if(!empty($pic))
    <img class="logo" src="{{ $pic }}" alt="Company Logo">
  @endif

  <h1>Quotation #{{ $quotation->quotationId ?? '' }}</h1>
  <p>Client: {{ $quotation->client_name ?? '' }}</p>
  <!-- Add your table/lines here -->
</body>
</html>
