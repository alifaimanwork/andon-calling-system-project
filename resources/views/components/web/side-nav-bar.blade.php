@section('side-nav-bar')
    @parent

    <div class="iposweb-side-nav-bar">
        <ul>
            <li class="iposweb-side-nav-bar-button {{ (($menuActive ?? null) == 'overview')?'active':'' }}">
                <a href="{{ route('overview.index',$plant->uid) }}" title="Plant Overview Dashboard">
                    <i class="fa-solid fa-table-layout"></i>
                </a>
            </li>
            <li class="iposweb-side-nav-bar-button {{ (($menuActive ?? null) == 'realtime')?'active':'' }}">
                <a href="{{ route('realtime.factory-oee.index',$plant->uid) }}" title="Real Time Production Monitoring">
                    <i class="fa-solid fa-timer"></i>
                </a>
            </li>
            <li class="iposweb-side-nav-bar-button {{ (($menuActive ?? null) == 'analysis')?'active':'' }}">
                <a href="{{ route('analysis.summary.index',$plant->uid) }}?r=1" title="Operational Analysis">
                    <i class="fa-solid fa-folder-open"></i>
                </a>
            </li>
            @if(App\Models\User::getCurrent()->isPlantAdmin())
                <li class="iposweb-side-nav-bar-button {{ (($menuActive ?? null) == 'settings')?'active':'' }}">
                    <a href="{{ route('settings.downtime.index',$plant->uid) }}" title="Plant Settings">
                        <i class="fa-solid fa-sliders"></i>
                    </a>
                </li>
            @endif
            @if(App\Models\User::getCurrent()->isPlantAdmin())
                <li class="iposweb-side-nav-bar-button {{ (($menuActive ?? null) == 'pps')?'active':'' }}">
                    <a href="{{ route('settings.pps.index',$plant->uid) }}" title="PPS">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endsection
@section('head')
    @parent
    <?php // TODO: put style in mix except component compensate
    ?>
    <style>

        .iposweb-side-nav-bar {
            position: fixed;
            left: 0;
            top: 40px;
            /* topnav height */
            height: calc(100vh - 40px);
            width: 60px;
            background-color: #A3A3A3;
        }

        .iposweb-side-nav-bar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;

        }

        .iposweb-side-nav-bar-button a {
            text-decoration: none;

        }

        .iposweb-side-nav-bar-button a:hover {
            color: #73A5C6;
        }

        .iposweb-side-nav-bar .active a {
            color: #000080;
        }

        .iposweb-side-nav-bar-button a {
            color: white;
        }

        .iposweb-side-nav-bar-button {
            font-size: 2rem;
            width: 100%;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
        }
    </style>

    <style>
        @media only screen and (max-width: 768px) {
            .iposweb-side-nav-bar {
                left: 0;
                right: 0;
                width: 100%;
                top: calc(100% - 42px);
                bottom: 0;
                z-index: 5;
            }

            .iposweb-side-nav-bar ul {
                display: flex;
                justify-content: center;
            }

            .iposweb-side-nav-bar-button {
                height: 42px;
                width: 64px;
            }
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>

    </script>
@endsection