@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'downtime-reason'])
@include('templates.search-field-text')
@include('templates.search-field-select')

@section('head')
    @parent
        <style>
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
            <h5 class="secondary-text mt-4">DOWNTIME REASON</h5>
            <hr>
            <div class="my-2 text-end">
                <a href="{{ route('settings.downtime-reason.create',[ $plant->uid ]) }}" class="btn btn-action"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW REASON</a>
            </div>
            <div class="search-box">
                <div class="search-header p-1 px-2 collapsed" data-bs-toggle="collapse" href="#search-main-table" role="button" aria-expanded="false" aria-controls="search-main-table">
                    SEARCH &nbsp;<i class="fas fa-chevron-up chevron"></i>
                </div>
                <div id="search-main-table" class="collapse">
                    <div class="search-container">
                        <div id="search-field-container" class="row"></div>
                        <div class="text-end">
                            <button class="btn btn-primary search-submit">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <table id="main-table" class="table table-striped w-100"> </table>
            </div>
        </div>
    </main>
@endsection

@section('modals')
    @parent
    <div>

    </div>
@endsection