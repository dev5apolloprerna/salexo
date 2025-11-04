{{-- resources/views/company/quotations/designs_index.blade.php --}}
@extends('layouts.client')
@section('title', 'Quotation Templates')

@section('content')
<div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
  @include('common.alert')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Quotation Templates</h4>
    <a href="{{ route('company.quotations.designs.create') }}" class="btn btn-primary">Upload / Create Template</a>
  </div>

  @php
  $currentDefault = \DB::table('company_client_master')
      ->where('company_id', auth()->user()->company_id)
      ->value('companyTemplate');
@endphp

<div class="row g-3">
  @foreach($templates as $tpl)
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 border {{ $tpl->version === $currentDefault ? 'border-success' : 'border-light' }}">
        <div class="card-body">
          <h5 class="card-title mb-1">
            {{ $tpl->name }}
            @if($tpl->version === $currentDefault)
              <span class="badge bg-success">Default</span>
            @endif
          </h5>
          <p class="text-muted mb-2">{{ strtoupper($tpl->version) }} • {{ strtoupper($tpl->engine) }}</p>

          <a href="{{ route('company.quotations.latest.preview', $tpl->version) }}"
             target="_blank"
             class="btn btn-outline-primary btn-sm">Preview</a>

          <form action="{{ route('company.quotations.designs.setDefault', $tpl) }}"
                method="post" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit"
              class="btn btn-outline-success btn-sm"
              {{ $tpl->version === $currentDefault ? 'disabled' : '' }}>
              {{ $tpl->version === $currentDefault ? 'Default Template' : 'Mark as Default' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>


  <!-- <div class="row g-3">
    @forelse($templates as $tpl)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title mb-1">{{ $tpl->name }}</h5>
            <div class="text-muted small mb-2">{{ strtoupper($tpl->version) }} • {{ strtoupper($tpl->engine) }}</div>
            <div class="mb-3">
              @if($tpl->file_path)
                <div class="small text-muted">File: {{ $tpl->file_path }}</div>
              @else
                <div class="small text-muted">Inline template</div>
              @endif
            </div>
            <a class="btn btn-outline-primary btn-sm" target="_blank"
               href="{{ route('company.quotations.latest.preview', $tpl->version) }}">Preview</a>
            <form action="{{ route('company.quotations.designs.toggle', $tpl) }}" method="post" class="d-inline">
              @csrf @method('PATCH')
              <button class="btn btn-outline-secondary btn-sm" type="submit">
                {{ $tpl->is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
            <form action="{{ route('company.quotations.designs.destroy', $tpl) }}" method="post" class="d-inline"
                  onsubmit="return confirm('Delete this template?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">No templates yet. Upload one to begin.</div></div>
    @endforelse
  </div> -->
</div>
</div>
</div>

@endsection
