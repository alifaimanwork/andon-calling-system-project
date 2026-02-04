@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'user'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
@parent
<style>
    .logo-badge {
        max-height: 26px;
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
        @yield('tab-nav-bar')
        <h5 class="secondary-text mt-4">ADMINISTRATOR</h5>
        <hr>
        <div class="my-2 text-end">
            <a href="{{ route('admin.user.create') }}" class="btn btn-action btn-warna"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW ADMINISTRATOR</a>
        </div>


        <div class="search-box">
            <div class="search-header p-1 px-2 collapsed" data-bs-toggle="collapse" href="#search-main-table" role="button" aria-expanded="false" aria-controls="search-main-table">
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
        <table id="main-table" class="table table-striped w-100 mt-2"> </table>

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
        dataUrl: "{{ route('admin.user.list') }}",
        datatableColumns: [{
                title: 'ID',
                data: 'id'
            },
            {
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
                title: 'Role',
                data: 'role',
                render: function(data, type, row) {
                    if (data == '0') {
                        return '<span>Super Admin</span>';
                    }
                    if(data == '1') {
                        return '<span>Plant Admin</span>';
                    }
                    if(data == '2') {
                        return '<span>Normal User</span>';
                    }
                        
                },
                class: 'text-center',
            },
            {
                title: 'Status',
                data: 'enabled',
                render: function(data, type, row) {
                    if (data)
                        return '<span class="badge bg-success">Active</span>';
                    else
                        return '<span class="badge bg-danger">Inactive</span>';
                },
                class: 'text-center',
            },
            {
                title: '',
                data: 'id',
                class: 'text-center list-action',
                orderable: false,
                render: function(data, type, row) {
                    return `<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                }
            }
        ],
        searchFields: [{
                title: 'ID',
                data: 'id'
            }, {
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
                title: 'Role',
                data: 'role',
                type: 'select',
                options: [{
                        value: '0',
                        text: 'Super Admin'
                    },
                    {
                        value: '1',
                        text: 'Plant Admin'
                    },
                    {
                        value: '2',
                        text: 'Normal User'
                    }
                ],
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
            }
        ],
        datatableConfig: {}
    };
    var page = new PageDatatableList(config);
    $(() => {
        page.initializeDatatable('#main-table')
            .initializeSearchFields('#search-field-container');
    });
</script>
<script>
    const baseUrl = "{{ route('admin.user.index') }}";

    function editItem(sender) {
        let userId = $(sender).data('id');
        window.location.href = `${baseUrl}/${userId}/edit`;
    }
</script>
@endsection