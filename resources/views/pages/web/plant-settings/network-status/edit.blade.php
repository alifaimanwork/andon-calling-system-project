@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'network-status'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
        .form-label {
            font-weight: 500;
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
            {{-- @include('components.web.change-plant-selector') --}}
            @yield('mobile-title')
            @yield('tab-nav-bar')
            <div class="row mt-4">
                <h5 class="col-6 secondary-text">EDIT NETWORK NODE</h5>
                <div class="col-6 text-end">
                        <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">DELETE RECORD</button>
                </div>
            </div>
            <hr>
            <form method="post" action="{{ route('settings.network-status.update', [$plant->uid, $monitorClient->id]) }}">
                @method('PUT')
                @csrf
                <div class="col-12 mt-3">
                    <!-- Data Form -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label for="client_type" class="form-label">CLIENT TYPE <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="client_type" id="client_type" required
                                        onchange="updateFormType(this)">
                                        <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_TERMINAL }}"
                                            {{ old('client_type', $monitorClient->client_type) == \App\Models\MonitorClient::CLIENT_TYPE_TERMINAL ? 'selected' : '' }}>
                                            Terminal</option>
                                        <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_DASHBOARD }}"
                                            {{ old('client_type', $monitorClient->client_type) == \App\Models\MonitorClient::CLIENT_TYPE_DASHBOARD ? 'selected' : '' }}>
                                            Dashboard</option>
                                        <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_NETWORK_NODE }}"
                                            {{ old('client_type', $monitorClient->client_type) == \App\Models\MonitorClient::CLIENT_TYPE_NETWORK_NODE ? 'selected' : '' }}>
                                            Network Node</option>
                                    </select>
                                    @error('client_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-12 mt-3 js-client-type" data-vistype="2">
                                    <label for="target_host" class="form-label">TARGET HOST/IP <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="target_host" id="target_host"
                                        value="{{ old('target_host', $monitorClient->client_info->host ?? null) }}"
                                        placeholder="127.0.0.1" required>
                                    @error('target_host')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-12 mt-3 js-client-type" data-vistype="0">
                                    <label for="target_id" class="form-label">TARGET WORK CENTER <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="target_id" id="target_id" required
                                        onchange="generateName(this)">
                                        @foreach ($workCenters as $workCenter)
                                            <option value="{{ $workCenter->id }}"
                                                {{ old('target_id', $monitorClient->target_id) == $workCenter->id ? 'selected' : '' }}>
                                                {{ $workCenter->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-12 mt-3">
                                    <label for="name" class="form-label">NAME <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $monitorClient->name) }}" placeholder="Node Name" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="text-end m-3 mt-0">
                            <a href="{{ route('settings.network-status.index', [$plant->uid]) }}"
                                class="btn btn-secondary">CANCEL</a>
                            <button type="submit" class="btn btn-action btn-warna">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>


    </main>
@endsection
@section('modals')
    @parent
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationLabel">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Delete Network Node?<br>
                    Name: <strong>{{ $monitorClient->name }}</strong><br>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('settings.network-status.destroy', [$plant->uid, $monitorClient->id]) }}"
                        method="POST">
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
        //TODO: Guard variable
        $(() => {
            page.initialize();
            updateFormType($('#client_type'));
        });

        function updateFormType(sender) {
            if ($(sender).val() == 2) {
                //network node
                $('.js-client-type').addClass('d-none');
                $('.js-client-type[data-vistype="2"]').removeClass('d-none');
            } else {
                //terminal / dashboard
                $('.js-client-type').removeClass('d-none');
                $('.js-client-type[data-vistype="2"]').addClass('d-none');
                generateName(sender);
            }
        }

        function generateName(sender) {

            let clientType = $('#client_type').val();
            if (clientType == 2 || $('#name').val() != '')
                return;

            let clientTypeText = clientType == 0 ? 'Terminal' : 'Dashboard';
            let clientName = $('#name').find(':selected').html();
            let newName = `${clientName} [${clientTypeText}]`;
            console.log(newName);
            $('#name').val(newName);
            // if ($('#name').val().length() <= 0)
            //     $('#name').val(`} [${}]`);
        }
        var page = {
            _token: "{{ csrf_token() }}",

            initialize: function() {
                return this;
            },
        }
    </script>
@endsection
