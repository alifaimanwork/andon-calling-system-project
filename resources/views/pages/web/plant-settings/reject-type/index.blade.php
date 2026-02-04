@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'reject-type'])
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
    <div class="container pt-3">
        @yield('mobile-title')
        @include('components.web.change-plant-selector')
        @yield('tab-nav-bar')

        <div class="row mt-3">
            @foreach ( $groups as $group )
                <div class="col-12 col-md-6 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="secondary-text text-uppercase">{{ $group->name }} REJECT TYPE</h5>

                        <div class="my-2 text-end">
                            <a href="{{ route('settings.reject-type.create',[ $plant->uid, $group->id  ]) }}" class="btn btn-action text-uppercase center-all btn-warna"><i class="me-3 fa-duotone fa-file-plus"></i>NEW REJECT TYPE</a>
                        </div>
                    </div>

                    <div class="search-box mt-3">
                        <div class="search-header p-1 px-2 collapsed" data-bs-toggle="collapse" href="#search-table-{{ $group->id }}" role="button" aria-expanded="false" aria-controls="search-main-table">
                            SEARCH &nbsp;<i class="fas fa-chevron-up chevron"></i>
                        </div>
                        <div id="search-table-{{ $group->id }}" class="collapse">
                            <div class="search-container">
                                <div id="search-container-{{ $group->id }}" class="row">
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary search-submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <table id="table-{{ $group->id }}" class="table table-striped w-100"> </table>
                    </div>
                </div>
            @endforeach
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
    var config = {
        _token: "{{ csrf_token() }}",
        dataUrl: "{{ route('settings.reject-type.list',[$plant->uid, $group->id]) }}",
        datatableColumns: [{
                title: 'No.',
                data: 'id',
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                title: 'Reject Type',
                data: 'reject_type',
            },
            {
                title: 'Status',
                data: 'enabled',
                render: function(data, type, row) {
                    if (data)
                        return '<div class="d-flex justify-content-center w-100"><div class="status-container d-flex justify-content-between"><div class="status-active">ACTIVE</div><div class="filler"></div></div></div>';
                    else
                        return '<div class="d-flex justify-content-center w-100"><div class="status-container d-flex justify-content-between"><div class="filler"></div><div class="status-inactive">INACTIVE</div></div></div>';
                },
                class: 'text-center',
            },
            {
                title: 'Locked',
                data: 'locked',
                visible: false,
                searchable: false,
                
            },
            {
                title: 'Action',
                data: 'id',
                sortable: false,
                render: function(data, type, row) {
                    if(row['locked']){
                        return '<a href="#" class="clickable" disabled><i class="fa-solid fa-lock"></i></a>';
                    }else{
                        return `<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                    }
                },
                class: 'text-center list-action',
            },
        ],
        searchFields: [{
                title: 'ID',
                data: 'id',
            },
            {
                title: 'Reject Type',
                data: 'reject_type',
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
                    }
                ]
            },
        ],
        datatableConfig: {
            order: [
                [1, 'asc']
            ]
        },
    };

    var page = new PageDatatableList(config);
    var groups = <?php echo json_encode($groups); ?>

    $(() => {

        // foreach $groups initialize page datatable
        groups.forEach(group => {
            //set data url for config
            config.dataUrl = "{{ route('settings.reject-type.list',[$plant->uid, 'group_id']) }}".replace('group_id', group.id);
            page.initializeDatatable('#table-' + group.id);
            page.initializeSearchFields('#search-container-' + group.id);
        });

    });

    const baseUrl = "{{ route('settings.reject-type.index',[$plant->uid]) }}";

    function editItem(sender) {
        let thisID = $(sender).data('id');
        window.location.href = `${baseUrl}/${thisID}/edit`;
    }
</script>
@endsection