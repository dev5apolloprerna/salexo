<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Quotation {{ $quotationNumber ?? 'QTN-0001' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* ---------- Base ---------- */
    :root{
      --ink:#111;
      --muted:#666;
      --line:#D9D9D9;
      --bg:#fff;
      --accent:#0d6efd;
    }
    *{ box-sizing: border-box; }
    html,body{ margin:0; padding:0; background:#f5f6f8; color:var(--ink); }
    body{ font: 13px/1.45 "Helvetica Neue", Arial, sans-serif; }
    .wrap{
      max-width: 820px; /* ~ A4 width */
      margin: 24px auto;
      background: var(--bg);
      color: var(--ink);
      border: 1px solid #eee;
      box-shadow: 0 1px 8px rgba(0,0,0,.05);
    }

    /* ---------- Header ---------- */
    .head{ display:flex; align-items:center; gap:16px; padding:18px 22px; border-bottom:1px solid var(--line); }
    .logo{
      width: 120px; height: 60px;
      display:flex; align-items:center; justify-content:center;
      border:1px solid var(--line); border-radius:6px; overflow:hidden;
    }
    .logo img{ max-width:100%; max-height:100%; object-fit:contain; }
    .h-meta{ flex:1; }
    .title{ margin:0; font-size:22px; letter-spacing:.3px; }
    .subtitle{ margin:2px 0 0; color:var(--muted); font-size:12px; }
    .q-meta{
      text-align:right; font-size:12px; line-height:1.6;
    }
    .q-meta strong{ color:var(--ink); }

    /* ---------- Blocks ---------- */
    .block-row{ display:flex; gap:18px; padding:16px 22px; }
    .block{ flex:1; border:1px solid var(--line); border-radius:8px; padding:12px 12px; }
    .block h3{
      margin:0 0 8px; font-size:13px; letter-spacing:.3px; text-transform:uppercase; color:#333;
    }
    .kv{ margin:2px 0; font-size:12px; }
    .kv .k{ width:110px; display:inline-block; color:var(--muted); }
    .muted{ color:var(--muted); }

    /* ---------- Items Table ---------- */
    table{ width:100%; border-collapse:collapse; }
    .items{ margin:10px 22px 0; border:1px solid var(--line); border-radius:8px; overflow:hidden; }
    .items th, .items td{ padding:10px 10px; font-size:12px; vertical-align:top; }
    .items thead th{
      background:#fafafa; border-bottom:1px solid var(--line);
      text-align:left; font-weight:600; letter-spacing:.2px;
    }
    .items tbody td{ border-bottom:1px solid #eee; }
    .items tfoot td{ border-top:1px solid var(--line); font-weight:600; }
    .text-right{ text-align:right; }
    .text-center{ text-align:center; }
    .w-40{ width:40px; }
    .w-70{ width:70px; }
    .w-90{ width:90px; }
    .nowrap{ white-space:nowrap; }

    /* ---------- Totals ---------- */
    .totals{
      margin:10px 22px 0; display:grid; grid-template-columns: 1fr 320px; gap:16px;
    }
    .note{
      border:1px solid var(--line); border-radius:8px; padding:10px 12px; font-size:12px;
    }
    .sum{
      border:1px solid var(--line); border-radius:8px; overflow:hidden;
    }
    .sum-row{ display:flex; padding:8px 12px; border-bottom:1px solid #eee; font-size:12px; }
    .sum-row:last-child{ border-bottom:none; }
    .sum-row .lbl{ flex:1; }
    .sum-row .val{ width:120px; text-align:right; }
    .sum .grand{ background:#0f172a; color:#fff; font-weight:700; }
    .currency{ font-family: inherit; }

    /* ---------- Terms ---------- */
    .terms{
      margin:14px 22px 18px; border:1px solid var(--line); border-radius:8px; overflow:hidden;
    }
    .terms h3{ margin:0; padding:10px 12px; background:#fafafa; border-bottom:1px solid var(--line); font-size:13px; text-transform:uppercase; }
    .terms ol{ margin:10px 18px 14px; padding-left:16px; font-size:12px; }
    .terms li{ margin:6px 0; }

    /* ---------- Footer ---------- */
    .foot{
      padding:10px 22px 18px; font-size:11px; color:var(--muted); display:flex; justify-content:space-between; align-items:center;
    }
    .stamp{
      width:140px; height:60px; border:1px dashed var(--line); display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:11px; color:var(--muted);
    }

    /* ---------- Print ---------- */
    @media print {
      body{ background:#fff; }
      .wrap{ box-shadow:none; border:none; margin:0; max-width: 100%; }
      .head, .block-row, .items, .totals, .terms, .foot{ page-break-inside: avoid; }
      a{ color:inherit; text-decoration:none; }
    }
  </style>
</head>
<body>
  <div class="wrap">

    <!-- ===== Header ===== -->
    <div class="head">
      <div class="logo">
        <!-- Fallback border box if image missing -->
        <img src="{{ $companyLogoUrl ?? 'https://via.placeholder.com/300x120?text=LOGO' }}" alt="Company Logo" />
      </div>

      <div class="h-meta">
        <h1 class="title">{{ $companyName ?? 'Your Company Pvt. Ltd.' }}</h1>
        <p class="subtitle">
          {{ $companyAddress ?? 'Street, City, State - Pincode' }} · GSTIN: {{ $companyGstin ?? '22AAAAA0000A1Z5' }} ·
          Phone: {{ $companyPhone ?? '+91 99999 99999' }} · Email: {{ $companyEmail ?? 'info@company.com' }}
        </p>
      </div>

      <div class="q-meta">
        <div><strong>Quotation</strong></div>
        <div>No: <strong>{{ $quotationNumber ?? 'QTN-0001' }}</strong></div>
        <div>Date: <strong>{{ $quotationDate ?? date('d-m-Y') }}</strong></div>
        <div>Valid Till: <strong>{{ $validTill ?? date('d-m-Y', strtotime('+7 days')) }}</strong></div>
      </div>
    </div>

    <!-- ===== Parties ===== -->
    <div class="block-row">
      <div class="block">
        <h3>Bill To (Party)</h3>
        <div class="kv"><span class="k">Party Name</span>: <strong>{{ $partyName ?? 'ABC Enterprises' }}</strong></div>
        <div class="kv"><span class="k">Address</span>: {{ $partyAddress ?? 'Party Street, City, State - Pin' }}</div>
        <div class="kv"><span class="k">GSTIN</span>: {{ $partyGstin ?? '27BBBBB1111B2Z6' }}</div>
        <div class="kv"><span class="k">Contact</span>: {{ $partyPhone ?? '+91 98xxxxxxx' }}</div>
      </div>
      <div class="block">
        <h3>Company</h3>
        <div class="kv"><span class="k">Company</span>: <strong>{{ $companyName ?? 'Your Company Pvt. Ltd.' }}</strong></div>
        <div class="kv"><span class="k">Address</span>: {{ $companyAddress ?? 'Street, City, State - Pincode' }}</div>
        <div class="kv"><span class="k">GSTIN</span>: {{ $companyGstin ?? '22AAAAA0000A1Z5' }}</div>
        <div class="kv"><span class="k">State</span>: {{ $companyState ?? 'Gujarat (24)' }}</div>
      </div>
    </div>

    <!-- ===== Items ===== -->
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
        <!-- Repeat tr for each item -->
        @php
          $idx = 1;
          $subTotal = 0;
        @endphp
        @foreach(($items ?? []) as $it)
          @php
            $amount = ($it['qty'] ?? 0) * ($it['rate'] ?? 0);
            $subTotal += $amount;
          @endphp
          <tr>
            <td class="text-center">{{ $idx++ }}</td>
            <td>
              <strong>{{ $it['name'] ?? 'Product Name' }}</strong>
              <div class="muted">{{ $it['desc'] ?? '' }}</div>
            </td>
            <td class="text-center">{{ $it['hsn'] ?? '-' }}</td>
            <td class="text-center">{{ number_format($it['qty'] ?? 0, 2) }}</td>
            <td class="text-right">{{ number_format($it['rate'] ?? 0, 2) }}</td>
            <td class="text-right">{{ number_format($amount, 2) }}</td>
          </tr>
        @endforeach

        @if(empty($items))
          <!-- Sample Row (remove in production) -->
          <tr>
            <td class="text-center">1</td>
            <td><strong>Sample Product</strong><div class="muted">Short description</div></td>
            <td class="text-center">9983</td>
            <td class="text-center">2.00</td>
            <td class="text-right">1,000.00</td>
            <td class="text-right">2,000.00</td>
          </tr>
          @php $subTotal = 2000; @endphp
        @endif
      </tbody>
    </table>

    <!-- ===== Totals & Notes ===== -->
    @php
      $discount = $discount ?? 0;                        // absolute discount
      $taxable  = max(0, ($subTotal - $discount));
      $gstRate  = $gstRate ?? 18;                        // % GST
      $isInter  = $isInterState ?? false;                // true => IGST, false => CGST+SGST
      if($isInter){
        $igst = round($taxable * $gstRate / 100, 2);
        $cgst = $sgst = 0;
      }else{
        $cgst = round($taxable * ($gstRate/2) / 100, 2);
        $sgst = round($taxable * ($gstRate/2) / 100, 2);
        $igst = 0;
      }
      $roundOff = round(($taxable + $cgst + $sgst + $igst) - floor($taxable + $cgst + $sgst + $igst), 2);
      $grand    = round($taxable + $cgst + $sgst + $igst); // rounded to nearest rupee
    @endphp

    <div class="totals">
      <div class="note">
        <strong>Notes:</strong>
        <div class="muted">Thank you for your interest. Prices are in INR and subject to applicable taxes.</div>
        <div><strong>Payment Terms:</strong> {{ $paymentTerms ?? '50% advance, balance on delivery' }}</div>
        <div><strong>Delivery:</strong> {{ $delivery ?? 'Within 7–10 business days from PO' }}</div>
        <div><strong>Warranty:</strong> {{ $warranty ?? '12 months from invoice date' }}</div>
      </div>

      <div class="sum">
        <div class="sum-row"><div class="lbl">Sub Total</div><div class="val currency">&#8377; {{ number_format($subTotal,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Discount</div><div class="val currency">- &#8377; {{ number_format($discount,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Taxable Value</div><div class="val currency">&#8377; {{ number_format($taxable,2) }}</div></div>

        @if(!$isInter)
          <div class="sum-row"><div class="lbl">CGST ({{ $gstRate/2 }}%)</div><div class="val currency">&#8377; {{ number_format($cgst,2) }}</div></div>
          <div class="sum-row"><div class="lbl">SGST ({{ $gstRate/2 }}%)</div><div class="val currency">&#8377; {{ number_format($sgst,2) }}</div></div>
        @else
          <div class="sum-row"><div class="lbl">IGST ({{ $gstRate }}%)</div><div class="val currency">&#8377; {{ number_format($igst,2) }}</div></div>
        @endif

        <div class="sum-row"><div class="lbl">Round Off</div><div class="val currency">&#8377; {{ number_format($roundOff,2) }}</div></div>
        <div class="sum-row grand"><div class="lbl">Grand Total</div><div class="val currency">&#8377; {{ number_format($grand,0) }}</div></div>
      </div>
    </div>

    <!-- ===== Terms & Conditions ===== -->
    <div class="terms">
      <h3>Terms &amp; Conditions</h3>
      <ol>
        <li>Goods once sold will not be taken back or exchanged.</li>
        <li>Any disputes are subject to {{ $jurisdiction ?? 'Surat, Gujarat' }} jurisdiction only.</li>
        <li>Quotation valid till {{ $validTill ?? date('d-m-Y', strtotime('+7 days')) }}.</li>
        <li>Payments to be made in favour of <strong>{{ $companyName ?? 'Your Company Pvt. Ltd.' }}</strong>.</li>
        <li>Delivery schedule may vary due to unforeseen circumstances.</li>
        @foreach(($extraTerms ?? []) as $t)
          <li>{{ $t }}</li>
        @endforeach
      </ol>
    </div>

    <!-- ===== Footer ===== -->
    <div class="foot">
      <div>
        <div><strong>Bank Details</strong></div>
        <div class="muted">
          A/C Name: {{ $bankName ?? 'Your Company Pvt. Ltd.' }} ·
          A/C No: {{ $bankAccount ?? '1234567890' }} ·
          IFSC: {{ $bankIfsc ?? 'HDFC0000000' }} ·
          Branch: {{ $bankBranch ?? 'Main Branch' }}
        </div>
      </div>
      <div class="stamp">Authorised Signatory</div>
    </div>

  </div>
</body>
</html>
