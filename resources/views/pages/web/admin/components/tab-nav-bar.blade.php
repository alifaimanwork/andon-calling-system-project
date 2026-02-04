@section('head')
@parent
@endsection
@section('tab-nav-bar')
<ul class="nav top-nav">
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'company')?'active':'' }}" href="{{ route('admin.company.index') }}">Company</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'plant')?'active':'' }}" href="{{ route('admin.plant.index') }}">Plant</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'user')?'active':'' }}" href="{{ route('admin.user.index') }}">Super Admin</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'opc-server')?'active':'' }}" href="{{ route('admin.opc-server.index') }}">OPC Server</a>
    </li>
</ul>
@endsection
@section('scripts')
@parent
@endsection