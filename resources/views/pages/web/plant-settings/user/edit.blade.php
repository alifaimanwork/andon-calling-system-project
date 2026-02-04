@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar', ['tabActive' => 'user'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@include('components.web.file-upload')
@section('head')
    @parent
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            row-gap: 1em;
            column-gap: 1em;
        }

        @media(min-width:768px) {

            .form-grid {
                grid-template-columns: 1fr auto auto;
                grid-template-rows: auto 1fr auto;
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
        }

        .profile-pic-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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
    </style>
@endsection

@section('body')
    <main>
        <div class="container">
            {{-- @include('components.web.change-plant-selector') --}}
            @yield('tab-nav-bar')

            <h5 class="secondary-text mt-3">EDIT USER</h5>
            <hr>

            <form method="post" action="{{ route('settings.user.update', [$plant->uid, $user_data->id]) }}">
                @csrf
                @method('PUT')
                <div class="form-grid px-3">
                    <!-- Data Form -->
                    {{-- profile picture --}}
                    <div class="profile-picture-cell">
                        <div class="row">
                            <div class="col-12">
                                <div class="card p-3">
                                    <div class="profile-pic-container mt-3">
                                        <div class="profile-pic d-flex">
                                            <img id="profile-picture-preview"
                                                src="{{ $user_data->getProfilePictureUrl() }}">
                                        </div>


                                        <div id="profile-photo-delete"
                                            class="d-flex flex-wrap mt-3 {{ !$user_data->profile_picture ? 'd-none' : '' }}">
                                            <div class="mt-2">
                                                <button class="btn btn-danger" type="button"
                                                    onclick="deleteProfilePicture()">Delete</button>
                                            </div>
                                        </div>


                                        <div id="profile-photo-upload"
                                            class="d-flex flex-wrap mt-3 {{ $user_data->profile_picture ? 'd-none' : '' }}">
                                            <div class="mt-2">
                                                <button class="btn btn-primary" type="button"
                                                    data-bs-target="#modal-file-upload"
                                                    data-bs-toggle="modal">Upload</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- platform/plant access --}}
                                <div class="col-12 mt-3 ">
                                    <div class="card p-3 h-100">
                                        <div class="card-header">
                                            PLATFORM ACCESS PERMISSION <span class="text-danger">*</span>
                                        </div>
                                        <div class="card-body mt-3">
                                            <label>I-POS:</label>

                                            <div class="form-check">
                                                <input class="form-check-input" name="access-ipos[]" type="checkbox"
                                                    value="1" id="access-web">
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

                                <div class="col-12 mt-3" id="wc_selection_div">
                                    <div class="card p-3 ">
                                        <div class="card-header h-100">
                                            WORK CENTER PERMISSION <span class="text-danger">*</span>
                                        </div>
                                        {{-- table with 3 column and 2 checkbox column --}}
                                        <div class="card-body">
                                            <div class="table-responsive mt-3">
                                                <table class="table table-bordered table-striped mt-1">
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
                                                                    <input class="form-check-input wc-op-checkbox"
                                                                        name="wc-op[]" type="checkbox"
                                                                        value="{{ $workcenter->id }}">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input class="form-check-input wc-rework-checkbox"
                                                                        name="wc-rework[]" type="checkbox"
                                                                        value="{{ $workcenter->id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- user info --}}
                    <div class="data-form-cell">
                        <div class="card p-3 h-100">
                            <div class="card-body p-0">
                                <div class="row">

                                    <div class="col-12 mt-3">
                                        <label for="wc_plant_id" class="form-label">Plant</label>
                                        <select id="wc_plant_id" name="wc_plant_id"
                                            class="form-select @error('wc_plant_id') is-invalid @enderror" required>
                                            @if ($user_data->plant_id)
                                                {{-- if $user_data->plant_id in $admin_plants --}}

                                                {{ $plant_found = false }}
                                                @foreach ($admin_plants as $admin_plant)
                                                    @if ($admin_plant->id == $user_data->plant_id)
                                                        {{ $plant_found = true }}
                                                        <option value="{{ $admin_plant->id }}" selected>
                                                            {{ $admin_plant->name }}</option>
                                                    @else
                                                        <option value="{{ $admin_plant->id }}">{{ $admin_plant->name }}
                                                        </option>
                                                    @endif
                                                @endforeach

                                                @if ($plant_found == false)
                                                    <option value="{{ $user_data->plant_id }}" selected>
                                                        {{ $user_data->plant_name }}</option>
                                                @endif
                                            @else
                                                @foreach ($admin_plants as $admin_plant)
                                                    <option value="{{ $admin_plant->id }}"
                                                        {{ $plant->uid == $admin_plant->uid ? 'selected' : '' }}>
                                                        {{ $admin_plant->name }}
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>
                                        @error('wc_plant_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-3">
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
                                        <label for="enabled" class="form-label">USER ENABLED <span
                                                class="text-danger">*</span></label>
                                        <select name="enabled" id="enabled" class="form-control">
                                            <option value="1"
                                                {{ old('enabled', $user_data->enabled) ? 'selected' : '' }}>YES</option>
                                            <option value="0"
                                                {{ old('enabled', $user_data->enabled) ? '' : 'selected' }}>NO</option>
                                        </select>
                                        @error('enabled')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <hr>

                                    {{-- staft no --}}
                                    <div class="col-12 mt-3">
                                        <label for="staff_no" class="form-label">STAFF NO <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="staff_no" class="form-control" id="staff_no"
                                            placeholder="STAFF NO" value="{{ old('staff_no', $user_data->staff_no) }}"
                                            required>
                                        @error('staff_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- full name --}}
                                    <div class="col-12 mt-3">
                                        <label for="full_name" class="form-label">FULL NAME <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" id="full_name"
                                            placeholder="FULL NAME" value="{{ old('full_name', $user_data->full_name) }}"
                                            required>
                                        @error('full_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label for="sap_id" class="form-label">USER NAME <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="sap_id" class="form-control" id="sap_id"
                                            placeholder="USER NAME (FOR SAP)"
                                            value="{{ old('sap_id', $user_data->sap_id) }}" required>
                                        @error('sap_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label for="sap_id" class="form-label">EMAIL ADDRESS <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="address@email.com" value="{{ old('email', $user_data->email) }}"
                                            required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- password --}}
                                    <div class="col-12 mt-3">
                                        <label for="password" class="form-label">PASSWORD <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="PASSWORD" value="{{ old('password') }}">
                                        @error('password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- confirm password --}}
                                    <div class="col-12 mt-3">
                                        <label for="password_confirmation" class="form-label">CONFIRM PASSWORD <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation" placeholder="CONFIRM PASSWORD">
                                        @error('password_confirmation')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="submit-button-cell">
                        <div class="text-end">
                            <a href="{{ route('settings.user.index',[$plant->uid]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update</button>
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

        function updateProfilePictureView(user) {
            if (user.profile_picture == null) {
                $('#profile-photo-delete').addClass('d-none');
                $('#profile-photo-upload').removeClass('d-none');
            } else {
                $('#profile-photo-upload').addClass('d-none');
                $('#profile-photo-delete').removeClass('d-none');
            }
            $('#profile-picture-preview').attr('src', user.profile_picture_url);
        }

        function deleteProfilePicture() {

            $.post("{{ route('settings.user.edit.photo.delete', [$plant->uid, $user_data->id]) }}", {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                },
                function(data) {
                    updateProfilePictureView(data);
                });
        }

        function initializeFileUploader() {
            fileUploader.target = "{{ route('settings.user.edit.photo.update', [$plant->uid, $user_data->id]) }}";

            fileUploader.onUploadCompleted = updateProfilePictureView;
        }
        $(() => {
            page.initialize();
            initializeFileUploader();
            disable_inputs();
            // role 
            let role_access = {!! $role_access !!};
            if (role_access == 0) {
                $('#access-web').prop('checked', true);
                $('#access-web').prop('disabled', true);

                $('#access-terminal').prop('checked', true);
                $('#access-terminal').prop('disabled', true);

                $('#wc_selection_div').show();
                $('#wc_selection_div').find('input').prop('checked', true);
                $('#wc_selection_div').find('input').prop('disabled', true);

                //get all workcenter ids and put them in hidden field
                data = [];
                $('#wc_selection_div').find('input').each(function() {
                    data.push($(this).val());
                });

                // data unique
                data = data.filter(function(item, pos) {
                    return data.indexOf(item) == pos;
                });
                console.log("data", data);
                //foreach data add to data['wc-op'] and data['wc-rework']

                $('.hidden-inputs').html('');
                $.each(data, function(index, value) {
                    $('.hidden-inputs').append(`
                        <input type="hidden" name="wc-op[]" value="${value}">
                        <input type="hidden" name="wc-rework[]" value="${value}">
                    `);
                });

                $('#hidden-wc-op').val(data);
            }

            //web_platform_access_checkbox
            let web_access = {!! $web_platform_access !!};
            if (web_access) {
                $('#access-web').prop('checked', true);
            }

            let terminal_access = {!! $terminal_platform_access !!};
            if (terminal_access) {
                $('#access-terminal').prop('checked', true);
                $('#wc_selection_div').show();
            } else {
                $('#wc_selection_div').hide();
                $('#wc_selection_div').find('input').prop('checked', false);
            }

            //workcenter_access_checkbox
            let wc_access = {!! $workcenter_access !!};
            // get all workcenter value in table
            $('.wc-op-checkbox').each(function() {
                let wc_id = $(this).val();

                let wc_access_value = wc_access.find(wc => wc.work_center_id == wc_id);
                console.log(wc_access_value);
                if (wc_access_value) {
                    //get terminal access value
                    let wc_op_access = wc_access_value.terminal_permission;
                    console.log(wc_op_access);
                    if (wc_op_access == 1 || wc_op_access == 3) {
                        $(this).prop('checked', true);
                    }
                }
            });

            $('.wc-rework-checkbox').each(function() {
                let wc_id = $(this).val();

                let wc_access_value = wc_access.find(wc => wc.work_center_id == wc_id);
                console.log(wc_access_value);
                if (wc_access_value) {
                    //get terminal access value
                    let wc_rework_access = wc_access_value.terminal_permission;
                    console.log(wc_rework_access);
                    if (wc_rework_access == 2 || wc_rework_access == 3) {
                        $(this).prop('checked', true);
                    }
                }
            });

        });

        $('#access-admin').change(function() {
            if ($(this).is(':checked')) {
                $('#access-web').prop('checked', true);
                $('#access-web').prop('disabled', true);

                $('#access-terminal').prop('checked', true);
                $('#access-terminal').prop('disabled', true);

                $('#wc_selection_div').show();
                $('#wc_selection_div').find('input').prop('checked', true);
                $('#wc_selection_div').find('input').prop('disabled', true);

                //get all workcenter ids and put them in hidden field
                data = [];
                $('#wc_selection_div').find('input').each(function() {
                    data.push($(this).val());
                });

                // data unique
                data = data.filter(function(item, pos) {
                    return data.indexOf(item) == pos;
                });
                console.log("data", data);
                //foreach data add to data['wc-op'] and data['wc-rework']

                $('.hidden-inputs').html('');
                $.each(data, function(index, value) {
                    $('.hidden-inputs').append(`
                        <input type="hidden" name="wc-op[]" value="${value}">
                        <input type="hidden" name="wc-rework[]" value="${value}">
                    `);
                });

                $('#hidden-wc-op').val(data);
                // $('#hidden-wc-rework').val(wc_ids);

            } else {
                $('#access-web').prop('checked', false);
                $('#access-web').prop('disabled', false);

                $('#access-terminal').prop('checked', false);
                $('#access-terminal').prop('disabled', false);

                $('#wc_selection_div').hide();
                $('#wc_selection_div').find('input').prop('checked', false);
                $('#wc_selection_div').find('input').prop('disabled', false);

                $('.hidden-inputs').html('');
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


        //function to disabled all input, if user->plant_id not in array admin_plant->id
        function disable_inputs() {
            let admin_plant_array = @json($admin_plants);
            let user_plant_id = {!! $user_data->plant_id !!};

            let admin_plant_id = [];

            $.each(admin_plant_array, function(index, value) {
                admin_plant_id.push(value.id);
            });

            console.log(admin_plant_id, user_plant_id);

            //if user_plant_id not in array admin_plant_id
            if (admin_plant_id.indexOf(user_plant_id) == -1) {
                $('#admin_plant').prop('disabled', true);
                $('#company').prop('disabled', true);
                $('#enabled').prop('disabled', true);
                $('#staff_no').prop('disabled', true);
                $('#full_name').prop('disabled', true);
                $('#sap_id').prop('disabled', true);
                $('#email').prop('disabled', true);
                $('#password').prop('disabled', true);
                $('#password_confirmation').prop('disabled', true);
            } else {
                $('#admin_plant').prop('disabled', false);
                $('#company').prop('disabled', true);
                $('#enabled').prop('disabled', false);
                $('#staff_no').prop('disabled', false);
                $('#full_name').prop('disabled', false);
                $('#sap_id').prop('disabled', false);
                $('#email').prop('disabled', false);
                $('#password').prop('disabled', false);
                $('#password_confirmation').prop('disabled', false);
            }
        }
    </script>
@endsection
