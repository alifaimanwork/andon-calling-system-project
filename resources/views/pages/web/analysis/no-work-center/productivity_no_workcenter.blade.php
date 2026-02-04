@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'PRODUCTIVITY'])
@section('head')
@parent
<style>
    .table {
        background-color: #FFFFFF;
    }

    .table thead{
        background-color: #CB84A3;
        color: #FFFFFF;
    }

    .table thead th{
        font-size: 14px;
        font-weight: 500;
    }

    .table th, .table td{
        text-align: center !important;
        vertical-align: middle !important;
    }
    .table tbody {
        font-weight: 500;
        color: #575353;
    }
</style>
@endsection

@section('body')
<main>
    @yield('drop-menu-navigation')
    <div class="container">
        @yield('mobile-title')
        <div class="my-2 d-flex justify-content-between align-items-center">
            <div>
                @include('components.web.change-plant-selector')
            </div>
            @if(isset($workCenter,$workCenters))
            <div>
                @include('pages.web.analysis.components.change-workcenter-selector')
            </div>
            @endif
            <div>
                <div class="d-flex align-items-center">
                    <div class="text-nowrap me-2">DATE RANGE</div>
                    <div>
                        <input type="text" class="form-control text-center" id="production-date" style="color: #9b003e; background-color: #dddddd;" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3" style="background-color: #fff; height: 500px">
            <div class="d-flex justify-content-center align-items-center my-4" style="font-size: 2.5rem; font-weight: 400">
                No Work Center
            </div>
        </div>

    </div>
</main>
@endsection