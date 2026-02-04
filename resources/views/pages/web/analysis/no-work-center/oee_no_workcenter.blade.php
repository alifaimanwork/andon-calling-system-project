@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'OEE'])

@section('head')
    @parent
    <style>
        @media only screen and (max-width: 768px) {
            .chart-title {
                font-size: 65% !important;
            }
        }

        .table {
            background-color: #FFFFFF;
        }

        .chart-label-middle.mini-chart{
            top: calc(50%);
            font-size: 1.2rem;
        }

        @media only screen and (max-width: 1400px) {
            .chart-label-middle.mini-chart{
                font-size: 1rem;
            }
        }

        .chart-label-middle {
            position: absolute;
            top: calc(50% - 20px);
            left: 0;
            right: 0;
            text-align: center;
            font-size: 1.6rem;
            font-weight: bold;
            color : #9B003E;
        }

        .chart-label-middle span:nth-child(2){
            font-size: 0.8rem;
            font-weight: 600;
        }



        .bar-active {
            transition-duration: 500ms;
        }

        .bar-availability {
            background-color: #f98a8a;
        }

        .bar-availability .bar-active {
            background-color: #f31414;
        }

        .bar-performance {
            background-color: #f98a8a;
        }

        .bar-performance .bar-active {
            background-color: #f31414;;
        }

        .bar-quality {
            background-color: #f98a8a;
        }

        .bar-quality .bar-active {
            background-color: #f31414;
        }

        .box-header {
            display: flex;
            padding: 0.3rem;
            font-weight: bold;
            font-size: 1.2rem;
            color: #9B003E;
        }

        .table {
            background-color: #FFFFFF;
        }

        .table thead{
            background-color: #CB84A3;
            color: #FFFFFF;
        }

        .table thead th{
            font-weight: 500;
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
    <div class="container mb-3">
        @yield('mobile-title')
        <div class="my-2 d-flex justify-content-between align-items-center">
            <div>
                @include('components.web.change-plant-selector')
            </div>
            @if(isset($workCenter,$workCenters))
                @include('pages.web.analysis.components.change-workcenter-selector')
            @endif
            <div>
                <div class="d-flex align-items-center">
                    <div class="text-nowrap me-2">DATE RANGE</div>
                    <input type="text" class="form-control text-center" id="production-date" style="color: #9b003e; background-color: #dddddd;" disabled>
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