@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'network-status'])
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
        <h5 class="secondary-text mt-4" style="text-transform: uppercase">ADD NEW NETWORK NODE</h5>
        <hr>
        <form method="post" action="{{ route('settings.network-status.store',[ $plant->uid ]) }}">
            @csrf
            <div class="mt-3">
                <!-- Data Form -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12 mt-3">
                                <label for="client_type" class="form-label">CLIENT TYPE <span class="text-danger">*</span></label>
                                <select class="form-control" name="client_type" id="client_type" required onchange="updateFormType(this)">
                                    <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_TERMINAL }}">Terminal</option>
                                    <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_DASHBOARD }}">Dashboard</option>
                                    <option value="{{ \App\Models\MonitorClient::CLIENT_TYPE_NETWORK_NODE }}" selected>Network Node</option>
                                </select>
                                @error('client_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-12 mt-3 js-client-type" data-vistype="2">
                                <label for="target_host" class="form-label">TARGET HOST/IP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="target_host" id="target_host" value="{{ old('target_host') }}" placeholder="127.0.0.1" required>
                                @error('target_host')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-12 mt-3 js-client-type" data-vistype="0">
                                <label for="target_id" class="form-label">TARGET WORK CENTER <span class="text-danger">*</span></label>
                                <select class="form-control" name="target_id" id="target_id" required onchange="generateName(this)">
                                    @foreach($workCenters as $workCenter)
                                    <option value="{{ $workCenter->id }}" {{ old('target_id') == $workCenter->id ? 'selected':'' }}>{{ $workCenter->name }}</option>
                                    @endforeach
                                </select>
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-12 mt-3">
                                <label for="name" class="form-label">NAME <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="Node Name" required>
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="text-end m-3 mt-0">
                        <a href="{{ route('settings.network-status.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
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
        if (clientType == 2 || $('#name').val() == '')
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