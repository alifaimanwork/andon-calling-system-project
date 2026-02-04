@section('top-nav-bar')
    @parent
    <?php //TODO: use png logo
    ?>
    {{-- topbartitle empty --}}
    <div class="iposweb-top-title d-flex justify-content-center align-items-center" style="pointer-events: none;">
        <div id="top-nav-bar-title">{{ $topBarTitle ?? '' }}</div>
    </div>

    <div class="iposweb-top-nav-bar d-flex justify-content-between align-items-center px-2">
        <div class="d-flex align-items-center">
            <div class="iposweb-top-nav-bar-logo"><a href="{{ url('/') }}"><img
                        src="{{ asset('images/SMI_logo.jpeg') }}"></a></div>
            <div class="font-mono iposweb-top-nav-clock ms-1"
                style="width: 12.46rem; word-wrap: break-word; line-height: 0.8rem">SYARIKAT METAL INDUSTRIES <br>OF MALAYSIA SDN BHD</div>
        </div>

        @auth
            <div class="top-nav-bar-tr-container d-flex align-items-center">
                <div class="flex-fill">
                    <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="plant"
                        data-format="dddd DD/MM/YYYY"></div>
                    <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="plant"
                        data-format="HH:mm:ss"></div>
                </div>

                <div class="ms-2">
                    <div role="button" class="dropdown-toggle" id="top-nav-bar-menu" data-bs-toggle="dropdown"
                        aria-expanded="false" style="font-size: 0.8rem">
                        <div class="micro-pp"></div>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="top-nav-bar-menu">
                        @if (App\Models\User::getCurrent()->isSuperAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.company.index') }}">Manage IPOS Settings</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('manage-account.index') }}">Manage Account</a></li>
                        <li><a class="dropdown-item"
                                href="{{ route('terminal.logout', [$plant->uid, $workCenter->uid]) }}">Logout</a></li>
                    </ul>
                </div>
            </div>
        @endauth
    </div>
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
            font-size: 0.8rem;
        }

        /*utk saiz logo*/
        .iposweb-top-nav-bar-logo img {
            height: 1.4rem;
            position: relative;
            right: 0.05rem;
            top: 0.05rem;
        }

        .iposweb-top-title {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 2.4rem;
            z-index: 100;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            color: white;
        }

        /*css utk main tab*/
        .iposweb-top-nav-bar {
            position: fixed;
            top: 0;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            color: white;
            height: 2.4rem;
            background-color: #000080;
            z-index: 99;
        }

        /* box utk flag */
        main {
            margin-top: 2.4rem;
            height: calc(100vh - 2.4rem);
            overflow-y: auto;
        }

        /* Saiz main tab */
        .top-nav-bar-tr-container {
            width: 12.5rem;
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
