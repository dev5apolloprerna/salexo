@php $subTotal=0; @endphp
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Quotation {{ $quotationNumber }} – V5</title>
<style>
  html,body{margin:0;padding:0;background:#f3f4f6;color:#111;font:13px/1.45 Arial,Helvetica,sans-serif}
  .wrap{max-width:820px;margin:16px auto;background:#fff;position:relative;border:1px solid #e5e7eb}
  .wm{position:absolute;inset:120px 0 0 0;display:flex;align-items:center;justify-content:center;opacity:.05;font-size:96px;font-weight:900;letter-spacing:6px;transform:rotate(-18deg);pointer-events:none}
  .head{padding:18px 20px 12px;border-bottom:2px solid #4338ca;background:linear-gradient(90deg,#eef2ff, #fff)}
  .row{display:flex;gap:12px;align-items:center}
  .logo{width:120px;height:60px;border:1px solid #c7d2fe;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:center}
  .logo img{max-width:100%;max-height:100%}
  .brand{flex:1}
  .brand .name{font-size:22px;font-weight:800;color:#3730a3}
  .brand .muted{color:#6b7280}
  .meta{text-align:right}
  .tag{display:inline-block;background:#e0e7ff;color:#312e81;padding:3px 8px;border-radius:999px;font-weight:700}
  .sec{padding:12px 20px;position:relative}
  .cards{display:flex;gap:12px}
  .card{flex:1;border:1px solid #e5e7eb;border-radius:10px;padding:8px 10px;background:#fff}
  .card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;color:#312e81}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:10px;font-size:12px;vertical-align:top;border-bottom:1px solid #eef2ff}
  thead th{background:#eef2ff;border-bottom:2px solid #4338ca;text-align:left}
  .tc{text-align:center}.tr{text-align:right}.w40{width:40px}.w70{width:70px}.w90{width:90px}
  .grid{display:grid;grid-template-columns:1fr 320px;gap:12px;margin-top:10px}
  .note{border:1px solid #e5e7eb;border-radius:10px;padding:10px;background:#fafafa}
  .sum{border:2px solid #4338ca;border-radius:12px;overflow:hidden}
  .sum .r{display:flex;justify-content:space-between;padding:8px 12px;border-bottom:1px dashed #c7d2fe}
  .sum .r:last-child{border-bottom:none}
  .grand{background:#3730a3;color:#eef2ff;font-weight:900;text-align:center;padding:12px}
  .terms{border-top:2px dashed #c7d2fe;margin-top:12px;padding-top:10px}
  .foot{display:flex;justify-content:space-between;align-items:center;margin:10px 0 16px;font-size:11px;color:#6b7280;padding:0 20px}
  .stamp{width:140px;height:60px;border:1px dashed #c7d2fe;border-radius:8px;display:flex;align-items:center;justify-content:center}
  @media print{body{background:#fff}.wrap{border:none}.wm{opacity:.08}}
</style></head><body>
<div class="wrap">
  <div class="wm">QUOTATION</div>

  <div class="head">
    <div class="row">
      <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
      <div class="brand">
        <div class="name">{{ $companyName }}</div>
        <div class="muted">{{ $companyAddress }} · GSTIN {{ $companyGstin }} · {{ $companyEmail }} · {{ $companyPhone }}</div>
      </div>
      <div class="meta">
        <div class="tag">Quotation</div>
        <div>No: <b>{{ $quotationNumber }}</b></div>
        <div>Date: <b>{{ $quotationDate }}</b></div>
        <div>Valid Till: <b>{{ $validTill }}</b></div>
      </div>
    </div>
  </div>

  <div class="sec">
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
      </div>
    </div>

    <table>
      <thead><tr>
        <th class="w40 tc">#</th><th>Item</th><th class="w70 tc">HSN</th><th class="w70 tc">Qty</th><th class="w90 tr">Rate</th><th class="w90 tr">Amount</th>
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

    @php
      $discount=$discount??0; $taxable=max(0,$subTotal-$discount); $gstRate=$gstRate??18; $isInter=$isInterState??false;
      if($isInter){$igst=round($taxable*$gstRate/100,2);$cgst=$sgst=0;} else {$cgst=round($taxable*($gstRate/2)/100,2);$sgst=$cgst;$igst=0;}
      $pay=$taxable+$cgst+$sgst+$igst; $roundOff=round($pay-floor($pay),2); $grand=round($pay);
    @endphp

    <div class="grid">
      <div class="note">
        <b>Notes</b><br>
        <b>Payment Terms:</b> {{ $paymentTerms }}<br>
        <b>Delivery:</b> {{ $delivery }}<br>
        <b>Warranty:</b> {{ $warranty }}
      </div>
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
    </div>

    <div class="terms">
      <div style="font-weight:700;color:#312e81;margin-bottom:6px">Terms &amp; Conditions</div>
      <ol style="margin:0 0 0 18px">
        <li>Goods once sold will not be taken back or exchanged.</li>
        <li>Any disputes are subject to {{ $jurisdiction ?? 'Surat, Gujarat' }} jurisdiction only.</li>
        <li>Quotation valid till {{ $validTill }}.</li>
        <li>Payments to be made in favour of <b>{{ $companyName }}</b>.</li>
        <li>Delivery schedule may vary due to unforeseen circumstances.</li>
        @foreach(($extraTerms ?? []) as $t) @if($t)<li>{{ $t }}</li>@endif @endforeach
      </ol>
    </div>
  </div>

  <!-- <div class="foot">
    <div><b>Bank:</b> {{ $bankName }} · A/C {{ $bankAccount }} · IFSC {{ $bankIfsc }} · {{ $bankBranch }}</div>
    <div class="stamp">Authorised Signatory</div>
  </div> -->
</div>
</body></html>
