@section('side-nav-bar')
    @parent
    <style>
        .isDisabled {
            color: currentColor;
            cursor: not-allowed;
            opacity: 0.5;
            text-decoration: none;
        }
    </style>
    <?php
    $plant = App\Models\Plant::where('uid', '=', $plant->uid)->firstOrFail();
    $plant->loadAppDatabase();
    $plantConnection = $plant->getPlantConnection();
    $workCenter = $plant->onPlantDb()->workCenters()->where('uid', '=', $workCenter->uid)->first();
    
    $current_status = $workCenter ? $workCenter->status : 0;
    ?>

    <div class="iposterminal-side-nav-bar">
        <div class="iposterminal-side-nav-bar-profile">
            <div class="iposterminal-side-nav-bar-profile-pic">
                <img src="{{ $user->getProfilePictureUrl() }}">
            </div>
            <div class="iposterminal-side-nav-bar-user-name flex-fill text-center">
                {{ $user->full_name }}
            </div>
        </div>
        <ul>
            @if ($user->isTerminalOperator($plant->uid, $workCenter->uid) && $current_status == App\Models\WorkCenter::STATUS_IDLE)
                <li><?php $menuKey = 'production-planning';
                $isActive = ($menuActive ?? null) == $menuKey; ?>
                    <a class="iposterminal-side-nav-bar-button {{ $isActive ? 'active' : '' }}"
                        href="{{ $isActive ? '#' : route('terminal.' . $menuKey . '.index', [$plant->uid, $workCenter->uid]) }}"
                        class="d-flex">
                        <div><i class="fa-regular fa-file"></i></div>
                        <div class="iposterminal-side-nav-bar-label">PRODUCTION PLANNING</div>
                    </a>
                </li>
            @endif
            @if($user->isTerminalOperator($plant->uid, $workCenter->uid) && $current_status != App\Models\WorkCenter::STATUS_IDLE)
                <li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-file"></i></div>
                        <div class="iposterminal-side-nav-bar-label">PRODUCTION PLANNING</div>
                    </span>
                </li>
            @endif

            @if ($user->isTerminalOperator($plant->uid, $workCenter->uid) &&($current_status == App\Models\WorkCenter::STATUS_DIE_CHANGE || $current_status == App\Models\WorkCenter::STATUS_FIRST_CONFIRMATION))
                <li><?php $menuKey = 'die-change';
                $isActive = ($menuActive ?? null) == $menuKey; ?>
                    <a class="iposterminal-side-nav-bar-button {{ $isActive ? 'active' : '' }}"
                        href="{{ $isActive ? '#' : route('terminal.' . $menuKey . '.index', [$plant->uid, $workCenter->uid]) }}"
                        class="d-flex">
                        <div><i class="fa-regular fa-timer"></i></div>
                        <div class="iposterminal-side-nav-bar-label">DIE CHANGE</div>
                    </a>
                </li>
            @endif

            @if ($user->isTerminalOperator($plant->uid, $workCenter->uid) &&($current_status != App\Models\WorkCenter::STATUS_DIE_CHANGE || $current_status != App\Models\WorkCenter::STATUS_FIRST_CONFIRMATION))
                <li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-timer"></i></div>
                        <div class="iposterminal-side-nav-bar-label">DIE CHANGE</div>
                    </span>
                </li>
            @endif

            @if ($user->isTerminalOperator($plant->uid, $workCenter->uid) && $current_status == App\Models\WorkCenter::STATUS_RUNNING)
                <li><?php $menuKey = 'progress-status';
                $isActive = ($menuActive ?? null) == $menuKey; ?>
                    <a class="iposterminal-side-nav-bar-button {{ $isActive ? 'active' : '' }}"
                        href="{{ $isActive ? '#' : route('terminal.' . $menuKey . '.index', [$plant->uid, $workCenter->uid]) }}"
                        class="d-flex">
                        <div><i class="fa-regular fa-arrow-up-right-dots"></i></div>
                        <div class="iposterminal-side-nav-bar-label">PROGRESS STATUS</div>
                    </a>
                </li>
                <li><?php $menuKey = 'reject';
                $isActive = ($menuActive ?? null) == $menuKey; ?>
                    <a class="iposterminal-side-nav-bar-button {{ $isActive ? 'active' : '' }}"
                        href="{{ $isActive ? '#' : route('terminal.' . $menuKey . '.index', [$plant->uid, $workCenter->uid]) }}"
                        class="d-flex">
                        <div><i class="fa-regular fa-circle-xmark"></i></div>
                        <div class="iposterminal-side-nav-bar-label">REJECT</div>
                    </a>
                </li>
                <li><?php $menuKey = 'downtime';
                $isActive = ($menuActive ?? null) == $menuKey; ?>
                    <a class="iposterminal-side-nav-bar-button {{ $isActive ? 'active' : '' }}"
                        href="{{ $isActive ? '#' : route('terminal.' . $menuKey . '.index', [$plant->uid, $workCenter->uid]) }}"
                        class="d-flex">
                        <div><i class="fa-regular fa-triangle-exclamation"></i></div>
                        <div class="iposterminal-side-nav-bar-label">DOWNTIME</div>
                    </a>
                </li>

                
            @endif

            @if ($user->isTerminalOperator($plant->uid, $workCenter->uid) && $current_status != App\Models\WorkCenter::STATUS_RUNNING)
                <li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-arrow-up-right-dots"></i></div>
                        <div class="iposterminal-side-nav-bar-label">PROGRESS STATUS</div>
                    </span>
                </li>
                <li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-circle-xmark"></i></div>
                        <div class="iposterminal-side-nav-bar-label">REJECT</div>
                    </span>
                </li>
                <li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-triangle-exclamation"></i></div>
                        <div class="iposterminal-side-nav-bar-label">DOWNTIME</div>
                    </span>
                </li>
                <!--<li>
                    <span class="iposterminal-side-nav-bar-button isDisabled" href="#" class="d-flex">
                        <div><i class="fa-regular fa-clipboard-question"></i></div>
                        <div class="iposterminal-side-nav-bar-label">PENDING</div>
                    </span>
                </li>-->
            @endif


        </ul>
    </div>
