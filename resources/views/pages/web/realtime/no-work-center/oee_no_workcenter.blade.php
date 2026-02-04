<?php $pageTemplateUrl = route('realtime.oee.index', '__uid__'); ?>
@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'OEE'])

@section('head')
    @parent

    <style>
        .chart-center-text {
            width: 100%;
            height: 100%;
            position: absolute;
            font-size: 300%;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
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


