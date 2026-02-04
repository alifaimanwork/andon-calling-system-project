@include('utils.auto-toast')
@extends('layouts.app')
@include('components.web.top-nav-bar')
@section('head')
@parent
<style>
    #profile-pic {
        width: 200px;
        max-height: 200px;
    }
</style>

@endsection

@section('body')
<main>
    <div class="container">
        @yield('mobile-title')
        <div class="card mt-4">
            <div class="card-header">
                Update Account Info
            </div>
            <div class="card-body">
                <div>
                    <div class="d-flex justify-content-center">
                        <div class="m-4 border p-4 text-center">
                            <img id="profile-pic" src="{{ Auth::user()->getProfilePictureUrl() }}" class="profile-pic">
                        </div>
                    </div>

                    <div class="mt-2 d-flex justify-content-center">
                        @if(Auth::user()->profile_picture)
                            <div>
                                <form method="post" enctype="multipart/form-data" action="{{ route('manage-account.picture.destroy') }}">
                                    @csrf
                                    @method('delete')
                                    <div class="d-flex flex-wrap">
                                        <div class="mt-2">
                                            <button class="btn btn-danger" onclick="deleteProfilePicture()">Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div>
                                <form method="post" enctype="multipart/form-data" action="{{ route('manage-account.picture.update') }}">
                                    @csrf
                                    <div class="d-flex flex-wrap">
                                        <div class="mt-2 me-2">
                                            <input name="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror" type="file" id="profile_picture" accept="image/png, image/jpeg">
                                            @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <form class="mt-2" method="post" action="{{ route('manage-account.update') }}">
                    @csrf
                    <div class="form-group">

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="staff_no" class="form-label">Staff No</label>
                                <input type="text" class="form-control" id="staff_no" value="{{ Auth::user()->staff_no }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input name="full_name" type="text" class="form-control @error('name') is-invalid @enderror" id="full_name" value="{{ old('full_name', Auth::user()->full_name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input name="email" type="text" class="form-control @error('name') is-invalid @enderror" id="email" value="{{ old('email', Auth::user()->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" value="">
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" value="">
                                @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input name="new_password_confirmation" type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" value="">
                                @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('modals')
@parent
@endsection