@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'user'])
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
            @include('components.web.change-plant-selector')
            @yield('tab-nav-bar')
            
            <h5 class="secondary-text mt-3">ADD NEW USER</h5>
            <hr>
            <form method="post" action="{{ route('settings.user.store', [$plant->uid]) }}">
                @csrf
                <div class="row px-3">
                    <!-- Data Form -->

                    {{-- user info --}}
                    <div class="col-12 col-md-4 mt-3 px-3">
                        <div class="card p-3 h-100">
                            <div class="card-body p-0">
                                <div class="row">

                                    <div class="mb-3">
                                        <label for="wc_plant_id" class="form-label">Plant</label>
                                        <select id="wc_plant_id" name="wc_plant_id"
                                            class="form-select @error('wc_plant_id') is-invalid @enderror" required>
                                            @foreach($admin_plants as $admin_plant)
                                                <option value="{{ $admin_plant->id }}" {{ $plant->uid == $admin_plant->uid ? 'selected' : '' }}>
                                                    {{ $admin_plant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('wc_plant_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
        
                                    {{-- company -> name xde --}}
                                    <div class="col-12 mb-3">
                                        <label for="company" class="form-label">Company</label>
                                        <input type="hidden" name="company" value="{{ $plant->company->id }}">
                                        <input type="text" class="form-control" id="company"
                                            value="{{ $plant->company->name ?? '' }}" disabled>
                                        @error('company')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    {{-- user enabled or disabled --}}
                                    <div class="col-12 mb-3">
                                        <label for="enabled" class="form-label">USER ENABLED</label>
                                        <select name="enabled" id="enabled" class="form-control" disabled>
                                            <option value="1" selected>YES</option>
                                            <option value="0" >NO</option>
                                        </select>
                                        @error('enabled')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
        
                                    <hr>
                                    
                                    {{-- staft no --}}
                                    <div class="col-12 mt-1">
                                        <label for="staff_no" class="form-label">STAFF NO <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="staff_no" class="form-control" id="staff_no"
                                            placeholder="STAFF NO" value="{{ old('staff_no') }}" required>
                                        @error('staff_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- full name --}}
                                    <div class="col-12 mt-3">
                                        <label for="full_name" class="form-label">FULL NAME <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" id="full_name"
                                            placeholder="FULL NAME" value="{{ old('full_name') }}" required>
                                        @error('full_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- username -> wrong name --}}
                                    <div class="col-12 mt-3">
                                        <label for="sap_id" class="form-label">USER NAME <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="sap_id" class="form-control" id="sap_id"
                                            placeholder="USER NAME (FOR SAP)" value="{{ old('sap_id') }}" required>
                                        @error('sap_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- email -> wrong name --}}
                                    <div class="col-12 mt-3">
                                        <label for="sap_id" class="form-label">EMAIL ADDRESS <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="address@email.com" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- password --}}
                                    <div class="col-12 mt-3">
                                        <label for="password" class="form-label">PASSWORD <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="PASSWORD" value="{{ old('password') }}" required>
                                        @error('password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- confirm password --}}
                                    <div class="col-12 mt-3">
                                        <label for="password_confirmation" class="form-label">CONFIRM PASSWORD <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation" placeholder="CONFIRM PASSWORD" required>
                                        @error('password_confirmation')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-8 px-3">
                        <div class="row">
                            {{-- platform/plant access --}}
                            <div class="col-12 mt-3">
                                <div class="card p-3">
                                    <div class="card-header">
                                        PLATFORM ACCESS PERMISSION <span class="text-danger">*</span>
                                    </div>
                                    <div class="card-body">
                                        <label>I-POS:</label>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" name="access-ipos[]" type="checkbox" value="1"
                                                id="access-web">
                                            <label class="form-check-label" for="access-web">
                                                WEB
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" name="access-ipos[]" type="checkbox"
                                                value="2" id="access-terminal">
                                            <label class="form-check-label" for="access-terminal">
                                                TERMINAL
                                            </label>
                                        </div>
                                    </div>
                                    @error('access-ipos')
                                        <div class="text-danger">{{ $message }}</div>                                        
                                    @enderror
                                </div>
                            </div>

                            {{-- company access permission --}}
                            <div class="col-12 mt-3" id="wc_selection_div" style="display: none;">
                                <div class="card p-3 ">
                                    <div class="card-header">
                                        WORK CENTER PERMISSION <span class="text-danger">*</span>
                                    </div>
                                    {{-- table with 3 column and 2 checkbox column --}}
                                    <div class="card-body">
                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">WORK CENTER</th>
                                                        <th class="text-center" style="min-width: 40px">OPERATION</th>
                                                        <th class="text-center" style="min-width: 40px">REWORK</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($workCenters as $workcenter)
                                                        <tr>
                                                            <td>{{ $workcenter->name }}</td>
                                                            <td class="text-center">
                                                                <input class="form-check-input" name="wc-op[]" type="checkbox" value="{{ $workcenter->id }}">
                                                            </td>
                                                            <td class="text-center">
                                                                <input class="form-check-input" name="wc-rework[]" type="checkbox" value="{{ $workcenter->id }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- cancel/create --}}
                            <div class="col-12">
                                <div class="text-end mt-3">
                                    <div class="d-none hidden-inputs"></div>
                                    <button type="submit" class="btn btn-action btn-warna">CREATE</button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('modals')
    @parent
    <!-- Upload Temp Profile Picture -->
    <div class="modal fade" id="manage-profile-picture" tabindex="-1" aria-labelledby="manage-profile-picture-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manage-profile-picture-label">Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    TODO: Set temporary profile picture for new user
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
        $(() => {
            page.initialize();
        });

        var page = {
            _token: "{{ csrf_token() }}",

            initialize: function() {
                return this;
            },
        }

        $(document).ready(function() {
            if ($('#access-terminal').is(':checked')) {
                $('#wc_selection_div').show();
            } else {
                $('#wc_selection_div').hide();
                $('#wc_selection_div').find('input').prop('checked', false);
            }
        });

        $('#access-terminal').change(function() {
            if ($(this).is(':checked')) {
                $('#wc_selection_div').show();
            } else {
                $('#wc_selection_div').hide();
                $('#wc_selection_div').find('input').prop('checked', false);
            }
        });
    </script>
@endsection
