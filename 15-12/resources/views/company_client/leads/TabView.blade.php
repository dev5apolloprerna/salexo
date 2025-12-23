<ul class="nav nav-pills animation-nav nav-justified mb-3 gap-3" role="tablist">
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('leads.index')) {{ 'active' }} @endif" href="{{ route('leads.index') }}"
            role="tab"><i class="fas fa-fire" title="Active Lead"></i>
            Active Lead <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('leads.done')) {{ 'active' }} @endif" href="{{ route('leads.done') }}"
            role="tab"> <i class="far fa-thumbs-up"></i>
            Lead Done <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link @if (request()->routeIs('leads.cancel')) {{ 'active' }} @endif"
            href="{{ route('leads.cancel') }}" role="tab"> <i class="fas fa-ban " title="Lead Rejected"></i>
            Lead Cancel <span class="badge bg-danger rounded-circle"></span>
        </a>
    </li>

</ul>
