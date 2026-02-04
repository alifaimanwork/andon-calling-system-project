@section('drop-menu-navigation')
    <div class="d-flex justify-content-center">
        <div class="trapezium">
            <div class="dropdown d-flex justify-content-center">
                <button class="btn btn-link dropdown-toggle iposweb-drop-menu-navigation" type="button"
                    id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $dropMenuSelected ?? 'Factory OEE' }}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <button class="dropdown-item" type="button" value="factory-oee"
                        onclick="dropMenuNavigation.navigateMenu(this)">FACTORY OEE</button>
                    <button class="dropdown-item" type="button" value="oee"
                        onclick="dropMenuNavigation.navigateMenu(this)">OEE</button>
                    <button class="dropdown-item" type="button" value="productivity"
                        onclick="dropMenuNavigation.navigateMenu(this)">PRODUCTIVITY</button>
                    <button class="dropdown-item" type="button" value="quality"
                        onclick="dropMenuNavigation.navigateMenu(this)">QUALITY</button>
                    <button class="dropdown-item" type="button" value="downtime"
                        onclick="dropMenuNavigation.navigateMenu(this)">DOWNTIME</button>
                </div>
            </div>
        </div>
    </div>
    <div class="trapzium-pad"></div>
@endsection

@section('head')
    @parent
    <style>
        .iposweb-drop-menu-navigation {
            margin-top: -45px;
            background-color: transparent;
            border: none;
            width: 100%;
            color: #000080;
            font-weight: 600;
            font-size: 1.2rem;
            text-decoration: none !important;
        }

        .iposweb-drop-menu-navigation:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .iposweb-drop-menu-navigation div {
            width: 240px;
        }

        /* Creating the trapezium shape*/
        .trapezium {
            position: fixed;
            top: 40px;
            height: 0;
            width: 300px;
            border-top: 45px solid #a3a3a3;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            z-index: 9999;
        }

        .dropdown-menu.show {
            width: 200px;
        }

        .trapzium-pad {
            height: 40px;
        }

        @media (max-width:767px) {
            .trapezium {
                top: 80px;
            }
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        var dropMenuNavigation = {
            navUrlParameters: {
                plant: '{{ $plant->uid }}',
                workcenter: '{{ $workCenter->uid ?? '' }}'
            },
            navUrls: {
                "factory-oee": "{{ route('realtime.factory-oee.index', '__parameters__') }}",
                "oee": "{{ route('realtime.oee.index', '__parameters__') }}",
                "productivity": "{{ route('realtime.productivity.index', '__parameters__') }}",
                "quality": "{{ route('realtime.quality.index', '__parameters__') }}",
                "downtime": "{{ route('realtime.downtime.index', '__parameters__') }}",
            },
            currentPage: "{{ $dropMenuSelected ?? null }}",
            navigateMenu: function(sender) {

                let target = $(sender).val();
                if (target != this.currentPage) {

                    let urlParameters = '';
                    if (this.navUrlParameters.plant)
                        urlParameters = this.navUrlParameters.plant;

                    if (this.navUrlParameters.workcenter && target !== 'factory-oee')
                        urlParameters += '/' + this.navUrlParameters.workcenter;

                    window.location.href = this.navUrls[target].replace('__parameters__', urlParameters);
                }

            }
        }
    </script>
@endsection
