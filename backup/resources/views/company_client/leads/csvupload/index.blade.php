@extends('layouts.client')
@section('title', 'Add CSV')
@section('content') <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @if ($errors->any())
                    <h5 style="color:red">Following errors exists in your excel file</h5>
                    <ol>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                @endif
                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="d-flex justify-content-between card-body">
                                <h5 class="card-title mb-0">Upload CSV
                                    <a href="{{ route('lead.csvupload.dummyexcel') }}"
                                        class="btn btn-sm btn-primary text-white">
                                        Sample Excel
                                    </a>

                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('lead.csvupload.store') }}" method="post"
                                    onsubmit="return validateFile()" enctype="multipart/form-data"> @csrf <div
                                        class="row gy-4" style="align-items: end;">
                                        <div class="col-lg-3 col-md-3">
                                            <div>
                                                <span style="color:red;">*</span>Upload CSV File
                                                <input type="file" class="form-control" name="csvfile" id="csvfile"
                                                    accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                    required>

                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3">
                                            <button type="submit" class="btn btn-success btn-user float-right">Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function validateFile() {
            var allowedExtensions = ['xlsx', 'xls', 'csv'];
            var fileInput = document.getElementById('csvfile');
            var filePath = fileInput.value;
            var extension = filePath.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(extension)) {
                alert('Allowed Extensions are: ' + allowedExtensions.join(', '));
                fileInput.value = ""; // clear the invalid file
                return false;
            }
            return true;
        }
    </script>
@endsection
