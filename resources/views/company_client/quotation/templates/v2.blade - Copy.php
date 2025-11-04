@php $subTotal=0; @endphp
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Quotation {{ $quotationNumber }} – V2</title>
<style>
  html,body{margin:0;padding:0;background:#f6f7f9;color:#111;font:13px/1.45 Arial,Helvetica,sans-serif}
  .wrap{max-width:820px;margin:16px auto;background:#fff;border:1px solid #e5e7eb;display:grid;grid-template-columns:140px 1fr}
  .band{background:#0f172a;color:#e5e7eb;padding:16px 12px}
  .band .logo{width:100%;height:80px;border:1px solid #334155;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#111827;margin-bottom:10px}
  .band img{max-width:96%;max-height:96%}
  .band .cname{font-size:16px;font-weight:700;margin:6px 0}
  .band .muted{color:#cbd5e1;font-size:11px}
  .content{padding:14px 18px}
  .top{display:flex;align-items:flex-start;gap:10px;border-bottom:1px solid #e5e7eb;padding-bottom:8px}
  .meta{margin-left:auto;text-align:right}
  .chip{display:inline-block;padding:3px 8px;border-radius:999px;background:#e0e7ff;color:#312e81;font-weight:700}
  .cards{display:flex;gap:12px;margin-top:10px}
  .card{flex:1;border:1px solid #e5e7eb;border-radius:10px;padding:10px}
  .card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;color:#374151}
  .kv{font-size:12px;margin:3px 0}.k{display:inline-block;width:110px;color:#667085}
  table{width:100%;border-collapse:collapse}
  .items{margin-top:10px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
  th,td{padding:10px;font-size:12px} thead th{background:#eef2ff;border-bottom:1px solid #e5e7eb;text-align:left}
  tbody td{border-bottom:1px solid #f1f5f9}.tr{text-align:right}.tc{text-align:center}.w40{width:40px}.w70{width:70px}.w90{width:90px}
  .grid{display:grid;grid-template-columns:1fr 300px;gap:12px;margin-top:10px}
  .note{border:1px solid #e5e7eb;border-radius:10px;padding:10px;font-size:12px}
  .sum{border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
  .sum-row{display:flex;padding:8px 12px;border-bottom:1px solid #f1f5f9;font-size:12px}
  .sum-row:last-child{border-bottom:none}
  .grand{background:#3730a3;color:#eef2ff;font-weight:700}
  .foot{display:flex;justify-content:space-between;align-items:center;margin-top:12px;font-size:11px;color:#6b7280}
  .stamp{width:140px;height:60px;border:1px dashed #d1d5db;border-radius:8px;display:flex;align-items:center;justify-content:center}
  @media print{body{background:#fff}.wrap{border:none;display:block}.band{page-break-inside:avoid}}
</style></head><body>
<div class="wrap">
  <aside class="band">
    <div class="logo"><img src="{{ $companyLogoUrl }}" alt="Logo"></div>
    <div class="cname">{{ $companyName }}</div>
    <div class="muted">{{ $companyAddress }}</div>
    <div class="muted">GSTIN: {{ $companyGstin }}</div>
    <div class="muted">Ph: {{ $companyPhone }}</div>
    <div class="muted">{{ $companyEmail }}</div>
  </aside>
  <main class="content">
    <div class="top">
      <div class="chip">Quotation</div>
      <div class="meta">
        No: <b>{{ $quotationNumber }}</b><br>
        Date: <b>{{ $quotationDate }}</b><br>
        Valid Till: <b>{{ $validTill }}</b>
      </div>
    </div>

    <div class="cards">
      <div class="card">
        <h3>Bill To</h3>
        <div class="kv"><span class="k">Party</span>: <b>{{ $partyName }}</b></div>
        <div class="kv"><span class="k">Address</span>: {{ $partyAddress }}</div>
        <div class="kv"><span class="k">GSTIN</span>: {{ $partyGstin }}</div>
        <div class="kv"><span class="k">Contact</span>: {{ $partyPhone }}</div>
      </div>
      <div class="card">
        <h3>Company</h3>
        <div class="kv"><span class="k">State</span>: {{ $companyState }}</div>
        <div class="kv"><span class="k">Email</span>: {{ $companyEmail }}</div>
        <div class="kv"><span class="k">Phone</span>: {{ $companyPhone }}</div>
      </div>
    </div>

    <table class="items">
      <thead><tr>
        <th class="w40 tc">#</th><th>Description</th><th class="w70 tc">HSN</th><th class="w70 tc">Qty</th><th class="w90 tr">Rate</th><th class="w90 tr">Amount</th>
      </tr></thead>
      <tbody>
      @foreach(($items??[]) as $i=>$it)
        @php $amt=($it['qty']??0)*($it['rate']??0); $subTotal+=$amt; @endphp
        <tr>
          <td class="tc">{{ $i+1 }}</td>
          <td><b>{{ $it['name'] }}</b><div style="color:#667085">{{ $it['desc']??'' }}</div></td>
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
        Prices in INR; taxes extra as applicable.<br>
        <b>Payment Terms:</b> {{ $paymentTerms }}<br>
        <b>Delivery:</b> {{ $delivery }}<br>
        <b>Warranty:</b> {{ $warranty }}
      </div>
      <div class="sum">
        <div class="sum-row"><div class="lbl">Sub Total</div><div class="val">₹ {{ number_format($subTotal,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Discount</div><div class="val">- ₹ {{ number_format($discount,2) }}</div></div>
        <div class="sum-row"><div class="lbl">Taxable</div><div class="val">₹ {{ number_format($taxable,2) }}</div></div>
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

    <div style="margin-top:12px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
      <div style="background:#eef2ff;padding:10px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;text-transform:uppercase">Terms &amp; Conditions</div>
      <div style="padding:10px 12px">
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
  </main>
</div>
</body></html>
