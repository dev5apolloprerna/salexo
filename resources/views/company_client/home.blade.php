@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')

    @php
        use Illuminate\Support\Facades\DB;
        use App\Models\LeadMaster;
        use App\Models\LeadPipeline;
        use App\Models\DealDone;

        $emp_id = Auth::guard('web_employees')->user()->company_id;
        $lead_pipeline = LeadPipeline::where([
            'company_id' => $emp_id,
            'pipeline_name' => 'Deal Done',
        ])->first();
        $leadsGenerated = DB::table(function ($query) use ($emp_id) {
            $leadQuery = DB::table('lead_master')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->where('iCustomerId', $emp_id)
                ->groupByRaw('MONTH(created_at)');

            $dealDoneQuery = DB::table('deal_done')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->where('iCustomerId', $emp_id)
                ->groupByRaw('MONTH(created_at)');

            $dealCancelQuery = DB::table('deal_cancel')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->where('iCustomerId', $emp_id)
                ->groupByRaw('MONTH(created_at)');

            $query->fromSub($leadQuery->unionAll($dealDoneQuery)->unionAll($dealCancelQuery), 'combined');
        }, 'monthly')
            ->select('month', DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        /*  $leadsGenerated = DB::table(function ($query) use ($emp_id) {
            $query
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->from('lead_master')
                ->whereYear('created_at', date('Y'))
                ->where('iCustomerId', $emp_id)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->unionAll(
                    DB::table('deal_done')
                        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                        ->whereYear('created_at', date('Y'))
                        ->where('iCustomerId', $emp_id)
                        ->groupBy(DB::raw('MONTH(created_at)')),
                    DB::table('deal_cancel')
                        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                        ->whereYear('created_at', date('Y'))
                        ->where('iCustomerId', $emp_id)
                        ->groupBy(DB::raw('MONTH(created_at)')),
                    // DB::table('lead_master')
                    //    ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                    //    ->whereYear('created_at', date('Y'))
                    //    ->where('iCustomerId', $emp_id)
                    //    ->groupBy(DB::raw('MONTH(created_at)')),
                );
        }, 'combined')
            ->select('month', DB::raw('SUM(total) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();  */

        /*$leadsGenerated = LeadMaster::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->where('iCustomerId', $emp_id)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->toArray();*/
        //->get();
        //dd($leadsGenerated);

        $leadsConverted = DealDone::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->where('status', $lead_pipeline->pipeline_id)
            ->whereYear('created_at', date('Y'))
            ->where('iCustomerId', $emp_id)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->toArray();

        $currentMonth = now()->month;
        $monthsToShow = 6; // show current + previous 6
        $startMonth = max(1, $currentMonth - $monthsToShow);

        $allLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $labels = [];
        $generatedData = [];
        $convertedData = [];

        for ($i = $startMonth; $i <= $currentMonth; $i++) {
            $labels[] = $allLabels[$i - 1]; // -1 because index starts from 0
            $generatedData[] = $leadsGenerated[$i] ?? 0;
            $convertedData[] = $leadsConverted[$i] ?? 0;
        }

        $employeeLeads = LeadMaster::selectRaw('employee_id, COUNT(*) as leads')
            ->groupBy('employee_id')
            ->where('iCustomerId', $emp_id)
            ->pluck('leads', 'employee_id')
            ->toArray();
    @endphp

    <style>
        .card {
            border: 1px solid #c1c1c1 !important;
            border-radius: 1rem;
            box-shadow: 10px 14px 20px rgb(8 8 8 / 9%);
            position: relative;
            height: 100%;
            margin-bottom: 0;
        }

        .status-box {
            color: white;
            padding: 20px;
            border-radius: 1rem;
            text-align: center;
            position: relative;
        }

        /*.bg-gradient-blue { background: linear-gradient(135deg, #1d8cf8, #3358f4); }*/
        /*.bg-gradient-green { background: linear-gradient(135deg, #00b894, #00cec9); }*/
        /*.bg-gradient-orange { background: linear-gradient(135deg, #f39c12, #e67e22); }*/
        /*.bg-gradient-red { background: linear-gradient(135deg, #e74c3c, #c0392b); }*/
        /*.bg-gradient-dark { background: linear-gradient(135deg, #2c3e50, #34495e); }*/
        /*.bg-gradient-light-green { background: linear-gradient(135deg, #246a18, #49dc80) }*/
        /*.bg-gradient-purple { background: linear-gradient(135deg, #8e44ad, #9b59b6);}*/
        /*.bg-gradient-teal {  background: linear-gradient(135deg, #06b6d4, #3b82f6);}*/


        .icon-box {
            font-size: 32px;
            position: absolute;
            right: 10px;
            top: 0px;
            opacity: 0.5;
        }

        .circle-progress {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
            background: conic-gradient(#27ae60 0% 10%, #ccc 10% 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .circle-progress::before {
            content: '';
            position: absolute;
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
        }

        .circle-progress span {
            position: relative;
            font-size: 20px;
            font-weight: bold;
            color: #27ae60;
        }

        h5 {
            color: #fff!important;
            text-align: left;
        }

        h6 {
            font-weight: 600;
            color: #1f3762!important;
            border-bottom: 1px solid #c1c1c1;
            padding-bottom: 10px;
        }

        .card-foot {
            font-size: 30px;


            text-align: right;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }



        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f7f7f7;
        }

        .add-member-row {
            text-align: center;
        }

        .no-members {
            background-color: #0096a0;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 10px;
        }

        .add-member-btn {
            background-color: white;
            border: 1px solid #0096a0;
            color: #0096a0;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .view-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .view-btn:hover {
            background-color: #0069d9;
        }

        .table-wrapper {
            max-height: 200px;
            /* Adjust height as needed */
            overflow-y: auto;
        }

        [data-layout=vertical][data-sidebar-size=sm] .navbar-menu .navbar-nav .nav-item:hover>a.menu-link {
            position: relative;
            width: calc(200px + 70px);
            color: #fff;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%), radial-gradient(at top center, rgba(255, 255, 255, 0.40) 0%, rgba(0, 0, 0, 0.40) 120%) #989898 !important;
            background-blend-mode: multiply, multiply;
            -webkit-transition: none;
            transition: none;
        }

        thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            /* Bootstrap table-light background */
            z-index: 2;
        }

        .profile-card {
            width: 100%;
            max-width: 320px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', sans-serif;
            transition: 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-3px);
        }

        .profile-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 25px 20px 20px;
            position: relative;
            text-align: center;
        }

        .profile-card .edit-icon {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
        }

        .profile-card .avatar i {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }

        .profile-info h5 {
            margin: 5px 0;
            font-size: 18px;
        }

        .profile-info .email {
            font-size: 13px;
            opacity: 0.9;
        }

        .profile-stats {
            display: flex;
            justify-content: space-around;
            padding: 15px 10px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
        }

        .profile-stats div {
            text-align: center;
        }

        .profile-stats strong {
            display: block;
            font-size: 18px;
            color: #2d3436;
        }

        .profile-stats p {
            font-size: 12px;
            color: #636e72;
            margin: 0;
        }

        .update-btn {
            width: 100%;
            background: #2d3436;
            color: white;
            border: none;
            padding: 12px 0;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .update-btn:hover {
            background: #1e272e;
        }

        .profile-footer {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            font-size: 12px;
            color: #2d3436;
            background: #f1f2f6;
        }

        .profile-footer i {
            margin-right: 5px;
        }

        .color-picker {
            width: 40px;
            border: none;
            background: none;
        }

        .add-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .pipeline-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pipeline-list li {
            display: flex;
            align-items: center;
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            position: relative;
        }

        .pipeline-title {
            flex: 1;
            margin-left: 10px;
            font-size: 14px;
        }

        .color-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 15px;
        }

        .action-icons {
            display: flex;
            gap: 10px;
        }

        .edit-icon,
        .delete-icon {
            cursor: pointer;
            color: #888;
            font-size: 14px;
        }

        .edit-icon:hover {
            color: #2980b9;
        }

        .delete-icon:hover {
            color: #c0392b;
        }
        
        .enter-btn {
                text-align: left;
                font-size: 16px;
                padding-top: 15px;
            }
        
    </style>
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
