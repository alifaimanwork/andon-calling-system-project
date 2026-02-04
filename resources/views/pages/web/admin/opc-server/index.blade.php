@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'opc-server'])
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
        <h5 class="secondary-text mt-4">OPC Server</h5>
        <hr>
        <div class="my-2 text-end">
            <a href="{{ route('admin.opc-server.create') }}" class="btn btn-action btn-warna"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW OPC SERVER</a>
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
<div id="confirm-resync-modal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Force Resync</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Confirm force resync tags from target opc server?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button id="confirm-resync-button" type="button" class="btn btn-danger" onclick="confirmResync(this)">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    var config = {
        _token: "{{ csrf_token() }}",
        dataUrl: "{{ route('admin.opc-server.list') }}",
        datatableColumns: [{
                title: 'Name',
                data: 'name'
            },
            {
                title: 'Hostname',
                data: 'hostname'
            },
            {
                title: 'Port',
                data: 'port'
            },
            {
                title: '',
                data: 'id',
                class: 'text-center list-action',
                orderable: false,
                render: function(data, type, row) {
                    //return `<a class="clickable" data-id="${data}" onclick="deleteItem(this)"><i class="fa-solid fa-trash-can"></i></a>&nbsp;<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>`;
                    return `<a class="clickable" data-id="${data}" onclick="editItem(this)"><i class="fa-solid fa-pen-to-square"></i></a>&nbsp;` +
                        `<a class="clickable" data-id="${data}" onclick="forceResync(this)"><i class="fa-solid fa-arrows-rotate"></i></i></a>`;
                }
            }

        ],
        searchFields: [{
                title: 'Name',
                data: 'name'
            },
            {
                title: 'Hostname',
                data: 'hostname'
            },
            {
                title: 'Port',
                data: 'port'
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
    const baseUrl = "{{ route('admin.opc-server.index') }}";

    function editItem(sender) {
        let opcServerId = $(sender).data('id');

        const baseUrl = "{{ route('admin.opc-server.edit','_SERVER_ID_') }}";
        let url = baseUrl.replace('_SERVER_ID_', opcServerId);

        window.location.href = url;
    }

    var resyncBusy = false;

    function confirmResync(sender) {
        if (resyncBusy)
            return;
        resyncBusy = true;
        let opcServerId = $(sender).data('id');

        const baseUrl = "{{ route('admin.opc-server.force-sync','_SERVER_ID_') }}";
        let url = baseUrl.replace('_SERVER_ID_', opcServerId);

        $.post(url, {
            _token: "{{ csrf_token() }}"
        }, function(data, status, xhr) {
            //result code
            const RESULT_OK = 0;
            const RESULT_INVALID_STATUS = -1;
            const RESULT_INVALID_PARAMETERS = -2;

            if (data.result === RESULT_OK) {
                //stopped,try refresh page
            } else {
                //
            }
        }).always(
            e => {
                $('#confirm-resync-modal').modal('hide');
                resyncBusy = false;
            }
        );


    }

    function forceResync(sender) {
        let opcServerId = $(sender).data('id');
        $('#confirm-resync-button').data('id', opcServerId);
        $('#confirm-resync-modal').modal('show');
    }
</script>
@endsection