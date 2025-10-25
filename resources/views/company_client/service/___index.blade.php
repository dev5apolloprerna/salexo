@extends('layouts.client')
@section('title', 'Service List')
@section('content')

    <?php $profileId = Request::segment(3); ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Service List
                                    <a href="{{ route('service.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add New Service
                                    </a>

                                </h5>

                            </div>
                            <!-- <div class="card-body">
                                        
                                        <form method="post" action="{{ route('service.create') }}" id="myForm">
                                                @csrf
                                                 <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label for="name">Search By Service Name </label>
                                                            <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                        <input class="btn btn-primary" style="margin-top: 15%;" type="submit" value="{{ 'Search' }}">
                                                        <input class="btn btn-primary" style="margin-top: 15%;" type="submit" onclick="myFunction()" value="{{ 'Reset' }}">

                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div> -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr No</th>
                                                        <th>Company Name</th>
                                                        <th>Service Name</th>
                                                        <th>Description</th>
                                                        <th>Image</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($services as $index => $service)
                                                        <tr>
                                                            <td>{{ $services->firstItem() + $index }}</td>
                                                            <td>{{ $service->company->company_name ?? 'N/A' }}</td>
                                                            <td>{{ $service->service_name }}</td>
                                                            <td>{{ Str::limit($service->service_description, 50) }}</td>
                                                            <td>
                                                                @if ($service->service_image)
                                                                    <img src="{{ asset($service->service_image) }}"
                                                                        width="80" height="80" alt="Image">
                                                                @else
                                                                    <span>No Image</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a
                                                                    href="{{ route('service.edit', $service->service_id) }}"><i
                                                                        class="fa fa-edit"></i></a>
                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $service->service_id ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No service Found.</td>
                                                        </tr>
                                                    @endforelse
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {!! $services->links() !!}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Delete Modal -->
    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you Sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Record
                                ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <a class="btn btn-primary mx-2" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('user-delete-form').submit();">
                            Yes,
                            Delete It!
                        </a>
                        <button type="button" class="btn w-sm btn-primary mx-2" data-bs-dismiss="modal">Close</button>
                        <form action="{{ route('service.destroy', $service->service_id ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="service_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Modal -->

@endsection

@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }
    </script>
@endsection
