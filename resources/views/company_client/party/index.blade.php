@extends('layouts.client')
@section('title', 'Party List')
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
                            <div class="card-body">
                                <h5 class="card-title mb-0">Party List
                                    <a href="{{ route('party.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Party
                                    </a>

                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">

                                <form method="get" action="{{ route('party.index') }}">

                                    <div class="row">
                                        <div class="col-md-5 ">
                                            <div class="form-group">
                                                <label for="name">Search By Party Name </label>
                                                <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name / GST / email / mobile">

                                            </div>
                                        </div>

                                        <div class="col-md-5" style="padding-top:30px">
                                            <div class="form-group ">

                                                <input class="btn btn-primary" type="submit" value="{{ 'Search' }}">
                                                <a href="{{ route('party.index') }}" class="btn btn-secondary">Reset</a>

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
                                                        <th>Sr.No</th>
                                                        <th>Party</th>
                                                        <th>GST</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     @php
                                                        $fmtDMY = function($date) {
                                                          try {
                                                            if (empty($date) || $date==='0000-00-00' || $date==='0000-00-00 00:00:00') return '-';
                                                            return \Carbon\Carbon::parse($date)->format('d-m-Y');
                                                          } catch (\Exception $e) { return '-'; }
                                                        };
                                                      @endphp

                                                      @forelse($list as $index => $row)
                                                        @php
                                                          $pid    = $row->partyId ?? $row->party_id ?? $row->id;
                                                          $active = (int)($row->iStatus ?? 0) === 1;
                                                          $dateDMY = $fmtDMY($row->strEntryDate ?? $row->created_at ?? null);
                                                        @endphp
                                                        <tr class="text-center">

                                                           <td>{{ $list->firstItem() + $index }}</td>
                                                          <td>{{ $row->strPartyName }}
<!--                                                             <div class="fw-semibold">{{ $row->strPartyName }}</div>
                                                            @if(!empty($row->company?->company_name))
                                                              <small class="text-muted">Company: {{ $row->company->company_name }}</small>
                                                            @endif -->
                                                          </td>
                                                          <td>{{ $row->strGST }}</td>
                                                          <td>{{ $row->iMobile }}</td>
                                                          <td>{{ $row->strEmail }}</td>
                                                          <td>{{ $dateDMY }}</td>
                                                            <td>
                                                                <a href="{{ route('party.edit', $pid) }}"><i
                                                                        class="fa fa-edit"></i></a>

                                                                <a class="" href="#" data-bs-toggle="modal"
                                                                        title="Delete" data-bs-target="#deleteRecordModal"
                                                                        onclick="deleteData(<?= $pid ?>);">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </a>


                                                                
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No Employees Found.</td>
                                                        </tr>
                                                    @endforelse
                                            </table>
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $list->links() }}
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
                        <form action="{{ route('party.destroy', $pid ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="partyId" id="deleteid" value="">

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

        function editpassword(id) {
            $("#GetId").val(id);
        }
    </script>
@endsection
