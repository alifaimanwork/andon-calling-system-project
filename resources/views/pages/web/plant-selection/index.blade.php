<?php $pageTitle = 'IPOS - SELECT REGION';  //Page title can be assign here or in controller, if not define, automatically set as IPOS WEB ?>
@extends('layouts.app')
@include('components.web.top-nav-bar')

@section('head')
    @parent
    <style>
        /* saiz flag */
        .flag-button {
            box-shadow: 3px 3px 5px #999;
        }
        .flag-button-image {
            height: 100px;
            width: 150px;
        }

        /* mcm x de guna */
        .chibi-flag {
            border-radius: 50%;
            width: 32px;
            height: 32px;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(0, 0, 0, .2);
        }

        .select-plant-button,
        .select-plant-button:hover {
            display: block;
            border: 1px solid rgba(0, 0, 0, .2);
            height: 48px;
            border-radius: 0.5rem;
            margin-top: 0.4rem;
            margin-bottom: 0.4rem;
            text-decoration: none;
            color: #333;
        }

        a.select-plant-button:hover {
            text-decoration: none;
            color: #333;
            background-color: rgba(0, 0, 0, .1);
        }

        .select-plant-icon {
            height: 100%;
            font-size: 1.6rem;
            width: 48px;
            border-right: 1px solid rgba(0, 0, 0, .2);
        }

        .select-plant-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            padding-left: 0.4rem;
        }

        body {
            height:100%;
            font: 14px sans-serif;
            background-repeat: no-repeat;
            background: url("/images/mapSpotMiniv2.png");
            background-size: cover;
        }
        html {
            height: 100%;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.8)
        }

        .cardtitle {
            color: #626162;
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-top: 1em;
        }

        .cardRegion {
            font-size: 18px;
            /* width: 30em;
            position: relative;
            top: 15px;
            left: 35em; */
        }

        .titleipos {
            margin-top: 5px;
            margin-left: 151px;
        }

        .tag-logo {
            margin-top: -13px;
            margin-bottom: 13px;
            margin-left: 0px;
            color: #ffffff;
            text-shadow: 3px 3px 3px #000000;
        }

        h1.region {
            font-size: 17px;
            padding: 6px 2px 2px 2px;
            color: #626162;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .userBar {
            border: 1px solid #d6d6d6;
            border-radius: 55px 55px 1px 1px;
            margin: 0 40px;
            /* width: 23em; */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* position: relative;
            left: 53em;
            top: 40px; */
            padding: 0em 2.3em;
        }

        .boxcenter {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endsection

@section('body')
    <main>
        <div class="row d-flex justify-content-center w-100">
            <div class="col-12 col-md-9 mt-3 ms-5 ms-md-0">
                <img src="images/iposLogoWhite.png" height="103px" />
                <div class="tag-logo">
                    INTELLIGENT PRODUCTION ONLINE SYSTEM
                </div>
            </div>
            <div class="col-10 col-md-6 d-flex flex-column mt-3 mt-md-5 mb-3 boxcenter" style="max-width: 550px; text-align:center;">
                <div class="userBar bg-light text-dark d-flex justify-content-center align-items-center">
                    <h1 class="region">WELCOME, {{ strtoupper($user->full_name) }}<h1>
                </div>
                <div class="cardRegion">
                    <div class="d-flex justify-content-center">
                        <div class="card w-100">
                            <div class="cardtitle">
                                <h4>SELECT REGION</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($regions as $region)
                                    <div class="d-flex justify-content-center">
                                            <div role="button" class="flag-button mx-2 my-4" data-bs-toggle="modal" data-bs-target="#select-plant-{{ $region->id }}">
                                                <img class="flag-button-image" src="{{ $region->getFlagUrl() }}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="titleipos">
                <img src="images/iposLogoWhite.png" height="103px" />
                <div class="tag-logo">
                    INGRESS PRODUCTION ONLINE SYSTEM
                </div>
            </div>
        </div> --}}
        {{-- <div class="userBar bg-light text-dark d-flex justify-content-center align-items-center">
            <h1 class="region">WELCOME, {{ strtoupper($user->full_name) }}<h1>
        </div> --}}
        {{-- <div class="cardRegion">
            <div class="d-flex justify-content-center">
                <div class="card mt-4" style="width: 600px;">
                    <div class="cardtitle">
                        <h6>Please select a region and a company you want to view :<h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($regions as $region)
                            <div class="col-6 d-flex justify-content-center">
                                <div role="button" class="flag-button m-4" style="background-image: url('{{ $region->getFlagUrl() }}')" data-bs-toggle="modal" data-bs-target="#select-plant-{{ $region->id }}"> </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </main>
@endsection

@section('modals')
    @parent
    @foreach($regions as $region)
        <div class="modal fade" id="select-plant-{{ $region->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="d-flex align-items-center select-plant-button">
                            <div class="select-plant-icon d-flex align-items-center justify-content-center">
                                <div class="chibi-flag" style="background-image: url('{{ $region->getFlagUrl() }}')"></div>
                            </div>
                            <div class="select-plant-text">
                                {{$region->name}}
                            </div>
                        </div>
                        @foreach($region->plants as $plant)
                            @if(App\Models\User::getCurrent()->isSuperAdmin() || App\Models\User::getCurrent()->isWebAccessible($plant->uid))
                                <a href="{{ route('overview.index',$plant->uid) }}" class="d-flex align-items-center select-plant-button" role="button">
                                    <div class="select-plant-icon d-flex align-items-center justify-content-center"><i class="fa-solid fa-industry-windows"></i></div>
                                    <div class="select-plant-text">
                                        {{$plant->name}}
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection