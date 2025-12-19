@extends('layouts.client')
@section('title', 'ROI Report List')
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
                                <h5 class="card-title mb-0">ROI Report List

                                </h5>
                                <hr>
                            </div>
                            <div class="card-body">

                                <form method="GET" action="{{ route('clients.reports.roi_report') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="{{ route('clients.reports.roi_report') }}"
                                                class="btn btn-secondary ms-2">Reset</a>

                                            @php
                                                $company_client_master = App\Models\CompanyClient::where([
                                                    'iStatus' => 1,
                                                    'isDeleted' => 0,
                                                    'company_id' => Auth::user('web_employees')->company_id,
                                                ])->first();
                                            @endphp

                                            @if ($company_client_master && $company_client_master->plan_id != 1)
                                                <a href="{{ route('clients.reports.roi_report.export', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
                                                    class="btn btn-success ms-2">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @else
                                                <a target="_blank" class="btn btn-success ms-2"
                                                    href="{{ route('front.index') }}#cta"
                                                    style="filter: blur(0.5px); opacity:0.5;">
                                                    <i class="fa-solid fa-file-excel fa-xl"></i>
                                                </a>
                                            @endif

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
                                                        <th>Sr.No</th>
                                                        <th>Lead Source</th>
                                                        <th class="text-center">Leads found</th>
                                                        <th class="text-center">Leads Converted</th>
                                                        <th style="text-align:right">Lead Conveted Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($reportData as $index => $data)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $data['source_name'] }}</td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_found_detail', [
                                                                        'lead_source_id' => $data['lead_source_id'],
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $data['leads_found'] }}
                                                                </a>
                                                            </td>
                                                            <td class="text-center">
                                                                <a
                                                                    href="{{ route('clients.reports.lead_converted_detail', [
                                                                        'lead_source_id' => $data['lead_source_id'],
                                                                        'from_date' => request('from_date'),
                                                                        'to_date' => request('to_date'),
                                                                    ]) }}">
                                                                    {{ $data['leads_converted'] }}
                                                                </a>
                                                            </td>
                                                            <td style="text-align:right">₹
                                                                {{ number_format($data['converted_amount'], 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No Report Data Found.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                            </table>

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

        function editpassword(id) {
            $("#GetId").val(id);
        }
    </script>
@endsection
