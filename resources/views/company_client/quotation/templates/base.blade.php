@php
  $theme = $design ?? 'v1';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Quotation {{ $quotationNumber ?? 'QTN-0001' }} - {{ strtoupper($theme) }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --ink:#111; --muted:#666; --line:#D9D9D9; --bg:#fff; --accent:#0d6efd;
      --head-bg:#fafafa; --table-head:#fafafa; --grand-bg:#0f172a; --grand-fg:#fff;
      --chip-bg:#eef2ff; --chip-fg:#3730a3; --border:1px solid var(--line);
    }
    *{ box-sizing: border-box; }
    html,body{ margin:0; padding:0; background:#f5f6f8; color:var(--ink); }
    body{ font: 13px/1.45 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif; }
    .wrap{ max-width:820px; margin:18px auto; background:var(--bg); color:var(--ink); border:1px solid #eee; box-shadow:0 1px 8px rgba(0,0,0,.06); }
    .head{ display:flex; align-items:center; gap:16px; padding:18px 22px; border-bottom:var(--border); background:var(--head-bg); }
    .logo{ width:120px; height:60px; display:flex; align-items:center; justify-content:center; border:var(--border); border-radius:8px; overflow:hidden; background:#fff; }
    .logo img{ max-width:100%; max-height:100%; object-fit:contain; }
    .h-meta{ flex:1; }
    .title{ margin:0; font-size:22px; letter-spacing:.2px; }
    .subtitle{ margin:2px 0 0; color:var(--muted); font-size:12px; }
    .q-meta{ text-align:right; font-size:12px; line-height:1.6; }
    .chip{ display:inline-block; padding:3px 8px; border-radius:999px; background:var(--chip-bg); color:var(--chip-fg); font-weight:600; }

    .block-row{ display:flex; gap:18px; padding:16px 22px; }
    .block{ flex:1; border:var(--border); border-radius:10px; padding:12px; }
    .block h3{ margin:0 0 8px; font-size:13px; text-transform:uppercase; letter-spacing:.3px; color:#374151; }
    .kv{ margin:2px 0; font-size:12px; } .kv .k{ width:110px; display:inline-block; color:var(--muted); }

    table{ width:100%; border-collapse:collapse; }
    .items{ margin:10px 22px 0; border:var(--border); border-radius:10px; overflow:hidden; }
    .items th, .items td{ padding:10px; font-size:12px; vertical-align:top; }
    .items thead th{ background:var(--table-head); border-bottom:var(--border); text-align:left; font-weight:600; letter-spacing:.2px; }
    .items tbody td{ border-bottom:1px solid #eee; }
    .items tfoot td{ border-top:var(--border); font-weight:600; }
    .text-right{ text-align:right; } .text-center{ text-align:center; }
    .w-40{ width:40px; } .w-70{ width:70px; } .w-90{ width:90px; }
    .nowrap{ white-space:nowrap; }

    .totals{ margin:10px 22px 0; display:grid; grid-template-columns:1fr 320px; gap:16px; }
    .note{ border:var(--border); border-radius:10px; padding:10px 12px; font-size:12px; }
    .sum{ border:var(--border); border-radius:10px; overflow:hidden; }
    .sum-row{ display:flex; padding:8px 12px; border-bottom:1px solid #eee; font-size:12px; }
    .sum-row:last-child{ border-bottom:none; }
    .sum-row .lbl{ flex:1; } .sum-row .val{ width:120px; text-align:right; }
    .sum .grand{ background:var(--grand-bg); color:var(--grand-fg); font-weight:700; }

    .terms{ margin:14px 22px 18px; border:var(--border); border-radius:10px; overflow:hidden; }
    .terms h3{ margin:0; padding:10px 12px; background:var(--table-head); border-bottom:var(--border); font-size:13px; text-transform:uppercase; }
    .terms ol{ margin:10px 18px 14px; padding-left:16px; font-size:12px; }
    .foot{ padding:10px 22px 18px; font-size:11px; color:#6b7280; display:flex; justify-content:space-between; align-items:center; }
    .stamp{ width:140px; height:60px; border:1px dashed var(--line); display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:11px; color:#9ca3af; }

    /* ===== THEMES ===== */
    /* v1 - Classic Blue */
    .t-v1{ --accent:#0d6efd; --grand-bg:#0f172a; --grand-fg:#fff; --head-bg:#f8fafc; --table-head:#f8fafc; --chip-bg:#e0f2fe; --chip-fg:#075985; }

    /* v2 - Emerald Card */
    .t-v2{ --accent:#047857; --grand-bg:#064e3b; --grand-fg:#ecfdf5; --head-bg:#ecfdf5; --table-head:#ecfdf5; --chip-bg:#d1fae5; --chip-fg:#065f46; }
    .t-v2 .head{ border-bottom:2px solid #10b981; }
    .t-v2 .items thead th{ border-bottom:2px solid #10b981; }

    /* v3 - Minimal Lines */
    .t-v3{ --accent:#111827; --grand-bg:#111827; --grand-fg:#fff; --head-bg:#ffffff; --table-head:#ffffff; }
    .t-v3 .wrap{ border:1px solid #111827; }
    .t-v3 .items, .t-v3 .block, .t-v3 .terms, .t-v3 .sum, .t-v3 .note{ border:1px solid #111827; }

    /* v4 - Warm Brand */
    .t-v4{ --accent:#c2410c; --grand-bg:#7c2d12; --grand-fg:#fff7ed; --head-bg:#fff7ed; --table-head:#ffedd5; --chip-bg:#ffedd5; --chip-fg:#7c2d12; }
    .t-v4 .head{ border-bottom:2px solid #fb923c; }
    .t-v4 .items thead th{ border-bottom:2px solid #fb923c; }

    /* v5 - Indigo Stripe */
    .t-v5{ --accent:#3730a3; --grand-bg:#3730a3; --grand-fg:#eef2ff; --head-bg:#eef2ff; --table-head:#eef2ff; --chip-bg:#e0e7ff; --chip-fg:#312e81; }
    .t-v5 .head{ background:linear-gradient(90deg,#eef2ff 0%, #fff 60%); }
    .t-v5 .items thead th{ border-bottom:2px solid #4338ca; }

    @media print{
      body{ background:#fff; }
      .wrap{ box-shadow:none; border:none; margin:0; max-width:100%; }
      .head, .block-row, .items, .totals, .terms, .foot{ page-break-inside: avoid; }
      a{ color:inherit; text-decoration:none; }
    }
  </style>
</head>
<body class="t-{{ $theme }}">
  <div class="wrap">

    {{-- HEADER --}}
    <div class="head">
      <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
      <div class="h-meta">
        <h1 class="title">{{ $companyName }}</h1>
        <p class="subtitle">{{ $companyAddress }} · GSTIN: {{ $companyGstin }} · Phone: {{ $companyPhone }} · Email: {{ $companyEmail }}</p>
      </div>
      <div class="q-meta">
        <div class="chip">Quotation</div>
        <div>No: <strong>{{ $quotationNumber }}</strong></div>
        <div>Date: <strong>{{ $quotationDate }}</strong></div>
        <div>Valid Till: <strong>{{ $validTill }}</strong></div>
      </div>
    </div>

    {{-- PARTIES --}}
    <div class="block-row">
      <div class="block">
        <h3>Bill To (Party)</h3>
        <div class="kv"><span class="k">Party Name</span>: <strong>{{ $partyName }}</strong></div>
        <div class="kv"><span class="k">Address</span>: {{ $partyAddress }}</div>
        <div class="kv"><span class="k">GSTIN</span>: {{ $partyGstin }}</div>
        <div class="kv"><span class="k">Contact</span>: {{ $partyPhone }}</div>
      </div>
      <div class="block">
        <h3>Company</h3>
        <div class="kv"><span class="k">Company</span>: <strong>{{ $companyName }}</strong></div>
        <div class="kv"><span class="k">Address</span>: {{ $companyAddress }}</div>
        <div class="kv"><span class="k">GSTIN</span>: {{ $companyGstin }}</div>
        <div class="kv"><span class="k">State</span>: {{ $companyState }}</div>
      </div>
    </div>

    {{-- ITEMS --}}
    @php $subTotal = 0; @endphp
    <table class="items">
      <thead>
      <tr>
        <th class="w-40 text-center">#</th>
        <th>Product / Service</th>
        <th class="w-70 text-center">HSN/SAC</th>
        <th class="w-70 text-center">Qty</th>
        <th class="w-90 text-right">Rate (₹)</th>
        <th class="w-90 text-right">Amount (₹)</th>
      </tr>
      </thead>
      <tbody>
      @foreach(($items ?? []) as $i => $it)
        @php $amt = ($it['qty'] ?? 0) * ($it['rate'] ?? 0); $subTotal += $amt; @endphp
        <tr>
          <td class="text-center">{{ $i+1 }}</td>
          <td><strong>{{ $it['name'] }}</strong><div class="muted">{{ $it['desc'] ?? '' }}</div></td>
          <td class="text-center">{{ $it['hsn'] ?? '-' }}</td>
          <td class="text-center">{{ number_format($it['qty'] ?? 0, 2) }}</td>
          <td class="text-right">{{ number_format($it['rate'] ?? 0, 2) }}</td>
          <td class="text-right">{{ number_format($amt, 2) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>

    {{-- TOTALS --}}
    @php
      $discount = $discount ?? 0;
      $taxable  = max(0, ($subTotal - $discount));
      $gstRate  = $gstRate ?? 18;
      $isInter  = $isInterState ?? false;
      if($isInter){ $igst = round($taxable * $gstRate / 100, 2); $cgst=$sgst=0; }
      else { $cgst = round($taxable * ($gstRate/2) / 100, 2); $sgst = round($taxable * ($gstRate/2) / 100, 2); $igst=0; }
      $payable = $taxable + $cgst + $sgst + $igst;
      $roundOff = round($payable - floor($payable), 2);
      $grand    = round($payable);
    @endphp

    <div class="totals">
      <div class="note">
        <strong>Notes:</strong>
        <div class="muted">Prices in INR; taxes extra as applicable.</div>
        <div><strong>Payment Terms:</strong> {{ $paymentTerms }}</div>
        <div><strong>Delivery:</strong> {{ $delivery }}</div>
        <div><strong>Warranty:</strong> {{ $warranty }}</div>
      </div>

      <div class="sum">
        <div class="sum-row"><div class="lbl">Sub Total</div><div class="val">&#8377; {{ number_format($subTotal,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Discount</div><div class="val">- &#8377; {{ number_format($discount,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Taxable Value</div><div class="val">&#8377; {{ number_format($taxable,2) }}</div></div>
        @if(!$isInter)
          <div class="sum-row"><div class="lbl">CGST ({{ $gstRate/2 }}%)</div><div class="val">&#8377; {{ number_format($cgst,2) }}</div></div>
          <div class="sum-row"><div class="lbl">SGST ({{ $gstRate/2 }}%)</div><div class="val">&#8377; {{ number_format($sgst,2) }}</div></div>
        @else
          <div class="sum-row"><div class="lbl">IGST ({{ $gstRate }}%)</div><div class="val">&#8377; {{ number_format($igst,2) }}</div></div>
        @endif
        <div class="sum-row"><div class="lbl">Round Off</div><div class="val">&#8377; {{ number_format($roundOff,2) }}</div></div>
        <div class="sum-row grand"><div class="lbl">Grand Total</div><div class="val">&#8377; {{ number_format($grand,0) }}</div></div>
      </div>
    </div>

    {{-- TERMS --}}
    <div class="terms">
      <h3>Terms &amp; Conditions</h3>
      <ol>
        <li>Goods once sold will not be taken back or exchanged.</li>
        <li>Any disputes are subject to {{ $jurisdiction ?? 'Surat, Gujarat' }} jurisdiction only.</li>
        <li>Quotation valid till {{ $validTill }}.</li>
        <li>Payments to be made in favour of <strong>{{ $companyName }}</strong>.</li>
        <li>Delivery schedule may vary due to unforeseen circumstances.</li>
        @foreach(($extraTerms ?? []) as $t) @if($t)<li>{{ $t }}</li>@endif @endforeach
      </ol>
    </div>

    {{-- FOOT --}}
    <div class="foot">
      <div>
        <div><strong>Bank Details</strong></div>
        <div class="muted">
          A/C Name: {{ $bankName }} · A/C No: {{ $bankAccount }} · IFSC: {{ $bankIfsc }} · Branch: {{ $bankBranch }}
        </div>
      </div>
      <div class="stamp">Authorised Signatory</div>
    </div>

  </div>
</body>
</html>
