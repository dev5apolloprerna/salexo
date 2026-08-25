@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="main-content">


        <div class="page-content">
            <div class="container mb-5">
                
                @include('common.alert')

                <div class="row mb-3">
                    <!-- <div class="col-md-12"> -->
                        <!-- <div class="card p-3"> -->
                            <form action="{{ route('userhome') }}" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="dashboard_emp_id" class="form-label">Search by Employee</label>
                                    <select name="emp_id" id="dashboard_emp_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">----- All Employees -----</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->emp_id }}"
                                                {{ (string)($filterEmpId ?? '') === (string)$employee->emp_id ? 'selected' : '' }}>
                                                {{ $employee->emp_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="dashboard_from_date" class="form-label">From Date</label>
                                    <input type="date" name="from_date" id="dashboard_from_date" class="form-control"
                                        value="{{ $fromDate ?? '' }}" max="{{ $toDate ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="dashboard_to_date" class="form-label">To Date</label>
                                    <input type="date" name="to_date" id="dashboard_to_date" class="form-control"
                                        value="{{ $toDate ?? '' }}" min="{{ $fromDate ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <a href="{{ route('userhome') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        <!-- </div> -->
                    <!-- </div> -->
                </div>

<div class="card p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h6 class="mb-0"><i class="fa-solid fa-list-check"></i> EMP Analysis Report</h6>
                        <small class="text-muted">
                            {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'All dates' }}
                            @if ($toDate)
                                to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                            @endif
                        </small>
                    </div>

                    @if ($reportEmployees->isEmpty())
                        <div class="alert alert-info mb-0">No employees found for the selected filters.</div>
                    @else
                        <div class="accordion" id="employeeLeadStatusAccordion">
                            @foreach ($reportEmployees as $employee)
                                @php
                                    $employeeStatuses = $leadStatusCounts->get($employee->emp_id, collect());
                                    $employeeTotal = $employeeStatuses->sum();
                                    $accordionId = 'employee-lead-status-' . $employee->emp_id;
                                @endphp
                                <div class="accordion-item">
    <h2 class="accordion-header" id="heading-{{ $accordionId }}">
        <button class="accordion-button collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}"
            aria-expanded="false" aria-controls="{{ $accordionId }}">
            <span class="fw-semibold">{{ $employee->emp_name }}</span>
            @if ($employee->isCompanyAdmin)
                <span class="badge bg-info text-dark ms-2">Company Login</span>
            @endif
            <span class="badge bg-primary ms-2">{{ $employeeTotal }} Total Leads</span>
        </button>
    </h2>
    <div id="{{ $accordionId }}" class="accordion-collapse collapse"
        aria-labelledby="heading-{{ $accordionId }}" data-bs-parent="#employeeLeadStatusAccordion">

<!-- default ek open rakhva niche no code use krvo -->
                                <!-- <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $accordionId }}">
                                            <span class="fw-semibold">{{ $employee->emp_name }}</span>
                                            <span class="badge bg-primary ms-2">{{ $employeeTotal }} Total Leads</span>
                                        </button>
                                    </h2>
                                    <div id="{{ $accordionId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $accordionId }}" data-bs-parent="#employeeLeadStatusAccordion"> -->
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
                                                                <td class="text-end fw-semibold">{{ $employeeStatuses->get($pipeline->pipeline_id, 0) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th>Total</th>
                                                            <th class="text-end">{{ $employeeTotal }}</th>
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
 <div class="card p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h6 class="mb-0"><i class="fa-solid fa-filter-circle-dollar"></i> Lead Source Analysis Report</h6>
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
                                        aria-labelledby="heading-{{ $accordionId }}" data-bs-parent="#leadSourceStatusAccordion">
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
                                                                <td class="text-end fw-semibold">{{ $sourceStatuses->get($pipeline->pipeline_id, 0) }}</td>
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
                <br>
                <div class="row g-3 mb-4 text-white">
                    <div class="col-md-4">
   <a href="{{ route('clients.todays_followup', array_filter([
                            'emp_id' => $filterEmpId,
                            'from_date' => $fromDate,
                            'to_date' => $toDate,
                        ], fn ($value) => filled($value))) }}">
                            <div class="card  text-center p-3 text-white" style="background:#7171cb;">
                                <div class="card-title">
                                    <h5>Today's Followup</h5>
                                </div>
                                <div class="icon-box">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>

                                <div class="card-foot d-flex justify-content-between">
                                    <div class="col-lg-6 enter-btn">
                                        View
                                    </div>
                                    <div class="col-lg-6">
                                        {{ $todays_followup_count ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Overdue Followup -->
                    <div class="col-md-4">
                        <a href="{{ route('clients.over_due_followup', array_filter([
                            'emp_id' => $filterEmpId,
                            'from_date' => $fromDate,
                            'to_date' => $toDate,
                        ], fn ($value) => filled($value))) }}">
                            <div class="card  text-center p-3 text-white" style="background:#ed7e7e;">
                                <div class="card-title">
                                    <h5>Overdue's Followup</h5>
                                </div>
                                <div class="icon-box">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>

                                <div class="card-foot d-flex justify-content-between">
                                    <div class="col-lg-6 enter-btn">
                                        View
                                    </div>
                                    <div class="col-lg-6">
                                    {{ $overdues_followup_count ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    @foreach ($piplines as $pipline)
                        <div class="col-md-4">
                            {{--  @if ($pipline->pipeline_name == 'New Lead')  --}}
                            @php
                                $slug = Str::slug($pipline->pipeline_name);
                            @endphp
<a href="{{ route('clients.new_lead', array_filter([
                                'status' => $slug,
                                'emp_id' => $filterEmpId,
                                'from_date' => $fromDate,
                                'to_date' => $toDate,
                            ], fn ($value) => filled($value))) }}">
                                {{--  @endif  --}}
                                <div class="card  text-center p-3 text-white"
                                    style="background:{{ $pipline->color ?? '#000000' }};">
                                    <div class="card-title">
                                        <h5>{{ $pipline->pipeline_name }}</h5>
                                    </div>
                                    <div class="icon-box">{!! $pipline->icon ?? '<i class="fa fa-file"></i>' !!}</div>
                                    <div class="card-foot"> 
                                        <div class="card-foot d-flex justify-content-between">
                                            <div class="col-lg-6 enter-btn">
                                                View
                                            </div>
                                            <div class="col-lg-6">
                                                {{ $pipline->status_count }} 
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                                {{--  @if ($pipline->pipeline_name == 'New Lead')  --}}
                            </a>
                            {{--  @endif  --}}
                        </div>
                    @endforeach

                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card p-4">
                            <h6><i class="fa-solid fa-chart-bar"></i> Lead Performance</h6>
                            <canvas id="leadChart" height="350"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card  p-4">
                            <h6><i class="fa-solid fa-chart-pie"></i> Top Selling Products</h6>
                            <div class="table-responsive mb-3 table-wrapper">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">

                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th style="text-align:right"> Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topProducts as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product->service_name ?? 'N/A' }}</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td style="text-align:right">{{ $product->total_value ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No completed deals found.</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="card p-4">
                            <h6><i class="fa-solid fa-trophy"></i> Top Performer Report</h6>
                            <div class="table-responsive mb-3 table-wrapper">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Employee Name</th>
                                            <th>Deals Closed</th>
                                            <th class="text-end">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topPerformers as $index => $performer)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $performer->emp_name }}</td>
                                                <td>{{ $performer->deals_closed }}</td>
                                                <td class="text-end">{{ $performer->total_value ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No performer data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{--  <div class="col-md-6">
                        <div class="card p-4 text-center">
                            <h6><i class="fa-solid fa-chart-bar"></i> Top Performers of Month</h6>
                            <form class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Select Duration</label>
                                    <select class="form-select" id="duration">
                                        <option selected>This Month</option>
                                        <option>Last Month</option>
                                        <option>Custom Range</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="team-member" class="form-label">Filter by Team Member</label>
                                    <select class="form-select" id="team-member">
                                        <option selected>All Members</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->emp_id }}">{{ $employee->emp_name }}</option>
                                        @endforeach()
                                    </select>
                                </div>
                            </form>

                            <div class="table-responsive mb-3 table-wrapper">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">

                                        <tr class="text-center">
                                            <th>S.No</th>
                                            <th>Name</th>

                                            <th>Target Value</th>
                                            <th>Achieved Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i= 1; @endphp
                                        @foreach ($employees as $employee)
                                            <tr class="text-center">
                                                <td>{{ $i }}</td>
                                                <td>{{ $employee->emp_name }}</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            @php $i++; @endphp
                                        @endforeach()
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between">
                                <!--<button class="btn btn-outline-primary">-->
                                <!--+ Add New Member-->
                                <a target="_blank" class="btn btn-outline-primary" href="{{ route('employee.create') }}"> +
                                    Add New Member </a>
                                <!--</button>-->
                                <!--<button class="btn btn-primary">View Full Table</button>-->
                                <a target="_blank" class="btn btn-primary" href="{{ route('employee.index') }}"> View Full
                                    Table </a>
                            </div>
                        </div>
                    </div>  --}}
                </div>

                <div class="row g-3">

                    {{--  <div class="col-md-5">
                        <div class="card text-center p-4">
                            <h6><i class="fa-solid fa-chart-pie"></i> Employee Progress</h6>
                            <div class="circle-progress" id="circleProgress">
                                <span>0%</span>
                            </div>
                            <select id="employeeSelect" class="form-select my-2">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->emp_id }}">{{ $employee->emp_name }}</option>
                                @endforeach
                            </select>
                            <select class="form-select my-2">
                                <option>2025</option>
                            </select>
                            <select class="form-select my-2">
                                <option>Month</option>
                            </select>

                        </div>
                    </div>  --}}

                    {{--  <div class="col-md-7">
                        <div class="card text-center p-4">
                            <h6><i class="fa-solid fa-chart-pie"></i> Top Selling Products</h6>
                            <div class="table-responsive mb-3 table-wrapper">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">

                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th>

                                            <th>Quantity</th>
                                            <th> Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Product Name</td>

                                            <td>1000</td>
                                            <td>$11,500</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  --}}
                </div>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>
                        document.write(new Date().getFullYear())
                    </script> © {{ env('APP_NAME') }}
                </div>

            </div>
        </div>
    </footer>

    <script>
        const ctx = document.getElementById('leadChart').getContext('2d');

        const leadChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                        label: 'Leads Generated',
                        data: {!! json_encode($generatedData) !!},
                        backgroundColor: '#3498db'
                    },
                    {
                        label: 'Leads Converted',
                        data: {!! json_encode($convertedData) !!},
                        backgroundColor: '#2ecc71'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        const employeeSelect = document.getElementById("employeeSelect");
        const pieCtx = document.getElementById("leadPieChart").getContext("2d");

        const employeeData = {!! json_encode($employeeLeads) !!}; // { 1: 32, 2: 20, ... }

        let pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['No Selection'],
                datasets: [{
                    label: 'Leads',
                    data: [0],
                    backgroundColor: ['#4caf50']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Handle employee selection
        employeeSelect.addEventListener("change", () => {
            const selectedId = employeeSelect.value;
            const leads = employeeData[selectedId] || 0;
            const label = employeeSelect.options[employeeSelect.selectedIndex].text;

            // Update pie chart
            pieChart.data.labels = [label];
            pieChart.data.datasets[0].data = [leads];
            pieChart.update();

            // Update circular progress
            const progressDiv = document.getElementById("circleProgress");
            const span = progressDiv.querySelector("span");

            const percent = leads > 100 ? 100 : Math.round((leads / 100) * 100); // max 100%
            span.textContent = percent + '%';
            progressDiv.style.background = `conic-gradient(#27ae60 0% ${percent}%, #ccc ${percent}% 100%)`;
        });

        // select.addEventListener("change", () => {
        //     const selected = Array.from(select.selectedOptions).map(opt => opt.value);
        //     const labels = selected.map(id => employeeData[id].name);
        //     const data = selected.map(id => employeeData[id].leads);

        //     pieChart.data.labels = labels;
        //     pieChart.data.datasets[0].data = data;
        //     pieChart.update();
        // });
    </script>

@endsection