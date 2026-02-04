@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'PRODUCTIVITY'])

@section('head')
@parent
<style>
    .box-shadow {
        box-shadow: 6px 3px 5px #00000040;
        border-radius: 5px;
        border: 2px solid #00000020;
        border-top: 1px solid #00000020;
        border-left: 1px solid #00000020;
    }

    .elapsed-time-legend-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }

    .productivity-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 1rem;
    }

    .timer-label {
        font-weight: 500;
    }

    .elapsed-timer-label {
        color: #c62828;
        font-family: 'Roboto Mono', monospace;
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