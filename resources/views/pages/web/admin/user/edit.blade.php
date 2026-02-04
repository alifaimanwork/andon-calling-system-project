@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar', ['tabActive' => 'user'])
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
                grid-column-start: 2;
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

            <h5 class="secondary-text mt-4">EDIT USER</h5>
            <hr>
            <form method="post" action="{{ route('admin.user.update', [$user->id]) }}">
                @method('put')
                @csrf
                <div class="form-grid mb-4">
                    <!-- Profile Picture -->
                    <div class="card profile-picture-cell">

                        <div class="profile-pic-container">
                            <label>Profile Picture</label>
                            <div class="profile-pic mt-3 text-center">
                                <img id="profile-picture-preview" src="{{ $user->getProfilePictureUrl() }}">
                            </div>
                            <div id="profile-photo-delete"
                                class="mt-3 text-center {{ !$user->profile_picture ? 'd-none' : '' }}">
                                <button class="btn btn-danger" type="button"
                                    onclick="deleteProfilePicture()">Delete</button>
                            </div>


                            <div id="profile-photo-upload"
                                class="mt-3 text-center {{ $user->profile_picture ? 'd-none' : '' }}">
                                <button class="btn btn-primary" type="button" data-bs-target="#modal-file-upload"
                                    data-bs-toggle="modal">Upload</button>
                            </div>
                        </div>

                    </div>

                    <!-- Company Access Permission -->
                    <div class="card company-access-permission-cell">
                        <div class="card-header">
                            PLANT ACCESS PERMISSION <span class="text-danger">*</span>
                        </div>
                        <div class="card-body">
                            @foreach ($regionPlants as $region => $plants)
                                <label>{{ $region }}</label>
                                @foreach ($plants as $p)
                                    <div class="form-check">
                                        <input class="form-check-input checkbox-plant" id="plant-access-{{ $p->id }}"
                                            name="plant-access[]" type="checkbox" value="{{ $p->id }}"
                                            id="access-web">
                                        <label class="form-check-label" for="plant-access-{{ $p->id }}">
                                            {{ $p->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Data Form -->
                    <div class="card data-form-cell">
                        <div class="card-body">

                            <div class="my-3">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" name="role"
                                    class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="" @if (!$user->role) selected @endif disabled>-
                                    </option>
                                    <option value="0" @if ($user->role == 0) selected @endif>Super Admin
                                    </option>
                                    <option value="1" @if ($user->role == 1) selected @endif>Plant Admin
                                    </option>
                                    <option value="2" @if ($user->role == 2) selected @endif>Normal User
                                    </option>
                                </select>
                                @error('role')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select id="company_id" name="company_id"
                                    class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">-</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" <?php echo old('company_id', $user->company_id) == $company->id ? 'selected' : ''; ?>>{{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>


                            <div class="mb-3">
                                <label for="staff_no" class="form-label">Staff No<span class="text-danger">*</span></label>
                                <input type="text" name="staff_no"
                                    class="form-control @error('staff_no') is-invalid @enderror" id="staff_no"
                                    placeholder="Staff No" value="{{ old('staff_no', $user->staff_no) }}">
                                @error('staff_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="full_name"
                                    class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                                    placeholder="Full Name" value="{{ old('full_name', $user->full_name) }}">
                                @error('full_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sap_id" class="form-label">SAP ID<span class="text-danger">*</span></label>
                                <input type="text" name="sap_id"
                                    class="form-control @error('sap_id') is-invalid @enderror" id="sap_id"
                                    placeholder="User Name (for SAP)" value="{{ old('sap_id', $user->sap_id) }}">
                                @error('sap_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="designation" class="form-label">DESIGNATION<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="designation"
                                    class="form-control @error('sap_id') is-invalid @enderror" id="designation"
                                    placeholder="Designation" value="{{ old('designation', $user->designation ?? '-') }}">
                                @error('sap_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="address@email.com" value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- TODO: Proper reset password method -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Reset Password<span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    placeholder="Password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Reset Password<span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" placeholder="Confirm Password">
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>
                    </div>



                    <div class="submit-button-cell text-end">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Cancel</a>
                        <a class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmationModal">Delete</a>
                        <button type="submit" class="btn btn-primary">Update</button>
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
                    Delete plant?<br>
                    Name: <strong>{{ $user->name }}</strong><br>
                    UID: <strong>{{ $user->uid }}</strong><br>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" action="{{ route('admin.user.destroy', [$user->id]) }}">
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
        var plant_user = @json($plant_user);

        function initializeFileUploader() {
            fileUploader.target = "{{ route('admin.user.edit.photo.update', $user->id) }}";
            fileUploader.onUploadCompleted = updateProfilePictureView;
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
            $.post("{{ route('admin.user.edit.photo.delete', $user->id) }}", {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                },
                function(data) {
                    updateProfilePictureView(data);
                });
        }


        $(() => {
            page.initialize();
            initializeFileUploader();
            CheckLevel();

            plant_user.forEach(function(plant) {
                $('#plant-access-' + plant).prop('checked', true);
            });
        });

        var page = {
            _token: "{{ csrf_token() }}",

            initialize: function() {
                return this;
            },
        }

        $('#role').on('change', function() {
            CheckLevel();
        });

        function CheckLevel() {
            let level = $('#role').val();

            if (level == '0') {
                $('.checkbox-plant').prop('disabled', true);
                $('.checkbox-plant').prop('checked', true);

            } else if (level == '1') {
                $('.checkbox-plant').prop('disabled', false);

            } else {
                $('.checkbox-plant').prop('disabled', true);
                $('.checkbox-plant').prop('checked', false);
            }
        }
    </script>
@endsection
