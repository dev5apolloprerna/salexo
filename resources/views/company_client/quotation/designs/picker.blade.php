@extends('layouts.client')

@section('title','Quotation Designs')
@section('page_title','Choose Quotation Design')

@section('content')
<div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

              <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                          <div class="card-body">


  @if(!$latestId)
    <div class="alert alert-warning">No quotations found. Create one to preview.</div>
  @else
    <div class="grid" style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;">
      @foreach($designs as $d)
        <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fff;">
          <div style="padding:12px 12px 0;font-weight:700;">Design {{ strtoupper($d) }}</div>
          {{-- Tiny thumbnail: we embed an iframe pointing to preview route --}}
          <div style="height:240px;padding:8px;">
            <iframe
              src="{{ route('company.quotations.latest.preview', $d) }}"
              style="width:100%;height:100%;border:1px solid #eee;border-radius:8px;background:#fff;"
              loading="lazy">
            </iframe>
          </div>
          <div style="display:flex;gap:8px; padding:12px; border-top:1px solid #f1f5f9;">
            <a class="btn btn-sm btn-outline-secondary"
               href="{{ route('company.quotations.latest.preview',$d) }}"
               target="_blank">Open Preview</a>
            <a class="btn btn-sm btn-primary"
               href="{{ route('company.quotations.latest.pdf',$d) }}">Download PDF</a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
</div>
</div>
</div>
</div>
</div>
</div>

@endsection
