<ul class="nav nav-pills animation-nav nav-justified mb-3" role="tablist">
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.index')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.index') }}" role="tab">
            Active Lead <span class="badge bg-danger rounded-circle">{{ App\Models\LeadMaster::where('iCustomerId', Auth::user()->company_id)->where('employee_id', Auth::user()->emp_id)->where('isDelete', 0)->count() }}</span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.done')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.done') }}" role="tab">
            Lead Done <span class="badge bg-danger rounded-circle">{{ App\Models\DealDone::where('iCustomerId', Auth::user()->company_id)->where('employee_id', Auth::user()->emp_id)->where('isDelete', 0)->count() }}</span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('employee.leads.cancel')) {{ 'active' }} @endif"
            href="{{ route('employee.leads.cancel') }}" role="tab">
            Lead Cancel <span class="badge bg-danger rounded-circle">{{ App\Models\DealCancel::where('iCustomerId', Auth::user()->company_id)->where('employee_id', Auth::user()->emp_id)->where('isDelete', 0)->count() }}</span>
        </a>
    </li>

</ul>
