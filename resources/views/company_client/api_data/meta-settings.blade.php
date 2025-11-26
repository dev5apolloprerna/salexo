@extends('layouts.client')

@section('title', 'Quotation Detail List')

@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            {{-- Alert Messages --}}
            @include('common.alert')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-0">
                                <h5 class="mb-sm-0">Add Meta API Setting</h5></h5>

                                <hr> 
                            </div>

                            <div class="card-body">
                                {{-- Alert Messages --}}

                                <form action="{{ route('api_data.meta.store') }}" method="POST">
                                    @csrf

                                    <div class="row">

                                        {{-- Assign Type --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Assignment Type</label><br>
                                            <label class="me-3">
                                                <input type="radio" name="assign_type" value="single" checked> Single
                                            </label>
                                            <label>
                                                <input type="radio" name="assign_type" value="multiple"> Multiple
                                            </label>
                                        </div>

                                        {{-- Employee --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Employee</label>
                                            <select name="employee_id" class="form-control">
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $emp)
                                                <option value="{{ $emp->emp_id }}">{{ $emp->emp_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Source --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Lead Source</label>
                                            <select name="source_id" class="form-control">
                                                <option value="">Select Source</option>
                                                @foreach($sources as $src)
                                                <option value="{{ $src->lead_source_id }}">{{ $src->lead_source_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Product --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Product</label>
                                            <select name="product_id" class="form-control">
                                                <option value="">Select Product</option>
                                                @foreach($products as $prd)
                                                <option value="{{ $prd->service_id }}">{{ $prd->service_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- API ID --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Advertisement ID (For multiple assignment)</label>
                                            <input type="text" name="ad_id" id="ad_id" class="form-control"
                                            placeholder="Enter Advertisement Id (required for multiple)">
                                        </div>

                                    </div>
                                        <div class="card-footer mt-2">
                                                <div class="mb-3" style="float: right;">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-user float-right mb-3 mx-2">Save</button>
                                                    <button type="reset"
                                                        class="btn btn-primary float-right mr-3 mb-3 mx-2">Clear</button>
                                                </div>
                                            </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTales Example -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-0">Saved Meta API Settings
                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">


                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Employee</th>
                                                <th>Lead Source</th>
                                                <th>Product</th>
                                                <th>Advertisement Id</th>
                                                <th>Assign Type</th>
                                                <!-- <th>Created</th> -->
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php $i=1;  ?>
                                           @if(!empty($metaSettings))
                                            @foreach($metaSettings as $row)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $row->employee->emp_name }} ({{ $row->emp_id }})</td>
                                                <td>{{ $row->source->lead_source_name }}</td>
                                                <td>{{ $row->product->service_name }}</td>
                                                <td>{{ $row->ad_id }}</td>
                                                <td>{{ ucfirst($row->assign_type) }}</td>
                                                <!-- <td>{{ $row->created_at }}</td> -->
                                                  <td>
                                                        <button class="btn btn-danger btn-sm" 
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteMetaModal"
                                                                onclick="setDeleteId('{{ $row->data_id }}')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                            </tr>

                                            <?php $i++; ?>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No records found</td>
                                            </tr>
                                            @endif 

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

                    <!-- Delete Modal -->
<div class="modal fade zoomIn" id="deleteMetaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btn-close"></button>
                </div>

            <div class="modal-body text-center">
                <p class="text-muted">Are you sure you want to delete this record?</p>

                <form id="deleteMetaForm" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
{{-- JS VALIDATION --}}
<script>
     function setDeleteId(id) {
        let url = "{{ route('api_data.meta.delete', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('deleteMetaForm').setAttribute('action', url);
    }


        // AD ID required only when Multiple
        document.querySelectorAll('input[name="assign_type"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const adField = document.getElementById('ad_id');
                if (this.value === 'multiple') {
                    adField.setAttribute('required', true);
                } else {
                    adField.removeAttribute('required');
                }
            });
        });
</script>

@endsection
