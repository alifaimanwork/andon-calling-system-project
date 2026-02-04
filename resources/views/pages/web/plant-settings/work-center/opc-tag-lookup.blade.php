@section('head')
@parent
<style>
    .opc-tag-lookup-table-container {
        width: 100%;

    }

    #opc-tag-lookup-table {
        height: 400px;
    }
</style>
@endsection
@section('modals')
@parent
<div class="modal fade" id="opc-tag-lookup-modal" tabindex="-1" aria-labelledby="opc-tag-lookup-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-xxl-down modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header align-items-start">
                <div>
                    <h5 class="modal-title h4" id="opc-tag-lookup-modal-label">Select OPC Tag</h5>
                    <div class="d-flex">
                        <span class="opc-tag-lookup-data me-1 fw-bold" data-tag="tag-type-name"></span>:
                        <span class="opc-tag-lookup-data ms-1 fst-italic fs-6" data-tag="tag-subtype-name"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column">
                <div class="search-box">
                    <div class="search-header p-1 px-2 collapsed" data-bs-toggle="collapse" href="#search-main-table" role="button" aria-expanded="false" aria-controls="search-main-table">
                        FILTERS &nbsp;<i class="fas fa-chevron-up chevron"></i>
                    </div>
                    <div id="search-main-table" class="collapse">
                        <div class="search-container">
                            <div id="opc-tag-lookup-search-container" class="row">
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary search-submit">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="opc-tag-lookup-table-container flex-fill">
                    <table id="opc-tag-lookup-table" class="table table-striped w-100">
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    var opcTagLookupTableDialog = {
        datatableList: null,
        selected: null,
        selectTag: function(e) {
            opcTagLookupTableDialog.selected = $(e).data('row-data');
        },
        config: {
            _token: "{{ csrf_token() }}",
            dataUrl: "{{ route('settings.work-center.opc-tags.list',[$plant->uid]) }}",
            datatableColumns: [{
                    title: '',
                    data: 'tag',
                    orderable: false,
                    class: 'active-opc-tag-radio-cell text-center',
                    render: function() {
                        return "";
                    }

                },
                {
                    title: 'Server Name',
                    data: 'opc_server_name'
                },
                {
                    title: 'Tag',
                    data: 'tag'
                },
                {
                    title: 'Data Type',
                    data: 'data_type'
                },
                {
                    title: 'State',
                    data: 'state',
                    class: 'text-center',
                    render: function(data, type, row) {
                        switch (data) {
                            case 1:
                                return '<span class="badge rounded-pill bg-success">OK</span>';
                            case -1:
                                return '<span class="badge rounded-pill bg-danger">ERROR</span>';
                            case -2:
                                return '<span class="badge rounded-pill bg-danger">MISSING</span>';
                            default:
                                return '<span class="badge rounded-pill bg-secondary">UNKNOWN</span>';
                        }
                    }
                },
                {
                    title: 'Assigned Work Center',
                    data: '_assigned',
                    class: 'text-center',
                    render: function(data, type, row) {
                        if (data == null)
                            return "<i>Unassigned</i>"
                        else
                            return data;
                    }
                },
                {
                    title: 'Assigned Type',
                    data: '_assigned_type',
                    class: 'text-center',
                    render: function(data, type, row) {
                        if (data == null)
                            return "<i>Unassigned</i>"
                        else
                            return data;
                    }
                }
            ],
            searchFields: [{
                    title: 'Tag',
                    data: 'tag'
                }, , {
                    title: 'Data Type',
                    data: 'data_type'
                },
                {
                    title: 'State',
                    data: 'state',
                    type: 'select',
                    options: [{
                            value: 1,
                            text: 'OK'
                        },
                        {
                            value: -1,
                            text: 'ERROR'
                        },
                        {
                            value: -2,
                            text: 'MISSING'
                        }
                    ]
                },
            ],
            datatableConfig: {
                rowCallback: function(row, data) {
                    console.log(row);
                    // $(row).find('td').first().addClass('active-opc-tag-radio-cell');
                    let radioElement = null;
                    radioElement = $(`<input type="radio" name="active-opc-tag" class="form-check-input clickable active-opc-tag-radio" onclick="opcTagLookupTableDialog.selectTag(this)">`);
                    radioElement.data('row-data', data);

                    if (opcTagLookupTableDialog.selected && opcTagLookupTableDialog.selected.tag == data.tag && opcTagLookupTableDialog.selected.opc_server_id == data.opc_server_id)
                        radioElement.prop('checked', true);

                    $(row).children('td').first().html(radioElement);
                },
                serverSide: false,
                scrollX: true,
                searching: true,
                order: [
                    [2, 'asc']
                ]
            }
        },

        initialize: function() {
            this.datatableList = new PageDatatableList(this.config);
            this.datatableList.initializeDatatable('#opc-tag-lookup-table')
                .initializeSearchFields('#opc-tag-lookup-search-container');
        },
        show: function(typeName, subTypeName) {
            let modalData = {
                'tag-type-name': typeName,
                'tag-subtype-name': subTypeName,
            };
            $('#opc-tag-lookup-modal').find('.opc-tag-lookup-data').each((idx, e) => {
                $(e).html(modalData[$(e).data('tag')]);
            });
            $('#opc-tag-lookup-modal').modal('show');

            if (!this.datatableList)
                this.initialize();

            this.datatableList.dataTableObject.columns.adjust();


        }

    }
</script>
@endsection