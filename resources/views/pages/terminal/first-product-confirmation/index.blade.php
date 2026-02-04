@extends('layouts.terminal')
@include('components.terminal.break-resume')
@include('components.terminal.numpad-modal')

@section('head')
@parent
<style>
    /* start fakhrul */
    :root {
        font-size: 1vw;
    }

    .btn:hover {
        color: white !important;
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

    .content-sub-title {
        font-size: 1rem;
    }

    .content-sub-title span {
        font-weight: bold;
    }

    .btn-terminal {
        font-size: 0.8rem;
        color: white;
        font-weight: bold;
        width: 10rem;
        height: 3rem;
        box-shadow: 1px 1px 7px black;
        display: flex;
        justify-content: center;
        align-items: center;
        /*border: 0.1rem solid #FFFFFF;*/
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
    }

    .terminal-link.active {
        color: white !important;
        background-color: #000080 !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        opacity: 100%;
    }

    .production-info-container {
        border: 0.1rem solid #626162 !important;
        height: 31.25rem;
    }

    th.box-title {
        width: 18.4375rem;
    }

    .table-container {
        height: 100%;
        overflow: hidden;
        width: 38.875rem;
    }

    .box-title {
        background-color: #000080 !important;
        color: #FFFFFF;
        border: 0.05208rem solid #626162;
        padding: 0.78125rem;
        text-align: center;
        vertical-align: middle;
        font-weight: 500;
    }

    .box-content {
        color: #626162;
        border: 0.05208rem solid #626162;
        padding: 0.78125rem;
        text-align: center;
        vertical-align: middle;
        font-weight: 500;
    }

    .title-rejset {
        border-bottom: 0.05208rem solid #626162;
        text-align: center;
        padding: 0.2604rem;
        font-weight: bold;
    }

    .box-rejectsetfi {
        border: 0.05208rem solid #000000;
        height: 350px;
        display: flex;
        align-items: center;
        padding: 15px;
    }

    .reject-container {
        border: 0.05208rem solid #626162;
        width: 39.27rem;
    }

    table {
        border-spacing: 0.1042rem;
    }

    table,
    td,
    th,
    tr {
        border: 0.05208rem solid #c7c7c7;
    }

    .table>:not(caption)>*>* {
        border-bottom-width: 0.05208rem !important;
    }

    .table>:not(:first-child) {
        border-top: 0.10417rem solid;
    }

    .reject-box {
        width: 18rem;
        height: 3.4rem;
        font-weight: 500;
    }

    /* end fakhrul */


    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .text-pst {

        background-color: #EFB45C;
        border: 0.052083rem solid #414141;
        text-align: center;
        color: #FFFFFF;
        width: 10rem;
        font-size: 1rem;
        padding: 0.5rem;
    }

    .countd-pst {
        background-color: #CCCCCC;
        border: 0.052083rem solid #414141;
        text-align: center;
        color: #000000;
        width: 5.2083rem;
        font-size: 1rem;
        padding: 0.5rem;
    }

    .countu-pst {
        background-color: #FE5F6D;
        border: 0.052083rem solid #414141;
        color: #FFFFFF;
        width: 100%;
        height: 2.4479rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.61458rem;
        padding: 1.7rem;
    }

    .detail-rejsetcu {
        padding: 0.3646rem;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #CDCDCD;
        /* height: 60px; */
        font-weight: 500;
    }

    .detail-rejsetin {
        padding: 0.3646rem;
        border: 0.1042rem solid #CDCDCD;
        text-align: center;
        width: 5.365rem;
        /* height: 60px; */
        display: flex;
        justify-content: center;
        align-items: center;
        border-right: 0px;
        font-weight: 500;
    }

    .plus-custom {
        width: 1.5625rem;
        /* height: 30px; */
        border: 0.1042rem solid #CDCDCD;
        border-radius: 0px 0.15625rem 0px 0px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-bottom: 0px;
        flex-basis: 100%;
        font-weight: 500;
    }

    .minus-custom {
        width: 1.5625rem;
        /* height: 30px; */
        border: 0.1042rem solid #CDCDCD;
        border-radius: 0px 0px 0.15625rem 0px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-basis: 100%;
        font-weight: 500;
    }

    .cus-conf {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-conf {
        background-color: #1C008A;
        border-color: #1C008A;
    }

    .btn-conf:hover {
        background-color: #2800C8;
        border-color: #2800C8;
    }


    .blinking-countup {
        animation: blink-animation 1s step-start 0s infinite;
        -webkit-animation: blink-animation 1s step-start 0s infinite;
    }

    @keyframes blink-animation {
        50% {
            background-color: red;
        }
    }

    @-webkit-keyframes blink-animation {
        50% {
            background-color: red;
        }
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
        background-color: #000080;
        border-radius: 1rem;
        /* background-size: cover; */
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
</style>
@endsection

@section('body')
<main class="px-3 py-2">
    {{-- fakhrul --}}

    {{-- page title, countdown timer, act button --}}
    <div class="d-flex justify-content-between align-items-center">
        {{-- page title, act button --}}
        <div class="d-flex flex-column">
            {{-- page-title --}}
            <div class="d-flex flex-column">
                <div class="content-title">DIE CHANGE : FIRST PRODUCT CONFIRMATION</div>
                <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                    <span>WORK CENTRE</span>
                    <i class='fa-duotone fa-caret-right'></i>
                    <span style="color: #000080;">{{ $workCenter->name }}</span>
                </div>
            </div>

            {{-- act button --}}
            <div class="d-flex gap-4 mt-3">

                <button type="button" class="btn btn-terminal" style="background-color: #008000" data-bs-toggle="modal" data-bs-target="#start-production-modal">
                    <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem; letter-spacing: 0.5rem;" class="fa-solid fa-play"></i><span style="letter-spacing: 0.03rem;" class="flex-fill">START<br>PRODUCTION</span></div>
                </button>

                <button type="button" class="btn btn-terminal" style="background-color: #FF3A4C" data-bs-toggle="modal" data-bs-target="#cancel-first-product-confirmation-modal">
                    <div class="d-flex gap-2 align-items-center"><i style="font-size: 1.3rem; letter-spacing: 0.5rem;" class="fa-regular fa-xmark"></i><span style="letter-spacing: 0.03rem;" class="flex-fill">CANCEL ALL<br>PLANNING</span></div>
                </button>

            </div>
        </div>

        {{-- countdown timer --}}
        <div class="d-flex gap-3">
            @yield('terminal-break-button')

            <div class="d-flex flex-column">
                <div class="d-flex">
                    <div class="text-pst">Planned Setup Time</div>
                    <div class="countd-pst live-downtime-timer" data-tag="plan_die_change" data-format="timer_full" data-process="countdown" data-countdown="{{ $production->setup_time }}">&nbsp;</div>
                </div>
                <div class="countu-pst flex-fill unplan-die-change live-downtime-timer" data-tag="unplan_die_change" data-format="timer_full">&nbsp;</div>
            </div>
        </div>

    </div>

    {{-- line selection, production info, reject --}}
    <div class="d-flex flex-column mt-5">
        {{-- line selection --}}
        <ul class="nav nav-tabs">
            @foreach($productionLines as $productionLine)
            <li class="nav-item" type="button" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="{{ $productionLine->id }}">
                <div class="terminal-link" data-production-line-id="{{ $productionLine->id }}">LINE {{ $productionLine->line_no }}</div>
            </li>
            @endforeach
        </ul>

        {{-- production info, reject --}}
        <div class="d-flex p-4 gap-2 production-info-container">
            {{-- production info --}}
            <div class="flex-fill" style="flex-basis: 100%;">
                <div class="table-container">
                    <table class="table w-100 h-100 overflow-hidden">
                        <tbody>
                            <tr>
                                <th class="box-title">PRODUCTION ORDER</th>
                                <td class="box-content current-production-line-data production-order-data" data-tag="order_no">&nbsp;</td>
                            </tr>
                            <tr>
                                <th class="box-title">PART NO</th>
                                <td class="box-content current-production-line-data part-data" data-tag="part_no">&nbsp;</td>
                            </tr>
                            <tr>
                                <th class="box-title">PART NAME</th>
                                <td class="box-content current-production-line-data part-data" data-tag="name">&nbsp;</td>
                            </tr>
                            <tr>
                                <th class="box-title">STANDARD OUTPUT (PCS)</th>
                                <td class="box-content current-production-line-data live-production-line-data" data-tag="standard_output">&nbsp;</td>
                            </tr>
                            <tr>
                                <th class="box-title">ACTUAL OUTPUT (PCS)</th>
                                <td class="box-content current-production-line-data live-production-line-data" data-tag="actual_output">&nbsp;</td>
                            </tr>
                            <tr>
                                <th class="box-title">ACTUAL REJECT (PCS)</th>
                                <td class="box-content current-production-line-data live-production-line-data" data-tag="reject_count">&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>




            {{-- reject --}}
            <div class="d-flex flex-column reject-container">
                <div class="title-rejset">REJECT SETTING</div>

                <div class="p-3 d-flex flex-column justify-content-between flex-fill">

                    {{-- maintenance --}}
                    <div class="d-flex justify-content-between mt-3 gap-5">
                        <div class="box-title reject-box">
                            MAINTENANCE SETTING
                        </div>
                        <div class="detail-rejsetcu flex-fill current-production-line-data reject-item-count" data-tag="maintenance">
                            &nbsp;
                        </div>
                        <div class="d-flex" style="color:#414141">
                            <div class="detail-rejsetin pending-reject-count" data-tag="maintenance" style="cursor: pointer" onclick="showNumpadModal(this, 'PENDING REJECT COUNT', 1)">
                                &nbsp;
                            </div>
                            <div class=" d-flex flex-column">
                                <div class="plus-custom" type="button" onclick="increaseReject(this)" data-tag="maintenance">
                                    <div class="plustext-custom">+</div>
                                </div>
                                <div class="minus-custom" type="button" onclick="decreaseReject(this)" data-tag="maintenance">
                                    <div class="minustext-custom">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- quality --}}
                    <div class="d-flex justify-content-between mt-3 gap-5">
                        <div class="box-title reject-box">
                            QUALITY SETTING
                        </div>
                        <div class="detail-rejsetcu flex-fill current-production-line-data reject-item-count" data-tag="quality">
                            &nbsp;
                        </div>
                        <div class="d-flex" style="color:#414141">
                            <div class="detail-rejsetin pending-reject-count" data-tag="quality" style="cursor: pointer" onclick="showNumpadModal(this, 'QUALITY REJECT COUNT', 1)">
                                &nbsp;
                            </div>
                            <div class="d-flex flex-column">
                                <div class="plus-custom" type="button" onclick="increaseReject(this)" data-tag="quality">
                                    <div class="plustext-custom">+</div>
                                </div>
                                <div class="minus-custom" type="button" onclick="decreaseReject(this)" data-tag="quality">
                                    <div class="minustext-custom">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- total --}}
                    <div class="d-flex justify-content-between mt-3 gap-5">
                        <div class="box-title reject-box">
                            TOTAL REJECT SETTING
                        </div>
                        <div class="detail-rejsetcu flex-fill me-6 current-production-line-data reject-group-count" data-reject-group-id="1">
                            &nbsp;
                        </div>
                    </div>

                    {{-- button submit --}}
                    <div class="d-flex justify-content-center mt-3">
                        <button onclick="submitRejectSetting(this)" type="button" class="btn btn-terminal current-production-line-data" style="background-color: #1C008A">
                            <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem" class="fa-solid fa-hexagon-check"></i><span style="letter-spacing: 0.10417rem;" class="flex-fill">CONFIRM</span></div>
                        </button>
                    </div>

                </div>
            </div>
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

