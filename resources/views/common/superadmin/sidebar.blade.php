<?php
if (Auth::guard('web')->user()) {
    $roleid = Auth::guard('web')->user()->role_id;
} else {
    $roleid = Auth::guard('web_employees')->user()->role_id;
}
?>
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu"></span></li>
                @if ($roleid == '1' && $roleid != 2)
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('home')) {{ 'active' }} @endif"
                            href="{{ route('home') }}">
                            <i class="mdi mdi-speedometer"></i>
                            <span data-key="t-dashboards">Dashboards</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('state.index')) {{ 'active' }} @endif"
                            href="{{ route('state.index') }}">
                            <i class="fas fa-location"></i>
                            <span data-key="t-dashboards">State Master</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('plan.index')) {{ 'active' }} @endif"
                            href="{{ route('plan.index') }}">
                            <i class="fa fa-bookmark"></i>
                            <span data-key="t-dashboards">Plan Master</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('company-client.index')) {{ 'active' }} @endif"
                            href="{{ route('company-client.index') }}">
                            <i class="fas fa-user-tie"></i>
                            <span data-key="t-dashboards">Company Client Master</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('reports.subscription')) {{ 'active' }} @endif"
                            href="{{ route('reports.subscription') }}">
                            <i class="fa-solid fa fa-list"></i>
                            <span data-key="t-dashboards">Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link @if (request()->routeIs('joining_requests.index')) {{ 'active' }} @endif"
                            href="{{ route('joining_requests.index') }}">
                            <i class="fa-solid fa fa-images"></i>
                            <span data-key="t-dashboards">Joining Requests</span>
                        </a>
                    </li>
                @endif


            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
