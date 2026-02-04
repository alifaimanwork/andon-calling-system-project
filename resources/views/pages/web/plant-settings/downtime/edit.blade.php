@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'downtime'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent

    <style>
        /* TODO: Responsive grid layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr auto auto;
            grid-template-rows: auto 1fr auto;
            row-gap: 1em;
            column-gap: 1em;
        }

        .data-form-cell {
            grid-column-start: 1;
            grid-column-end: 2;
            grid-row-start: 1;
            grid-row-end: 3;
        }

        .profile-picture-cell {
            grid-column-start: 2;
            grid-column-end: 4;
            grid-row-start: 1;
            grid-row-end: 2;
        }

        .platform-permission-cell {
            grid-column-start: 2;
            grid-column-end: 3;
            grid-row-start: 2;
            grid-row-end: 3;
        }

        .company-access-permission-cell {
            grid-column-start: 3;
            grid-column-end: 4;
            grid-row-start: 2;
            grid-row-end: 3;

        }

        .submit-button-cell {
            grid-column-start: 1;
            grid-column-end: 4;
            grid-row-start: 3;
            grid-row-end: 4;
        }

        .profile-pic-container {
            padding: 1em;
        }

        .profile-pic img {
            width: 120px;
            height: 120px;
            border: 2px solid black;
            border-radius: 50%;
        }

        .form-label {
            font-weight: 500;
        }


        .table {
            background-color: #FFFFFF;
        }
        .table thead{
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
        <div class="container pt-4">
            <?php $pageTemplateUrl = route('settings.downtime.index', ['__uid__']); ?>
            @yield('mobile-title')
            @include('components.web.change-plant-selector')
            {{-- TODO: REPAIR CHANGE PLANT SELECTOR --}}
            @yield('tab-nav-bar')
            <div class="row mt-4">
                <h5 class="col-6 secondary-text">EDIT DOWNTIME</h5>
                <div class="col-6 submit-button-cell text-end">
                    <form action="{{ route('settings.downtime.destroy',[ $plant->uid, $downtime->id ]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">DELETE RECORD</button>
                    </form>
                </div>
            </div>
            <hr>
            <form method="POST" action="{{ route('settings.downtime.update',[ $plant->uid , $downtime->id ]) }}">
                @method('PUT')
                @csrf
                <div class="col-12 mb-4">
                    <!-- Data Form -->
                    <div class="card data-form-cell">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="type" class="form-label">DOWNTIME TYPE <span class="text-danger">*</span></label>
                                    <select name="type" id="downtime_type_id" class="form-select">
                                        <option value="0" selected disabled> SELECT MACHINE TYPE</option>
                                        @foreach($types as $type)
                                            @if ($type->id == $downtime->downtime_type_id)
                                                <option value="{{ $type->id }}" selected>{{ $type->name }}</option>
                                            @else
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="category" class="form-label">DOWNTIME CATEGORY <span class="text-danger">*</span></label>
                                    <input type="text" name="category" id="category" class="form-control" value="{{ $downtime->category }}" required>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- Select active or inactive --}}
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span class="text-danger">*</span></label>
                                    <select name="enabled" id="enabled" class="form-select">
                                        @if ($downtime->enabled == 1)
                                            <option value="1" class="text-success" selected>Active</option>
                                            <option value="0" class="text-danger">Inactive</option>
                                        @else
                                            <option value="1" class="text-success">Active</option>
                                            <option value="0" class="text-danger" selected>Inactive</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3 text-end">
                                <a href="{{ route('settings.downtime-reason.create',[ $plant->uid, $downtime->id ]) }}" class="btn btn-action btn-warna"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW REASON</a>
                            </div>
                            <div class="mt-3">
                                <table id="main-table" class="table w-100"> </table>
                            </div>

                        </div>
                        <div class="submit-button-cell text-end m-3">
                            <a href="{{ route('settings.downtime.index',[ $plant->uid ]) }}" class="btn btn-secondary">BACK</a>
                            <button type="submit" class="btn btn-action btn-warna">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </main>
@endsection

@section('scripts')
@parent
<script>
    //TODO: Guard variable
    $(() => {
        page.initialize();
    });


    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }


    var config = {
        _token: "{{ csrf_token() }}",
        dataUrl: "{{ route('settings.downtime-reason.list',[$plant->uid]) }}?id={{ $downtime->id }}",
        datatableColumns: [{
                title: 'Downtime Reason',
                data: 'reason'
            },
            {
                title: 'Allow User Input',
                data: 'user_input',
                render: function(data, type, row, meta) {
                    if (data == 1) {
                        return '<span class="badge bg-success">YES</span>';
                    } else {
                        return '<span class="badge bg-danger">NO</span>';
                    }
                },
                width: '20%',
                class: 'text-center',
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
                title: 'Downtime Reason',
                data: 'reason'
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
        datatableConfig: {}
    };
    var page = new PageDatatableList(config);
    $(() => {
        page.initializeDatatable('#main-table')
            .initializeSearchFields('#search-field-container');
    });

    
    function editItem(sender) {
        let thisID = $(sender).data('id');
        const baseUrl = "{{ route('settings.downtime-reason.edit',[ $plant->uid, $downtime->id, ':id' ]) }}";
        console.log(thisID);
        const url = baseUrl.replace(':id', thisID);
        window.location.href = url;
    }
</script>
@endsection