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
            {{-- @include('components.web.change-plant-selector') --}}
            @yield('mobile-title')
            @yield('tab-nav-bar')
            <div class="row mt-3">
                <h5 class="col-6 secondary-text"style="text-transform: uppercase">EDIT {{ $rejectType->rejectGroup->name }} REJECT TYPE</h5>
                <div class="col-6 submit-button-cell text-end">
                    {{-- form delete button --}}
                    <form action="{{ route('settings.reject-type.destroy',[ $plant->uid , $rejectType->id ]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">DELETE RECORD</button>
                    </form>
                </div>
            </div>
            <hr>
            <form method="post" action="{{ route('settings.reject-type.update',[ $plant->uid , $rejectType->id ]) }}">
                @method('PUT')
                @csrf
                <div class="col-12 mt-3">
                    <!-- Data Form -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="group_id" value="{{ $rejectType->rejectGroup->id }}">
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="group_name" class="form-label">GROUP NAME <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="{{ $rejectType->rejectGroup->name }}" readonly>

                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="type" class="form-label">REJECT TYPE <span class="text-danger">*</span></label>
                                    {{-- <input name="type" list="datalistOptions" id="type" class="form-control" value="{{ $rejectType->name }}" required>
                                    <datalist id="datalistOptions">
                                        @foreach($all_reject_types as $type)
                                            <option value="{{ $type->name }}"></option>
                                        @endforeach
                                    </datalist> --}}
                                    <select name="type" id="" class="form-select" required>
                                        <option selected disabled> SELECT REJECT TYPE</option>
                                        @foreach($all_reject_types as $type)
                                            <option value="{{ $type->name }}" @if($type->name == $rejectType->name) selected @endif>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- enabled active, inactive --}}
                                <div class="col-12 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span class="text-danger">*</span></label>
                                    <select name="enabled" id="enabled" class="form-select" required>
                                        @if ($rejectType->enabled)
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        @else
                                            <option value="1">Active</option>
                                            <option value="0" selected>Inactive</option>
                                        @endif
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