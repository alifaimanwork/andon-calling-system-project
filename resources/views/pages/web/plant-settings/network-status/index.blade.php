@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'network-status'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
        .status-container {
            width: 100px;
            border: 1px solid #575353;
            font-weight: 500;
            text-align: center;
            border-radius: 5px;
            color: #000000;
            pointer-events: none;
        }

        .status-container .status-active {
            width: 90px;
            background-color: #52AF61;
            border-radius: 5px;
        }

        .status-container .status-inactive {
            width: 90px;
            background-color: #FE5F6D;
            border-radius: 5px;
        }

        .table {
            background-color: #FFFFFF;
        }

        .table thead {
            background-color: #E1E1E1;
        }

        .table tbody {
            font-weight: 500;
            color: #575353;
        }

        .list-action a {
            font-size: 1.2em;
            color: brown;
        }

        .btn-warna {
            background-color: #0000e4;
        }
        .btn-warna:hover {
            background-color: #000080;
        }
    </style>
@endsection

@section('body')
    <main>
        <div class="container">
            @yield('mobile-title')
            @include('components.web.change-plant-selector')
            @yield('tab-nav-bar')
            <h5 class="secondary-text mt-3">NETWORK STATUS</h5>
            <hr>
            <div class="my-2 text-end">
                <a href="{{ route('settings.network-status.create', [$plant->uid]) }}" class="btn btn-action btn-warna"><i
                        class="me-3 fa-duotone fa-file-plus"></i>ADD NEW NODE</a>
            </div>


            <div class="search-box mt-3">
                <div class="search-header p-1 px-2 collapsed" data-bs-toggle="collapse" href="#search-main-table"
                    role="button" aria-expanded="false" aria-controls="search-main-table">
                    SEARCH &nbsp;<i class="fas fa-chevron-up chevron"></i>
                </div>
                <div id="search-main-table" class="collapse">
                    <div class="search-container">
                        <div id="search-field-container" class="row">
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary search-submit">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <table id="main-table" class="table w-100"> </table>
            </div>
        </div>
    </main>
@endsection

