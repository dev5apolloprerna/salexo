@extends('layouts.app')
@section('title', 'Student Plan List')
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
                                <h5 class="card-title mb-0">Plan List
                                    <a href="{{ route('plan.create') }}" style="float: right;" class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Plan
                                    </a>

                                </h5>

                            </div>
                            <div class="card-body">
                                <form method="post" action="{{ route('plan.index') }}" id="myForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="name">Search By Plan Name </label>
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
                                                    <tr class="text-center">
                                                        <th width="50"> Sr No </th>
                                                        <th> Plan Name </th>
                                                        <th> Plan Amount </th>
                                                        <th> Plan Days </th>
                                                        {{--  <th> Action </th>  --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (count($plan) > 0)
                                                        <?php $i = 1;
                                                        ?>
                                                        @foreach ($plan as $key => $cdata)
                                                            <tr data-entry-id="{{ $cdata->plan_id }}" class="text-center">
                                                                <td>
                                                                    {{ $i + $plan->perPage() * ($plan->currentPage() - 1) }}
                                                                </td>
                                                                <td>
                                                                    {{ $cdata->plan_name ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $cdata->plan_amount ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $cdata->plan_days ?? '' }}
                                                                </td>
                                                                {{--  <td>
                                                                    <div class="gap-2 te">
                                                                        <a class="" title="Edit"
                                                                            href="{{ route('plan.edit', $cdata->plan_id) }}">
                                                                            <i class="far fa-edit"></i>
                                                                        </a>
                                                                       <?php /* if(!isset($cdata->student_plan_id) && $cdata->student_plan_id == "") {*/?>

                                                                        <a class="" href="#" data-bs-toggle="modal"
                                                                            title="Delete" data-bs-target="#deleteRecordModal"
                                                                            onclick="deleteData(<?= $cdata->plan_id ?>);">
                                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                                        </a>
                                                                        <?php //}
                                                                        ?>
                                                                    </div>
                                                                </td>  --}}
                                                            </tr>
                                                            <?php $i++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="6">
                                                                <center>
                                                                    No data Found
                                                                </center>
                                                            <td>

                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $plan->links() }}
                                            </div>
                                            @if (count($plan) > 0)
                                                <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"
                                                                    id="btn-close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mt-2 text-center">
                                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                                                        trigger="loop"
                                                                        colors="primary:#f7b84b,secondary:#f06548"
                                                                        style="width:100px;height:100px">
                                                                    </lord-icon>
                                                                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                                        <h4>Are you Sure ?</h4>
                                                                        <p class="text-muted mx-4 mb-0">Are you Sure You
                                                                            want to Remove this Record
                                                                            ?</p>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                                                    <a class="btn btn-danger" href="{{ route('logout') }}"
                                                                        onclick="event.preventDefault(); document.getElementById('bus-delete-form').submit();">
                                                                        Yes,
                                                                        Delete It!
                                                                    </a>
                                                                    <button type="button" class="btn w-sm btn-light"
                                                                        data-bs-dismiss="modal">Close</button>

                                                                    <form id="bus-delete-form" method="POST"
                                                                        action="{{ route('plan.delete') }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <input type="hidden" name="plan_id" id="deleteid"
                                                                            value="">

                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end modal -->
                                            @endif
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

@endsection
@section('scripts')
    <script>
        function deleteData(id) {
            $("#deleteid").val(id);
        }

        function myFunction() {
            $('#search').removeAttr('value');
        }
    </script>

@endsection
