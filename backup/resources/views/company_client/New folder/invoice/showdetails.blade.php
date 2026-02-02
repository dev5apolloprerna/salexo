{{-- resources/views/pdf/company-client-quotation.blade.php --}}
@php
  // Helpers
  $fmt = fn($n) => number_format((float)$n, 2);

  // Company full address
  $fullAddress = trim(
      trim($popupInvoice->strAddressOne . ' ' . $popupInvoice->strAddressTwo . ' ' . $popupInvoice->strAddressThree)
  );

  // Totals from details
  $iGstAmount = 0.0;
  $TotalNetAmount = 0.0;
  foreach ($InvoiceDetail as $d) {
      $gstAmt = ((float)$d->netAmount) * ((float)$d->iGstPercentage) / 100.0;
      $iGstAmount += $gstAmt;
      $TotalNetAmount += (float)$d->netAmount;
  }
  $grand = $TotalNetAmount + $iGstAmount;

  // GST label
  $gstLabel = ($popupInvoice->iGstType ?? 1) == 2 ? 'IGST' : 'GST';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quotation {{ $popupInvoice->iQuotationNo }}</title>
  <style>
    /* Proper Rupee symbol in Dompdf */
    @font-face{
      font-family:'DejaVuSans';
      src:url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
      font-weight:normal; font-style:normal;
    }
    html,body,*{ font-family:'DejaVuSans', Arial, sans-serif !important; }
    *{ box-sizing:border-box }
    html,body{ margin:0; background:#f6f7fb; color:#111 }
    table{ width:100%; border-collapse:collapse }
    .wrap{ max-width:820px; margin:16px auto; background:#fff; border:1px solid #e5e7eb; box-shadow:0 8px 24px rgba(51,65,85,.06) }

    /* Header */
    .head{ display:flex; align-items:center; gap:16px; padding:18px 20px; background:#f8fafc; border-bottom:2px solid #e5e7eb; }
    .logo{ width:140px; height:70px; border:1px solid #e5e7eb; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; overflow:hidden; }
    .logo img{ max-width:100%; max-height:100%; object-fit:contain }
    .h-meta{ flex:1 }
    h1{ margin:0; font-size:22px; letter-spacing:.2px }
    .subtitle{ margin:2px 0 0; font-size:12px; color:#64748b }
    .q-meta{ text-align:right; font-size:12px; line-height:1.6 }
    .chip{ display:inline-block; padding:2px 8px; border-radius:999px; background:#e0f2fe; color:#075985; font-weight:600 }

    .block-row{ display:flex; gap:16px; padding:14px 16px; }
    .block{ flex:1; border:1px solid #e5e7eb; border-radius:10px; padding:12px; background:#fff; }
    .block h3{ margin:0 0 8px; font-size:12px; letter-spacing:.3px; text-transform:uppercase; color:#334155 }
    .kv{ font-size:12px; margin:3px 0 }
    .kv .k{ display:inline-block; width:110px; color:#6b7280 }

    thead th{ background:#f1f5f9; border-bottom:1px solid #e5e7eb; font-size:12px; text-align:left; padding:10px }
    tbody td{ border-bottom:1px solid #f3f4f6; padding:10px; font-size:12px; vertical-align:top }
    .right{ text-align:right } .center{ text-align:center }
    .muted{ color:#6b7280 }

    .totals{ display:grid; grid-template-columns:1fr 320px; gap:16px; padding:0 16px 16px }
    .note{ border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:12px; background:#fff; }
    .sum{ border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fff; }
    .sum-row{ display:flex; justify-content:space-between; padding:8px 12px; border-bottom:1px solid #e5e7eb; font-size:12px }
    .sum-row:last-child{ border-bottom:none }
    .grand{ background:#0f172a; color:#fff; font-weight:700 }
    .r:before{ content:"\20B9\00A0"; } /* ₹ + NBSP */

    .terms{ margin:0 16px 16px; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fff; }
    .terms-h{ background:#f8fafc; padding:10px 12px; border-bottom:1px solid #e5e7eb; font-size:12px; text-transform:uppercase; letter-spacing:.3px }
    .terms-b{ padding:10px 12px; font-size:12px }
    .foot{ display:flex; justify-content:space-between; align-items:center; padding:12px 16px 18px; font-size:12px; color:#64748b }
    .stamp{ width:140px; height:60px; border:1px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:11px; color:#9ca3af }
  </style>
</head>
<body>
  <div class="wrap">
    {{-- Header --}}
    <div class="head">
      <div class="logo">
        @if(!empty($pic))
          <img src="{{ $pic }}" alt="Logo">
        @else
          <span class="muted">LOGO</span>
        @endif
      </div>
      <div class="h-meta">
        <h1>Sales Quotation</h1>
        @if(!empty($popupInvoice->strCompanyName))
          <div class="subtitle">{{ $popupInvoice->strCompanyName }}</div>
        @endif
        @if(!empty($fullAddress))
          <div class="subtitle">{{ $fullAddress }}</div>
        @endif
        <div class="subtitle">
          @if(!empty($popupInvoice->companyEmail)) Email: {{ $popupInvoice->companyEmail }} @endif
          @if(!empty($popupInvoice->companyMobile)) &nbsp;|&nbsp; Contact: {{ $popupInvoice->companyMobile }} @endif
        </div>
      </div>
      <div class="q-meta">
        <div><span class="chip">SQ No:</span> {{ $popupInvoice->iQuotationNo }}</div>
        <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($popupInvoice->entryDate)->format('d-m-Y') }}</div>
        @if(!empty($popupInvoice->quotationValidity))
          <div><strong>Quote Validity:</strong> {{ $popupInvoice->quotationValidity }}</div>
        @endif
      </div>
    </div>

    {{-- GST / PAN --}}
    <table style="padding:0 16px 8px">
      <tr>
        <td style="font-size:12px"><strong>GSTIN:</strong> {{ $popupInvoice->strGST }}</td>
        <td class="right" style="font-size:12px"><strong>PAN:</strong> {{ $popupInvoice->strPanNo }}</td>
      </tr>
    </table>

    {{-- Parties --}}
    <div class="block-row">
      <div class="block">
        <h3>Bill To (Customer)</h3>
        <div class="kv"><span class="k">Name:</span> {{ $popupInvoice->strPartyName }}</div>
        @if(!empty($popupInvoice->address1)) <div class="kv"><span class="k">Address 1:</span> {{ $popupInvoice->address1 }}</div> @endif
        @if(!empty($popupInvoice->address2)) <div class="kv"><span class="k">Address 2:</span> {{ $popupInvoice->address2 }}</div> @endif
        @if(!empty($popupInvoice->address3)) <div class="kv"><span class="k">Address 3:</span> {{ $popupInvoice->address3 }}</div> @endif
        @if(!empty($popupInvoice->iMobile))  <div class="kv"><span class="k">Mobile:</span> {{ $popupInvoice->iMobile }}</div> @endif
        @if(!empty($popupInvoice->strEmail))  <div class="kv"><span class="k">Email:</span> {{ $popupInvoice->strEmail }}</div> @endif
      </div>
      <div class="block">
        <h3>Quotation Terms</h3>
        @if(!empty($popupInvoice->modeOfDespatch)) <div class="kv"><span class="k">Mode of Despatch:</span> {{ $popupInvoice->modeOfDespatch }}</div> @endif
        @if(!empty($popupInvoice->deliveryTerm))   <div class="kv"><span class="k">Delivery Term:</span> {{ $popupInvoice->deliveryTerm }}</div> @endif
        @if(!empty($popupInvoice->paymentTerms))   <div class="kv"><span class="k">Payment Term:</span> {{ $popupInvoice->paymentTerms }}</div> @endif
      </div>
    </div>

    {{-- Items --}}
    <div style="padding:0 16px 12px">
      <table>
        <thead>
          <tr>
            <th class="center" style="width:48px">SrNo.</th>
            <th>Product Description</th>
            <th class="center" style="width:70px">UOM</th>
            <th class="right"  style="width:80px">Qty</th>
            <th class="right"  style="width:100px">Unit Rate</th>
            <th class="right"  style="width:80px">GST %</th>
            <th class="right"  style="width:110px">GST Amt</th>
            <th class="right"  style="width:120px">Net Amount</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
          @foreach ($InvoiceDetail as $detail)
            @php
              $qty  = (float)($detail->quantity ?? 0);
              $rate = (float)($detail->rate ?? 0);
              $net  = (float)($detail->netAmount ?? 0);       // your code uses netAmount for base
              $gstP = (float)($detail->iGstPercentage ?? 0);  // per-line GST%
              $gstA = $net * $gstP / 100.0;
            @endphp
            <tr>
              <td class="center">{{ $i }}</td>
              <td style="white-space:pre-line">{!! $detail->description !!}</td>
              <td class="center">{{ $detail->uom }}</td>
              <td class="right">{{ $fmt($qty) }}</td>
              <td class="right r">{{ $fmt($rate) }}</td>
              <td class="right">{{ $fmt($gstP) }}</td>
              <td class="right r">{{ $fmt($gstA) }}</td>
              <td class="right r">{{ $fmt($net) }}</td>
            </tr>
            @php $i++; @endphp
          @endforeach

          @if(count($InvoiceDetail) === 0)
            <tr><td colspan="8" class="center muted" style="padding:10px">No items</td></tr>
          @endif
        </tbody>
      </table>
    </div>

    {{-- Totals --}}
    <div class="totals">
      <div class="note">
        <div><strong>GST Type:</strong> {{ $gstLabel }}</div>
        @if(!empty($popupInvoice->quotationValidity))
          <div><strong>Quote Validity:</strong> {{ $popupInvoice->quotationValidity }}</div>
        @endif
        @if(!empty($popupInvoice->modeOfDespatch))
          <div><strong>Mode of Despatch:</strong> {{ $popupInvoice->modeOfDespatch }}</div>
        @endif
        @if(!empty($popupInvoice->deliveryTerm))
          <div><strong>Delivery Term:</strong> {{ $popupInvoice->deliveryTerm }}</div>
        @endif
        @if(!empty($popupInvoice->paymentTerms))
          <div><strong>Payment Term:</strong> {{ $popupInvoice->paymentTerms }}</div>
        @endif
      </div>

      <div class="sum">
        <div class="sum-row">
          <div>Sub Total</div>
          <div class="r">{{ $fmt($TotalNetAmount) }}</div>
        </div>
        <div class="sum-row">
          <div>{{ $gstLabel }} Amount</div>
          <div class="r">{{ $fmt($iGstAmount) }}</div>
        </div>
        <div class="sum-row grand">
          <div>Total Amount</div>
          <div class="r">{{ $fmt($grand) }}</div>
        </div>
      </div>
    </div>

    {{-- Terms & Conditions --}}
    <div class="terms">
      <div class="terms-h">Terms &amp; Conditions</div>
      <div class="terms-b">
        {!! $popupInvoice->strTermsCondition !!}
      </div>
    </div>

    {{-- Footer --}}
    <div class="foot">
      <div>Place of Supply / Jurisdiction: <strong>{{ $popupInvoice->jurisdiction ?? '-' }}</strong></div>
      <div class="stamp">Authorized Signatory</div>
    </div>
  </div>
</body>
</html>
