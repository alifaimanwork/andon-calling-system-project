@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'opc-server'])
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

        <h5 class="secondary-text mt-4">ADD NEW OPC SERVER</h5>
        <hr>
        <form method="post" action="{{ route('admin.opc-server.store') }}">
            @csrf
            <div class="form-grid mb-4">
                <!-- Data Form -->
                <div class="card data-form-cell">
                    <div class="card-body">
                        <div class="my-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Opc Server Name" value="{{ old('name','') }}">
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="hostname" class="form-label">Hostname<span class="text-danger">*</span></label>
                            <input type="text" name="hostname" class="form-control @error('hostname') is-invalid @enderror" id="hostname" placeholder="127.0.0.1" value="{{ old('hostname','') }}">
                            @error('hostname')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="port" class="form-label">Port<span class="text-danger">*</span></label>
                            <input type="number" min="1" max="65535" step="1" name="port" class="form-control @error('port') is-invalid @enderror" id="port" placeholder="52240" value="{{ old('port','') }}">
                            @error('port')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="adapter_hostname" class="form-label">Adapter Hostname<span class="text-danger">*</span></label>
                            <input type="text" name="adapter_hostname" class="form-control @error('adapter_hostname') is-invalid @enderror" id="adapter_hostname" placeholder="127.0.0.1" value="{{ old('adapter_hostname','127.0.0.1') }}">
                            @error('adapter_hostname')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="adapter_port" class="form-label">Adapter Port<span class="text-danger">*</span></label>
                            <input type="number" min="1" max="65535" step="1" name="adapter_port" class="form-control @error('adapter_port') is-invalid @enderror" id="adapter_port" placeholder="8000" value="{{ old('adapter_port','8000') }}">
                            @error('adapter_port')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="configuration_data" class="form-label">Configuration Data</label>
                            <textarea name="configuration_data" class="form-control @error('configuration_data') is-invalid @enderror" id="configuration_data" placeholder="Configuration Data">{{ old('configuration_data','') }}</textarea>
                            @error('configuration_data')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="submit-button-cell text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

    </div>


</main>
@endsection

@section('modals')
@parent
@endsection
@section('scripts')
@parent
<script>
    $(() => {
        page.initialize();
    });

    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }
</script>
@endsection