{{-- modal start production --}}
<div class="modal fade" id="start-production-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- icon --}}
                <i style="color:#eed202" class="fa-regular fa-triangle-exclamation"></i>

                {{-- message --}}
                <span>CONFIRM TO<br>START PRODUCTION?</span>

                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button" onclick="startProduction()">YES</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal cancel die change --}}
<div class="modal fade" id="cancel-first-product-confirmation-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- icon --}}
                <i style="color:#eed202" class="fa-regular fa-triangle-exclamation"></i>

                {{-- message --}}
                <span>CONFIRM TO CANCEL<br>FIRST PRODUCT CONFIRMATION?</span>

                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button" onclick="cancelFirstProductConfirmation()">YES</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
@include('snippets.live-production-scripts')

{{-- script modal --}}
<script>
    $(() => {
        $('.plan-die-change').data('render', function(e, val) {
            // console.log("render called", e, val);
            if (LivePage.liveProduction.currentSummary.downtimes.unplan_die_change.total && !dieChangeExpired) {
                dieChangeExpired = true;
                $('.unplan-die-change').addClass('blinking-countup');
            }
            return val;
        })

        LivePage.initializeProductionLineTab(function(e) {
            currentProductionLineChanged(e);
        });
        /*
        .listenAnyChanges(function(e) {
            if (e.downtimes.unplan_die_change.total && !dieChangeExpired) {
                $('.unplan-die-change').addClass('blinking-countup');
                dieChangeExpired = true;
            }
        });
        */


    });

    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
            checkValidTerminalStatus();
        });

    const pageValidStatus = [2]; //Status First Confirmation Only
    function checkValidTerminalStatus() {
        if (!pageValidStatus.includes(LivePage.terminalData.workCenter.status)) {
            location.reload();
            return;
        }
    }


    function currentProductionLineChanged(e) {
        $('.current-production-line-data').data('production-line-id', e.id);

        $('.terminal-link').removeClass('active');
        $(`.terminal-link[data-production-line-id="${e.id}"`).addClass('active');
        LivePage.updateLiveData();
        updateUncommitRejectSettingsCounter();
    }
