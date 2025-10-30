@php $subTotal=0; @endphp
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Quotation {{ $quotationNumber }} – V3</title>
<style>
  html,body{margin:0;padding:0;background:#ffffff;color:#111;font:13px/1.45 Arial,Helvetica,sans-serif}
  .wrap{max-width:820px;margin:0 auto}
  .bar{display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:2px solid #111}
  .logo{width:120px;height:60px;border:1px solid #d1d5db;border-radius:8px;display:flex;align-items:center;justify-content:center}
  .logo img{max-width:100%;max-height:100%}
  .grow{flex:1}
  .right{min-width:260px;text-align:right}
  .tag{font-weight:800;letter-spacing:.5px}
  .gt{font-size:22px;font-weight:800}
  .muted{color:#6b7280}
  .row{display:flex;gap:12px;margin:12px 0}
  .box{flex:1;border:1px solid #e5e7eb;padding:8px 10px}
  .box h3{margin:0 0 6px;font-size:12px;text-transform:uppercase}
  table{width:100%;border-collapse:collapse;margin:8px 0}
  th,td{padding:8px 6px;font-size:12px;border-bottom:1px solid #e5e7eb}
  thead th{border-bottom:2px solid #111;text-align:left}
  .tc{text-align:center}.tr{text-align:right}.w40{width:40px}.w70{width:70px}.w90{width:90px}
  .twocol{display:grid;grid-template-columns:1fr 280px;gap:12px}
  .note{border:1px solid #e5e7eb;padding:8px 10px}
  .sum .r{display:flex;justify-content:space-between;border-bottom:1px dashed #e5e7eb;padding:6px 0}
  .sum .r:last-child{border-bottom:none}
  .sum .grand{background:#111;color:#fff;padding:8px 10px;font-weight:800}
  .terms{border:1px solid #e5e7eb;margin:10px 0}
  .terms h3{margin:0;background:#f8fafc;padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;text-transform:uppercase}
  .terms ol{margin:8px 0 8px 20px}
  .foot{display:flex;justify-content:space-between;align-items:center;font-size:11px;margin:10px 0}
  .stamp{border:1px dashed #d1d5db;width:140px;height:60px;display:flex;align-items:center;justify-content:center}
  @media print{.bar,.row,table,.twocol,.terms,.foot{page-break-inside:avoid}}
</style></head><body><div class="wrap">

<div class="bar">
  <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
  <div class="grow">
    <div class="tag">QUOTATION</div>
    <div class="muted">{{ $companyName }} — {{ $companyAddress }} — GSTIN {{ $companyGstin }}</div>
  </div>
  @php
    // Pre-compute grand total for header callout (after table it will be recomputed again consistently)
  @endphp
  <div class="right">
    <div>No: <b>{{ $quotationNumber }}</b></div>
    <div>Date: <b>{{ $quotationDate }}</b></div>
    <div>Valid Till: <b>{{ $validTill }}</b></div>
  </div>
</div>

<div class="row">
  <div class="box">
    <h3>Bill To</h3>
    <div><b>{{ $partyName }}</b></div>
    <div class="muted">{{ $partyAddress }}</div>
    <div>GSTIN: {{ $partyGstin }} · Ph: {{ $partyPhone }}</div>
  </div>
  <div class="box">
    <h3>Company</h3>
    <div><b>{{ $companyName }}</b></div>
    <div class="muted">{{ $companyState }}</div>
    <div>{{ $companyEmail }} · {{ $companyPhone }}</div>
  </div>
</div>

<table>
  <thead><tr>
    <th class="w40 tc">#</th><th>Description</th><th class="w70 tc">HSN</th><th class="w70 tc">Qty</th><th class="w90 tr">Rate</th><th class="w90 tr">Amount</th>
  </tr></thead>
  <tbody>
    @foreach(($items??[]) as $i=>$it)
      @php $amt=($it['qty']??0)*($it['rate']??0); $subTotal+=$amt; @endphp
      <tr>
        <td class="tc">{{ $i+1 }}</td>
        <td><b>{{ $it['name'] }}</b><div class="muted">{{ $it['desc']??'' }}</div></td>
        <td class="tc">{{ $it['hsn']??'-' }}</td>
        <td class="tc">{{ number_format($it['qty']??0,2) }}</td>
        <td class="tr">{{ number_format($it['rate']??0,2) }}</td>
        <td class="tr">{{ number_format($amt,2) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

@php
  $discount=$discount??0; $taxable=max(0,$subTotal-$discount); $gstRate=$gstRate??18; $isInter=$isInterState??false;
  if($isInter){$igst=round($taxable*$gstRate/100,2);$cgst=$sgst=0;} else {$cgst=round($taxable*($gstRate/2)/100,2);$sgst=$cgst;$igst=0;}
  $pay=$taxable+$cgst+$sgst+$igst; $roundOff=round($pay-floor($pay),2); $grand=round($pay);
@endphp

<div class="twocol">
  <div class="note">
    <b>Notes</b><br>
    {{ $paymentTerms }} · {{ $delivery }} · {{ $warranty }}
  </div>
  <div class="sum">
    <div class="r"><div>Sub Total</div><div>₹ {{ number_format($subTotal,2) }}</div></div>
    <div class="r"><div>Discount</div><div>- ₹ {{ number_format($discount,2) }}</div></div>
    <div class="r"><div>Taxable Value</div><div>₹ {{ number_format($taxable,2) }}</div></div>
    @if(!$isInter)
      <div class="r"><div>CGST ({{ $gstRate/2 }}%)</div><div>₹ {{ number_format($cgst,2) }}</div></div>
      <div class="r"><div>SGST ({{ $gstRate/2 }}%)</div><div>₹ {{ number_format($sgst,2) }}</div></div>
    @else
      <div class="r"><div>IGST ({{ $gstRate }}%)</div><div>₹ {{ number_format($igst,2) }}</div></div>
    @endif
    <div class="r"><div>Round Off</div><div>₹ {{ number_format($roundOff,2) }}</div></div>
    <div class="grand">Grand Total — ₹ {{ number_format($grand,0) }}</div>
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

<!-- <div class="foot">
  <div><b>Bank:</b> {{ $bankName }} · A/C {{ $bankAccount }} · IFSC {{ $bankIfsc }} · {{ $bankBranch }}</div>
  <div class="stamp">Authorised Signatory</div>
</div> -->

</div></body></html>
