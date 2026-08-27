@extends('layouts.client')
@section('title', 'Lead Source Analysis Report')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    @include('common.alert')
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Lead Source Analysis Report</h5></div>
        <div class="card-body border-bottom">
            @include('company_client.reports._analysis_filters', ['filterRoute' => route('clients.reports.lead_source_analysis')])
        </div>
        <div class="card-body">
            @forelse ($leadSources as $source)
                @php($counts = $statusCounts->get($source->lead_source_id, collect()))
                <div class="accordion mb-2" id="source-analysis-{{ $source->lead_source_id }}">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#source-analysis-body-{{ $source->lead_source_id }}">
                                <span class="fw-semibold">{{ $source->lead_source_name }}</span>
                                <span class="badge bg-primary ms-2">{{ $counts->sum() }} Total Leads</span>
                            </button>
                        </h2>
                        <div id="source-analysis-body-{{ $source->lead_source_id }}" class="accordion-collapse collapse">
                            <div class="accordion-body table-responsive">
                                @include('company_client.reports._status_table', ['counts' => $counts])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mb-0">No lead sources found for the selected company.</div>
            @endforelse
        </div>
    </div>
</div></div></div>
@endsection
