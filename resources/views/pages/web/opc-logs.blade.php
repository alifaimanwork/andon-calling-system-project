@extends('layouts.app')
@include('templates.search-field-text')
@section('body')
<main>
    <div class="container">
        <div class="card">
            <div class="card-header">
                OPC Logs
            </div>
            <div class="card-body">
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
                <table id="tag-log-table" class="table table-striped w-100">
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
@parent
<script>
    $(function() {
        opcLogDataTable.initializeDatatable('#tag-log-table')
            .initializeSearchFields('#search-field-container');
    });

    var config = {
        _token: "{{ csrf_token() }}",
        dataUrl: "{{ route('opc-log-datatable') }}",
        datatableColumns: [{
                title: 'ID',
                data: 'id'
            }, {
                title: 'Server Name',
                data: 'server_name'
            },
            {
                title: 'Tag',
                data: 'tag'
            },
            {
                title: 'Value',
                data: 'value',
            },
            {
                title: 'From',
                data: '_from',
                visible: false,
            },
            {
                title: 'To',
                data: '_to',
                visible: false,
            },
            {
                title: 'Time',
                data: 'created_at',
                // render: function(data, type, row, meta) {
                //     return moment.utc(data).tz(plant.time_zone).format(
                //         'YYYY-MM-DD HH:mm:ss'); //convert to local time
                // }
            }
        ],
        searchFields: [{
                title: 'Tag',
                data: 'tag'
            },
            {
                title: 'From',
                data: '_from',
            },
            {
                title: 'To',
                data: '_to',
            },
            {
                title: 'Value',
                data: 'value',
            },
        ],
        datatableConfig: {}
    };
    //-- OPC Log Datatable --//
    var opcLogDataTable = new PageDatatableList(config);
</script>

@endsection