</script>
<script>
    /** Start Reject Setting */
    var pendingPayloads = {};


    function increaseReject(sender) {
        if (!LivePage.tabCurrentProductionLine)
            return;

        let tag = $(sender).data('tag');

        if (!tag)
            return;

        if (!pendingPayloads[LivePage.tabCurrentProductionLine.id])
            pendingPayloads[LivePage.tabCurrentProductionLine.id] = {};

        if (!pendingPayloads[LivePage.tabCurrentProductionLine.id][tag])
            pendingPayloads[LivePage.tabCurrentProductionLine.id][tag] = 0;


        let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count;

        let otherUncommitted = 0;
        Object.entries(pendingPayloads[LivePage.tabCurrentProductionLine.id]).forEach(([key, value]) => {
            if (key != tag)
                otherUncommitted += value;
        });

        if ((okPart - otherUncommitted) > pendingPayloads[LivePage.tabCurrentProductionLine.id][tag])
            pendingPayloads[LivePage.tabCurrentProductionLine.id][tag]++;

        updateUncommitRejectSettingsCounter();
        return;
    }

    function decreaseReject(sender) {
        if (!LivePage.tabCurrentProductionLine)
            return;
        let tag = $(sender).data('tag');

        if (!tag)
            return;

        if (!pendingPayloads[LivePage.tabCurrentProductionLine.id])
            pendingPayloads[LivePage.tabCurrentProductionLine.id] = {};

        if (!pendingPayloads[LivePage.tabCurrentProductionLine.id][tag])
            pendingPayloads[LivePage.tabCurrentProductionLine.id][tag] = 0;

        if (pendingPayloads[LivePage.tabCurrentProductionLine.id][tag] > 0)
            pendingPayloads[LivePage.tabCurrentProductionLine.id][tag]--;

        updateUncommitRejectSettingsCounter();
    }

    function updateUncommitRejectSettingsCounter() {
        if (!LivePage.tabCurrentProductionLine)
            return;


        $('.pending-reject-count').each((idx, e) => {
            let tag = $(e).data('tag');
            if (!tag)
                return;
            let value = 0;
            let productionLineId = LivePage.tabCurrentProductionLine.id;
            if (pendingPayloads[productionLineId] && pendingPayloads[productionLineId][tag])
                value = pendingPayloads[productionLineId][tag];

            LivePage.updateDomContent(e, value);
        });
    }
    /** End Reject Setting */
