@extends('layouts.app')
@section('title', 'Company Client List')
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
                                <h5 class="card-title mb-0">Company Client List
                                    <a href="{{ route('company-client.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add New Company Client
                                    </a>

                                </h5>

                            </div>
                            <div class="card-body">

                                <form method="POST" action="{{ route('company-client.index') }}" id="myForm">
                                    @csrf
                                    @method('GET')
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="name">Search By Company Name </label>
                                                <input type="text" name="search" id="search" class="form-control"
                                                    value="{{ $search ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="btn btn-primary" style="margin-top: 15%;" type="submit"
                                                    value="{{ 'Search' }}">
                                                <input class="btn btn-primary" style="margin-top: 15%;" type="submit"
                                                    onclick="myFunction()" value="{{ 'Reset' }}">

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sr No</th>
                                                        <th>Company</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Login Id</th>
                                                        <th>Plan</th>
                                                        <th>Plan Amount</th>
                                                        <th>Subscription Start Date</th>
                                                        <th>Subscription End Date</th>
                                                        <th>Login Date Time</th>
                                                        <th>No. of leads</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <?php $i = 1; ?>
                                                    @foreach ($clients as $client)
                                                        <tr>
                                                            <td>{{ $i + $clients->perPage() * ($clients->currentPage() - 1) }}
                                                            </td>
                                                            <td>{{ $client->company_name }}</td>
                                                            <td>{{ $client->mobile }}</td>
                                                            <td>
                                                                {{ $client->email ?? '-' }}
                                                                <!--@if ($client->employee && $client->employee->first())-->
                                                                <!--    {{ $client->employee->first()->emp_loginId }}-->
                                                                <!--@endif-->
                                                            </td>
                                                            <td>{{ $client->email }}</td>
                                                            <td>{{ $client->plan->plan_name }}</td>
                                                            <td>{{ $client->plan->plan_amount }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($client->subscription_start_date)) }}
                                                            </td>
                                                            <td>{{ date('d-m-Y', strtotime($client->subscription_end_date)) }}
                                                            </td>
                                                            <td>
                                                                @if($client->employee->last_login)
                                                                    {{ date('d-m-Y H:i', strtotime($client->employee->last_login)) }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>{{ $client->total_leads  }}</td>
                                                            <td>
                                                                <a
                                                                    href="{{ route('company-client.edit', $client->company_id) }}"><i
                                                                        class="fa fa-edit"></i></a>
                                                                <a class="mx-1" title="change password"
                                                                    href="{{ route('company-client.changepassword', $client->company_id) }}">
                                                                    <i class="fa-solid fa-key"></i>
                                                                </a>
                                                               
                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                    title="Delete" data-bs-target="#deleteRecordModal"
                                                                    onclick="deleteData(<?= $client->company_id ?>);">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </a>

                                                            </td>
                                                        </tr>
                                                        <?php $i++; ?>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $clients->links() }}
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
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                        </lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you Sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Record
                                ?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <a class="btn btn-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('bus-delete-form').submit();">
                            Yes,
                            Delete It!
                        </a>
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>

                        <form id="bus-delete-form" method="POST"
                            action="{{ route('company-client.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="company_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }

        function myFunction() {
            $('#search').val('');

        }
    </script>

@endsection
