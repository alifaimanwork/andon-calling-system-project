@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'reject-type'])
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
        <div class="container">
            @yield('mobile-title')
            {{-- @include('components.web.change-plant-selector') --}}
            @yield('tab-nav-bar')
            <h5 class="secondary-text mt-3" style="text-transform: uppercase">ADD NEW {{ $group->name }} REJECT TYPE</h5>
            <hr>
            <form method="post" action="{{ route('settings.reject-type.store',[ $plant->uid ]) }}">
                @csrf
                <div class="mt-3">
                    <!-- Data Form -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="group" class="form-label">GROUP NAME <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="group" id="group" value="{{ $group->name }}" readonly>

                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="type" class="form-label">REJECT TYPE <span class="text-danger">*</span></label>
                                    <input type="text" name="type" id="type" class="form-control" placeholder="REJECT TYPE NAME" required>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- enabled active, inactive --}}
                                <div class="col-12 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span class="text-danger">*</span></label>
                                    <select name="enabled" id="enabled" class="form-select" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('enabled')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-end m-3 mt-0">
                            <a href="{{ route('settings.reject-type.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
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

    });


    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }
</script>
@endsection