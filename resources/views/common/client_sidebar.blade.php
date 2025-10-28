@php

    $company_id = Auth::guard('web_employees')->user()->company_id;

    $roleid = Auth::guard('web_employees')->user()->role_id;

    if ($roleid == 1) {
        $Route = route('home');
    } elseif ($roleid == 2) {
        $Route = route('userhome');
    } elseif ($roleid == 3) {
        $Route = route('employee.home');
    } else {
        $Route = '#'; // fallback
    }
@endphp

<style>
    /* Sidebar Gradient and Base Styles */
    .app-menu.navbar-menu {
        /*background: linear-gradient(to bottom, #3ccf90, #246fdb) !important;*/
        background-image: linear-gradient(to right, #141E30 0%, #243B55 51%, #141E30 100%) !important;
        color: #333 !important;
        box-shadow: 5px 0 5px 5px #cccccc9c;
    }


    .navbar-brand-box {
        text-align: center;
        padding: 20px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: #fff;
    }

    .navbar-brand-box img {
        max-height: 60px;
        max-width: 160px;
        object-fit: contain;
    }

    .app-menu .nav-link {
        color: #f0f0f0;
        border-radius: 25px 0px 0px 25px;
        margin: 2px 0;
        padding: 10px 15px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.3s;
    }

    .app-menu .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .app-menu .nav-link.active {
        background-color: rgba(0, 0, 0, 0.2);
        color: #fff;
    }

    .app-menu .nav-item i {
        font-size: 1rem;
    }
</style>

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ $Route }}" class="logo d-flex justify-content-center align-items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav">

                @if ($roleid == '2')
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs(['userhome', 'clients.todays_followup', 'clients.over_due_followup'])) active @endif"
                            href="{{ route('userhome') }}">
                            <!--<i class="mdi mdi-view-dashboard-outline"></i>-->
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboards</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('employee.index')) active @endif"
                            href="{{ route('employee.index') }}">
                            <i class="fa fa-id-badge"></i>

                            <span>Employee Master</span>
                        </a>
                    </li>

                    <!-- Lead Master Menu -->
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ in_array(request()->route()->getName(), [
                            'lead-source.index',
                            'lead-cancel-reason.index',
                            'lead-pipeline.index',
                            'service.index',
                            'udf.index',
                        ])
                            ? 'active'
                            : 'collapsed' }}"
                            href="#leadMaster" data-bs-toggle="collapse"
                            aria-expanded="{{ in_array(request()->route()->getName(), [
                                'lead-source.index',
                                'lead-cancel-reason.index',
                                'lead-pipeline.index',
                                'service.index',
                                'udf.index',
                            ])
                                ? 'true'
                                : 'false' }}"
                            aria-controls="leadMaster">
                            <!--<i class="fas fa-user-graduate"></i>-->
                            <i class="fas fa-file-text"></i>
                            <span>Lead Master</span>
                        </a>
                        <div class="collapse menu-dropdown {{ in_array(request()->route()->getName(), [
                            'lead-source.index',
                            'lead-cancel-reason.index',
                            'lead-pipeline.index',
                            'service.index',
                            'udf.index',
                        ])
                            ? 'show'
                            : '' }}"
                            id="leadMaster">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('lead-source.index')) active @endif"
                                        href="{{ route('lead-source.index') }}">
                                        <i class="fa fa-bullhorn"></i>


                                        <span> Lead Source</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('lead-cancel-reason.index')) active @endif"
                                        href="{{ route('lead-cancel-reason.index') }}">
                                        <i class="fa fa-times-circle"></i> Lead Cancel Reason
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('lead-pipeline.index')) active @endif"
                                        href="{{ route('lead-pipeline.index') }}">
                                        <i class="fa fa-project-diagram"></i> Lead Pipeline
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('service.index')) active @endif"
                                        href="{{ route('service.index') }}">
                                        <i class="fa fa-cogs"></i> Service Master
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('udf.index')) active @endif"
                                        href="{{ route('udf.index') }}">
                                        <i class="fa fa-list-alt"></i> UDF Master
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Lead Entry -->
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ in_array(request()->route()->getName(), [
                            'leads.index',
                            'leads.edit',
                            'leads.create',
                            'leads.done',
                            'leads.cancel',
                            'leads.lead_history',
                            'lead.csvupload.index',
                        ])
                            ? 'active'
                            : 'collapsed' }}"
                            href="#leadEntry" data-bs-toggle="collapse"
                            aria-expanded="{{ in_array(request()->route()->getName(), ['leads.index', 'leads.edit', 'leads.create', 'leads.done', 'leads.cancel', 'leads.lead_history', 'lead.csvupload.index']) ? 'true' : 'false' }}"
                            aria-controls="leadEntry">
                            <!--<i class="fas fa-user-graduate"></i>-->
                            <i class="fas fa-pencil-square "></i>
                            <span>Lead Entry</span>
                        </a>
                        <div class="collapse menu-dropdown {{ in_array(request()->route()->getName(), ['leads.index', 'leads.edit', 'leads.create', 'leads.done', 'leads.cancel', 'leads.lead_history', 'lead.csvupload.index']) ? 'show' : '' }}"
                            id="leadEntry">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('leads.index')) active @endif"
                                        href="{{ route('leads.index') }}">
                                        <i class="fa fa-list"></i> Lead List
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('leads.create')) active @endif"
                                        href="{{ route('leads.create') }}">
                                        <i class="fa fa-plus-circle"></i> Add Lead
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs('lead.csvupload.index')) active @endif"
                                        href="{{ route('lead.csvupload.index') }}">
                                        <i class="fas fa-file-upload"></i> CSV Upload
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('clients.calender.index')) active @endif"
                            href="{{ route('clients.calender.index') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span>Calender</span>
                        </a>
                    </li>
                    @if($company_id == 5)
                    <li class="nav-item">
                      <a href="{{ route('party.index') }}"
                         class="nav-link {{ request()->routeIs('party.*') ? 'active' : '' }}">
                        <i class="fa fa-users me-2"></i>
                        <span>Party Master</span>
                      </a>
                    </li>

                     <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('quotation.index') }}">
                            <!-- <i class="fas fa-user-alt"></i> -->
                            <i class="fas fa-file-pdf"></i>
                            <span>Quotation Master</span>
                        </a>
                    </li>
                    @endif
                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ in_array(request()->route()->getName(), [
                            'clients.reports.roi_report',
                            'clients.reports.emp_performance',
                            'clients.reports.emp_lead_analysis',
                            'clients.reports.emp_lead_cancel_analysis',
                            'clients.reports.lead_cancel_analysis_detail',
                            'clients.reports.lead_generated_detail',
                            'clients.reports.lead_given_detail',
                            'clients.reports.lead_analysis_detail',
                            'clients.reports.lead_found_detail',
                            'clients.reports.lead_converted_detail',
                        ])
                            ? 'active'
                            : 'collapsed' }}"
                            href="#Reports" data-bs-toggle="collapse"
                            aria-expanded="{{ in_array(request()->route()->getName(), [
                                'clients.reports.roi_report',
                                'clients.reports.emp_performance',
                                'clients.reports.emp_lead_analysis',
                                'clients.reports.emp_lead_cancel_analysis',
                                'clients.reports.lead_cancel_analysis_detail',
                                'clients.reports.lead_generated_detail',
                                'clients.reports.lead_given_detail',
                                'clients.reports.lead_analysis_detail',
                                'clients.reports.lead_found_detail',
                                'clients.reports.lead_converted_detail',
                            ])
                                ? 'true'
                                : 'false' }}"
                            aria-controls="Reports">
                            <i class="fas fa-chart-line"></i>

                            <span>Reports</span>
                        </a>
                        <div class="collapse menu-dropdown {{ in_array(request()->route()->getName(), [
                            'clients.reports.roi_report',
                            'clients.reports.emp_performance',
                            'clients.reports.emp_lead_analysis',
                            'clients.reports.emp_lead_cancel_analysis',
                            'clients.reports.lead_cancel_analysis_detail',
                            'clients.reports.lead_generated_detail',
                            'clients.reports.lead_given_detail',
                            'clients.reports.lead_analysis_detail',
                            'clients.reports.lead_found_detail',
                            'clients.reports.lead_converted_detail',
                        ])
                            ? 'show'
                            : '' }}"
                            id="Reports">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs([
                                            'clients.reports.roi_report',
                                            'clients.reports.lead_found_detail',
                                            'clients.reports.lead_converted_detail',
                                        ])) active @endif"
                                        href="{{ route('clients.reports.roi_report') }}">
                                        <i class="fas fa-list"></i> ROI Report
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs([
                                            'clients.reports.emp_performance',
                                            'clients.reports.lead_generated_detail',
                                            'clients.reports.lead_given_detail',
                                        ])) active @endif"
                                        href="{{ route('clients.reports.emp_performance') }}">
                                        <i class="fas fa-user-tie"></i> Employee Performance
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs(['clients.reports.emp_lead_analysis', 'clients.reports.lead_analysis_detail'])) active @endif"
                                        href="{{ route('clients.reports.emp_lead_analysis') }}">
                                        <i class="fas fa-chart-pie"></i> Lead Analysis
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if (request()->routeIs(['clients.reports.emp_lead_cancel_analysis', 'clients.reports.lead_cancel_analysis_detail'])) active @endif"
                                        href="{{ route('clients.reports.emp_lead_cancel_analysis') }}">
                                        <i class="fas fa-times-circle me-1"></i> Lead Cancel Analysis

                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs(['employee.home', 'employee.status', 'employee.followup_detail'])) active @endif"
                            href="{{ route('employee.home') }}">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span>Dashboards</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs(['employee.leads.index', 'employee.leads.cancel', 'employee.leads.done'])) active @endif"
                            href="{{ route('employee.leads.index') }}">
                            <i class="fa fa-list"></i>
                            <span>Lead List</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('employee.leads.create')) active @endif"
                            href="{{ route('employee.leads.create') }}">
                            <i class="fa fa-plus-circle"></i>
                            <span>Add Lead</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('employee.calender.index')) active @endif"
                            href="{{ route('employee.calender.index') }}">
                            <i class="fa fa-calendar-alt"></i>
                            <span>Calender</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
