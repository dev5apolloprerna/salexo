@extends('layouts.client')
@section('title','Upload Quotation Template')

@section('content')
<div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
  @include('common.alert')

  <h4 class="mb-3">Upload Template</h4>

  <form method="post" action="{{ route('quotations.templates.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Display Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="row">
      <div class="col-md-6 mb-3" style="display: none">
        <label class="form-label">GUID (optional)</label>
        <input type="hidden" name="guid" class="form-control" value="{{ old('guid') }}" placeholder="Leave blank to auto-generate">
        @error('guid') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Template File</label>
        <input type="file" name="file" class="form-control" accept=".blade.php,.php,.html,.htm" required>
        <div class="form-text">Allowed: .blade.php / .php (Blade / PHP) or .html / .htm</div>
        @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>
    </div>

    <button class="btn btn-primary">Save Template</button>
    <a href="{{ route('quotations.templates') }}" class="btn btn-light">Cancel</a>
  </form>
</div>
</div>
</div>

@endsection
