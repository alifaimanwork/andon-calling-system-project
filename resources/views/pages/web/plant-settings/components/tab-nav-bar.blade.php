@section('head')
@parent
@endsection
@section('tab-nav-bar')
<ul class="nav top-nav mt-3">
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'downtime')?'active':'' }}" href="{{ route('settings.downtime.index', $plant->uid) }}">DOWNTIME</a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'downtime-reason')?'active':'' }}" href="{{ route('settings.downtime-reason.index', $plant->uid) }}">DOWNTIME REASON</a>
    </li> --}}
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'reject-type')?'active':'' }}" href="{{ route('settings.reject-type.index', $plant->uid) }}">REJECT TYPE</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'work-center')?'active':'' }}" href="{{ route('settings.work-center.index', $plant->uid) }}">WORK CENTER</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'break-schedules')?'active':'' }}" href="{{ route('settings.break-schedule.index', $plant->uid) }}">BREAK SCHEDULES</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'part')?'active':'' }}" href="{{ route('settings.part.index', $plant->uid) }}">PART</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'shift')?'active':'' }}" href="{{ route('settings.shift.index', $plant->uid) }}">SHIFT</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'user')?'active':'' }}" href="{{ route('settings.user.index', $plant->uid) }}">USER</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'factory')?'active':'' }}" href="{{ route('settings.factory.index', $plant->uid) }}">FACTORY</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (($tabActive ?? null) == 'network-status')?'active':'' }}" href="{{ route('settings.network-status.index', $plant->uid) }}">NETWORK STATUS</a>
    </li>
</ul>
@endsection
@section('scripts')
@parent
@endsection