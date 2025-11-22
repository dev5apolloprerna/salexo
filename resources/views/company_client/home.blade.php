@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="main-content">


        <div class="page-content">
            <div class="container mb-5">
                
                @include('common.alert')

                <div class="row g-3 mb-4 text-white">
                    <div class="col-md-4">
                        <a href="{{ route('clients.todays_followup') }}">
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
                        <a href="{{ route('clients.over_due_followup') }}">
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
                            <a href="{{ route('clients.new_lead', $slug) }}">
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
                    <div class="col-md-6">
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
                                        @foreach ($topProducts as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product->service_name ?? 'N/A' }}</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td style="text-align:right">{{ $product->total_value ?? '-' }}</td>
                                            </tr>
                                        @endforeach

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
