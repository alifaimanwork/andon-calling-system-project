@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar', ['tabActive' => 'opc-server'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
        /* TODO: Responsive grid layout */
        .form-grid {
            display: grid;
            grid-template-columns: auto;
            grid-template-rows: auto auto;
            row-gap: 1em;
            column-gap: 1em;
        }

        .data-form-cell {
            grid-column-start: 1;
            grid-column-end: 2;
            grid-row-start: 1;
            grid-row-end: 2;
        }

        .submit-button-cell {
            grid-column-start: 1;
            grid-column-end: 2;
            grid-row-start: 2;
            grid-row-end: 3;
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
    </style>
@endsection

@section('body')
    <main>
        <div class="container">
            @yield('mobile-title')
            @yield('tab-nav-bar')

            <h5 class="secondary-text mt-4">EDIT OPC SERVER</h5>
            <hr>
            <form method="post" action="{{ route('admin.opc-server.update', [$opcServer->id]) }}">
                @method('put')
                @csrf
                <div class="form-grid mb-4">
                    <!-- Data Form -->
                    <div class="card data-form-cell">
                        <div class="card-body">
                            <div class="my-3">
                                <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    placeholder="Opc Server Name" value="{{ old('name', $opcServer->name) }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="hostname" class="form-label">Hostname<span class="text-danger">*</span></label>
                                <input type="text" name="hostname"
                                    class="form-control @error('hostname') is-invalid @enderror" id="hostname"
                                    placeholder="127.0.0.1" value="{{ old('hostname', $opcServer->hostname) }}">
                                @error('hostname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="port" class="form-label">Port<span class="text-danger">*</span></label>
                                <input type="number" min="1" max="65535" step="1" name="port"
                                    class="form-control @error('port') is-invalid @enderror" id="port"
                                    placeholder="52240" value="{{ old('port', $opcServer->port) }}">
                                @error('port')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="adapter_hostname" class="form-label">Adapter Hostname<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="adapter_hostname"
                                    class="form-control @error('adapter_hostname') is-invalid @enderror"
                                    id="adapter_hostname" placeholder="127.0.0.1"
                                    value="{{ old('adapter_hostname', $opcServer->adapter_hostname) }}">
                                @error('adapter_hostname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="adapter_port" class="form-label">Adapter Port<span
                                        class="text-danger">*</span></label>
                                <input type="number" min="1" max="65535" step="1" name="adapter_port"
                                    class="form-control @error('adapter_port') is-invalid @enderror" id="adapter_port"
                                    placeholder="8000" value="{{ old('adapter_port', $opcServer->adapter_port) }}">
                                @error('adapter_port')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="configuration_data" class="form-label">Configuration Data</label>
                                <textarea name="configuration_data" class="form-control @error('configuration_data') is-invalid @enderror"
                                    id="configuration_data" placeholder="Configuration Data">{{ old('configuration_data', $opcServer->configuration_data) }}</textarea>
                                @error('configuration_data')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="submit-button-cell text-end">
                        <a href="{{ route('admin.opc-server.index') }}" class="btn btn-secondary">Cancel</a>
                        <a class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal">Delete</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div class="mb-2 d-flex align-items-end">
                        <h5 class="secondary-text mt-4">OPC Tags</h5>
                        <div class="text-end flex-fill">
                            <button class="btn btn-danger btn-sm" onclick="syncTagsFromOpcServer()">Sync Tags From OPC
                                Server</button>
                        </div>

                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="d-flex p-2">
                                <div class="ms-2">
                                    <i><span class="selected-count">0</span> selected</i>
                                </div>
                                <div class="ms-2">
                                    <button class="btn btn-secondary btn-sm" onclick="clearSelection()">Clear
                                        Selection</button>
                                </div>

                                <div class="d-flex ms-2">
                                    <select id="assign-plant-select" class="form-select form-select-sm"
                                        style="width: 240px;">
                                        <option value="0" selected>Unassigned</option>
                                        @foreach ($plants as $plant)
                                            <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary btn-sm ms-2" onclick="assignToPlant()">Assign Selected
                                        To Plant</button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="search-box">
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
                    <table id="main-table" class="table table-striped w-100 mt-2"></table>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('modals')
    @parent
    <div id="warning-modal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationLabel">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Delete Opc Server?<br>
                    Name: <strong>{{ $opcServer->name }}</strong><br>
                    Code: <strong>{{ $opcServer->code }}</strong><br>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" action="{{ route('admin.opc-server.destroy', [$opcServer->id]) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var selectActiveOpcTagIds = [];

        function syncTagsFromOpcServer() {
            if (!confirm("Fetch all tags from OPC Server?"))
                return;

            $.post("{{ route('admin.opc-server.sync', [$opcServer->id]) }}", {
                _token: window.csrf.getToken()
            }, function(data, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;
                const RESULT_RESTRICTED = -3;

                //TODO: display error message in modal
                if (data.result === RESULT_OK) {
                    showWarningDialog("Result",
                        "<strong>Found Tags: </strong>" + data.data.total.toString() + "<br>" +
                        "<strong>New Tags: </strong>" + data.data.new.toString() + "<br>" +
                        "<strong>Updated Tags: </strong>" + data.data.updated.toString() + "<br>" +
                        "<strong>Missing Tags: </strong>" + data.data.missing.toString());
                } else {
                    showWarningDialog("Error", data.message.replaceAll("\r\n", '<br>'));
                }
            });

        }

        function assignToPlant() {

            console.log('assignToPlant');

            // console.log(selectActiveOpcTagIds);
            if (selectActiveOpcTagIds.length <= 0) {
                alert('No tag selected');
                return;
            }

            let plant_id = $('#assign-plant-select').val();

            if (plant_id <= 0) {
                if (!confirm("Unassign selected tags from assigned plant?"))
                    return;
            } else {
                if (!confirm("Assign selected tags to selected plant?"))
                    return;
            }

            //Ajax requests
            $.post("{{ route('admin.opc-server.assign-tags', [$opcServer->id]) }}", {
                _token: window.csrf.getToken(),
                tags: selectActiveOpcTagIds,
                plant_id: plant_id
            }, function(e) {
                page.dataTableObject.draw();
            });
        }

        function showWarningDialog(title, text) {
            let modal = $('#warning-modal');
            modal.find('.modal-body p').html(text);
            modal.find('.modal-title').html(title);
            modal.modal('show');
        }

        function addRemoveTag(sender) {
            let opcTagId = parseInt($(sender).data('id'));

            if ($(sender).is(':checked')) {
                if (selectActiveOpcTagIds.indexOf(opcTagId) == -1) {
                    selectActiveOpcTagIds.push(opcTagId);
                }
            } else {
                if (selectActiveOpcTagIds.indexOf(opcTagId) >= 0) {
                    selectActiveOpcTagIds.splice(selectActiveOpcTagIds.indexOf(opcTagId), 1);
                }
            }
            updateSelectionCount();
        };

        function clearSelection() {
            selectActiveOpcTagIds.length = 0;
            $('.active-opc-tag-checkbox').prop("checked", false);
            $('#assign-plant-select').val(0);
            updateSelectionCount();
        }

        function updateSelectionCount() {
            $('.selected-count').html(selectActiveOpcTagIds.length);

        }

        var config = {
            _token: "{{ csrf_token() }}",
            dataUrl: "{{ route('admin.opc-active-tag.list') }}",
            datatableColumns: [{
                    title: '',
                    data: 'id',
                    orderable: false,
                    class: 'text-center',
                    render: function(data, type, row) {
                        let opcTagId = parseInt(data);
                        if (selectActiveOpcTagIds.indexOf(opcTagId) < 0)
                            return `<input type="checkbox" data-id="${data}" class="form-check-input clickable active-opc-tag-checkbox" onclick="addRemoveTag(this)">`;
                        else
                            return `<input type="checkbox" data-id="${data}" class="form-check-input clickable active-opc-tag-checkbox" onclick="addRemoveTag(this)" checked>`;
                    }
                },
                {
                    title: 'Tag',
                    data: 'tag',
                    class: 'font-mono'
                },
                {
                    title: 'Data Type',
                    data: 'data_type'
                },
                {
                    title: 'Assigned Plant',
                    data: 'plant_name',
                    render: function(data, type, row) {
                        if (data == null)
                            return `<i>Unassigned</i>`
                        return data;
                    }
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
            ],
            searchFields: [{
                    title: 'Tag',
                    data: 'tag'
                },
                {
                    title: 'Data Type',
                    data: 'data_type'
                },
                {
                    title: 'Plant Name',
                    data: 'plant_name'
                },
                {
                    title: 'Assigned Plant',
                    data: '_assigned',
                    type: 'select',
                    options: [{
                            value: '*',
                            text: 'All'
                        },
                        {
                            value: 'unassigned',
                            text: 'Unassigned'
                        },
                        {
                            value: 'assigned',
                            text: 'Assigned'
                        }
                    ],
                }
            ],
            datatableConfig: {
                order: [
                    [1, "asc"]
                ],
            }
        };
        var page = new PageDatatableList(config);
        $(() => {
            page.initializeDatatable('#main-table')
                .initializeSearchFields('#search-field-container');
        });
    </script>
@endsection
