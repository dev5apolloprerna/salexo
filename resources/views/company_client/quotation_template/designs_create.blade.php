{{-- resources/views/company/quotations/designs_create.blade.php --}}
@extends('layouts.client')
@section('title','Upload Quotation Template')
@section('content')
<div class="main-content">
        <div class="page-content">
<div class="container-fluid">
  @include('common.alert')
  <h4 class="mb-3">Upload / Create Template</h4>

  <form method="post" action="{{ route('company.quotations.designs.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Display Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Version (e.g., v1..v5)</label>
        <input type="text" name="version" class="form-control" value="{{ old('version','v1') }}" required>
        @error('version') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Engine</label>
        <select name="engine" class="form-control" required>
          <option value="blade" @selected(old('engine')==='blade')>Blade file</option>
          <option value="html"  @selected(old('engine')==='html')>Inline/HTML</option>
        </select>
        @error('engine') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Upload File (optional)</label>
        <input type="file" name="file" class="form-control" accept=".blade.php,.html,.htm,.php,.txt">
        <div class="form-text">If provided, it overrides inline HTML.</div>
        @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Inline HTML (optional)</label>
      <textarea name="inline_html" rows="10" class="form-control" placeholder="Paste HTML if not uploading a file">{{ old('inline_html') }}</textarea>
      @error('inline_html') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <button class="btn btn-primary">Save Template</button>
    <a href="{{ route('company.quotations.designs') }}" class="btn btn-light">Cancel</a>
  </form>
</div>
</div>
</div>

@endsection
