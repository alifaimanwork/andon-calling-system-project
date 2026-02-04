@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('pages.web.admin.components.tab-nav-bar',['tabActive' => 'network-monitoring'])
@section('head')
@parent
@endsection

@section('body')
<main>
    <div class="container">
        @yield('mobile-title')
        @yield('tab-nav-bar')
        TODO: Manage Network Monitoring
    </div>
</main>
@endsection

@section('modals')
@parent
<div>

</div>
@endsection