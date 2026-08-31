@extends('layouts.client')
@section('title', 'Meeting Done Report')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Alert Messages --}}
                @include('common.alert')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Meeting Done Report</h5>
                                <hr>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.reports.meeting_done') }}" method="GET"
                                    class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="meeting_done_emp_id" class="form-label">User</label>
                                        <select name="emp_id" id="meeting_done_emp_id" class="form-control">
                                            <option value="">----- All Users -----</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->emp_id }}"
                                                    {{ (string) ($filterEmpId ?? '') === (string) $employee->emp_id ? 'selected' : '' }}>
                                                    {{ $employee->emp_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="meeting_done_from_date" class="form-label">From Date</label>
                                        <input type="date" name="from_date" id="meeting_done_from_date"
                                            class="form-control" value="{{ $fromDate ?? '' }}" max="{{ $toDate ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="meeting_done_to_date" class="form-label">To Date</label>
                                        <input type="date" name="to_date" id="meeting_done_to_date"
                                            class="form-control" value="{{ $toDate ?? '' }}" min="{{ $fromDate ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('clients.reports.meeting_done') }}"
                                                class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h6 class="mb-0">
                            <i class="fas fa-handshake"></i> Leads with a Meeting Done Activity
                            <span class="badge bg-primary ms-2">Total: {{ $leadHistory->total() }}</span>
                        </h6>
                            <a href="{{ route('clients.reports.meeting_done.export', request()->only(['emp_id', 'from_date', 'to_date'])) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Export to Excel
                                </a>
                    </div>

                    @if ($meetingDonePipelineIds->isEmpty())
                        <div class="alert alert-warning mb-0">
                            No pipeline stage named "Meeting Done" is set up yet. Add one from Lead Master &rarr; Lead
                            Pipeline, and any lead moved to that stage will automatically show up here.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Company</th>
                                        <th>Mobile</th>
                                        <th>Current Status</th>
                                        <th>Comments</th>
                                        @if ($showAmount)
                                            <th>Amount</th>
                                        @endif
                                        <th>Follow Up By</th>
<!--                                         <th>Next Follow-up Date</th>
                                        <th>Date</th> -->
                                        <th>Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($leadHistory as $index => $history)
                                        <tr>
                                            <td>{{ $leadHistory->firstItem() + $index }}</td>
                                            <td>{{ $history->customer_name ?? '-' }}</td>
                                            <td>{{ $history->company_name ?? '-' }}</td>
                                            <td>{{ $history->mobile ?? '-' }}</td>
                                            <td>{{ $history->current_status_name ?? '-' }}</td>
                                            <td class="text-start">{{ $history->Comments ?? '-' }}</td>
                                            @if ($showAmount)
                                                <td>{{ $history->amount && $history->amount != '0' ? $history->amount : '-' }}</td>
                                            @endif
                                            <td>{{ $history->followup_by_name ?? '-' }}</td>
<!--                                             <td>{{ $history->next_followup_date ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i') }}</td> -->
                                            <td>{{ $history->created_by_name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $showAmount ? 9 : 8 }}">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $leadHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection