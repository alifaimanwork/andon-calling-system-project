@extends('layouts.guest')
<?php // TODO: Guest Welcome page ?>

@section('head')
    <style>

    </style>
@endsection

@section('body')

    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-check me-2"></i> AVAILABILITY
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">02</span>
                                            <span class="box-title">HRS</span>
                                        </div>
                                        <div class="main-box ms-3">
                                            <span class="box-value">30</span>
                                            <span class="box-title">Min</span>
                                        </div>
                                    </div>
                                    <div class="title">
                                        RUN TIME
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">08</span>
                                            <span class="box-title">HRS</span>
                                        </div>
                                        <div class="main-box ms-3">
                                            <span class="box-value">30</span>
                                            <span class="box-title">Min</span>
                                        </div>

                                    </div>
                                    <div class="title">
                                        PLANNED PRODUCTION TIME
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-check me-2"></i> PERFORMANCE
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">250</span>
                                            <span class="box-title">PCS</span>
                                        </div>
                                    </div>
                                    <div class="title">
                                        ACTUAL PRODUCTION RATE
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">750</span>
                                            <span class="box-title">PCS</span>
                                        </div>

                                    </div>
                                    <div class="title">
                                        STANDARD PRODUCTION RATE
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-check me-2"></i> QUALITY
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">242</span>
                                            <span class="box-title">PCS</span>
                                        </div>
                                    </div>
                                    <div class="title">
                                        TOTAL PART OK
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column">
                                    <div class="d-flex">
                                        <div class="main-box">
                                            <span class="box-value">250</span>
                                            <span class="box-title">PCS</span>
                                        </div>

                                    </div>
                                    <div class="title">
                                        TOTAL PARTS PRODUCED
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="dialogue-container mt-3">
                <div class="dialogue-title p-3">
                    <span class="ms-2">MANAGE ACCOUNT</span>
                </div>
                <div class="dialogue-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mt-3">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <div class="w-100">
                                    <div class="d-flex flex-column px-5 align-items-center">
                                        <span class="text-center primary-text">PROFILE PICTURE</span>
                                        <img class="img-container mt-2" src="{{ asset('images/profile.jpg') }}" alt="">
                                        <button class="btn btn-action mt-2">CHANGE PHOTO</button>
                                        <button class="btn btn-cancel mt-2">REMOVE PHOTO</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-3">
                            <div class="d-flex flex-column p-4">
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-id-card-clip mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="DATUK (DR.) RAMELI BIN MUSA">
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-envelope mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="bod@email.com">
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-lock-keyhole mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="*********">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-eye-slash mx-auto"></i></span>
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-lock-keyhole mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="*********">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-eye-slash mx-auto"></i></span>
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="DRM2021">
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-message-smile mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="RAMELI">
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-location-dot mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="THAILAND">
                                </div>
                                <div class="input-group mt-3 flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-industry-windows mx-auto"></i></span>
                                    <input type="text" class="form-control" placeholder="Username" value="IAV - RAYONG PLANT">
                                </div>
                                <div class="w-100 mt-3">
                                    <div class="float-end">
                                        <button class="btn btn-cancel">CANCEL</button>
                                        <button class="btn btn-action">UPDATE</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mt-3">
                <div class="dialogue-container">
                    <div class="d-flex flex-column p-4">
                        <div class="input-group mt-3 flex-nowrap">
                            <span class="input-group-text bg-white" id="addon-wrapping"><img src="{{ asset('images/thailand.png') }}" alt="" style="width: 24px; height:24px"></span>
                            <input type="text" class="form-control" placeholder="Username" value="THAILAND">
                        </div>
                        <div class="input-group mt-3 flex-nowrap">
                            <span class="input-group-text bg-white" id="addon-wrapping"><i class="fa-solid fa-industry-windows"></i></span>
                            <input type="text" class="form-control" placeholder="Username" value="IAV - RAYONG PLANT">
                        </div>
                        <div class="input-group mt-3 flex-nowrap">
                            <span class="input-group-text bg-white" id="addon-wrapping"><i class="fa-solid fa-industry-windows"></i></span>
                            <input type="text" class="form-control" placeholder="Username" value="IAV- AYUTTHAYA PLANT">
                        </div>
                        <div class="input-group mt-3 flex-nowrap">
                            <span class="input-group-text bg-white" id="addon-wrapping"><i class="fa-solid fa-industry-windows"></i></span>
                            <input type="text" class="form-control" placeholder="Username" value="FINE COMPONENT">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-3">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">LINE 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 3</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 4</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 5</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 6</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-12 col-md-6 mt-3">
                        <div class="d-flex justify-content-between">
                            <div class="part-container px-2">
                                <i class="fa-solid fa-tag big-icon"></i>
                                <div class="d-flex flex-column">
                                    <span class="primary-text">Part Name <i class="ms-3 fa-solid fa-caret-down"></i></span>
                                    <span class="value">CHAN FR DRWDO GL, RH</span>
                                </div>
                            </div>
                            <div class="part-container px-2">
                                <i class="fa-duotone fa-tags big-icon"></i>
                                <div class="d-flex flex-column">
                                    <span class="primary-text">Part Number <i class="ms-3 fa-solid fa-caret-down"></i></span>
                                    <span class="value">AB39-2621468-BC_</span>
                                </div>
                            </div>
                        </div>
                        <div class="table-container mt-3">
                            <table class="table table-striped w-100 primary-text">
                                <tbody>
                                    <tr>
                                        <td>PLAN PRODUCTION</td>
                                        <td>1200 PCS</td>
                                    </tr>
                                    <tr>
                                        <td>ACTUAL PRODUCTION</td>
                                        <td>850 PCS</td>
                                    </tr>
                                    <tr>
                                        <td>PART OK</td>
                                        <td>800 PCS</td>
                                    </tr>
                                    <tr>
                                        <td>PART NG</td>
                                        <td>50 PCS</td>
                                    </tr>
                                    <tr>
                                        <td>PENDING QUANTITY</td>
                                        <td>15 PCS</td>
                                    </tr>
                                    <tr>
                                        <td>REJECT %</td>
                                        <td>6 %</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="col-12 col-md-6 mt-3">
                        <h5 class="secondary-text">REJECT (PCS)</h5>
                        <div class="w-100 my-3" style="">
                            <canvas id="reject_chart"></canvas>
                        </div>
                    </div>

                    <div class="col-12 mt-3" style="height:500px;">
                        <h5 class="secondary-text">TOP 10 REJECT BY DEFECT PART (PCS)</h5>
                        <div class="w-100 my-3" style="">
                            <canvas id="top_10_chart"></canvas>
                        </div>
                    </div>
                </div>
                
            </div>

        </div>

        <div class="row">
            <div class="col-12 col-md-6 mt-3">
                <div class="card-header">
                    <i class="fa-solid fa-clipboard-check me-2"></i> DOWNTIME
                </div>
                <div class="d-flex justify-content-between">
                    <div class="part-container px-2">
                        <i class="fa-solid fa-tag big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">Part Name <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value">CHAN FR DRWDO GL, RH</span>
                        </div>
                    </div>
                    <div class="part-container px-2">
                        <i class="fa-duotone fa-tags big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">Part Number <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value">AB39-2621468-BC_</span>
                        </div>
                    </div>
                </div>
                <div class="table-container mt-3">
                    <table class="table table-striped w-100 primary-text">
                        <tbody>
                            <tr>
                                <td>START TIME</td>
                                <td>07:50</td>
                            </tr>
                            <tr>
                                <td>CURRENT TIME</td>
                                <td>21:23</td>
                            </tr>
                            <tr>
                                <td>TOTAL WORKING</td>
                                <td>5 HRS 20 MIN</td>
                            </tr>
                            <tr>
                                <td>TOTAL DOWNTIME</td>
                                <td>1 HR 38 MIN</td>
                            </tr>
                            <tr>
                                <td>DOWNTIME %</td>
                                <td>31 %</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-3">
                <h5 class="secondary-text">DOWNTIME (MIN)</h5>
                <div class="w-100 my-3" style="">
                    <canvas id="downtime_chart"></canvas>
                </div>
            </div>

            <div class="col-12 col-md-6 mt-3">
                <div class="d-flex flex-column">
                    <div class="downtime-container justify-content-between px-2">
                        <h4 class="secondary-text my-auto">MACHINE DOWNTIME</h4>
                        <div class="timebox">
                            <i class="fa-solid fa-database my-auto" style="font-size:25px"></i>
                            <div class="px-2">
                                <span class="time-value">01</span>
                                <span class="time-unit">HRS</span>
                            </div>
                            <div class="px-2">
                                <span class="time-value">08</span>
                                <span class="time-unit">MINS</span>
                            </div>
                        </div>
                    </div>
    
                    <div class="row mt-3">
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #c62828; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        AUTOLOADER #1
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:20
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        EMERGENCY
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:33
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        AUTOLOADER #2
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:04
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        PRESS #1
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:10
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        ROBOT #1
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:20
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        PRESS #2
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        ROBOT #2
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:10
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        LIFTER #1
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        SBN
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        LIFTER #2
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:10
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="downtime-container justify-content-between px-2 mt-3">
                        <h4 class="secondary-text my-auto">HUMAN DOWNTIME</h4>
                        <div class="timebox">
                            <i class="fa-solid fa-database my-auto" style="font-size:25px"></i>
                            <div class="px-2">
                                <span class="time-value">00</span>
                                <span class="time-unit">HRS</span>
                            </div>
                            <div class="px-2">
                                <span class="time-value">30</span>
                                <span class="time-unit">MINS</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        WAITING PALLET
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #303f9f; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        WIP WAITING
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:25
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        MATERIAL SHORT
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:00
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        PRESS #1
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:10
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="w-100 d-flex">
                                <div class="" style="background-color: #e1e1e1; flex:1;">
                                    
                                </div>
                                <div class="mx-2" style="flex:4;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        OTHER
                                    </div>
                                </div>
                                <div class="" style="flex:1;border: 1px solid black">
                                    <div class="p-1" style="font-weight: 700">
                                        00:05
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 mt-3">
                <div class="d-flex flex-column h-100">
                    <h5 class="secondary-text" style="">TOP 10 DOWNTIME BY BREAKDOWN (MIN)</h5>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="top10down_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <ul class="nav top-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#">FACTORY</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">DOWNTIME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">DOWNTIME REASON</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">REJECT TYPE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">WORK CENTER</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">PART NUMBER</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">USER</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">SUPER ADMIN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">NETWORK STATUS</a>
                </li>
            </ul>
        </div>

        <div class="row mt-3">
            <h5 class="secondary-text">FACTORY</h5>

            <div class="w-100">
                <button class="btn btn-action float-start"><i class="me-3 fa-duotone fa-file-plus"></i>ADD NEW COMPANY</button>
            </div>

            <table id="dtProductionPlanning" class="table nowrap table-hover" style="width:100%;">
                <thead>
                    <tr style="background-color: #e1e1e1">
                        <th>ID</th>
                        <th>COUNTRY</th>
                        <th>TIME ZONE</th>
                        <th>COMPANY NAME</th>
                        <th>COMPANY LOGO</th>
                        <th>NUMBER OF EMPLOYEE</th>
                        <th>TOTAL LINE</th>
                        <th>STATUS</th>
                        <th>EDIT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>THAILAND</td>
                        <td>GMT+5:30</td>
                        <td>IAV-RAYONG PLANT</td>
                        <td><img src="{{ asset('images/ingress_logo.jpg') }}" alt="" style="height: 25px;"></td>
                        <td>53</td>
                        <td>6</td>
                        <td class="text-center"><span class="badge bg-success">Active</span></td>
                        <td class="text-center"><i class="fa-duotone fa-pencil"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-12 col-md-4 mt-3 pt-2">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-regular fa-clipboard-check me-2"></i> PRODUCTION STATUS SUMMARY
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">1200</span>
                                    <span class="primary-text">PLAN OUTPUT</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">1189</span>
                                    <span class="primary-text">ACTUAL OUTPUT</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">320</span>
                                    <span class="primary-text">TOTAL REJECT PART</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">12.5</span>
                                    <span class="primary-text">TOTAL DOWNTIME</span>
                                </div>
                                <span class="secondary-text align-self-end">HRS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">7.8</span>
                                    <span class="primary-text">TOTAL WORKING HOUR</span>
                                </div>
                                <span class="secondary-text align-self-end">HRS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="secondary-text" style="font-size:150%">86 %</span>
                                    <span class="primary-text">AVERAGE OEE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 mb-2">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body">
                        <div class="d-flex secondary-text mt-2" style="font-size: 40px;">
                            <i class="fa-light fa-file-pdf px-2"></i>
                            <i class="fa-light fa-file-spreadsheet px-2"></i>
                            <i class="fa-solid fa-print px-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-8 mt-3">
                <div class="d-flex flex-column h-100">
                    <div class="row flex-fill">
                        <div class="col-6 p-2" >
                            <div class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #6766ff; color: white;">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-calendar-lines-pen pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">OEE</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 p-2" >
                            <div class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #15c2c8; color: white;">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-duotone fa-forklift pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">PRODUCTIVITY</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 p-2" >
                            <div class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #fe5f6d; color: white;">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-regular fa-badge-check pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">QUALITY</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 p-2" >
                            <div class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #ff9059; color: white;">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-hourglass-clock pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">DOWNTIME</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row flex-fill">
                        <div class="col p-2">
                            <div class="rounded p-2 h-100 w-100 d-flex justify-content-center align-items-center" style="background-color: #3a5dd8; color: white;">
                                <span style="font-size:25px;"><i class="fa-thin fa-file-chart-column me-2"></i> DAILY PRODUCTION REPORT (DPR)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-12 col-md-4 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTIVITY SUMMARY STATUS
                    </div>
                    <div class="card-body" style="color:white;">
                        <div class="d-flex justify-content-between mt-3" >
                            <div class="flex-fill m-1 d-flex flex-column align-items-center p-3" style="background-color: #cb84a3;">
                                <span style="font-size: 80%">PLAN OUTPUT</span>
                                <span style="font-size: 200%; font-weight: 600">1190</span>
                                <span style="font-size: 80%">PCS</span>
                            </div>
                            <div class="flex-fill m-1 d-flex flex-column align-items-center p-3" style="background-color: #cb84a3;">
                                <span style="font-size: 80%">ACTUAL OUTPUT</span>
                                <span style="font-size: 200%; font-weight: 600">1190</span>
                                <span style="font-size: 80%">PCS</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between p-2 m-1" style="background-color: #cb84a3;">
                            <span class="text-wrap w-50">
                                PRODUCTIVITY PERCENTAGE (%)
                            </span>
                            <span class="align-self-center" style="font-size: 200%; font-weight: 600">
                                86 %
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body">
                        <div class="d-flex secondary-text mt-2" style="font-size: 40px;">
                            <i class="fa-light fa-file-pdf px-2"></i>
                            <i class="fa-light fa-file-spreadsheet px-2"></i>
                            <i class="fa-solid fa-print px-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column h-100">
                    <h5 class="secondary-text">PRODUCTIVITY - DAY SHIFT (PCS)</h5>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="p_day_chart"></canvas>
                    </div>
                    <div class="d-flex px-5">
                        <div class="d-flex me-4">
                            <div class="me-2" style="width:15px; background-color:#a3a3a3"></div>
                            <span class="primary-text" style="font-size: 60%">LINE 1</span>
                        </div>
                        <div class="d-flex">
                            <div class="me-2" style="width:15px; background-color:#000080"></div>
                            <span class="primary-text" style="font-size: 60%">LINE 2</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column h-100">
                    <h5 class="secondary-text">PRODUCTIVITY - NIGHT SHIFT (PCS)</h5>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="p_night_chart"></canvas>
                    </div>
                    <div class="d-flex px-5">
                        <div class="d-flex me-4">
                            <div class="me-2" style="width:15px; background-color:#1b0c4d"></div>
                            <span class="primary-text" style="font-size: 60%">LINE 1</span>
                        </div>
                        <div class="d-flex">
                            <div class="me-2" style="width:15px; background-color:#35d5d0"></div>
                            <span class="primary-text" style="font-size: 60%">LINE 2</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 overflow-auto mt-3">
                <table id="dt2" class="table nowrap table-hover mt-3 text-wrap" style="width:100%; font-size:80%">
                    <thead>
                        <tr class="text-wrap" style="background-color: #cb84a2; color: white;">
                            <th>NO</th>
                            <th>DATE</th>
                            <th>SHIFT</th>
                            <th>LINE</th>
                            <th class="text-wrap" style="width: 50px;">PRODUCTION ORDER</th>
                            <th>PART NUMBER</th>
                            <th>PART NAME</th>
                            <th class="text-wrap" style="width: 50px;">TOTAL WORKING HOURS</th>
                            <th class="text-wrap" style="width: 50px;">TOTAL PLAN</th>
                            <th class="text-wrap" style="width: 50px;">TOTAL STANDARD OUTPUT</th>
                            <th class="text-wrap" style="width: 50px;">TOTAL ACTUAL OUTPUT</th>
                            <th class="text-wrap" style="width: 50px;">PRODUCTIVITY (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>11/12/2021</td>
                            <td>DAY</td>
                            <td>1</td>
                            <td>241106293105</td>
                            <td>AB39-2621468-BC_</td>
                            <td>CHAN FR DR WDO GL, RH</td>
                            <td>7.6</td>
                            <td>1200</td>
                            <td>500</td>
                            <td>450</td>
                            <td>85</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>11/12/2021</td>
                            <td>NIGHT</td>
                            <td>2</td>
                            <td>241106293105</td>
                            <td>AB39-2621468-BC_</td>
                            <td>CHAN FR DR WDO GL, RH</td>
                            <td>6.8</td>
                            <td>1200</td>
                            <td>500</td>
                            <td>431</td>
                            <td>71</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>12/12/2021</td>
                            <td>DAY</td>
                            <td>1</td>
                            <td>241106293105</td>
                            <td>AB39-2621468-BC_</td>
                            <td>CHAN FR DR WDO GL, RH</td>
                            <td>6.6</td>
                            <td>1200</td>
                            <td>500</td>
                            <td>485</td>
                            <td>95</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>12/12/2021</td>
                            <td>NIGHT</td>
                            <td>2</td>
                            <td>241106293105</td>
                            <td>AB39-2621468-BC_</td>
                            <td>CHAN FR DR WDO GL, RH</td>
                            <td>7.2</td>
                            <td>1200</td>
                            <td>500</td>
                            <td>489</td>
                            <td>97</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="row">
            <div class="col-12 col-md-4 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text">AVAILABILITY</span>
                                <span class="primary-text">25  /  100</span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container">
                                    <div class="d-flex justify-content-center align-items-center" style="background-color: #f31414 ; width: 25%">
                                    </div>
                                    <div class="" style="background-color: #f98a8a  ; width: 75%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text">PERFORMANCE</span>
                                <span class="primary-text">63  /  100</span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container">
                                    <div class="d-flex justify-content-center align-items-center" style="background-color: #ff7f00 ; width: 63%">
                                    </div>
                                    <div class="" style="background-color: #ffbf80  ; width: 37%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text">QUALITY</span>
                                <span class="primary-text">75  /  100</span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container">
                                    <div class="d-flex justify-content-center align-items-center" style="background-color: #33a02c ; width: 75%">
                                    </div>
                                    <div class="" style="background-color: #99d096  ; width: 25%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> AVAILABILITY
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">02</span>
                                                <span class="box-title">HRS</span>
                                            </div>
                                            <div class="main-box ms-3">
                                                <span class="box-value">30</span>
                                                <span class="box-title">Min</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            RUN TIME
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">08</span>
                                                <span class="box-title">HRS</span>
                                            </div>
                                            <div class="main-box ms-3">
                                                <span class="box-value">30</span>
                                                <span class="box-title">Min</span>
                                            </div>
    
                                        </div>
                                        <div class="title">
                                            PLANNED PRODUCTION TIME
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> PERFORMANCE
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">250</span>
                                                <span class="box-title">PCS</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            ACTUAL PRODUCTION RATE
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">750</span>
                                                <span class="box-title">PCS</span>
                                            </div>
    
                                        </div>
                                        <div class="title">
                                            STANDARD PRODUCTION RATE
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> QUALITY
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">242</span>
                                                <span class="box-title">PCS</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            TOTAL PART OK
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value">250</span>
                                                <span class="box-title">PCS</span>
                                            </div>
    
                                        </div>
                                        <div class="title">
                                            TOTAL PARTS PRODUCED
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="secondary-text"><u>OEE HOURLY TREND</u></h5>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="me-2" style="width:15px;   background-color: rgb(255, 99, 132);"></div>
                                <span class="primary-text" style="font-size: 60%">STANDARD OEE</span>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="me-2" style="width:15px; background-color:#800000"></div>
                                <span class="primary-text" style="font-size: 60%">ACTUAL OEE</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="oee_trend_chart"></canvas>
                    </div>


                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-12 mt-3">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">LINE 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 3</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 4</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 5</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">LINE 6</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-12 mt-3 d-flex flex-column">
                        <div class="float-start">
                            <div class="w-50 d-flex justify-content-between">
                                <div class="part-container px-2">
                                    <i class="fa-solid fa-tag big-icon"></i>
                                    <div class="d-flex flex-column">
                                        <span class="primary-text">Part Name <i class="ms-3 fa-solid fa-caret-down"></i></span>
                                        <span class="value">CHAN FR DRWDO GL, RH</span>
                                    </div>
                                </div>
                                <div class="part-container px-2">
                                    <i class="fa-duotone fa-tags big-icon"></i>
                                    <div class="d-flex flex-column">
                                        <span class="primary-text">Part Number <i class="ms-3 fa-solid fa-caret-down"></i></span>
                                        <span class="value">AB39-2621468-BC_</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mt-3">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTIVITY
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-center mt-3 bg-body rounded shadow p-3">
                                                <div class="d-flex flex-column">
                                                    <span class="secondary-text text-center" style="font-size: 180%">1200 PCS</span>
                                                    <span class="primary-text text-center">PLAN OUTPUT</span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <div class="d-flex flex-column bg-body rounded m-1 shadow p-3">
                                                    <span class="secondary-text text-center" style="font-size: 180%">500 PCS</span>
                                                    <span class="primary-text text-center">STANDARD OUTPUT</span>
                                                </div>
                                                <div class="d-flex flex-column bg-body rounded m-1 shadow p-3">
                                                    <span class="secondary-text text-center" style="font-size: 180%">450 PCS</span>
                                                    <span class="primary-text text-center">ACTUAL OUTPUT</span>
                                                </div>
                                                <div class="d-flex flex-column bg-body rounded m-1 shadow p-3">
                                                    <span class="secondary-text text-center" style="font-size: 180%">750 PCS</span>
                                                    <span class="primary-text text-center">VARIANCES</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-3">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTION ELAPSED TIME
                                    </div>
                                    <div class="card-body">
                                        <div class="float-end">
                                            <div class="p-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="d-flex mt-2">
                                                            <div class="me-2" style="width:15px; background-color:#c4c4c4"></div>
                                                            <span class="primary-text" style="font-size: 60%">NO PRODUCTION</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex mt-2">
                                                            <div class="me-2" style="width:15px; background-color:#c62828"></div>
                                                            <span class="primary-text" style="font-size: 60%">DOWNTIME</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex mt-2">
                                                            <div class="me-2" style="width:15px; background-color:#800080"></div>
                                                            <span class="primary-text" style="font-size: 60%">BREAK</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex mt-2">
                                                            <div class="me-2" style="width:15px; background-color:#03941a"></div>
                                                            <span class="primary-text" style="font-size: 60%">RUNNING PRODUCTION</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex mt-2">
                                                            <div class="me-2" style="width:15px; background-color:#ffa000"></div>
                                                            <span class="primary-text" style="font-size: 60%">DIE CHANGE</span>
                                                        </div>
                                                    </div>


                                                    
                                                </div>
                                                <div class="w-100" style="height:150px;">
                                                    <canvas class="mb-3" id="timeline_chart" ></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>



@endsection

@section('scripts')
    @parent
    <script>
        var rejectChart = {
            chartCanvasID: 'reject_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['REJECT SETTING','REJECT PROCESS', 'REJECT MATERIAL'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Count',
                            backgroundColor: '#58006f',
                            borderColor: '#58006f',
                            data: [91,43,47],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: true,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var top10Chart = {
            chartCanvasID: 'top_10_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['PART DEFORM','PART BURN', 'PART OUT OF TOLERANCE', 'PART RUSTY', 'HOLE OFFSET', 'OVER CUTTING', 'MISS SETTING', 'SCRATCH', 'HUMP', 'DENTED'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Count',
                            backgroundColor: '#ffa000',
                            borderColor: '#ffa000',
                            data: [98,55,55,55, 50, 30, 23, 15, 10, 10],
                        }
                    ],
                },
                options: {
                    aspectRatio: 5,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var downtimeChart = {
            chartCanvasID: 'downtime_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['DIE CHANGE','MACHINE DOWNTIME', 'HUMAN MATERIAL'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Count',
                            backgroundColor: ['#ffa000', '#c62828', '#303f9f'],
                            borderColor: ['#ffa000', '#c62828', '#303f9f'],
                            data: [30,68,30],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: true,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var top10down_chart = {
            chartCanvasID: 'top10down_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['EMERGENCY','WIP WAITING', 'AUTOLOADER #1', 'ROBOT #1', 'ROBOT #2', 'LIFTER #1', 'PRESS #1', 'OTHER', 'AUTOLOADER #2', 'LIFTER #1'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Count',
                            backgroundColor: ['#c62828', '#303f9f', '#c62828', '#c62828','#c62828','#c62828','#c62828','#303f9f','#c62828','#c62828'],
                            borderColor: ['#c62828', '#303f9f', '#c62828', '#c62828','#c62828','#c62828','#c62828','#303f9f','#c62828','#c62828'],
                            data: [33,25,21,20,10,10,10,5,4,3],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var p_day_chart = {
            chartCanvasID: 'p_day_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['8AM-9AM','9AM-10AM', '10AM-11AM', '11AM-12PM', '12PM-1PM', '1PM-2PM', '2PM-3PM', '3PM-4PM', '4PM-5PM', '5PM-6PM'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Line 1',
                            backgroundColor: '#a3a3a3',
                            borderColor: '#a3a3a3',
                            data: [300,100,600, 770, 560, 50,500, 750, 560, 150],
                        },
                        {
                            type: 'bar',
                            label: 'Line 2',
                            backgroundColor: '#000080',
                            borderColor: '#000080',
                            data: [700,20,370,900, 200, 100, 850, 450, 970,300],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var p_night_chart = {
            chartCanvasID: 'p_night_chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['8PM-9PM','9PM-10PM', '10PM-11PM', '11PM-12AM', '12AM-1AM', '1AM-2AM', '2AM-3AM', '3AM-4AM', '4AM-5AM', '5AM-6AM'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Line 1',
                            backgroundColor: '#1b0c4d',
                            borderColor: '#1b0c4d',
                            data: [300,100,600, 770, 560, 50,500, 750, 560, 150],
                        },
                        {
                            type: 'bar',
                            label: 'Line 2',
                            backgroundColor: '#35d5d0',
                            borderColor: '#35d5d0',
                            data: [700,20,370,900, 200, 100, 850, 450, 970,300],
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var oee_trend_chart = {
            chartCanvasID: 'oee_trend_chart',
            chartConfig: {
                type: 'line',
                data: {
                    labels: ['8AM-9AM','9AM-10AM', '10AM-11AM', '11AM-12PM', '12PM-1PM', '1PM-2PM', '2PM-3PM', '3PM-4PM', '4PM-5PM', '5PM-6PM', '6PM-7PM', '7PM-8PM'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [
                        {
                            type: 'line',
                            label: 'ACTUAL OEE',
                            borderColor: '#800000',
                            backgroundColor: 'linear-gradient(#d8d9dd, #000080);0',
                            fill: false,
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4,
                            data: [45,15,29,23, 94, 40,97, 1, 19, 73, 49, 39],
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: 90,
                                    yMax: 90,
                                    borderColor: 'rgb(255, 99, 132)',
                                    borderWidth: 2,
                                }
                            }
                        },
                    }
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                
                this.chart = new Chart(ctx, this.chartConfig);
            },
        };

        var timeFormat = 'HH:mm:ss';
        var timeline_chart = {
            chartCanvasId: 'timeline_chart',
            chartConfig : {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Idle',
                        backgroundColor: '#c4c0c0',
                        borderColor: '#c4c0c0',
                        fill: true,
                        borderWidth:0,
                        stepped: true,
                        pointRadius: 0,
                        data: [{x:'00:00:00',y:1},{x:'00:01:00',y:0},{x:'00:01:30',y:0},{x:'00:03:00',y:0},{x:'07:04:00',y:0}]
                    },{
                        label: 'Run',
                        backgroundColor: '#0BDA51',
                        borderColor: '#0BDA51',
                        fill: true,
                        borderWidth:0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    },{
                        label: 'Down',
                        backgroundColor: '#ff3232',
                        borderColor: '#ff3232',
                        fill: true,
                        borderWidth:0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            ticks: {
                                autoSkip: true,
                                autoSkipPadding: 50,
                                fontColor: 'black',
                                maxRotation: 0,
                                minRotation: 0
                            },
                            min: '00:00:00',
                            max: '24:00:00',
                            time: {
                                parser: timeFormat,
                                tooltipFormat: 'HH:mm:ss'
                            },
                            scaleLabel: {
                                display: false,
                                labelString: 'Time'
                            },
                            gridLines: {
                                display: true ,
                                color: "#ecc94b"
                            },
                        },
                        y: {
                            display: false,
                            gridLines: {
                                drawOnChartArea: false,
                                display: false,
                            }
                        }
                    },
                    legend: {
                        labels: {
                            fontColor: 'black'
                        }
                    },
                }
            },
            initChart: function(){
                const ctx = document.getElementById(this.chartCanvasId).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };
        

        $(document).ready(function() {
            rejectChart.initChart();
            top10Chart.initChart();
            downtimeChart.initChart();
            top10down_chart.initChart();
            p_day_chart.initChart();
            p_night_chart.initChart();
            oee_trend_chart.initChart();
            timeline_chart.initChart();
            $('#dtProductionPlanning').DataTable( {
                dom: 'flrtp',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10', '25', '50', 'All' ]
                ],
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 9, targets: -1 }
                ]
            } );

            $('#dt2').DataTable( {
                dom: 'rt',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10', '25', '50', 'All' ]
                ],
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 9, targets: -1 }
                ]
            } );
            
        });
    </script>
@endsection