@extends('layouts.dashboard')
@include('components.commons.websocket')
@section('head')
    @parent

    <style>
        :root {
            /* font-size: 0.954vw; */
            font-size: 1vw;
        }

        main {
            display: flex;
            flex-direction: column;
            background-color: #205B84;
            overflow-x: hidden;
        }

        .row {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
        }

        .line-container {
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .production-detail-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
            grid-gap: 1rem;
        }

        .production-detail-data {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 1.7rem;
        }

        .product-detail-indicator {
            width: 0.8em;
            text-align: center;
        }





        .production-detail-label-odd {
            background-color: #9A003E;
            color: #FFFFFF;
            font-size: 1.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 500;
        }

        .production-detail-label-even {
            background-color: #FFFFFF;
            color: #9A003E;
            font-size: 1.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
        }


        #footer-status {
            width: 100%;
            text-align: center;
            height: 4rem;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .status-no-production {
            background-color: #FFFFFF;
            color: #414141;
        }

        .status-run {
            background-color: #39FF14;
            color: #414141;
        }

        .status-plan-die-change {
            background-color: #ffeb3b;
            color: #414141;
        }

        .status-unplan-die-change {
            background-color: #ffa000;
            color: #414141;
        }

        .status-break {
            background-color: #b026ff;
            color: #FFFBFB;
        }

        .status-machine-downtime {
            background-color: #FF073A;
            color: #FFFBFB;
        }

        .status-human-downtime {
            background-color: #0000FF;
            color: #FFFBFB;
        }

        .line-detail {
            height: 5.26rem;
        }


        .oee-detail {
            background-color: #9A003E;
            /* height: 12rem; */
            /* width: 45.807rem; */
            height: 100%;
            color: #FFFFFF;
            font-size: 2.8rem;
            font-weight: bold;
        }

        .apq-detail {
            padding: 0.625rem;
            background-color: #FFFBFB;
            text-align: center;
        }

        .apq-detail i {
            font-size: 2rem;
        }

        .apq-detail span {
            font-weight: bold;
            color: #9A003E;
            height: 100%;
        }

        .apq-detail-text {
            font-size: 7rem !important;
            color: #414141 !important;
        }

        .apq-detail-subtext {
            font-size: 3rem !important;
            color: #414141 !important;
            padding-bottom: 3.5rem;
        }

        .plan-detail {
            padding: 0.625rem;
            background-color: #FFFBFB;
            text-align: center;
            font-weight: bold;
            color: #414141;
            font-size: 1.9rem;
            height: 100%;
        }

        .plan-detail-data {
            font-size: 3.7rem;
            color: #9B003E;
        }

        .part-detail>span {
            color: #FFFFFF;
            font-weight: bold;
        }

        .part-detail>div>div {
            color: #FFFFFF;
            font-weight: bold;
        }

        .part-detail>span:first-child {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .production-line {
            background-color: #1C3058;
        }

        .not-available-label {
            color: #FFFFFF;
            font-size: 3rem;
            font-weight: bold;
        }

        .status-monofont {
            font-family: 'Consolas', 'Courier New', Courier, monospace;
        }

        .temp-display {
            position: fixed;
            bottom: 0;
            color: #1C3058;
            background-color: white;
        }

        .variance-bad {
            color: red;
        }

        .variance-good {
            color: green;
        }

        .cycle-time {
            background: #eee;
            color: #000;
            padding-left: 1em;
            padding-right: 1em;
        }
    </style>
@endsection

@section('body')
    <main>
        <div class="line-container flex-grow-1">
            <div class="p-3 production-line" data-line-no="1">

            </div>
        </div>
        <div id="footer-status" class="status-plan-die-change d-flex justify-content-center align-items-center">
            CALLING
        </div>

        <audio id="beepAudio" src="{{ asset('beep.mp3') }}" preload="auto"></audio>
    </main>
@endsection
@section('templates')
    @parent
    <template id="template-active-production-line">
        <div class="h-100 d-flex flex-column justify-content-between">
            {{-- line & part detail --}}

            {{-- plan & production detail --}}

            {{-- oee detail --}}

            {{-- a,p,q detail --}}
            <div class="row mt-0">

                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_availability"></div>
                        <span class="apq-detail-text"><span class="set-production-line-no live-production-line-data"
                                data-tag="availability" data-format="percentage_rounded">-</span> <!--%--></span>
                        <span class="apq-detail-subtext">CALLING LEADER</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_performance"></div>
                        <span class="apq-detail-text"><span class="set-production-line-no live-production-line-data"
                                data-tag="performance" data-format="percentage_rounded">-</span> <!--%--></span>
                        <span class="apq-detail-subtext">CALLING MAINTENANCE</span>
                    </div>
                </div>
            </div>
            <div class="row mt-3">

                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_availability"></div>
                        <span class="apq-detail-text"><span class="set-production-line-no live-production-line-data"
                                data-tag="availability" data-format="percentage_rounded">-</span> <!--%--></span>
                        <span class="apq-detail-subtext">CALLING LOGISTIC</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_performance"></div>
                        <span class="apq-detail-text"><span class="set-production-line-no live-production-line-data"
                                data-tag="performance" data-format="percentage_rounded">-</span> <!--%--></span>
                        <span class="apq-detail-subtext">CALLING QC</span>
                    </div>
                </div>
            </div>


        </div>
        </div>
    </template>
    <template id="template-inactive-production-line">
        <div class="h-100 d-flex flex-column">
            <div class="part-detail">
                <span style="font-size:2rem;">LINE <span class="flex-grow-1 line-no-data"></span></span>
            </div>
            <div class="flex-fill d-flex justify-content-center align-items-center not-available-label">
                NO PRODUCTION
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    @parent
    @include('snippets.live-production-scripts')
    <script>
        //Websocket
        Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
            .listen('.terminal.data-updated', (e) => {
                LivePage.terminalDataUpdatedHandler(e);
            });

        $(() => {
            LivePage.listenAnyChanges(e => {
                workCenterUpdated(e);
            });
            workCenterUpdated(LivePage.liveProduction.currentSummary);
        });

        var firstTime = true;


        function addVarianceRenderer() {
            $('.live-production-line-data[data-tag="variance"]').data('render', (e, value, summary) => {
                if (value < 0) {
                    $(e).removeClass('variance-good');
                    $(e).addClass('variance-bad');
                } else {
                    $(e).removeClass('variance-bad');
                    $(e).addClass('variance-good');
                }
                return Math.abs(value);
            })
        }

        function workCenterUpdated(e) {
            let workCenter = LivePage.terminalData.workCenter;

            //quick fix update line active

            //Update Dashboard Layout
            for (let index = 0; index < workCenter.production_line_count; index++) {
                let lineNo = index + 1;


                let productionLine = LivePage.getProductionLineByLineNo(lineNo);


                let lineElement = $(`.production-line[data-line-no=${lineNo}]`);
                let lineActive = lineElement.data('line-active') ?? false;


                if ((lineActive || firstTime) && !productionLine) {

                    lineElement.html($('#template-inactive-production-line').html());
                    lineElement.find('.set-production-line-no').data('line-no', lineNo);
                    lineElement.data('line-active', false);
                    lineElement.find('.line-no-data').html(lineNo);
                    addVarianceRenderer();
                } else if ((!lineActive || firstTime) && productionLine) {
                    lineElement.html($('#template-active-production-line').html());
                    lineElement.find('.set-production-line-no').data('line-no', lineNo);
                    lineElement.data('line-active', true);
                    lineElement.find('.line-no-data').html(lineNo);
                    addVarianceRenderer();
                }


            }

            //Update Status Bar Text & Color
            let statusBar = $('#footer-status');


            let workCenterStatus = workCenter.status;
            let workCenterDowntimeState = workCenter.downtime_state;

            let lastStatus = statusBar.data('status');
            let lastDowntimeState = statusBar.data('downtime-status');

            if (lastStatus != workCenterStatus || lastDowntimeState != workCenterDowntimeState) {
                removeAllStatusStateClass(statusBar);
                statusBar.addClass(getStatusBarClass(workCenterStatus, workCenterDowntimeState))
                    .html(getStatusBarText(workCenterStatus, workCenterDowntimeState))
                    .data('status', workCenterStatus)
                    .data('downtime-status', workCenterDowntimeState);

                LivePage.updateLiveData();
            }
            firstTime = false;
        }

        function removeAllStatusStateClass(ref) {
            ref.removeClass('status-no-production');
            ref.removeClass('status-run');
            ref.removeClass('status-plan-die-change');
            ref.removeClass('status-unplan-die-change');
            ref.removeClass('status-break');
            ref.removeClass('status-human-downtime');
            ref.removeClass('status-machine-downtime');
        }

        function getStatusBarClass(status, downtimeState) {
            let statusClass = "status-no-production";

            if (status) {
                //Work Center Running, 
                switch (downtimeState) {
                    case DOWNTIME_STATUS_NONE:
                        statusClass = "status-run";
                        break;
                    case DOWNTIME_STATUS_PLAN_BREAK:
                        statusClass = "status-break";
                        break;
                    case DOWNTIME_STATUS_PLAN_DIE_CHANGE:
                        statusClass = "status-plan-die-change";
                        break;
                    case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
                        statusClass = "status-unplan-die-change";
                        break;
                    case DOWNTIME_STATUS_UNPLAN_HUMAN:
                        statusClass = `status-human-downtime`;
                        break;
                    case DOWNTIME_STATUS_UNPLAN_MACHINE:
                        statusClass = `status-machine-downtime`;
                        break;
                }
            }

            return statusClass;
        }

        function getStatusBarText(status, downtimeState) {
            let statusText = "NO PRODUCTION";
            console.log('getStatusBarText', status, downtimeState);
            if (status) {
                //Work Center Running, 
                switch (downtimeState) {
                    case DOWNTIME_STATUS_NONE:
                        statusText = "RUNNING";
                        break;
                    case DOWNTIME_STATUS_PLAN_BREAK:
                        statusText = "BREAK";
                        break;
                    case DOWNTIME_STATUS_PLAN_DIE_CHANGE:
                        statusText = "DIE CHANGE";
                        break;
                    case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
                        statusText =
                            `DIE CHANGE&nbsp;|&nbsp;<span class="status-monofont live-downtime-timer" data-tag="unplan_die_change" data-format="timer_full">-</span>`;
                        break;
                    case DOWNTIME_STATUS_UNPLAN_HUMAN:
                        statusText =
                            `HUMAN DOWNTIME&nbsp;|&nbsp;<span class="status-monofont live-downtime-timer" data-tag="unplan_human" data-format="timer_full">-</span>`;
                        break;
                    case DOWNTIME_STATUS_UNPLAN_MACHINE:
                        statusText =
                            `MACHINE DOWNTIME&nbsp;|&nbsp;<span class="status-monofont live-downtime-timer" data-tag="unplan_machine" data-format="timer_full">-</span>`;
                        break;
                }
            }

            return statusText;
        }
        /** Work Center Idle */
        const STATUS_IDLE = 0;
        /** Work Center Die Change */
        const STATUS_DIE_CHANGE = 1;
        /** Work Center First Product Confirmation */
        const STATUS_FIRST_CONFIRMATION = 2;
        /** Work Center Running */
        const STATUS_RUNNING = 3;

        /** No Downtime */
        const DOWNTIME_STATUS_NONE = 0;
        /** Unplanned Downtime: Human */
        const DOWNTIME_STATUS_UNPLAN_HUMAN = -1;
        /** Unplanned Downtime: Machine */
        const DOWNTIME_STATUS_UNPLAN_MACHINE = -2;
        /** Unplanned Downtime: Die-Change */
        const DOWNTIME_STATUS_UNPLAN_DIE_CHANGE = -3;
        /** Planned Downtime: Die-Change */
        const DOWNTIME_STATUS_PLAN_DIE_CHANGE = 3;
        /** Planned Downtime: Break */
        const DOWNTIME_STATUS_PLAN_BREAK = 4;
    </script>
    <script>
        let currentScreen = 1;
    
        function flipScreen() {
            const url = new URL(window.location.href);
            const pathSegments = url.pathname.split('/');
    
            // Extract plantUid and workCenterUid from the URL
            const plantUid = pathSegments[2];
            const workCenterUid = pathSegments[3];
            currentScreen = parseInt(pathSegments[pathSegments.length - 1]) || 1;
            currentScreen = currentScreen === 8 ? 1 : 8;
    
            // Construct new URL
            const newUrl = `${url.origin}/dashboard/${plantUid}/${workCenterUid}`;
            window.location.href = newUrl;
        }
    
        setInterval(flipScreen, 3000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Get the beep audio element
            const beepAudio = document.getElementById('beepAudio');
            
            // Play beep sound on page load
            beepAudio.play().catch(error => {
                console.log('Error playing audio:', error);
            });
        });
    </script>
@endsection
