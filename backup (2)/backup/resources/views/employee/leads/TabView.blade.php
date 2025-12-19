<ul class="nav nav-pills animation-nav nav-justified mb-3" role="tablist">
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.index')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.index') }}" role="tab">
            Active Lead <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.done')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.done') }}" role="tab">
            Lead Done <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.cancel')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.cancel') }}" role="tab">
            Lead Cancel <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>

</ul>