@endsection
@section('head')
    @parent
    <?php // TODO: put style in mix except component compensate
    ?>
    <style>
        main {
            margin-left: 10.416rem;
            /* sidebar compensate */
        }

        .iposterminal-side-nav-bar li {
            padding: 0.26rem 0.631rem 0.26rem 0.631rem;

        }

        .iposterminal-side-nav-bar-button {
            border-radius: 0.3rem;
        }

        .iposterminal-side-nav-bar-button {
            display: flex;

            font-size: 1rem;
            width: 100%;
            height: 2.604rem;
            font-weight: 500;
        }

        .iposterminal-side-nav-bar-button i {
            width: 1.25rem;
            display: flex;
            justify-content: right;
            align-items: center;
        }

        .iposterminal-side-nav-bar-label {
            font-size: 1rem;
            padding-left: 0.417rem;
            padding-right: 0.417rem;
            line-height: 1.1rem;
        }

        .iposterminal-side-nav-bar-button div {
            display: flex;
            align-items: center;
        }

        .iposterminal-side-nav-bar-user-name {
            font-size: 1.2rem;
            font-weight: 500;
        }

        .iposterminal-side-nav-bar-profile-pic {
            margin: 1em;
        }

        .iposterminal-side-nav-bar-profile-pic img {
            width: 6.25rem;
            height: 6.25rem;
            border: 0.104rem solid black;
            border-radius: 50%;
        }

        .iposterminal-side-nav-bar-profile {
            height: 12.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }


        .iposterminal-side-nav-bar {
            position: fixed;
            left: 0;
            top: 2.4rem;
            /* topnav height */
            height: calc(100vh - 2.4rem);
            width: 10.416rem;
            background-color: #fff;
            filter: drop-shadow(0.208rem 0px 0.208rem #bfafb2);
        }

        .iposterminal-side-nav-bar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        a.iposterminal-side-nav-bar-button {
            text-decoration: none;

        }

        .iposterminal-side-nav-bar a.active,
        a.iposterminal-side-nav-bar-button:hover {
            color: white;
            background-color: #333399;
        }


        a.iposterminal-side-nav-bar-button {
            color: black;
        }

        a.iposterminal-side-nav-bar-button.inactive {
            color: #666;
        }
    </style>
@endsection
@section('scripts')
    @parent
    <script></script>
@endsection
