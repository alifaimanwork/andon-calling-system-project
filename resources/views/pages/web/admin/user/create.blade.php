@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar', ['tabActive' => 'user'])
@include('templates.search-field-text')
@include('templates.search-field-select')
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

            <h5 class="secondary-text mt-4">ADD NEW ADMINISTRATOR</h5>
            <hr>
            <form method="post" action="{{ route('admin.user.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid mb-4">
                    <!-- Profile Picture -->
                    <div class="card profile-picture-cell">
                        <div class="profile-pic-container">
                            <label>Profile Picture</label>
                            <div class="profile-pic mt-3 text-center">
                                <img id="profile-picture-preview" src="{{ url('images/default-profile.svg') }}">
                            </div>
                            <div class="upload-input-container mt-2">
                                <input id="file-upload-input" name="profile_picture" type="file"
                                    accept="image/png, image/jpeg" />
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
                                    <option value="" selected disabled>-</option>
                                    <option value="0">Super Admin</option>
                                    <option value="1">Plant Admin</option>
                                </select>
                                @error('role')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select id="company_id" name="company_id"
                                    class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="" <?php echo old('company_id') ? '' : 'selected'; ?>>-</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" <?php echo old('company_id', null) == $company->id ? 'selected' : ''; ?>>{{ $company->name }}
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
                                    placeholder="Staff No" value="{{ old('staff_no', '') }}">
                                @error('staff_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="full_name"
                                    class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                                    placeholder="Full Name" value="{{ old('full_name', '') }}">
                                @error('full_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sap_id" class="form-label">SAP ID<span class="text-danger">*</span></label>
                                <input type="text" name="sap_id"
                                    class="form-control @error('sap_id') is-invalid @enderror" id="sap_id"
                                    placeholder="User Name (for SAP)" value="{{ old('sap_id', '') }}">
                                @error('sap_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="designation" class="form-label">DESIGNATION<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="designation"
                                    class="form-control @error('sap_id') is-invalid @enderror" id="designation"
                                    placeholder="Designation" value="{{ old('designation', '') }}">
                                @error('sap_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address<span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="address@email.com" value="{{ old('email', '') }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password<span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    placeholder="Password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password<span
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

            CheckLevel();
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
                $('.checkbox-plant').prop('checked', false);

            } else {
                $('.checkbox-plant').prop('disabled', true);
                $('.checkbox-plant').prop('checked', false);
            }
        }
    </script>
@endsection
