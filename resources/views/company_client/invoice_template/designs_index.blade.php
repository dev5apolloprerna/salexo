@extends('layouts.client')
@section('title','Invoice Templates')

@section('content')
<div class="main-content">
        <div class="page-content">

<div class="container-fluid">
  @include('common.alert')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Invoice Templates</h4>
  </div>

  <div class="row g-3">
    @forelse($templates as $tpl)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 {{ $tpl->guid === $currentDefaultGuid ? 'border-success' : '' }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-1">
                {{ $tpl->name }}
                @if($tpl->guid === $currentDefaultGuid)
                  <span class="badge bg-success ms-2">Default</span>
                @endif
              </h5>

              {{-- Radio: mark this as default for the LOGGED-IN company --}}
              <form method="POST" action="{{ route('invoiceT.templates.default', $tpl) }}">
                @csrf @method('PATCH')
                <input type="radio"
                       name="default_template"
                       onchange="this.form.submit()"
                       {{ $tpl->guid === $currentDefaultGuid ? 'checked' : '' }}> Mark As Default
              </form>
            </div>

            <div class="small text-muted">GUID: {{ $tpl->guid }}</div>
            <div class="small text-muted mb-3">File: /{{ $tpl->file_path }}</div>

             @php
                $latestQuotation = \App\Models\Invoice::orderByDesc('invoiceId')->first();
            @endphp

            @if($latestQuotation)
                <a href="{{ route('invoiceT.templates.preview', [$tpl->id, $latestQuotation->invoiceId]) }}"
                   class="btn btn-sm btn-outline-primary" target="_blank">
                    Preview
                </a>
            @endif


           <!--  <form action="{{ route('invoiceT.templates.toggle', $tpl) }}" method="post" class="d-inline">
              @csrf @method('PATCH')
              <button class="btn btn-outline-secondary btn-sm" type="submit">
                {{ $tpl->is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </form>

            <form action="{{ route('invoiceT.templates.destroy', $tpl) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Delete this template?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
            </form> -->
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">No templates yet.</div></div>
    @endforelse
  </div>
</div>
</div>
</div>

@endsection
