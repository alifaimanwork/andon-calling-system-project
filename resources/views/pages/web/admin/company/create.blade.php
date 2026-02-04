@extends('layouts.app')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'company'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@include('components.web.top-nav-bar')
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

        <h5 class="secondary-text mt-4">ADD NEW COMPANY</h5>
        <hr>
        <form method="post" action="{{ route('admin.company.store') }}">
            @csrf
            <div class="form-grid mb-4">
                <!-- Data Form -->
                <div class="card data-form-cell">
                    <div class="card-body">
                        <div class="my-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="COMPANY NAME" value="{{ old('name','') }}">
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code<span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="COMPANY CODE" value="{{ old('code','') }}">
                            @error('code')
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