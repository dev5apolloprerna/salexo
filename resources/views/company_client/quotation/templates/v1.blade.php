@php $subTotal = 0; @endphp
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Quotation {{ $quotationNumber }} – V1</title>
<style>
  *{box-sizing:border-box} html,body{margin:0;padding:0;background:#f6f7f9;color:#111;font:13px/1.45 Arial,Helvetica,sans-serif}
  .wrap{max-width:820px;margin:16px auto;background:#fff;border:1px solid #e5e7eb;box-shadow:0 1px 6px rgba(0,0,0,.06)}
  .head{display:flex;gap:16px;padding:16px 20px;border-bottom:1px solid #e5e7eb;background:#f8fafc}
  .logo{width:120px;height:60px;border:1px solid #e5e7eb;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff}
  .logo img{max-width:100%;max-height:100%;object-fit:contain}
  .h1{margin:0;font-size:22px}
  .muted{color:#667085}
  .meta{margin-left:auto;text-align:right;font-size:12px}
  .chip{display:inline-block;padding:2px 8px;border-radius:999px;background:#e0f2fe;color:#075985;font-weight:700}
  .row{display:flex;gap:16px;padding:14px 20px}
  .card{flex:1;border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px}
  .card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.3px;color:#374151}
  .kv{font-size:12px;margin:3px 0} .k{display:inline-block;width:110px;color:#667085}
  table{width:100%;border-collapse:collapse}
  .items{margin:10px 20px 0;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
  th,td{padding:10px;font-size:12px;vertical-align:top} thead th{background:#f8fafc;border-bottom:1px solid #e5e7eb;text-align:left}
  tbody td{border-bottom:1px solid #f1f5f9}
  .tr{text-align:right}.tc{text-align:center}.w40{width:40px}.w70{width:70px}.w90{width:90px}
  .grid{margin:10px 20px;display:grid;grid-template-columns:1fr 320px;gap:16px}
  .note{border:1px solid #e5e7eb;border-radius:10px;padding:10px 12px;font-size:12px}
  .sum{border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
  .sum-row{display:flex;padding:8px 12px;border-bottom:1px solid #f1f5f9;font-size:12px}
  .sum-row:last-child{border-bottom:none}
  .lbl{flex:1}.val{width:120px;text-align:right}
  .grand{background:#0f172a;color:#fff;font-weight:700}
  .terms{margin:14px 20px 18px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
  .terms h3{margin:0;padding:10px 12px;background:#f8fafc;border-bottom:1px solid #e5e7eb;font-size:12px;text-transform:uppercase}
  .terms ol{margin:10px 18px 14px;padding-left:16px;font-size:12px}
  .foot{padding:10px 20px 18px;font-size:11px;color:#6b7280;display:flex;justify-content:space-between;align-items:center}
  .stamp{width:140px;height:60px;border:1px dashed #d1d5db;border-radius:8px;display:flex;align-items:center;justify-content:center}
  @media print{body{background:#fff}.wrap{border:none;box-shadow:none;margin:0;max-width:100%}.head,.row,.items,.grid,.terms,.foot{page-break-inside:avoid}}
</style></head><body><div class="wrap">

<div class="head">
  <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
  <div>
    <h1 class="h1">{{ $companyName }}</h1>
    <div class="muted">{{ $companyAddress }} · GSTIN: {{ $companyGstin }} · Ph: {{ $companyPhone }} · {{ $companyEmail }}</div>
  </div>
  <div class="meta">
    <div class="chip">Quotation</div>
    <div>No: <b>{{ $quotationNumber }}</b></div>
    <div>Date: <b>{{ $quotationDate }}</b></div>
    <div>Valid Till: <b>{{ $validTill }}</b></div>
  </div>
</div>

<div class="row">
  <div class="card">
    <h3>Bill To</h3>
    <div class="kv"><span class="k">Party</span>: <b>{{ $partyName }}</b></div>
    <div class="kv"><span class="k">Address</span>: {{ $partyAddress }}</div>
    <div class="kv"><span class="k">GSTIN</span>: {{ $partyGstin }}</div>
    <div class="kv"><span class="k">Contact</span>: {{ $partyPhone }}</div>
  </div>
  <div class="card">
    <h3>Company</h3>
    <div class="kv"><span class="k">Name</span>: <b>{{ $companyName }}</b></div>
    <div class="kv"><span class="k">State</span>: {{ $companyState }}</div>
    <div class="kv"><span class="k">Email</span>: {{ $companyEmail }}</div>
    <div class="kv"><span class="k">Phone</span>: {{ $companyPhone }}</div>
  </div>
</div>

<table class="items">
  <thead><tr>
    <th class="w40 tc">#</th>
    <th>Product / Service</th>
    <th class="w70 tc">HSN</th>
    <th class="w70 tc">Qty</th>
    <th class="w90 tr">Rate (₹)</th>
    <th class="w90 tr">Amount (₹)</th>
  </tr></thead>
  <tbody>
  @foreach(($items ?? []) as $i=>$it)
    @php $amt = ($it['qty'] ?? 0)*($it['rate'] ?? 0); $subTotal += $amt; @endphp
    <tr>
      <td class="tc">{{ $i+1 }}</td>
      <td><b>{{ $it['name'] }}</b><div class="muted">{{ $it['desc'] ?? '' }}</div></td>
      <td class="tc">{{ $it['hsn'] ?? '-' }}</td>
      <td class="tc">{{ number_format($it['qty'] ?? 0,2) }}</td>
      <td class="tr">{{ number_format($it['rate'] ?? 0,2) }}</td>
      <td class="tr">{{ number_format($amt,2) }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

@php
  $discount = $discount ?? 0;
  $taxable = max(0,$subTotal - $discount);
  $gstRate = $gstRate ?? 18;
  $isInter = $isInterState ?? false;
  if($isInter){ $igst=round($taxable*$gstRate/100,2); $cgst=$sgst=0; }
  else{ $cgst=round($taxable*($gstRate/2)/100,2); $sgst=$cgst; $igst=0; }
  $payable = $taxable + $cgst + $sgst + $igst;
  $roundOff = round($payable - floor($payable), 2);
  $grand = round($payable);
@endphp

<div class="grid">
  <div class="note">
    <b>Notes</b>
    <div class="muted">Prices in INR; taxes extra as applicable.</div>
    <div><b>Payment Terms:</b> {{ $paymentTerms }}</div>
    <div><b>Delivery:</b> {{ $delivery }}</div>
    <div><b>Warranty:</b> {{ $warranty }}</div>
  </div>
  <div class="sum">
    <div class="sum-row"><div class="lbl">Sub Total</div><div class="val">₹ {{ number_format($subTotal,2) }}</div></div>
    <div class="sum-row"><div class="lbl">Discount</div><div class="val">- ₹ {{ number_format($discount,2) }}</div></div>
    <div class="sum-row"><div class="lbl">Taxable Value</div><div class="val">₹ {{ number_format($taxable,2) }}</div></div>
    @if(!$isInter)
      <div class="sum-row"><div class="lbl">CGST ({{ $gstRate/2 }}%)</div><div class="val">₹ {{ number_format($cgst,2) }}</div></div>
      <div class="sum-row"><div class="lbl">SGST ({{ $gstRate/2 }}%)</div><div class="val">₹ {{ number_format($sgst,2) }}</div></div>
    @else
      <div class="sum-row"><div class="lbl">IGST ({{ $gstRate }}%)</div><div class="val">₹ {{ number_format($igst,2) }}</div></div>
    @endif
    <div class="sum-row"><div class="lbl">Round Off</div><div class="val">₹ {{ number_format($roundOff,2) }}</div></div>
    <div class="sum-row grand"><div class="lbl">Grand Total</div><div class="val">₹ {{ number_format($grand,0) }}</div></div>
  </div>
</div>

<div class="terms">
  <h3>Terms &amp; Conditions</h3>
  <ol>
    <li>Goods once sold will not be taken back or exchanged.</li>
    <li>Any disputes are subject to {{ $jurisdiction ?? 'Surat, Gujarat' }} jurisdiction only.</li>
    <li>Quotation valid till {{ $validTill }}.</li>
    <li>Payments to be made in favour of <b>{{ $companyName }}</b>.</li>
    <li>Delivery schedule may vary due to unforeseen circumstances.</li>
    @foreach(($extraTerms ?? []) as $t) @if($t)<li>{{ $t }}</li>@endif @endforeach
  </ol>
</div>

<div class="foot">
  <!-- <div>
    <b>Bank Details</b><br>
    A/C Name: {{ $bankName }} · A/C No: {{ $bankAccount }} · IFSC: {{ $bankIfsc }} · Branch: {{ $bankBranch }}
  </div> -->
  <div class="stamp">Authorised Signatory</div>
</div>

</div></body></html>
