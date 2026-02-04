@extends('layouts.app')

@section('body')
<main>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <table id="activeTagTable" class="table table-striped w-100">

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
    Echo.channel('opc-data').listen('OpcTagValueChangedEvent', function(e) {
        if (typeof(e.data) != 'undefined' && Array.isArray(e.data) && e.data.length > 0)
            page.updateActiveTags(e.data);
    });
</script>
<script>
    var config = {
        _token: "{{ csrf_token() }}",
        dataUrl: "{{ route('opc-tag-test') }}",
        datatableColumns: [{
                title: 'Tag',
                data: 'tag'
            },
            {
                title: 'DataType',
                data: 'data_type'
            },
            {
                title: 'Value',
                data: 'value'
            },
            {
                title: 'Value Updated At',
                data: 'value_updated_at'
            }
        ],
        datatableConfig: {
            processing: false,
            serverSide: false,
            paginate: false,
        }
    };
    var activeTagDataTable = new PageDatatableList(config);
    $(() => {
        activeTagDataTable.initializeDatatable('#activeTagTable');
        //test page, just use ajax
        setInterval(() => {
            activeTagDataTable.dataTableObject.ajax.reload();
        }, 1000);
    });
</script>



@endsection