@php $subTotal=0; @endphp
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Quotation {{ $quotationNumber }} – V4</title>
<style>
  html,body{margin:0;padding:0;background:#f6f7f9;color:#111;font:13px/1.45 Arial,Helvetica,sans-serif}
  .wrap{max-width:900px;margin:16px auto;background:#fff;border:1px solid #e5e7eb;display:grid;grid-template-columns:1fr 300px;gap:0}
  .main{padding:14px 18px}
  .side{border-left:1px solid #e5e7eb;padding:14px 14px 14px 14px;background:#fff7ed}
  .head{display:flex;gap:12px;border-bottom:1px solid #e5e7eb;padding-bottom:10px}
  .logo{width:110px;height:56px;border:1px solid #e5e7eb;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fff}
  .logo img{max-width:100%;max-height:100%}
  .meta{margin-left:auto;text-align:right}
  .tag{display:inline-block;padding:2px 8px;border-radius:999px;background:#ffedd5;color:#7c2d12;font-weight:700}
  .cards{display:flex;gap:10px;margin-top:10px}
  .card{flex:1;border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px}
  .card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;color:#7c2d12}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:8px 8px;font-size:12px;vertical-align:top;border-bottom:1px solid #f1f5f9}
  thead th{background:#ffedd5;border-bottom:1px solid #e5e7eb;text-align:left}
  .tc{text-align:center}.tr{text-align:right}.w40{width:40px}.w70{width:70px}.w90{width:90px}
  .sum{border:1px solid #f59e0b;background:#fffbe6;border-radius:8px;overflow:hidden}
  .sum .r{display:flex;justify-content:space-between;padding:8px 10px;border-bottom:1px dashed #f59e0b}
  .sum .r:last-child{border-bottom:none}
  .grand{background:#7c2d12;color:#fff7ed;padding:10px;font-weight:800;text-align:center;border-top:2px solid #f59e0b}
  .note{margin-top:10px;font-size:12px}
  .terms{margin-top:10px;border:1px solid #e5e7eb}
  .terms h3{margin:0;background:#ffedd5;padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:12px;text-transform:uppercase}
  .terms ol{margin:8px 0 8px 20px}
  .foot{display:flex;justify-content:space-between;align-items:center;margin-top:10px;font-size:11px;color:#6b7280}
  .stamp{width:140px;height:60px;border:1px dashed #d1d5db;border-radius:8px;display:flex;align-items:center;justify-content:center}
  @media print{.wrap{display:block}.side{page-break-inside:avoid}}
</style></head><body>
<div class="wrap">
  <main class="main">
    <div class="head">
      <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
      <div>
        <div style="font-size:20px;font-weight:700">{{ $companyName }}</div>
        <div style="color:#6b7280">{{ $companyAddress }} — GSTIN {{ $companyGstin }}</div>
      </div>
      <div class="meta">
        <div class="tag">QUOTATION</div>
        <div>No: <b>{{ $quotationNumber }}</b></div>
        <div>Date: <b>{{ $quotationDate }}</b></div>
        <div>Valid Till: <b>{{ $validTill }}</b></div>
      </div>
    </div>

    <div class="cards">
      <div class="card">
        <h3>Bill To</h3>
        <div><b>{{ $partyName }}</b></div>
        <div>{{ $partyAddress }}</div>
        <div>GSTIN: {{ $partyGstin }} · Ph: {{ $partyPhone }}</div>
      </div>
      <div class="card">
        <h3>Company</h3>
        <div>{{ $companyState }}</div>
        <div>{{ $companyEmail }} · {{ $companyPhone }}</div>
      </div>
    </div>

    <table>
      <thead><tr>
        <th class="w40 tc">#</th><th>Product / Service</th><th class="w70 tc">HSN</th><th class="w70 tc">Qty</th><th class="w90 tr">Rate</th><th class="w90 tr">Amount</th>
      </tr></thead>
      <tbody>
      @foreach(($items??[]) as $i=>$it)
        @php $amt=($it['qty']??0)*($it['rate']??0); $subTotal+=$amt; @endphp
        <tr>
          <td class="tc">{{ $i+1 }}</td>
          <td><b>{{ $it['name'] }}</b><div style="color:#6b7280">{{ $it['desc']??'' }}</div></td>
          <td class="tc">{{ $it['hsn']??'-' }}</td>
          <td class="tc">{{ number_format($it['qty']??0,2) }}</td>
          <td class="tr">{{ number_format($it['rate']??0,2) }}</td>
          <td class="tr">{{ number_format($amt,2) }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>

    <div class="terms">
      <h3>Terms &amp; Conditions</h3>
      <div style="padding:8px 10px">
        <ol>
          <li>Goods once sold will not be taken back or exchanged.</li>
          <li>Any disputes are subject to {{ $jurisdiction ?? 'Surat, Gujarat' }} jurisdiction only.</li>
          <li>Quotation valid till {{ $validTill }}.</li>
          <li>Payments to be made in favour of <b>{{ $companyName }}</b>.</li>
          <li>Delivery schedule may vary due to unforeseen circumstances.</li>
          @foreach(($extraTerms ?? []) as $t) @if($t)<li>{{ $t }}</li>@endif @endforeach
        </ol>
      </div>
    </div>

    <!--  -->
  </main>

  @php
    $discount=$discount??0; $taxable=max(0,$subTotal-$discount); $gstRate=$gstRate??18; $isInter=$isInterState??false;
    if($isInter){$igst=round($taxable*$gstRate/100,2);$cgst=$sgst=0;} else {$cgst=round($taxable*($gstRate/2)/100,2);$sgst=$cgst;$igst=0;}
    $pay=$taxable+$cgst+$sgst+$igst; $roundOff=round($pay-floor($pay),2); $grand=round($pay);
  @endphp

  <aside class="side">
    <div class="sum">
      <div class="r"><div>Sub Total</div><div>₹ {{ number_format($subTotal,2) }}</div></div>
      <div class="r"><div>Discount</div><div>- ₹ {{ number_format($discount,2) }}</div></div>
      <div class="r"><div>Taxable</div><div>₹ {{ number_format($taxable,2) }}</div></div>
      @if(!$isInter)
        <div class="r"><div>CGST ({{ $gstRate/2 }}%)</div><div>₹ {{ number_format($cgst,2) }}</div></div>
        <div class="r"><div>SGST ({{ $gstRate/2 }}%)</div><div>₹ {{ number_format($sgst,2) }}</div></div>
      @else
        <div class="r"><div>IGST ({{ $gstRate }}%)</div><div>₹ {{ number_format($igst,2) }}</div></div>
      @endif
      <div class="r"><div>Round Off</div><div>₹ {{ number_format($roundOff,2) }}</div></div>
      <div class="grand">Grand Total ₹ {{ number_format($grand,0) }}</div>
    </div>
    <div class="note"><b>Notes</b><br>{{ $paymentTerms }} · {{ $delivery }} · {{ $warranty }}</div>
  </aside>
</div>
</body></html>
