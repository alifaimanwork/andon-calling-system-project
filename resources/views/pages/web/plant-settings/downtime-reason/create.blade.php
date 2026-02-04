@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'downtime'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
        /* TODO: Responsive grid layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr auto auto;
            grid-template-rows: auto 1fr auto;
            row-gap: 1em;
            column-gap: 1em;
        }

        .data-form-cell {
            grid-column-start: 1;
            grid-column-end: 2;
            grid-row-start: 1;
            grid-row-end: 3;
        }

        .profile-picture-cell {
            grid-column-start: 2;
            grid-column-end: 4;
            grid-row-start: 1;
            grid-row-end: 2;
        }

        .platform-permission-cell {
            grid-column-start: 2;
            grid-column-end: 3;
            grid-row-start: 2;
            grid-row-end: 3;
        }

        .company-access-permission-cell {
            grid-column-start: 3;
            grid-column-end: 4;
            grid-row-start: 2;
            grid-row-end: 3;

        }

        .submit-button-cell {
            grid-column-start: 1;
            grid-column-end: 4;
            grid-row-start: 3;
            grid-row-end: 4;
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

        .form-label {
            font-weight: 500;
        }

        .form-check-input:checked {
            background-color: #710000;
            border-color: #710000;
        }

        input::-webkit-calendar-picker-indicator {
            display: none;
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
            <h5 class="secondary-text mt-3">ADD NEW REASON</h5>
            <hr>
            <form method="post" action="{{ route('settings.downtime-reason.store', [$plant->uid, $downtime->id]) }}">
                @csrf
                <div class="mt-3">
                    <!-- Data Form -->
                    <div class="card data-form-cell">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <label for="reason" class="form-label">DOWNTIME CATEGORY <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="category" id="category" class="form-control"
                                        value="{{ $downtime->category }}" readonly>
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="reason" class="form-label">NEW DOWNTIME REASON <span
                                            class="text-danger">*</span></label>
                                            <input name="reason" value="" class="form-control">
                                    
                                            {{-- <input list="reason" name="reason" value="" class="form-control">
                                    <datalist id="reason" name="reason">
                                        @foreach ($reasons as $reason)
                                            <option value="{{ $reason->reason }}"> {{ $reason->reason }} </option>
                                        @endforeach
                                    </datalist> --}}
                                    
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- checkbox for allow or not allow user input --}}
                                <div class="col-12 mt-3">
                                    <label for="user_input" class="form-label">USER INPUT <span
                                            class="text-danger">*</span></label>
                                    <div class="m-2 my-0 d-flex gap-3">
                                        <div class="form-check">
                                            <input type="radio" name="user_input" id="user_input" value="1"
                                                class="form-check-input">
                                            <label for="user_input" class="form-check-label">Allow</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="user_input" id="user_input" value="0"
                                                class="form-check-input" checked>
                                            <label for="user_input" class="form-check-label">Disallow</label>
                                        </div>
                                    </div>

                                    @error('user_input')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- checkbox for enabled active or inactive --}}
                                <div class="col-12 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span
                                            class="text-danger">*</span></label>
                                    <div class="m-2 my-0 d-flex gap-3">
                                        <div class="form-check">
                                            <input type="radio" name="enabled" id="enabled-active" value="1"
                                                class="form-check-input" checked>
                                            <label for="enabled-active" class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="enabled" id="enabled-inactive" value="0"
                                                class="form-check-input">
                                            <label for="enabled-inactive" class="form-check-label">Inactive</label>
                                        </div>
                                    </div>
                                    @error('enabled')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="submit-button-cell text-end m-3 mt-0">
                            <a href="{{ route('settings.downtime.edit', [$plant->uid, $downtime->id]) }}"
                                class="btn btn-secondary">BACK</a>
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
