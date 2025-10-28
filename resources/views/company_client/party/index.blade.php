@extends('layouts.client')
@section('title', 'Party Master')
@section('content')
<div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Validation errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- Form (Add/Edit combined) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <strong>
                {{ $editing ? 'Edit Party #'.$editing->partyId : 'Add Party' }}
            </strong>
            @if($editing)
                <a class="btn btn-sm btn-primary" href="{{ route('party.index', request()->except('edit')) }}">Cancel edit</a>
            @endif
        </div>
        <div class="card-body">
            @php
                $isEdit = (bool) $editing;
                $action = $isEdit ? route('party.update', $editing->partyId) : route('party.store');
            @endphp

            <form method="post" action="{{ $action }}">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Party Name</label>
                        <input type="text" class="form-control" name="strPartyName" maxlength="255" required
                               value="{{ old('strPartyName', $editing->strPartyName ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">GST</label>
                        <input type="text" class="form-control" name="strGST" maxlength="15"
                               value="{{ old('strGST', $editing->strGST ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Entry Date</label>
                        <input type="date" class="form-control" name="strEntryDate" required
                               value="{{ old('strEntryDate', optional($editing->strEntryDate ?? now())->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" name="iMobile" maxlength="20"
                               value="{{ old('iMobile', $editing->iMobile ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="strEmail" maxlength="255"
                               value="{{ old('strEmail', $editing->strEmail ?? '') }}">
                    </div>
                   <div class="col-md-4">
                        <label class="form-label">Address 1</label>
                        <input type="text" class="form-control" name="address1"
                               value="{{ old('address1', $editing->address1 ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Address 2</label>
                        <input type="text" class="form-control" name="address2"
                               value="{{ old('address2', $editing->address2 ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Address 3</label>
                        <input type="text" class="form-control" name="address3"
                               value="{{ old('address3', $editing->address3 ?? '') }}">
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                    @if($isEdit)
                        <a class="btn btn-light" href="{{ route('party.index', request()->except('edit')) }}">Cancel</a>
                    @else
                        <button class="btn btn-light" type="reset">Clear</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Filters + Bulk actions --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <form class="row g-2 align-items-center" method="get" action="{{ route('party.index') }}">
                <!-- <div class="col-auto">
                    <input type="number" class="form-control" name="company_id" placeholder="Company ID"
                           value="{{ $company_id }}">
                </div> -->
                <div class="col-md-6">
                    <input type="text" class="form-control" name="q" placeholder="Search name / GST / email / mobile"
                           value="{{ $q }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div> 
                <div class="col-auto">
                    <a href="{{ route('party.index') }}" class="btn btn-light" type="submit">Reset</a>
                </div>
                <!-- <div class="col-auto">
                    <button type="button" class="btn btn-danger" id="btn-bulk-delete" disabled>Bulk Delete</button>
                </div> -->
            </form>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:30px;"><input type="checkbox" id="check-all"></th>
                        <th>Party</th>
                        <th>GST</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($list as $row)
                    <tr>
                        <td><input type="checkbox" class="row-check" data-id="{{ $row->partyId }}"></td>
                        <td>
                            <div class="fw-bold">{{ $row->strPartyName }}</div>
                            <small class="text-muted">Company: {{ $row->company->company_name }}</small>
                        </td>
                        <td>{{ $row->strGST }}</td>
                        <td>{{ $row->iMobile }}</td>
                        <td>{{ $row->strEmail }}</td>
                        <td>{{ optional($row->strEntryDate)->format('Y-m-d') }}</td>
                        <td>
                            @if($row->iStatus)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{-- Edit: reload same page with ?edit=ID --}}
                            <a class="btn btn-sm btn-primary"
                               href="{{ route('party.index', array_merge(request()->except('page'), ['edit' => $row->partyId])) }}">
                               <i class="fa fa-edit"></i>
                            </a>

                            <form method="post" action="{{ route('party.destroy',$row->partyId) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this party?')"><i class="fa fa-trash"></i></button>
                            </form>

                            <form method="post" action="{{ route('party.toggle-status',$row->partyId) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-warning">
                                    {{ $row->iStatus ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No records</td></tr>
                @endforelse
                </tbody>
            </table>

            {{ $list->links() }}

            <form id="bulk-delete-form" method="post" action="{{ route('party.bulk-delete') }}">
                @csrf
                <input type="hidden" name="ids[]" id="bulk-ids-holder">
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function(){
    const checkAll = document.getElementById('check-all');
    const bulkBtn  = document.getElementById('btn-bulk-delete');
    const bulkForm = document.getElementById('bulk-delete-form');
    const holderId = 'bulk-ids-holder';

    function updateBulkState() {
        const selected = Array.from(document.querySelectorAll('.row-check:checked'))
            .map(cb => cb.getAttribute('data-id'));
        bulkBtn.disabled = selected.length === 0;

        // Clear existing hidden inputs
        document.querySelectorAll('#'+holderId).forEach(el => el.remove());

        // Append each id as input[name="ids[]"]
        selected.forEach(id => {
            const i = document.createElement('input');
            i.type = 'hidden';
            i.name = 'ids[]';
            i.value = id;
            i.id = holderId; // same id for quick cleanup
            bulkForm.appendChild(i);
        });
    }

    checkAll?.addEventListener('change', (e) => {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = e.target.checked);
        updateBulkState();
    });
    document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBulkState));
    bulkBtn?.addEventListener('click', () => {
        if (!confirm('Delete selected parties?')) return;
        bulkForm.submit();
    });
})();
</script>
@endsection