</script>

<script>
    /** Start Page Actions */
    function startProduction() {
        $.post("{{ route('terminal.first-product-confirmation.set.start-production',[ $plant->uid, $workCenter->uid ]) }}", {
                _token: window.csrf.getToken()
            },
            function(response, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    location.reload();
                } else {
                    alert(response.message);
                    location.reload();
                }
            }).always(function() {
            submitting = false;
        });
    }

    function cancelFirstProductConfirmation() {
        $.post("{{ route('terminal.first-product-confirmation.set.cancel-confirmation',[ $plant->uid, $workCenter->uid ]) }}", {
                _token: window.csrf.getToken()
            },
            function(response, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    location.reload();
                } else {
                    alert(response.message);
                    location.reload();
                }
            }).always(function() {
            submitting = false;
        });
    }

    function submitRejectSetting(sender) {


        let productionLineId = $(sender).data('production-line-id');
        if (!productionLineId)
            return;
        let pendingPayload = pendingPayloads[productionLineId];
        if (!pendingPayload)
            return;

        let maintenanceCount = pendingPayload.maintenance;
        let qualityCount = pendingPayload.quality;

        if (!maintenanceCount)
            maintenanceCount = 0;

        if (!qualityCount)
            qualityCount = 0;

        if (maintenanceCount == 0 && qualityCount == 0)
            return;
        /*
        {
            "production_line_id" : <production_line_id>
            "maintenance_count": <maintenance reject count>
            "quality_count": <quality reject count>
        }
        */
        $payload = {
            _token: window.csrf.getToken(),
            production_line_id: productionLineId,
            maintenance_count: maintenanceCount,
            quality_count: qualityCount
        };
        
        $.post("{{ route('terminal.first-product-confirmation.set.reject-settings',[ $plant->uid, $workCenter->uid ]) }}", $payload,
            function(response, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;


                if (response.result === RESULT_OK) {
                    //clear pendings
                    let pendingPayload = pendingPayloads[productionLineId];
                    if (!pendingPayload)
                        return;

                    pendingPayload.maintenance = 0;
                    pendingPayload.quality = 0;
                    updateUncommitRejectSettingsCounter();

                } else {
                    //TODO: display error message in modal
                    alert(response.message);
                }
            }).always(function() {
            submitting = false;
        });
    }
    /** End Page Actions */
</script>

@endsection