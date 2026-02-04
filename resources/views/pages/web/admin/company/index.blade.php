@extends('layouts.app')
@include('utils.auto-toast')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'company'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@include('components.web.top-nav-bar')
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
        <h5 class="secondary-text mt-4">COMPANY</h5>
        <hr>
        <div class="my-2 text-end">
            <a href="{{ route('admin.company.create') }}" class="btn btn-action btn-warna"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW COMPANY</a>
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
        dataUrl: "{{ route('admin.company.list') }}",
        datatableColumns: [{
                title: 'Name',
                data: 'name'
            },
            {
                title: 'Code',
                data: 'code'
            },
            {
                title: 'Logo',
                data: 'logo',
                orderable: false,
                render: function(data, type, row) {
                    if (typeof(data) === 'string')
                        return `<img src="{{ url('/images/logo') }}/${data}" class="logo-badge">`;
                    else
                        return '&nbsp;';
                }
            },
            {
                title: '',
                data: 'id',
                class: 'text-center list-action',
                orderable: false,
                render: function(data, type, row) {
                    //return `<a class="clickable" data-id="${data}" onclick="deleteItem(this)"><i class="fa-solid fa-trash-can"></i></a>&nbsp;<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                    return `<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                }
            }
        ],
        searchFields: [{
            title: 'Name',
            data: 'name'
        }, , {
            title: 'Code',
            data: 'code'
        }],
        datatableConfig: {}
    };
    var page = new PageDatatableList(config);
    $(() => {
        page.initializeDatatable('#main-table')
            .initializeSearchFields('#search-field-container');
    });
</script>
<script>
    const baseUrl = "{{ route('admin.company.index') }}";

    function editItem(sender) {
        let companyId = $(sender).data('id');
        window.location.href = `${baseUrl}/${companyId}/edit`;
    }
</script>
@endsection