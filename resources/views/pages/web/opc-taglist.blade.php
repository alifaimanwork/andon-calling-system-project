@extends('layouts.app')

@section('body')
<main>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Active OPC Tags
            </div>
            <div class="card-body">

                <table id="tag-table" class="table table-striped w-100">
                    <thead>
                        <th class="text-nowrap">
                            ID
                        </th>
                        <th class="text-nowrap">
                            OPC Server ID
                        </th>
                        <th class="text-nowrap">
                            Host Name
                        </th>
                        <th class="text-nowrap">
                            Port
                        </th>
                        <th style="width:100%">
                            Tag
                        </th>
                        <th class="text-nowrap">
                            Value
                        </th>
                        <th class="text-nowrap">
                            Updated At
                        </th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
@parent
<script src="{{ asset(mix('js/ws.js')) }}"></script>
<script>
    $(() => {
        window.Echo.connector.pusher.connection.bind('connecting', (payload) => {

            /**
             * All dependencies have been loaded and Channels is trying to connect.
             * The connection will also enter this state when it is trying to reconnect after a connection failure.
             */

            console.log('connecting...');

        });

        window.Echo.connector.pusher.connection.bind('connected', (payload) => {

            /**
             * The connection to Channels is open and authenticated with your app.
             */

            console.log('connected!', payload);
        });

        window.Echo.connector.pusher.connection.bind('unavailable', (payload) => {

            /**
             *  The connection is temporarily unavailable. In most cases this means that there is no internet connection.
             *  It could also mean that Channels is down, or some intermediary is blocking the connection. In this state,
             *  pusher-js will automatically retry the connection every 15 seconds.
             */

            console.log('unavailable', payload);
        });

        window.Echo.connector.pusher.connection.bind('failed', (payload) => {

            /**
             * Channels is not supported by the browser.
             * This implies that WebSockets are not natively available and an HTTP-based transport could not be found.
             */

            console.log('failed', payload);

        });

        window.Echo.connector.pusher.connection.bind('disconnected', (payload) => {

            /**
             * The Channels connection was previously connected and has now intentionally been closed
             */

            console.log('disconnected', payload);

        });

        window.Echo.connector.pusher.connection.bind('message', (payload) => {

            /**
             * Ping received from server
             */

            console.log('message', payload);
        });

        //load WS
        Echo.channel('opc-data').listen('OpcTagValueChangedEvent', function(e) {
            if (typeof(e.data) != 'undefined' && Array.isArray(e.data) && e.data.length > 0)
                page.updateActiveTags(e.data);
        });
    });
</script>
<script>
    $(function() {
        page.initialize().reloadActiveTag();
        // setInterval(function() {
        //     page.reloadActiveTag();
        // }, 1000);
    });

    var page = {
        activeTags: {},
        datatable: null,
        initialize: function() {
            this.initializeDataTable();
            return this;
        },
        initializeDataTable: function() {
            this.datatable = $('#tag-table').DataTable();
            return this;
        },
        reloadActiveTag: function() {
            let owner = this
            $.ajax({
                url: "{{ route('api.opc.get_active_tag') }}",
                method: 'get',
                // data: formData,
                processData: false,
                contentType: false,
            }).done(function(response) {
                owner.updateActiveTags(response.data);
            }).fail(function(xhr, status, e) {

            });

            return this;
        },
        updateActiveTags: function(data) {
            var owner = this;
            var addNew = false;

            data.forEach(e => {
                if (typeof(owner.activeTags[e.id]) === 'undefined') {
                    addNew = true;
                    owner.addTag(e);
                } else
                    owner.updateTag(e);
            });
            //TODO: remove deleted tags

            if (addNew)
                this.datatable.draw();

        },
        updateTag: function(data) {
            let row = this.activeTags[data['id']];
            $(row.node()).children('td').each(function(index, elem) {
                let e = $(elem);
                col = e.data('tag');
                if (typeof(data[col]) === 'undefined') {
                    if (e.html !== '')
                        e.html('');
                } else if (e.html() != data[col])
                    e.html(data[col]);
            });
        },
        addTag: function(data) {
            let columns = ['id', 'opc_server_id', 'hostname', 'port', 'tag', 'value', 'value_updated_at'];
            let rowData = [];
            columns.forEach(e => {
                if (typeof(data[e]) === 'undefined')
                    rowData.push('');
                else
                    rowData.push(data[e]);
            });


            let row = this.datatable.row.add(rowData);
            row.draw();
            for (let i = 0; i < columns.length; i++) {
                const e = columns[i];
                let col = $(row.node()).find('td').eq(i);
                col.data('tag', e);
            }
            this.activeTags[data['id']] = row;

        }
    }
</script>

@endsection