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

        .hidden {
            display: none;
        }

        .stopwatch-timer {
            font-size: 7rem !important;
            color: #9A003E !important;
            font-weight: bold;
            margin-top: 0.5rem;
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
    <main>
        <div class="line-container flex-grow-1">
            <div class="p-3 production-line" data-line-no="1">

            </div>
        </div>
        <div id="footer-status" class="status-plan-die-change d-flex justify-content-center align-items-center">
            CALLING
        </div>
        <!--sound from https://mixkit.co/free-sound-effects/alerts/-->
        <audio id="audioPlayer" autoplay>
            <source src="{{ asset('audio/alert.mp3') }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
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
                        <div id="newpostLeader" class="stopwatch-timer hidden" style="flex-basis: 100%">
                            <span id="hourLeader">00</span>:<span id="minuteLeader">00</span>:<span id="secondLeader">00</span>
                        </div>
                        <span class="apq-detail-subtext">CALLING LEADER</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_performance"></div>
                        <div id="newpostMaintenance" class="stopwatch-timer hidden" style="flex-basis: 100%">
                            <span id="hourMaintenance">00</span>:<span id="minuteMaintenance">00</span>:<span id="secondMaintenance">00</span>
                        </div>
                        <span class="apq-detail-subtext">CALLING MAINTENANCE</span>
                    </div>
                </div>
            </div>
            <div class="row mt-3">

                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_availability"></div>
                        <div id="newpostLogistic" class="stopwatch-timer hidden" style="flex-basis: 100%">
                            <span id="hourLogistic">00</span>:<span id="minuteLogistic">00</span>:<span id="secondLogistic">00</span>
                        </div>
                        <span class="apq-detail-subtext">CALLING LOGISTIC</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="apq-detail d-flex flex-column text-center flex-fill me-3">
                        <div class="w-100 h-100 product-detail-indicator set-production-line-no live-production-line-data"
                            data-renderer="caret-indicator-positive" data-tag="indicator_performance"></div>
                        <div id="newpostQccheck" class="stopwatch-timer hidden" style="flex-basis: 100%">
                            <span id="hourQccheck">00</span>:<span id="minuteQccheck">00</span>:<span id="secondQccheck">00</span>
                        </div>
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

        function navigateToDashboard() {
            const url = new URL(window.location.href);
            const pathSegments = url.pathname.split('/');

            // Extract plantUid and workCenterUid from the URL
            const plantUid = pathSegments[2];
            const workCenterUid = pathSegments[3];

            // Construct new URL
            const newUrl = `${url.origin}/dashboard/${plantUid}/${workCenterUid}`;
            window.location.href = newUrl;
        }

        // Navigate to the dashboard page after 3 seconds
        setTimeout(navigateToDashboard, 3000);
    </script>
    <script>
        "use strict";

        const plantUid = '{{ $plant->uid }}';
        const workCenterUid = '{{ $workCenter->uid }}';

        // Initialize timers object
        let timers = {};
        let previousStates = {};

        function fetchActiveCallingStates() {
            fetch(`{{ url('dashboard') }}/${plantUid}/${workCenterUid}/get-active-callings-state`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        data.data.forEach(calling => {
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

                                    if (previousStates[calling.type] === undefined) {
                                        previousStates[calling.type] = 1; // Assume active since it's in the active callings list
                                    }
                                }
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function startTimer(type) {
            const timerDiv = document.getElementById(`newpost${capitalizeFirstLetter(type)}`);
            if (timerDiv && !timerDiv.classList.contains('hidden')) {
                stopTimer(type);
                return;
            }
            resetTimer(type);
            if (timerDiv) {
                timerDiv.classList.remove('hidden');
            }
            timers[type] = setInterval(() => { updateTimer(type); }, 1000);
        }

        function stopTimer(type) {
            clearInterval(timers[type]);
            const timerDiv = document.getElementById(`newpost${capitalizeFirstLetter(type)}`);
            if (timerDiv) {
                timerDiv.classList.add('hidden');
            }
            resetTimer(type);
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

        function handleStateChange(states) {
            // Loop over each key in the states object
            for (let type in states) {
                if (states.hasOwnProperty(type)) {
                    if (states[type] !== previousStates[type]) {
                        // State has changed
                        if (states[type] === 1) {
                            // If the new state is 1, start the timer
                            startTimer(type);
                        } else if (states[type] === 0) {
                            // If the new state is 0, stop the timer
                            stopTimer(type);
                        }
                        // Update the previous state for this type
                        previousStates[type] = states[type];
                    }
                }
            }
        }

        async function checkAndUpdateState() {
            try {
                const response = await fetch(`{{ url('dashboard') }}/${plantUid}/${workCenterUid}/get-callings-state-by-type`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                handleStateChange(data);
            } catch (error) {
                console.error('Error fetching aggregated state:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {  
            fetchActiveCallingStates();
            setInterval(checkAndUpdateState, 3000); // Check and update state every 3 seconds
        });
    </script>
@endsection
