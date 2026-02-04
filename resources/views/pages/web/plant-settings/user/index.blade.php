@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'user'])
@include('templates.search-field-text')
@include('templates.search-field-select')

@section('head')
    @parent
    <style>
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

        /* inactive: 90px */
        /* active : 80px */

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
            @include('components.web.change-plant-selector')
            @yield('tab-nav-bar')

            <h5 class="secondary-text mt-3">USER</h5>
            <hr>
            <div class="mt-3 text-end">
                <a href="{{ route('settings.user.create', [$plant->uid]) }}" class="btn btn-action btn-warna">
                    <i class="me-3 fa-duotone fa-file-plus"></i>
                    ADD NEW USER
                </a>
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
                <table id="main-table" class="table table-striped w-100"> </table>
            </div>


        </div>


    </main>
@endsection

@section('modals')
    @parent
@endsection
@section('scripts')
    @parent
    <script>
        var config = {
            _token: "{{ csrf_token() }}",
            dataUrl: "{{ route('settings.user.list', [$plant->uid]) }}",
            datatableColumns: [{
                    title: 'Staff No.',
                    data: 'staff_no'
                },
                {
                    title: 'Name',
                    data: 'full_name'
                },
                {
                    title: 'Designation',
                    data: 'designation'
                },
                {
                    title: 'Email',
                    data: 'email'
                },
                {
                    title: 'Company',
                    data: 'company_name'
                },
                {
                    title: 'Platform Access',
                    data: 'platform_access',
                    render: function(data, type, row) {
                        if (!data)
                            return '<span>None</span>';
                        let platformAccess = JSON.parse(data);
                        if (platformAccess[0] == 0 && platformAccess[1] == 0) {
                            return '<span>None</span>';
                        } else if (platformAccess[0] == 1 && platformAccess[1] == 0) {
                            return '<span>Web Only</span>';
                        } else if (platformAccess[0] == 0 && platformAccess[1] == 1) {
                            return '<span>Terminal Only</span>';
                        } else {
                            return '<span>Web & Terminal</span>';
                        }
                    },
                }, {
                    title: 'Status',
                    data: 'enabled',
                    render: function(data, type, row) {
                        if (data)
                            return '<div class="d-flex justify-content-center w-100"><div class="status-container d-flex justify-content-between"><div class="status-active">ACTIVE</div><div class="filler"></div></div></div>';
                        else
                            return '<div class="d-flex justify-content-center w-100"><div class="status-container d-flex justify-content-between"><div class="filler"></div><div class="status-inactive">INACTIVE</div></div></div>';
                    },
                    class: 'text-center',
                }, {
                    title: 'Action',
                    data: 'id',
                    class: 'text-center list-action',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                    }
                }
            ],
            searchFields: [{
                    title: 'Staff No.',
                    data: 'staff_no'
                }, , {
                    title: 'Name',
                    data: 'full_name'
                },
                {
                    title: 'Designation',
                    data: 'designation'
                },
                {
                    title: 'Email',
                    data: 'email'
                },
                {
                    title: 'Company',
                    data: 'company_name'
                },
                {
                    title: 'Status',
                    data: 'enabled',
                    type: 'select',
                    options: [{
                            value: '',
                            text: 'All'
                        },
                        {
                            value: 1,
                            text: 'Active'
                        },
                        {
                            value: 0,
                            text: 'Inactive'
                        },


                    ]
                },
            ],
            datatableConfig: {}
        };
        var page = new PageDatatableList(config);
        $(() => {
            page.initializeDatatable('#main-table')
                .initializeSearchFields('#search-field-container');
        });

        const baseUrl = "{{ route('settings.user.index', [$plant->uid]) }}";

        function editItem(sender) {
            let thisID = $(sender).data('id');
            window.location.href = `${baseUrl}/${thisID}/edit`;
        }
    </script>
@endsection
