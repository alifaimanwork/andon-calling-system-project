@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'QUALITY'])

@section('head')
    @parent
@endsection

@section('body')
    <main>
        @yield('drop-menu-navigation')
        <div class="container">
            @include('pages.web.realtime.components.work-center-header')
    
            <div id="dashboard-container">
                <div class="row mt-3" style="background-color: #fff; height: 500px">
                    <div class="d-flex justify-content-center align-items-center my-4" style="font-size: 2.5rem; font-weight: 400">
                        No Work Center
                    </div>
                </div>
            </div>
    
        </div>
    </main>
@endsection