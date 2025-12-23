@extends('layouts.client')
@section('title', 'Year Master')
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
                                <h5 class="card-title mb-0">Year Master
                                   <!--  <a href="{{ route('employee.create') }}" style="float: right;"
                                        class="btn btn-sm btn-primary">
                                        <i class="far fa-plus"></i> Add Employee
                                    </a> -->

                                </h5>
                                <hr>
                            </div>
                           
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class=" table table-bordered table-striped table-hover datatable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Sr.No</th>
                                                        <th>Year</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($list as $index => $row)
                                                      <tr class="text-center">
                                                        <td>{{ $list->firstItem() + $index }}</td>
                                                        <td>{{ $row->strYear }}</td>
                                                        <td>
                                                          <!-- <form action="{{ route('year.status',$row->year_id) }}" method="POST" class="d-inline">
                                                            @csrf -->
                                                            <button class="btn btn-sm {{ $row->iStatus ? 'btn-success' : 'btn-secondary' }}" title="Toggle Status">
                                                              {{ $row->iStatus ? 'Active' : 'Inactive' }}
                                                            </button>
                                                          <!-- </form> -->
                                                        </td>
                                                        <td>
                                                          <a
                                                            class=" me-1 js-edit"
                                                            data-id="{{ $row->year_id }}"
                                                            data-year="{{ $row->strYear }}"
                                                            data-status="{{ $row->iStatus }}"
                                                            title="Edit"
                                                            data-bs-toggle="modal" data-bs-target="#editModal">
                                                            <i class="fa fa-edit"></i> 
                                                          </a>

                                                        <!--  <a class="m-2" href="#" data-bs-toggle="modal"
                                                          title="Delete" data-bs-target="#deleteRecordModal"
                                                          onclick="deleteData(<?= $row->year_id ?>);">
                                                          <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a> -->
                                                         
                                                        </td>
                                                      </tr>
                                                    @empty
                                                      <tr><td colspan="4" class="text-center text-muted">No records</td></tr>
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
                        <form action="{{ route('year.destroy',$row->year_id ?? '') }}" id="user-delete-form"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="year_id" id="deleteid" value="">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Delete Modal -->

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="editForm" method="POST" action="#">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Year</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Year <span class="text-danger">*</span></label>
            <input type="text" name="strYear" id="edit_strYear" class="form-control" required maxlength="12">
          </div>
         <!--  <div class="mb-0">
            <label class="form-label">Status</label>
            <select name="iStatus" id="edit_iStatus" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div> -->
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
{{-- Bootstrap 5 bundle (if not already included in layout) --}}

<script>
   function deleteData(id) {
            $("#deleteid").val(id);
        }

  // When clicking "Edit", populate modal and set form action
  document.querySelectorAll('.js-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      const id     = btn.dataset.id;
      const year   = btn.dataset.year || '';
      // const status = btn.dataset.status === '1' ? '1' : '0';

      document.getElementById('edit_strYear').value = year;
      // document.getElementById('edit_iStatus').value = status;

      const form = document.getElementById('editForm');
      form.action = "{{ route('year.update', ':id') }}".replace(':id', id);
    });
  });

  // Optional: reopen modal if validation failed on last request
  @if ($errors->any() && session('edit_id'))
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    document.getElementById('edit_strYear').value = "{{ old('strYear') }}";
    // document.getElementById('edit_iStatus').value = "{{ old('iStatus','1') }}";
    document.getElementById('editForm').action = "{{ route('year.update', session('edit_id')) }}";
    editModal.show();
  @endif
</script>
@endsection
