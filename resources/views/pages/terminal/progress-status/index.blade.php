@extends('layouts.terminal')
@include('components.terminal.break-resume')
@include('components.terminal.auto-redirect-downtime')
@section('head')
    @parent
    <meta name="google" content="notranslate" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>


    <style>
        :root {
            font-size: 1vw;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        main {
            height: calc(100vh - 2.4rem);
            width: calc(100vw - 10.416rem);
            background-color: white;
            overflow-x: hidden;
        }

        .content-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .hidden {
            display: none;
        }

        .content-sub-title {
            font-size: 1rem;
        }

        .content-sub-title span {
            font-weight: bold;
        }

        .btn-terminal {
            font-size: 0.9rem;
            color: white;
            font-weight: bold;
            width: 14rem;
            height: 3rem;
            box-shadow: 1px 1px 7px black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            color: white !important;
        }

        .terminal-link {
            color: #5E5E5E !important;
            font-size: 110%;
            font-weight: 600;
            text-decoration: none;
            background-color: #CCCCCC !important;
            border: 0.05208rem solid #626162 !important;
            opacity: 50%;
            display: block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            margin-bottom: -0.12rem;
            width: 8rem;
        }

        .terminal-link.active {
            color: white !important;
            background-color: #000078 !important;
            border-color: #dee2e6 #dee2e6 #fff !important;
            opacity: 100%;
        }

        .icon-complete {
            width: 1.5625rem;
            height: 1.5625rem;
            border-radius: 50%;
            background-color: #52AF61;
            color: white;
            text-align: center;
        }

        .icon-over {
            width: 1.5625rem;
            height: 1.5625rem;
            border-radius: 50%;
            background-color: rgba(255, 127, 0, 0.7);
            color: white;
            text-align: center;
        }

        .icon-short {
            width: 1.5625rem;
            height: 1.5625rem;
            border-radius: 50%;
            background-color: #FAE184;
            color: white;
            text-align: center;
        }

        .part-detail {
            font-weight: bold;
            line-height: 1;
        }

        .part-detail span:last-child {
            color: #000078;
        }

        .title-text {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .center-ter {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .larger-no {
            font-size: 2rem;
        }

        .unit-text {
            font-size: 1rem;
            font-weight: bold;
            align-self: flex-end;
        }

        .production-info-container {
            border: 0.05208rem solid #626162;
            height: 43rem;
        }

        .production-detail {
            border: 0.05208rem solid #626162;
        }

        .linear-gradient-black-white {
            background-image: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(219, 219, 219, 0.5218));
        }

        .grid-center {
            display: grid;
            place-items: center;
        }

        .chart-value {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.302rem;
        }

        .positive-count {
            color: #03941A;
        }

        .negative-count {
            color: #F04857;
        }

        .nav-tabs {
            border-bottom: 0.05208rem solid #dee2e6;
        }

        .modal-button {
            color: white;
            font-weight: bold;
            font-size: 1rem;
            width: 8rem;
            height: 3rem;
            border-radius: 0.8rem;
            letter-spacing: 0.05rem;
        }

        .info-container {
            background-image: linear-gradient(to bottom, #ffffff, #f1f4ff, #dbeaff 5%, #bde3ff, #98ddff, #80d7fd, #63d1fb, #3acbf9, #28c3f8, #17baf8, #08b1f6, #0aa8f4);
        }

        .info-container span:first-child {
            font-weight: bold;
            font-size: 1.25rem;
            color: #2699C9;
            letter-spacing: .05rem;
        }

        .info-content {
            background-color: rgba(233, 233, 233, 0.32);
        }

        .info-content p {
            font-size: 0.9rem;
        }
    </style>

    <style>
        .terminal-modal-content .terminal-title-text {
            color: #eed202;
        }

        .terminal-modal-content .terminal-text {
            font-size: 0.8rem;
        }

        .terminal-modal-content {
            background-image: url("{{ asset('images/shapes-backgroundv2.png') }}");
            background-repeat: repeat-x;
            background-color: #000078;
            border-radius: 1rem;
        }

        .terminal-modal-content i {
            font-size: 12rem;
        }

        .terminal-modal-content span {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }

        .bcg-clr {
            border-radius: 0.5rem;
            height: 7rem;
        }

        .button-green-ldr {
            background-color: transparent;
            color: black;
        }

        .button-red-mtn {
            background-color: transparent;
            color: black;
        }

        .button-yellow-lgt {
            background-color: transparent;
            color: black;
        }

        .button-blue-qcc {
            background-color: transparent;
            color: black;
        }

        .button-green-ldr.active-button {
            background-color: yellowgreen !important;
        }

        .button-red-mtn.active-button {
            background-color: orangered !important;
        }

        .button-yellow-lgt.active-button {
            background-color: goldenrod !important;
        }

        .button-blue-qcc.active-button {
            background-color: dodgerblue !important;
        }
    </style>
@endsection

@section('body')
    <main class="px-3 py-2">

        {{-- page title, act button --}}
        <div class="d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center">
                {{-- page-title --}}
                <div class="d-flex flex-column">
                    <div class="content-title">PROGRESS STATUS</div>
                    <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                        <span>WORK CENTRE</span>
                        <i class='fa-duotone fa-caret-right'></i>
                        <span style="color: #000080;">{{ $workCenter->name }}</span>
                    </div>
                </div>

                {{-- act button --}}
                <div class="d-flex gap-4 mt-3">
                    @yield('terminal-break-button')
                    <button type="button" class="btn btn-terminal" style="background-color: #F00000" data-bs-toggle="modal" data-bs-target="#stop-production-modal">
                        <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem; margin-right: 0.5rem;" class="fa-light fa-stop"></i><span style="letter-spacing: 0.10417rem;" class="flex-fill">STOP PRODUCTION</span></div>
                    </button>
                </div>
            </div>
        </div>

        {{-- line selection, part detail, production detail --}}
        {{-- icon complete -> <i class="fa-solid fa-check"></i> --}}
        {{-- icon short    -> <i class="fa-solid fa-chevron-left"></i> --}}
        {{-- icon over     -> <i class="fa-solid fa-chevron-right"></i> --}}
        <div class="d-flex flex-row">
            <div class="d-flex flex-column mt-3 justify-content-between w-75">

                {{-- line selection --}}
                <ul class="nav nav-tabs">
                    @foreach ($productionLines as $productionLine)
                        <li class="nav-item" type="button" onclick="LivePage_switchProductionLineTab(this)" data-production-line-id="{{ $productionLine->id }}">
                            <div class="terminal-link" data-production-line-id="{{ $productionLine->id }}">
                                <div class="d-flex gap-3 align-items-center"><span>LINE {{ $productionLine->line_no }}</span>
                                    <div class="live-production-line-data plan-variance-icon" data-production-line-id="{{ $productionLine->id }}" data-tag="plan_variance"><i class="fa-solid fa-check "></i></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>                                              

                <div class="d-flex px-5 pt-3 pb-5 flex-column production-info-container">

                    {{-- part detail --}}
                    <div class="d-flex justify-content-between w-100">
                        <div class="d-flex gap-5">
                            <div class="d-flex flex-column part-detail">
                                <span>PART NAME</span>
                                <span class="current-production-line-data part-data" data-tag="name"></span>
                            </div>
                            <div class="d-flex flex-column part-detail">
                                <span>PART NUMBER</span>
                                <span class="current-production-line-data part-data" data-tag="part_no"></span>
                            </div>
                            <div class="d-flex flex-column part-detail">
                                <span>PRODUCTION ORDER</span>
                                <span class="current-production-line-data production-order-data" data-tag="order_no"></span>
                            </div>
                        </div>

                        <div role="button" style="font-size: 0.8rem; color: #22b8f6;" data-bs-toggle="modal" data-bs-target="#info-modal"><i class="fa-solid fa-circle-question fs-2"></i></div>
                    </div>

                    {{-- production detail --}}
                    <div class="production-detail d-flex flex-column mt-3">
                        {{-- 1st row --}}
                        <div class="d-flex">
                            <div class="d-flex flex-column" style="flex-basis: 100%;border: 0.05208rem solid #626162;">
                                <div class="d-flex justify-content-center">
                                    <span class="title-text">PRODUCTION PLAN <span class="unit-text">(PCS)</span></span>
                                </div>
                                <span class="title-text larger-no text-center current-production-line-data live-production-line-data" data-tag="plan_quantity">-</span>
                            </div>
                            <div class="d-flex flex-column" style="flex-basis: 100%;border: 0.05208rem solid #626162;">
                                <div class="d-flex justify-content-center">
                                    <span class="title-text">STANDARD PLAN <span class="unit-text part-data">(PCS)</span></span>
                                </div>
                                <span class="title-text larger-no text-center current-production-line-data live-production-line-data" data-tag="standard_output">-</span>
                            </div>
                        </div>
                        {{-- 2nd row --}}
                        <div class="d-flex justify-content-between linear-gradient-black-white py-2" style="border: 0.05208rem solid #626162;">
                            <div class="d-flex justify-content-center center-ter" style="flex-basis: 100%">
                                <span class="title-text">ACTUAL OUTPUT <span class="unit-text">(PCS)</span></span>
                            </div>
                            <span class="title-text larger-no text-center current-production-line-data live-production-line-data" data-tag="actual_output" style="flex-basis: 100%">-</span>
                        </div>

                        {{-- 3rd row --}}
                        <div class="d-flex justify-content-between" style="text-align: center">
                            <div class="d-flex flex-column py-2" style="flex-basis: 100%;border: 0.05208rem solid #626162;background-color: #52AF61">
                                <div class="d-flex flex-column justify-content-center" style="line-height: 1;">
                                    <span class="title-text">OK PART</span>
                                    <span class="unit-text" style="align-self: center !important">(PCS)</span>
                                </div>
                                <span class="title-text larger-no current-production-line-data live-production-line-data" data-tag="ok_count">-</span>
                            </div>
                            <div class="d-flex flex-column py-2" style="flex-basis: 100%;border: 0.05208rem solid #626162;background-color: #FE5F6D">
                                <div class="d-flex flex-column justify-content-center" style="line-height: 1;">
                                    <span class="title-text">NG PART</span>
                                    <span class="unit-text" style="align-self: center !important">(PCS)</span>
                                </div>
                                <span class="title-text larger-no current-production-line-data live-production-line-data" data-tag="reject_count">-</span>
                            </div>

                            <div class="d-flex flex-column py-2" style="flex-basis: 100%;border: 0.05208rem solid #626162;background-color: #CEE5EA">
                                <div class="d-flex flex-column justify-content-center" style="line-height: 1;">
                                    <span class="title-text">PENDING PART</span>
                                    <span class="unit-text" style="align-self: center !important">(PCS)</span>
                                </div>
                                <span class="title-text larger-no current-production-line-data live-production-line-data" data-tag="pending_count">-</span>
                            </div>
                        </div>
                        {{-- 4th row --}}
                        <div class="d-flex justify-content-between linear-gradient-black-white p-2" style="border: 0.05208rem solid #626162;">
                            <div class="d-flex justify-content-center" style="flex-basis: 100%">
                                <span class="title-text my-auto">REQUIRED OUTPUT <span class="unit-text">(PCS)</span></span>
                            </div>
                            <div class="d-flex" style="flex-basis: 100%">
                                <span class="title-text larger-no text-end my-auto current-production-line-data live-production-line-data" data-tag="plan_variance" style="flex-basis: 100%">-</span>
                                <div class="d-flex flex-column align-items-end" style="flex-basis: 100%">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <div class="current-production-line-data live-production-line-data plan-variance-icon" data-tag="plan_variance"><i class="fa-solid fa-check"></i></div>
                                        <span class="unit-text current-production-line-data live-production-line-data plan-variance-text" data-tag="plan_variance">COMPLETED</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between py-2" style="border: 0.05208rem solid #626162;background-color: #FF707D">
                            <div class="d-flex center-ter justify-content-center" style="flex-basis: 100%">
                                <span class="title-text">TOTAL DOWNTIME</span>
                            </div>
                            <span class="title-text larger-no text-center current-production-line-data live-downtime-timer" data-tag="unplan" data-format="timer_full" style="flex-basis: 100%">-</span>
                        </div>

                        <div class="d-flex">
                            <div class="d-flex flex-column py-2 align-items-center" style="flex-basis: 33%;border: 0.05208rem solid #626162;">
                                <div class="fontwcproci input-pr" style="position:relative;height: 100%;padding: 0 !important; width: 100%;">
                                    <canvas id="chart_toee" style="height:100%;"></canvas>
                                    <div class="chart-value">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fa-solid fa-caret-up positive-count"></i>
                                            <span class="title-text larger-no"><span class="current-production-line-data live-production-line-data" data-tag="oee" data-format="percentage_rounded">- </span>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center title-text">OEE</div>
                            </div>
                            <div class="d-flex py-2 justify-content-around" style="flex-basis: 67%; border: 0.05208rem solid #626162;">
                                <div class="d-flex flex-column align-items-center" style="flex-basis: 100%">
                                    <div class="fontwcproci input-pr" style="position:relative;height: 100%;padding: 0 !important">
                                        <canvas id="chart_tava" style="height:100%;"></canvas>
                                        <div class="chart-value">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fa-solid fa-caret-up positive-count"></i>
                                                <span class="title-text larger-no"><span class="current-production-line-data live-production-line-data" data-tag="availability" data-format="percentage_rounded">- </span>%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center title-text">AVAILABILITY</div>
                                </div>

                                <div class="d-flex flex-column align-items-center" style="flex-basis: 100%">
                                    <div class="fontwcproci input-pr" style="position:relative;height: 100%;padding: 0 !important">
                                        <canvas id="chart_tper" style="height:100%;"></canvas>
                                        <div class="chart-value">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fa-solid fa-caret-up positive-count"></i>
                                                <span class="title-text larger-no"><span class="current-production-line-data live-production-line-data" data-tag="performance" data-format="percentage_rounded">- </span>%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center title-text">PERFORMANCE</div>
                                </div>

                                <div class="d-flex flex-column align-items-center" style="flex-basis: 100%">
                                    <div class="fontwcproci input-pr" style="position:relative;height: 100%;padding: 0 !important">
                                        <canvas id="chart_tqua" style="height:100%;"></canvas>
                                        <div class="chart-value">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fa-solid fa-caret-up positive-count"></i>
                                                <span class="title-text larger-no"><span class="current-production-line-data live-production-line-data" data-tag="quality" data-format="percentage_rounded">- </span>%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center title-text">QUALITY</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-start w-25 p-3" style="margin-top: 3rem !important; margin-left: 1.5rem !important;">
                <div class="d-flex justify-content-center">
                    <div class="d-flex justify-content-center center-ter" style="flex-basis: 100%">
                        <span class="title-text">ANDON CALLING</span>
                    </div>
                </div>
                <button class="d-flex justify-content-between center-ter linear-gradient-black-white p-2 mt-3 bcg-clr button-green-ldr" style="border: 0.05208rem solid #626162;" name="startLeader" onclick="startTimer('leader', this)">
                    <div class="d-flex justify-content-around center-ter" style="flex-basis: 100%">
                        <span class="title-text">LEADER </span>
                    </div>
                    <div id="newpostLeader" class="title-text larger-no text-center current-production-line-data live-production-line-data hidden" style="flex-basis: 100%">
                        <span id="hourLeader">00</span>:<span id="minuteLeader">00</span>:<span id="secondLeader">00</span>
                    </div>
                </button>
                <button class="d-flex justify-content-between center-ter linear-gradient-black-white p-2 mt-3 bcg-clr button-red-mtn" style="border: 0.05208rem solid #626162;" name="startMaintenance" onclick="startTimer('maintenance', this)">
                    <div class="d-flex justify-content-around center-ter bcg-clr-ld" style="flex-basis: 100%">
                        <span class="title-text">MAINTENANCE </span>
                    </div>
                    <div id="newpostMaintenance" class="title-text larger-no text-center current-production-line-data live-production-line-data hidden" style="flex-basis: 100%">
                        <span id="hourMaintenance">00</span>:<span id="minuteMaintenance">00</span>:<span id="secondMaintenance">00</span>
                    </div>
                </button>
                <button class="d-flex justify-content-between center-ter linear-gradient-black-white p-2 mt-3 bcg-clr button-yellow-lgt" style="border: 0.05208rem solid #626162;" name="startLogistic" onclick="startTimer('logistic', this)">
                    <div class="d-flex justify-content-around center-ter" style="flex-basis: 100%">
                        <span class="title-text">LOGISTIC </span>
                    </div>
                    <div id="newpostLogistic" class="title-text larger-no text-center current-production-line-data live-production-line-data hidden" style="flex-basis: 100%">
                        <span id="hourLogistic">00</span>:<span id="minuteLogistic">00</span>:<span id="secondLogistic">00</span>
                    </div>
                </button>
                <button class="d-flex justify-content-between center-ter linear-gradient-black-white p-2 mt-3 bcg-clr button-blue-qcc" style="border: 0.05208rem solid #626162;" name="startQccheck" onclick="startTimer('qccheck', this)">
                    <div class="d-flex justify-content-around center-ter" style="flex-basis: 100%">
                        <span class="title-text">QC CHECK </span>
                    </div>
                    <div id="newpostQccheck" class="title-text larger-no text-center current-production-line-data live-production-line-data hidden" style="flex-basis: 100%">
                        <span id="hourQccheck">00</span>:<span id="minuteQccheck">00</span>:<span id="secondQccheck">00</span>
                    </div>
                </button>
            </div>
        </div>
    </main>
@endsection

@section('modals')
    @parent
    {{-- modal warning --}}
    <div id="warning-modal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal stop production--}}
    <div class="modal fade" id="stop-production-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content terminal-modal-content">
                <div class="d-flex flex-column w-100 align-items-center p-4">
                    {{-- icon --}}
                    <i style="color:#eed202" class="fa-duotone fa-circle-stop"></i>
                    {{-- message --}}
                    <span>CONFIRM TO<br>STOP PRODUCTION?</span>

                    {{-- button --}}
                    <div class="d-flex justify-content-around w-100 mt-3">
                        <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button" data-bs-dismiss="modal" onclick="stopProduction()">YES</button>
                        <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal info --}}
    <div class="modal fade" id="info-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex flex-column w-100 align-items-center p-2 info-container">
                    {{-- title --}}
                    <span class="text-center">HELP<br>CENTER</span>

                    {{-- info-content --}}
                    <div class="d-flex flex-column info-content p-3">
                        {{-- complete info --}}
                        <div class="d-flex flex-column mt-2">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="d-flex justify-content-center align-items-center icon-complete"><i class="fa-solid fa-check"></i></div>
                                <span class="unit-text" style="align-self: center !important">COMPLETED</span>
                            </div>
                            <p>Indicates that the <strong>Production Plan equals</strong> to <strong>OK Part</strong>.
                                Production team can proceed to Stop Production if instructed by Production Supervisor</p>
                        </div>

                        {{-- short info --}}
                        <div class="d-flex flex-column mt-2">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="d-flex justify-content-center align-items-center icon-short"><i class="fa-solid fa-chevron-left"></i></div>
                                <span class="unit-text" style="align-self: center !important">SHORT</span>
                            </div>
                            <p>Indicates that the <strong>Production Plan less than OK Part</strong>. Production team need
                                to continue production output until the indicator turn to 'COMPLETED'</p>
                        </div>

                        {{-- over info --}}
                        <div class="d-flex flex-column mt-2">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="d-flex justify-content-center align-items-center icon-over"><i class="fa-solid fa-chevron-right"></i></div>
                                <span class="unit-text" style="align-self: center !important">OVER</span>
                            </div>
                            <p>Indicates that the <strong>Production Plan more than OK Part</strong>. Production team can
                                proceed to Stop Production if instructed by Production Supervisor</p>
                        </div>

                        {{-- button exit --}}
                        <div class="d-flex justify-content-center w-100 mt-3">
                            <button type="button" style="background-color: transparent; color:black !important;" class="btn p-2 modal-button" data-bs-dismiss="modal">EXIT</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @include('snippets.live-production-scripts')
    <script>
        // Call Modal Dialog with custom text
        function showConfirmationModal(message) {
            return new Promise(function(resolve, reject) {
                //duplicate the modal template and fill in the text
                var modal = $('#confirmation-modal').clone();
                modal.find('.modal-title').html(message);
                modal.find('.modal-body span').html(message);
                modal.modal('show');
                //wait for the user to click a button
                modal.on('click', '.modal-button', function(e) {
                    var result = $(e.target).data('dialog-result');
                    //delete clone modal and resolve promise
                    modal.remove();
                    if (result == 1)
                        resolve(true);
                    else
                        resolve(false);
                });
            });
        }
    </script>
    <script>
        var currentProductionLineDataConfig = [];

        function updateCurrentProductionLineConfigs(newProductionLineId) {
            currentProductionLineDataConfig.forEach(e => {
                e['production-line-id'] = newProductionLineId;
            });
        }

        function setConfigToCurrentLine(configData) {
            if (LivePage.tabCurrentProductionLine)
                configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

            currentProductionLineDataConfig.push(configData);
            return configData;
        }

        $(() => {
            //Plan Variance render callback
            $('.plan-variance-icon').data('render', function(e, val) {
                if (val > 0)
                    return '<div class="d-flex justify-content-center align-items-center icon-short"><i class="fa-solid fa-chevron-left"></i></div>'; // Short
                else if (val < 0)
                    return '<div class="d-flex justify-content-center align-items-center icon-over"><i class="fa-solid fa-chevron-right"></i></div>'; // Over
                else
                    return '<div class="d-flex justify-content-center align-items-center icon-complete"><i class="fa-solid fa-check"></i></div>'; // Complete
            });
            $('.plan-variance-text').data('render', function(e, val) {
                if (val > 0)
                    return 'SHORT'; // Short
                else if (val < 0)
                    return 'OVER'; // Over
                else
                    return 'COMPLETED'; // Complete
            });

            LivePage.initializeProductionLineTab(function(e) {
                currentProductionLineChanged(e);
            }).listenChanges(
                'live-production-line-data', //class
                setConfigToCurrentLine({
                    tag: 'oee'
                }), //Config
                updateOeeChart //callback
            ).listenChanges(
                'live-production-line-data', //class
                setConfigToCurrentLine({
                    tag: 'availability'
                }), //Config
                updateAvailabilityChart //callback
            ).listenChanges(
                'live-production-line-data', //class
                setConfigToCurrentLine({
                    tag: 'performance'
                }), //Config
                updatePerformanceChart //callback
            ).listenChanges(
                'live-production-line-data', //class
                setConfigToCurrentLine({
                    tag: 'quality'
                }), //Config
                updateQualityChart //callback
            );

            autoRedirectDowntime.initialize();
            fetchActiveCallingStates();
        });

        //Websocket
        Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
            .listen('.terminal.data-updated', (e) => {
                LivePage.terminalDataUpdatedHandler(e);
                checkValidTerminalStatus();
            });

        const pageValidStatus = [3]; //Status Running only
        function checkValidTerminalStatus() {
            if (!pageValidStatus.includes(LivePage.terminalData.workCenter.status)) {
                location.reload();
                return;
            }
        }

        function updateOeeChart(config, value, summary) {
            chart_oee.data.datasets[0].data[0] = value;
            chart_oee.data.datasets[0].data[1] = 1 - value;

            if (value > 0.64) { //green
                chart_oee.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                chart_oee.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                chart_oee.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }

            chart_oee.update();
        }

        function updateAvailabilityChart(config, value, summary) {
            chart_a.data.datasets[0].data[0] = value;
            chart_a.data.datasets[0].data[1] = 1 - value;

            if (value > 0.64) { //green
                chart_a.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                chart_a.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                chart_a.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }

            chart_a.update();
        }

        function updatePerformanceChart(config, value, summary) {
            chart_p.data.datasets[0].data[0] = value;
            chart_p.data.datasets[0].data[1] = 1 - value;

            if (value > 0.64) { //green
                chart_p.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                chart_p.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                chart_p.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }

            chart_p.update();
        }

        function updateQualityChart(config, value, summary) {
            chart_q.data.datasets[0].data[0] = value;
            chart_q.data.datasets[0].data[1] = 1 - value;

            if (value > 0.64) { //green
                chart_q.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                chart_q.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                chart_q.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }

            chart_q.update();
        }

        function currentProductionLineChanged(e) {
            $('.current-production-line-data').data('production-line-id', e.id);
            updateCurrentProductionLineConfigs(e.id);

            $('.terminal-link').removeClass('active');
            $(`.terminal-link[data-production-line-id="${e.id}"`).addClass('active');
            LivePage.updateLiveData();
        }
    </script>

    <script>
        var options_toee = {
            type: 'doughnut',
            data: {
                // labels: ["Red", "Pink"],
                datasets: [{
                    label: 'Average OEE',
                    data: [0, 1],
                    backgroundColor: ["#52AF61", "#E0E0E0"]
                }]
            },
            options: {
                rotation: 225, // start angle in degrees
                circumference: 270, // sweep angle in degrees
                cutout: '60%', // percentage of the chart that should be cut out of the middle
                // responsive: false,
                // maintainAspectRatio: false,
            }
        };

        var options_tava = {
            type: 'doughnut',
            data: {
                // labels: ["Red", "Pink"],
                datasets: [{
                    label: 'Availability',
                    data: [0, 1],
                    backgroundColor: ["#F04857", "#E0E0E0"]
                }]
            },
            options: {
                rotation: 225, // start angle in degrees
                circumference: 270, // sweep angle in degrees
                cutout: '60%', // percentage of the chart that should be cut out of the middle


            }
        };

        var options_tqua = {
            type: 'doughnut',
            data: {
                // labels: ["Red", "Pink"],
                datasets: [{
                    label: 'Quality',
                    data: [0, 1],
                    backgroundColor: ["#FFA000", "#E0E0E0"]
                }]
            },
            options: {
                rotation: 225, // start angle in degrees
                circumference: 270, // sweep angle in degrees
                cutout: '60%', // percentage of the chart that should be cut out of the middle

            }
        };

        var options_tper = {
            type: 'doughnut',
            data: {
                // labels: ["Red", "Pink"],
                datasets: [{
                    label: 'Performance',
                    data: [0, 1],
                    backgroundColor: ["#52AF61", "#E0E0E0"]
                }]
            },
            options: {
                rotation: 225, // start angle in degrees
                circumference: 270, // sweep angle in degrees
                cutout: '60%', // percentage of the chart that should be cut out of the middle

            }
        };

        var ctx_toee = document.getElementById('chart_toee').getContext('2d');
        var ctx_tava = document.getElementById('chart_tava').getContext('2d');
        var ctx_tqua = document.getElementById('chart_tqua').getContext('2d');
        var ctx_tper = document.getElementById('chart_tper').getContext('2d');

        var chart_oee = new Chart(ctx_toee, options_toee);
        chart_oee.canvas.parentNode.style.width = '10rem';

        var chart_a = new Chart(ctx_tava, options_tava);
        chart_a.canvas.parentNode.style.width = '10rem';

        var chart_q = new Chart(ctx_tqua, options_tqua);
        chart_q.canvas.parentNode.style.width = '10rem';

        var chart_p = new Chart(ctx_tper, options_tper);
        chart_p.canvas.parentNode.style.width = '10rem';
    </script>

    <script>
        function stopProduction() {
            //TODO: confirmation using modal
            showConfirmationModal("Confirm to stop production?").then((result) => {
                if (!result) {
                    return;
                } else {
                    $.post("{{ route('terminal.progress-status.set.stop-production', [$plant->uid, $workCenter->uid]) }}", {
                        _token: window.csrf.getToken()
                    }, function(data, status, xhr) {
                        //result code
                        const RESULT_OK = 0;
                        const RESULT_INVALID_STATUS = -1;
                        const RESULT_INVALID_PARAMETERS = -2;

                        //TODO: display error message in modal
                        if (data.result === RESULT_OK) {
                            location.reload();
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    });
                }
            });
        }

        "use strict";

        const plantUid = '{{ $plant->uid }}';
        const workCenterUid = '{{ $workCenter->uid }}';

        // Initialize timers object
        let timers = {};

        function fetchActiveCallingStates() {
            fetch(`{{ url('terminal') }}/${plantUid}/${workCenterUid}/progress-status/get-calling-states`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        data.data.forEach(calling => {
                            const button = document.querySelector(`[name="start${capitalizeFirstLetter(calling.type)}"]`);
                            if (button) {
                                button.classList.add('active-button');
                                const timerDiv = document.getElementById(`newpost${capitalizeFirstLetter(calling.type)}`);
                                if (timerDiv) {
                                    timerDiv.classList.remove('hidden');

                                    const startTime = moment.tz(calling.start_time, "UTC").tz("Asia/Kuala_Lumpur").toDate().getTime();
                                    const now = new Date().getTime();
                                    const diff = now - startTime;
                                    const hours = Math.floor(diff / (1000 * 60 * 60));
                                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                                    document.getElementById(`hour${capitalizeFirstLetter(calling.type)}`).innerText = hours.toString().padStart(2, '0');
                                    document.getElementById(`minute${capitalizeFirstLetter(calling.type)}`).innerText = minutes.toString().padStart(2, '0');
                                    document.getElementById(`second${capitalizeFirstLetter(calling.type)}`).innerText = seconds.toString().padStart(2, '0');

                                    timers[calling.type] = setInterval(() => { updateTimer(calling.type); }, 1000);
                                }
                            }
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function startTimer(type, button) {
            const timerDiv = document.getElementById(`newpost${capitalizeFirstLetter(type)}`);
            if (timerDiv && !timerDiv.classList.contains('hidden')) {
                stopTimer(type, button);
                return;
            }
            resetTimer(type);
            if (timerDiv) {
                timerDiv.classList.remove('hidden');
            }
            timers[type] = setInterval(() => { updateTimer(type); }, 1000);
            button.classList.add('active-button');

            fetch(`{{ url('terminal') }}/${plantUid}/${workCenterUid}/progress-status/start-calling-timer/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
        }

        function stopTimer(type, button) {
            clearInterval(timers[type]);
            const timerDiv = document.getElementById(`newpost${capitalizeFirstLetter(type)}`);
            if (timerDiv) {
                timerDiv.classList.add('hidden');
            }
            button.classList.remove('active-button');
            resetTimer(type);

            fetch(`{{ url('terminal') }}/${plantUid}/${workCenterUid}/progress-status/stop-calling-timer/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
        }

        function resetTimer(type) {
            const hourElem = document.getElementById(`hour${capitalizeFirstLetter(type)}`);
            const minuteElem = document.getElementById(`minute${capitalizeFirstLetter(type)}`);
            const secondElem = document.getElementById(`second${capitalizeFirstLetter(type)}`);
            if (hourElem) hourElem.innerText = '00';
            if (minuteElem) minuteElem.innerText = '00';
            if (secondElem) secondElem.innerText = '00';
        }

        function updateTimer(type) {
            const hourElem = document.getElementById(`hour${capitalizeFirstLetter(type)}`);
            const minuteElem = document.getElementById(`minute${capitalizeFirstLetter(type)}`);
            const secondElem = document.getElementById(`second${capitalizeFirstLetter(type)}`);

            if (!hourElem || !minuteElem || !secondElem) {
                console.error(`Timer elements not found for ${type}`);
                return;
            }

            let hours = parseInt(hourElem.innerText);
            let minutes = parseInt(minuteElem.innerText);
            let seconds = parseInt(secondElem.innerText);

            seconds++;
            if (seconds >= 60) {
                seconds = 0;
                minutes++;
            }
            if (minutes >= 60) {
                minutes = 0;
                hours++;
            }

            hourElem.innerText = hours.toString().padStart(2, '0');
            minuteElem.innerText = minutes.toString().padStart(2, '0');
            secondElem.innerText = seconds.toString().padStart(2, '0');
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endsection
