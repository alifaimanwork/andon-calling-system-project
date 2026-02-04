@section('top-nav-bar')
    @parent

    <?php //TODO: use png logo
    ?>
    <div class="iposweb-top-title d-flex justify-content-center align-items-center" style="pointer-events: none;">
        <div id="top-nav-bar-title">{{ $topBarTitle ?? '' }}</div>
    </div>

    <div class="iposweb-top-nav-bar d-flex justify-content-between align-items-center px-2">
        <div class="d-flex align-items-center">
            <div class="iposweb-top-nav-bar-logo"><a href="{{ url('/') }}"><img
                        src="{{ asset('images/SMI_logo.jpeg') }}"></a></div>
            <div class="iposweb-top-nav-clock ms-1"
                style="width: 14.46rem; word-wrap: break-word; line-height: 0.8rem; font-size: 0.7rem">SYARIKAT METAL INDUSTRIES <br>OF MALAYSIA SDN BHD</div>
        </div>
        @auth
            <div class="top-nav-bar-tr-container d-flex align-items-center">
                <div class="flex-fill">
                    <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="local"
                        data-format="dddd DD/MM/YYYY"></div>
                    <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="local"
                        data-format="HH:mm:ss"></div>
                </div>

                <div class="ms-2">
                    <div role="button" class="dropdown-toggle" id="top-nav-bar-menu" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="micro-pp"></div>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="top-nav-bar-menu">
                        @if (App\Models\User::getCurrent()->isSuperAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.company.index') }}">Manage IPOS Settings</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('manage-account.index') }}">Manage Account</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                </div>
            </div>
        @endauth
    </div>
@endsection

@section('mobile-title')
    @parent
    {{-- testtt --}}
@endsection

@section('head')
    @parent

    <?php // TODO: put style in mix
    ?>

    <style>
        .micro-pp {
            background-image: url("{{ Auth::user() ? Auth::user()->getProfilePictureUrl() : asset('images/circle-user.svg') }}");
            background-color: white;
            background-size: cover;
            background-repeat: no-repeat;
            width: 32px;
            height: 32px;
            display: inline-block;
            border-radius: 16px;
        }

        .iposweb-top-nav-clock {
            font-size: 0.9rem;
        }

        /*utk saiz logo*/
        .iposweb-top-nav-bar-logo img {
            height: 27px;
            position: relative;
            right: 1px;
            top: 1px;
        }

        .iposweb-top-title {
            position: fixed;
            top: 0;
            left: 60px;
            width: calc(100vw - 60px);
            height: 40px;
            z-index: 10;
            font-family: 'Poppins', sans-serif;
            color: white;
        }

        /* box utk flag */
        main {
            padding-top: 40px;
            height: 100%;
            overflow-y: auto;
            margin-left: 60px;
            /* sidebar compensate */
            padding-bottom: 16px;
        }

        /*css utk main tab*/
        .iposweb-top-nav-bar {
            position: fixed;
            top: 0;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            color: white;
            height: 40px;
            background-color: #000080;
            z-index: 5;
        }

        body {
            overflow: auto;
            height: 100%;
        }

        @media only screen and (max-width: 768px) {
            .iposweb-top-title {
                left: 0px;
                width: 100vw;
                height: 80px;
                background-color: #000080;
                z-index: 4;
                display: flex !important;
                justify-content: center !important;
                align-items: flex-end !important;
                padding-bottom: 10px;
            }

            main {
                z-index: -1;
                height: 100%;
                margin-top: 80px !important;
                margin-left: 0px;
                /* sidebar compensate */
                padding-bottom: 70px;
                /* overflow-y: scroll; */
            }

            main .container {
                z-index: -1;
            }
        }

        /* Saiz main tab */
        .top-nav-bar-tr-container {
            width: 240px;
        }

        /* font size */
        .font-mono {
            font-family: 'Roboto Mono', monospace;
        }

        .icon {
            height: 1em;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
        var liveClock = new LiveClock("{{ date('c') }}", "{{ isset($plant) ? $plant->time_zone : 'UTC' }}");
    </script>
@endsection
