@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'downtime'])
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
            @yield('mobile-title')
            @include('components.web.change-plant-selector')
            @yield('tab-nav-bar')
            <h5 class="secondary-text mt-4">CREATE DOWNTIME</h5>
            <hr>
            <form method="post" action="{{ route('settings.downtime.store',[ $plant->uid ]) }}">
                @csrf
                <div class="mb-4">
                    <!-- Data Form -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="type" class="form-label">DOWNTIME TYPE <span class="text-danger">*</span></label>
                                    <select name="type" id="downtime_type_id" class="form-select">
                                        <option value="0" selected disabled> SELECT MACHINE TYPE</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="category" class="form-label">DOWNTIME CATEGORY <span class="text-danger">*</span></label>
                                    <input type="text" name="category" id="category" class="form-control" placeholder="PLEASE SPECIFY MACHINE CATEGORY" required>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="submit-button-cell text-end m-3">
                            <a href="{{ route('settings.downtime.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                            <button type="submit" class="btn btn-action btn-warna">CREATE</button>
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
        var page = {
            _token: "{{ csrf_token() }}",

            initialize: function() {
                return this;
            },
        }
        //TODO: Guard variable
        $(() => {
            page.initialize();

        });

    </script>
@endsection