@section('modals')
    @parent
    <div>

    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var plant = @json($plant);
        var config = {
            _token: "{{ csrf_token() }}",
            dataUrl: "{{ route('settings.network-status.list', [$plant->uid]) }}",
            datatableColumns: [{
                    title: 'No.',
                    data: 'id',
                    sortable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    title: 'Name',
                    data: 'name'
                },
                {
                    title: '',
                    data: 'plant_uid',
                    visible: false
                },
                {
                    title: '',
                    data: 'target_uid',
                    visible: false
                },
                {
                    title: '',
                    data: 'state',
                    visible: false
                },
                {
                    title: '',
                    data: 'enabled',
                    visible: false
                },
                {
                    title: 'Type',
                    data: 'client_type',
                    class: 'text-center',
                    render: function(data, type, row) {
                        if (data == 0)
                            return `<span class="badge bg-primary text-bg-primary">TERMINAL</span>`;
                        else if (data == 1)
                            return `<span class="badge bg-danger text-bg-danger">DASHBOARD</span>`;
                        else if (data == 2)
                            return `<span class="badge bg-success text-bg-success">NETWORK NODE</span>`;
                        else
                            return `<span class="badge bg-secondary text-bg-secondary">UNKNOWN</span>`;

                    }
                },
                {
                    title: 'Last Updated',
                    class: 'text-center',
                    data: 'last_reported_at',
                    render: function(data, type, row) {
                        if (data == null)
                            return '-';

                        let t = moment.tz(data, 'UTC');
                        if (t)
                            return t.tz(plant.time_zone).format('YYYY-MM-DD HH:mm:ss');
                        return '-';
                    }
                },
                {
                    title: 'Host/IP Address',
                    data: 'client_info',
                    render: function(data, type, row) {
                        parsed = JSON.parse(data);
                        if (row.client_type == 2) {
                            if (parsed && typeof(parsed.host) !== 'undefined') {
                                if (typeof(parsed.latency) !== 'undefined' && parsed.latency !== null)
                                    return `${parsed.host} (${parsed.latency}ms)`;
                                else
                                    return parsed.host;
                            }
                        } else {
                            if (parsed && typeof(parsed.REMOTE_ADDR) !== 'undefined')
                                return parsed.REMOTE_ADDR;
                        }
                        return 'N/A';

                    }
                },
                {
                    title: 'Status',
                    data: 'last_reported_at',
                    class: 'text-center list-action',
                    render: function(data, type, row) {
                        if (!row.enabled)
                            return `<span class="badge bg-secondary text-bg-secondary">DISABLED</span>`;
                        let t = moment().unix() - moment.tz(data, 'UTC').unix();

                        if (row.client_type == 2) {
                            if (row.state == 1)
                                return `<span class="badge bg-success text-bg-success">ONLINE</span>`;
                            else
                                return `<span class="badge bg-danger text-bg-danger">OFFLINE</span>`;
                        } else {
                            if (t < 90) {
                                if (row.state == 1)
                                    return `<span class="badge bg-success text-bg-success">CONNECTED</span>`;
                                else if (row.state == 2)
                                    return `<span class="badge bg-primary text-bg-primary">LOGGED OUT</span>`;
                                else
                                    return `<span class="badge bg-warning text-bg-warning">ERROR</span>`;

                            } else if (t >= 90)
                                return `<span class="badge bg-danger text-bg-danger">DISCONNECTED</span>`;
                            else
                                return `<span class="badge bg-secondary text-bg-secondary">N/A</span>`;
                        }
                    }
                },
                {
                    title: 'Action',
                    data: 'uid',
                    class: 'text-center list-action',
                    orderable: false,
                    render: function(data, type, row) {
                        let plantUid = row.plant_uid;
                        let clientId = row.id;
                        let baseUrl =
                            "{{ route('settings.network-status.edit', ['__PLANT_UID__', '__CLIENT_ID__']) }}";
                        let configUrl = baseUrl.replace('__PLANT_UID__', plantUid).replace('__CLIENT_ID__',
                            clientId);
                        let configLink =
                            `<a title="Edit" class="clickable" href="${configUrl}"><i class="fa-duotone fa-pen-to-square"></i></a>`;
                        if (row.client_type == 0 || row.client_type == 1) {
                            let targetUid = row.target_uid;
                            let terminalType = row.client_type;
                            return configLink +
                                `&nbsp;<a title="Copy Link" class="clickable" href="#" data-type="${terminalType}" data-uid="${data}" data-plant-uid="${plantUid}" data-workcenter-uid="${targetUid}" onclick="copyLink(this)"><i class="fa-duotone fa-link"></i></a>`
                        } else
                            return configLink;
                    }
                }
            ],
            searchFields: [{
                title: 'Name',
                data: 'name',
            }, {
                title: 'Type',
                data: 'client_type',
                type: 'select',
                options: [{
                        value: '',
                        text: 'All'
                    },
                    {
                        value: 0,
                        text: 'Terminal'
                    },
                    {
                        value: 1,
                        text: 'Dashboard'
                    },
                    {
                        value: 2,
                        text: 'Network Node'
                    }
                ]
            }],
            datatableConfig: {
                pageLength: 25,
                order: [
                    [1, 'asc']
                ]
            }
        };
        var page = new PageDatatableList(config);
        $(() => {
            page.initializeDatatable('#main-table')
                .initializeSearchFields('#search-field-container');
        });

        function copyLink(sender) {
            let plantUid = $(sender).data('plant-uid');
            let workCenterUid = $(sender).data('workcenter-uid');
            let clientType = $(sender).data('type');
            let uid = $(sender).data('uid');
            console.log(plantUid, workCenterUid, clientType);

            let baseUrl = "{{ route('network-client.register', ['__PLANT_UID__', '__CLIENT_UID__']) }}";


            url = baseUrl.replace('__PLANT_UID__', plantUid).replace('__CLIENT_UID__', uid);
            navigator.clipboard.writeText(url);
            //TODO: POPUP indicator
            var popover = new bootstrap.Popover(sender, {
                content: 'Link Copied!',
                placement: 'auto',
                popperConfig: function(defaultBsPopperConfig) {
                    // var newPopperConfig = {...}
                    // use defaultBsPopperConfig if needed...
                    // return newPopperConfig
                }
            });
            popover.show();
            setTimeout(() => {
                popover.hide();
            }, 1000);
        }
    </script>
@endsection
