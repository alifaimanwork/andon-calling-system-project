@extends('layouts.dashboard')

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
            overflow: hidden;
        }

        .row {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
        }

        .line-container {
            /* height: calc(100vh - 4rem - 3.1rem); */
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            grid-template-rows: 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .production-line {
            background-color: #1C3058;
            /* height:41.5rem; */
            height: calc(100vh - 4rem - 3.1rem - 2rem);
        }

        .production-detail-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
            grid-gap: 0.6rem;
        }

        .production-detail-data {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .product-detail-indicator {
            width: 1.5rem;
            padding-left: 0.5rem;
        }

        .production-detail-label-odd {
            background-color: #9A003E;
            color: #FFFFFF;
            font-size: 1.2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 500;
        }

        .production-detail-label-even {
            background-color: #FFFFFF;
            color: #9A003E;
            font-size: 1.2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
        }

        #footer-status {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            color: white;
            text-align: center;
            padding: 5px 0 5px 0;
            height: 4rem;
            font-size: 2.5rem;
            font-weight: bold;
        }

        #footer-status.status-run {
            background-color: #39FF14;
            color: #414141;
        }

        #footer-status.status-no-production {
            background-color: #FFFFFF;
            color: #414141;
        }

        #footer-status.status-human-downtime {
            background-color: #0000FF;
            color: #FFFBFB;
        }

        #footer-status.status-machine-downtime {
            background-color: #FF073A;
            color: #FFFBFB;
        }

        #footer-status.status-break {
            background-color: #B026FF;
            color: #FFFFFF;
        }

        #footer-status.status-die-change {
            background-color: #FFAD00;
            color: #414141;
        }

        .part-detail>span {
            color: #FFFFFF;
            font-weight: bold;
        }

        .part-detail>div>div {
            color: #FFFFFF;
            font-weight: bold;
        }

        .part-detail span:first-child {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .plan-detail {
            padding: 0.625rem;
            background-color: #FFFBFB;
            text-align: center;
            font-weight: bold;
            color: #414141;
            font-size: 1.2rem;
        }



        .plan-detail-data {
            font-size: 2.4rem;
            color: #9B003E;
        }

        .plan-detail span:first-child {
            font-size: 3rem;
            color: #9B003E;
        }

        .production-detail {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 1.5rem;
            height: 2.5rem;
        }

        .center-kn {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .production-detail span:first-child {
            width: 15.76rem;
        }

        .production-detail span {
            width: 13rem;
        }

        .production-detail:nth-child(odd) span:first-child {
            background-color: #9A003E;
            color: #FFFFFF;
        }

        .production-detail:nth-child(even) span:first-child {
            background-color: #FFFFFF;
            color: #9A003E;
        }

        .oee-detail {
            background-color: #9A003E;
            height: 100%;
            color: #FFFFFF;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .apq-detail {
            padding: 0.625rem;
            background-color: #FFFBFB;
            text-align: center;
        }

        .apq-detail i {
            font-size: 2.3rem;
        }

        .apq-detail span {
            font-weight: bold;
            color: #9A003E;
            font-size: 0.9rem;
        }

        .apq-detail-text {
            /*font-size: 2.5rem !important;*/
            color: #414141 !important;
            white-space: nowrap !important;
            margin-left: -0.5rem !important;
            margin-right: -0.5rem !important;
            font-size: 3rem;
            margin-top: -0.7rem;
        }

        .apq-kn-text {
            font-size: 0.9rem !important;
            margin-left: -0.5rem !important;
            margin-right: -0.5rem !important;
        }

        .not-available-label {
            color: #FFFFFF;
            font-size: 2rem;
            font-weight: bold;
        }

        .temp-display {
            position: fixed;
            bottom: 0;
            color: #1C3058;
            background-color: white;
        }

        .cycle-time {
            background: #eee;
            color: #000;
        }
    </style>
@endsection

@section('body')
    <main>
        <div class="line-container flex-grow-1">
            <div class="p-2 production-line" data-line-no="1"></div>

            <div class="p-2 production-line" data-line-no="2"></div>

            <div class="p-2 production-line" data-line-no="3"></div>

            <div class="p-2 production-line" data-line-no="4"></div>
        </div>

        <div id="footer-status" class="status-run d-flex justify-content-center align-items-center">
            RUNNING
        </div>
        <div class="temp-display">Runtime: <span class="live-runtime-timer" data-tag="plan" data-format="timer_full"></span>
        </div>
    </main>
@endsection

@section('templates')
    @parent
    <template id="template-active-production-line">
        <div class="h-100 d-flex flex-column justify-content-between">
            {{-- line & part detail --}}
            <div class="d-flex flex-column line-detail part-detail">
                <span>LINE <span class="line-no-data"></span></span>
                <div class="d-flex justify-content-between">
                    <div>
                        <div style="font-size:1.2rem;">PART NAME: <span class="set-production-line-no part-data"
                                data-tag="name"></span></div>
                        <div style="font-size:1.2rem;" class="cycle-time">CYCLE TIME <span class="set-production-line-no part-data"
                                data-tag="cycle_time" data-format="timer_full"></span></div>
                        <div style="font-size:1.2rem;">PART NO: <span style="font-size:1.2rem; letter-spacing: 0.1em;"
                                class="set-production-line-no part-data" data-tag="part_no"></span></div>
                    </div>
                </div>
            </div>

            {{-- plan & production detail --}}
            <div class="row mt-2">
                <div class="col-12 production-detail-container">

                    <div class="h-100" style="grid-row: 1/4;">
                        <div class="plan-detail">
                            <div class="plan-detail-data set-production-line-no live-production-line-data"
                                data-tag="standard_output">-</div>
                            <div class="plan-detail-label">PLAN</div>
                        </div>
                    </div>

                    <div class="" style="grid-row: 1/4;">
                        <div class="plan-detail">
                            <div class="plan-detail-data set-production-line-no live-production-line-data"
                                data-tag="actual_output">-</div>
                            <div class="plan-detail-label">ACTUAL</div>
                        </div>
                    </div>

                    <div class="production-detail-label-odd">VARIANCE</div>
                    <div class="production-detail-data d-flex justify-content-end">
                        <div>
                            <span class="set-production-line-no live-production-line-data" data-tag="variance">-</span> PCS
                        </div>
                        <div class="product-detail-indicator">
                            <i class="fa-solid fa-caret-up" style="color: #CE2A22;opacity: 0%;"></i>
                        </div>
                    </div>

                    <div class="production-detail-label-even">REJECT</div>
                    <div class="production-detail-data d-flex justify-content-end">
                        <div>
                            <span class="set-production-line-no live-production-line-data" data-tag="reject_count">-</span>
                            PCS
                        </div>
                        <div class="product-detail-indicator">
                            <i class="fa-solid fa-caret-up" style="color: #CE2A22;opacity: 0%;"></i>
                        </div>
                    </div>

                    <div class="production-detail-label-odd">DOWNTIME</div>
                    <div class="production-detail-data d-flex justify-content-end">
                        <div>
                            <span class="status-monofont live-downtime-timer" data-tag="unplan"
                                data-format="timer_full">00:00:00</span>
                        </div>

                        <div class="product-detail-indicator">

                        </div>
                    </div>

                </div>
            </div>

            {{-- oee detail --}}
            <div class="row flex-fill">
                <div class="col mt-2">
                    <div class="oee-detail d-flex align-items-center p-3 text-center justify-content-between">

                        <i class="fa-solid fa-caret-up me-2" style="color: #99D366;opacity: 0%;"></i>
                        <span style="font-size:4rem;"><span class="live-production-line-data" data-tag="oee"
                                data-format="percentage_rounded">-</span>%</span>
                        <div class="d-flex flex-column justify-content-end ms-2 align-self-end">OEE</div>

                    </div>
                </div>
            </div>

            {{-- a,p,q detail --}}
            <div class="row mt-2">
                <div class="col-4">
                    <div class="apq-detail d-flex flex-column text-center me-2">
                        <i class="fa-solid fa-caret-up" style="color: #99D366;opacity: 0%;"></i>
                        <span class="apq-detail-text" style="font-size: 2.5rem !important; margin-top: -0.7rem;"><span
                                class="set-production-line-no live-production-line-data apq-detail-text"
                                data-tag="availability" data-format="percentage_rounded"
                                style="font-size: 2.5rem !important; margin-top: -0.7rem;">-</span> %</span>
                        <span class="apq-kn-text">AVAILABILITY</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="apq-detail d-flex flex-column text-center me-2">
                        <i class="fa-solid fa-caret-up" style="color: #99D366;opacity: 0%;"></i>
                        <span class="apq-detail-text" style="font-size: 2.5rem !important; margin-top: -0.7rem;"><span
                                class="set-production-line-no live-production-line-data apq-detail-text"
                                data-tag="performance" data-format="percentage_rounded"
                                style="font-size: 2.5rem !important; margin-top: -0.7rem;">-</span> %</span>
                        <span class="apq-kn-text">PERFORMANCE</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="apq-detail d-flex flex-column text-center">
                        <i class="fa-solid fa-caret-down" style="color: #CE2A22;opacity: 0%;"></i>
                        <span class="apq-detail-text" style="font-size: 2.5rem !important; margin-top: -0.7rem;"><span
                                class="set-production-line-no live-production-line-data apq-detail-text"
                                data-tag="quality" data-format="percentage_rounded"
                                style="font-size: 2.5rem !important; margin-top: -0.7rem;">-</span> %</span>
                        <span class="apq-kn-text">QUALITY</span>
                    </div>
                </div>
            </div>

        </div>
    </template>
    <template id="template-inactive-production-line">
        <div class="h-100 d-flex flex-column">
            <div class="part-detail">
                <span>LINE <span class="flex-grow-1 line-no-data"></span></span>
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
    {{-- web socket script --}}
    <script>
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

        function workCenterUpdated(e) {
            let workCenter = LivePage.terminalData.workCenter;

            console.log('workCenterUpdated', e);
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
                } else if ((!lineActive || firstTime) && productionLine) {
                    lineElement.html($('#template-active-production-line').html());
                    lineElement.find('.set-production-line-no').data('line-no', lineNo);
                    lineElement.data('line-active', true);
                    lineElement.find('.line-no-data').html(lineNo);
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
            ref.removeClass('status-die-change');
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
                        statusClass = "status-die-change";
                        break;
                    case DOWNTIME_STATUS_UNPLAN_HUMAN:
                    case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
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
    </script>

    {{-- footer update status --}}
    <script>
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
@endsection
