@extends('layouts.client')
@section('title', 'Lead Source Report')
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
                                <h5 class="card-title mb-0">Lead Source Report</h5>
                                <hr>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('clients.reports.lead_source_report') }}" method="GET"
                                    class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="lead_source_emp_id" class="form-label">Search by Employee</label>
                                        <select name="emp_id" id="lead_source_emp_id" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">----- All Employees -----</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->emp_id }}"
                                                    {{ (string) ($filterEmpId ?? '') === (string) $employee->emp_id ? 'selected' : '' }}>
                                                    {{ $employee->emp_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="lead_source_from_date" class="form-label">From Date</label>
                                        <input type="date" name="from_date" id="lead_source_from_date"
                                            class="form-control" value="{{ $fromDate ?? '' }}" max="{{ $toDate ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="lead_source_to_date" class="form-label">To Date</label>
                                        <input type="date" name="to_date" id="lead_source_to_date"
                                            class="form-control" value="{{ $toDate ?? '' }}" min="{{ $fromDate ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('clients.reports.lead_source_report') }}"
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
                        <h6 class="mb-0"><i class="fa-solid fa-filter-circle-dollar"></i> Lead Source Analysis Report
                        </h6>
                        <small class="text-muted">
                            {{ $filterEmpId ? 'Filtered by selected employee' : 'Includes employee and company login leads' }}
                            &middot;
                            {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'All dates' }}
                            @if ($toDate)
                                to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                            @endif
                        </small>
                    </div>

                    @if ($reportLeadSources->isEmpty())
                        <div class="alert alert-info mb-0">No lead sources found for the selected company.</div>
                    @else
                        <div class="accordion" id="leadSourceStatusAccordion">
                            @foreach ($reportLeadSources as $leadSource)
                                @php
                                    $sourceStatuses = $leadSourceStatusCounts->get($leadSource->lead_source_id, collect());
                                    $sourceTotal = $sourceStatuses->sum();
                                    $accordionId = 'lead-source-status-' . $leadSource->lead_source_id;
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}"
                                            aria-expanded="false" aria-controls="{{ $accordionId }}">
                                            <span class="fw-semibold">{{ $leadSource->lead_source_name }}</span>
                                            <span class="badge bg-primary ms-2">{{ $sourceTotal }} Total Leads</span>
                                        </button>
                                    </h2>
                                    <div id="{{ $accordionId }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading-{{ $accordionId }}"
                                        data-bs-parent="#leadSourceStatusAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Lead Status</th>
                                                            <th class="text-end">Lead Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($reportPipelines as $pipeline)
                                                            <tr>
                                                                <td>
                                                                    <span class="d-inline-block rounded-circle me-2"
                                                                        style="width:10px;height:10px;background-color:{{ $pipeline->color ?: '#6c757d' }}"></span>
                                                                    {{ $pipeline->pipeline_name }}
                                                                </td>
                                                                <td class="text-end fw-semibold">
                                                                    {{ $sourceStatuses->get($pipeline->pipeline_id, 0) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th>Total</th>
                                                            <th class="text-end">{{ $sourceTotal }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection
