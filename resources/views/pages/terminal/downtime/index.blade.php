@extends('layouts.terminal')
@include('components.terminal.break-resume')
@include('components.terminal.auto-redirect-downtime')
@section('head')
@parent
<style>
    :root {
        font-size: 1vw;
    }

    main {
        height: calc(100vh - 2.4rem);
        width: calc(100vw - 10.416rem);
        background-color: white;
        overflow-x: hidden;
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
        /*border: 0.1rem solid #FFFFFF;*/
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

    .total-downtime-container {
        background-color: #FE5F6D;
        color: white;
        border-radius: 0.3125rem 0.3125rem 0.3125rem 0.3125rem;
        font-weight: bold;
    }

    .total-downtime-container span {
        font-size: 0.75rem;
        letter-spacing: 0.10416rem;
    }

    .total-downtime-timer span {
        font-size: 1.25rem;
    }

    .downtime-type {
        font-size: 1rem;
        background-color: #000078;
        color: white;
        font-weight: bold;
        border: 0.05208rem #626162 solid;
        border-radius: 0.3125rem 0.3125rem 0 0;
        height: 2.3rem;
    }

    .downtime-type-timer {
        background-color: #E1E1E1;
        color: #000000;
        font-weight: bold;
        height: 2.1rem;
        width: 13rem;
    }

    .downtime-type-timer .live-downtime-timer {
        font-size: 1.3rem;
    }

    .downtime-type-timer-label {
        font-size: 0.65rem;
    }

    /* grid */

    .downtime-type-container {
        width: 42rem;
    }

    .downtime-lists {
        border: 0.05208rem #626162 solid;
        background-color: #F4F4F4;
    }

    .machine-downtime-headers {
        display: grid;
        grid-template-columns: 12rem 12rem 7.2rem 6.24rem;
        gap: 0.5rem;
        z-index: 2;
    }

    .human-downtime-headers {
        display: grid;
        grid-template-columns: 12rem 12rem 7.2rem 6.24rem;
        gap: 0.5rem;
        z-index: 2;
    }

    .downtime-body {
        margin-top: -1rem;
        border: 0.05208rem #626162 solid;
        background-color: #FFFFFF;
        z-index: 1;
        padding-top: 1.5rem;
        padding-bottom: 0.5rem;
        height: 36.3rem;
        overflow-y: auto;
    }

    .downtime-body::-webkit-scrollbar {
        width: 0.21rem;
    }

    .downtime-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .downtime-body::-webkit-scrollbar-thumb {
        background: #888;
    }

    .downtime-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .downtime-title span {
        color: white;
        background-color: #000078;
        padding: 0.3rem;
        border-radius: 0 0.3125rem 0.3125rem 0;
    }

    .machine-downtime {
        display: grid;
        grid-template-columns: 12rem 12rem 7.2rem 6.24rem;
        gap: 0.5rem;
        padding: 0.5rem !important;
    }

    .human-downtime {
        display: grid;
        grid-template-columns: 12rem 12rem 7.2rem 6.24rem;
        gap: 0.5rem;
        padding: 0.5rem !important;
    }

    .downtime-name {
        font-weight: bold;
        font-size: 0.9rem;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .downtime-item {
        min-height: 3em;
    }

    .downtime-item.active {
        background-color: rgba(254, 95, 109, 0.3);
    }

    .downtime-item.active.downtime-altrow {
        background-color: rgba(254, 95, 109, 0.4);
    }

    .downtime-reason {
        border: 0.05208rem solid #626162 !important;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: white;
    }

    .downtime-item-timer {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #FFBD4D;
        font-weight: bold;
        gap: 0.5rem;
    }

    .downtime-item.active .downtime-item-timer {
        background-color: #FF0000;
        color: white;
    }

    .downtime-button {
        font-size: 0.8rem;
        font-weight: bold;
        border: 0.05208rem solid #626162 !important;
        border-radius: 0.25rem;
        opacity: .65;
        cursor: default !important;
        width: 6rem;
        background-color: #ffffff;
    }

    .downtime-item.active .downtime-button {
        opacity: 1;
        cursor: pointer !important;
        background-color: white;
    }

    .set-clear-button {
        opacity: 1;
        cursor: pointer !important;
    }

    .human-downtime .downtime-button {
        cursor: pointer !important;
    }

    .modal-button {
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
        width: 8rem;
        height: 2.5rem;
        border-radius: 0.8rem;
    }

    .reason-button {
        background-color: #ffffff;
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
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }

    .unplan-die-change-container {
        background-color: #FFBD4D;
        font-size: 0.75em;
        font-weight: 500;
        margin-top: 0.2rem;
    }

    .unplan-downtime-timer {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #e1e1e1;
        font-weight: bold;
        gap: 0.5rem;
    }

    .downtime-altrow {
        background-color: rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('body')
<main class="px-3 py-2">
    {{-- fakhrul --}}

    {{-- page-title, total downtime timer --}}
    <div class="d-flex justify-content-between">
        <div class="d-flex flex-column">
            <div class="d-flex flex-column">
                <div class="content-title">DOWNTIME</div>
                <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                    <span>WORK CENTRE</span>
                    <i class='fa-duotone fa-caret-right'></i>
                    <span style="color: #000080;">{{ $workCenter->name }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-item-center">
            <div class="me-3 mt-1">
                @yield('terminal-break-button')
            </div>
            <div>


                <div class="total-downtime-container d-flex flex-column py-2">
                    <span class="text-center"><i class="fa-regular fa-clock-eleven-thirty"></i> TOTAL DOWNTIME</span>
                    <div class="d-flex justify-content-around align-items-center total-downtime-timer w-100">
                        <span class="text-center px-3"><span class="live-downtime-timer" data-tag="unplan" data-format="total_hours_floor">0</span> hrs</span>
                        <span class="text-center px-3"><span class="live-downtime-timer" data-tag="unplan" data-format="duration_minutes">00</span> mins</span>
                    </div>
                </div>
                <div class="unplan-die-change-container d-flex flex-column">
                    <div class="d-flex align-items-center ">
                        <div class="flex-fill text-center">UNPLAN DIE CHANGE</div>
                        <div class="unplan-downtime-timer py-1 px-2">
                            <i class="fa-regular fa-stopwatch"></i>
                            <span class="live-downtime-timer" data-tag="unplan_die_change" data-format="timer_full">00:00:00</span>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- downtime type & timer --}}
    <div class="downtime-container d-flex justify-content-around gap-3 mt-4">
        {{-- machine downtime --}}
        <div class="d-flex flex-column downtime-type-container">
            {{-- type and timer --}}
            <div class="d-flex justify-content-between">
                {{-- type --}}
                <div class="downtime-type p-1 my-auto">
                    MACHINE DOWNTIME
                </div>

                {{-- timer --}}
                <div class="downtime-type-timer d-flex justify-content-around m-0 p-1 gap-2">
                    <div><i class="fa-solid fa-database my-auto"></i></div>

                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_machine" data-format="total_hours_floor"></span>
                        <span class="downtime-type-timer-label">HRS</span>
                    </div>
                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_machine" data-format="duration_minutes">00</span>
                        <span class="downtime-type-timer-label">MINS</span>
                    </div>

                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_machine" data-format="duration_seconds">00</span>
                        <span class="downtime-type-timer-label">SECS</span>
                    </div>

                </div>
            </div>

            {{-- downtime list --}}
            <div class="d-flex flex-column p-3 downtime-lists">
                <div class="machine-downtime-headers px-2">
                    <div class="downtime-title">
                        <span class="pe-4">Downtime Category</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Downtime Reason</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Duration</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Action</span>
                    </div>
                </div>

                <div class="downtime-body">
                    @isset($machineDowntimes)
                    @foreach ($machineDowntimes as $downtimeItem)
                    <div class="machine-downtime downtime-item {{ (($loop->index % 2) ? 'downtime-altrow':'' ) }}" data-id="{{ $downtimeItem->id }}">
                        <div class="downtime-name">
                            {!! $downtimeItem->category ? $downtimeItem->category : '<i>-</i>' !!}
                        </div>
                        @if($downtimeItem && count($downtimeItem->downtimeReasons) > 0)
                        <div class="downtime-reason" data-category="{{ $downtimeItem->category }}" onclick="showReasonDetails(this)">
                            -
                        </div>
                        @else
                        <div>

                            &nbsp;
                        </div>
                        @endif
                        <div class=" downtime-item-timer">
                            <i class="fa-regular fa-stopwatch"></i>
                            <span class="live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $downtimeItem->id }}" data-format="timer_full">00:00:00</span>
                        </div>
                        <div>
                            @if($downtimeItem && count($downtimeItem->downtimeReasons) > 0)
                            <button disabled class="btn downtime-button reason-button" data-downtime-type-id="{{ $downtimeItem->downtime_type_id }}" data-id="{{ $downtimeItem->id }}" data-bs-toggle="modal" data-bs-target="#reason-modal" onclick="callReasonModal(this)">Set Reason</button>
                            @else
                            &nbsp;
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endisset

                </div>

            </div>
        </div>

        {{-- human downtime --}}
        <div class="d-flex flex-column downtime-type-container">
            {{-- type and timer --}}
            <div class="d-flex justify-content-between">
                {{-- type --}}
                <div class="downtime-type p-1 my-auto">
                    HUMAN DOWNTIME
                </div>

                {{-- timer --}}
                <div class="downtime-type-timer d-flex justify-content-around m-0 p-1 gap-2">
                    <div><i class="fa-solid fa-database my-auto"></i></div>

                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_human" data-format="total_hours_floor"></span>
                        <span class="downtime-type-timer-label">HRS</span>
                    </div>
                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_human" data-format="duration_minutes">00</span>
                        <span class="downtime-type-timer-label">MINS</span>
                    </div>
                    <div>
                        <span class="live-downtime-timer" data-tag="unplan_human" data-format="duration_seconds">00</span>
                        <span class="downtime-type-timer-label">SECS</span>
                    </div>
                </div>
            </div>

            {{-- downtime list --}}
            <div class="d-flex flex-column p-3 downtime-lists">
                <div class="human-downtime-headers px-2">
                    <div class="downtime-title">
                        <span class="pe-4">Downtime Category</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Downtime Reason</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Duration</span>
                    </div>
                    <div class="downtime-title">
                        <span class="pe-5">Action</span>
                    </div>
                </div>
                <div class="downtime-body">
                    @isset($humanDowntimes)
                    @foreach ($humanDowntimes as $downtimeItem)
                    <div class="human-downtime downtime-item  {{ (($loop->index % 2) ? 'downtime-altrow':'' ) }}" data-id="{{ $downtimeItem->id }}">
                        {{-- data tag: name downtime --}}
                        <div class="downtime-name">
                            {!! $downtimeItem->category ? $downtimeItem->category : '<i>Unable to get name</i>' !!}
                        </div>
                        @if($downtimeItem && count($downtimeItem->downtimeReasons) > 0)
                        <div class="downtime-reason" data-category="{{ $downtimeItem->category }}" onclick="showReasonDetails(this)">
                            -
                        </div>
                        @else
                        <div>
                            &nbsp;
                        </div>
                        @endif
                        <div class="downtime-item-timer">
                            <i class="fa-regular fa-stopwatch"></i>
                            <span class="live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $downtimeItem->id }}" data-format="timer_full">00:00:00</span>
                        </div>
                        <div>
                            @if($downtimeItem && count($downtimeItem->downtimeReasons) > 0)
                            <button disabled class="btn downtime-button reason-button" data-downtime-type-id="{{ $downtimeItem->downtime_type_id }}" data-id="{{ $downtimeItem->id }}" data-bs-toggle="modal" data-bs-target="#reason-modal" onclick="callReasonModal(this)">Set Reason</button>
                            @endif
                            <button class="btn downtime-button set-clear-button" onclick="setHumanDowntime(this)" data-downtime-id="{{ $downtimeItem->id }}">Set</button>
                        </div>
                    </div>
                    @endforeach
                    @endisset
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

{{-- modal downtime reason --}}
<div data-downtime-id="" class="modal fade" id="reason-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- select reason --}}
                <span class="text-start w-100 title">Select Reason</span>
                <select id="reason-selection" onchange="updateReasonComment(this)" class="form-select">
                    <option selected>Open this select menu</option>
                </select>

                {{-- text area --}}
                <span class="text-start w-100 title mt-2">Comment</span>
                <div class="w-100">
                    <input type="text" class="form-control" disabled placeholder="Leave a comment here">
                    <input type="hidden" name="downtime-id" val="">
                </div>

                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button" onclick="submitReason()">SUBMIT</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal reason detail --}}
<div id="reason-info-modal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="form-label">Downtime Reason</label>
                    <input class="downtime-reason-info form-control" data-tag="reason" readonly></input>
                </div>
                <div class="mt-3">
                    <label class="form-label">Comment</label>
                    <input class="downtime-reason-info form-control" data-tag="user_input_reason" readonly></input>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmation-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- icon --}}
                <i style="color:#eed202" class="fa-regular fa-triangle-exclamation"></i>

                {{-- message --}}
                <div class="modal-body text-center text-justify">
                    <span></span>
                </div>


                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button dialog-result" data-dialog-result="1" data-bs-dismiss="modal">YES</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button dialog-result" data-dialog-result="0" data-bs-dismiss="modal">NO</button>
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

    function showWarningDialog(title, text) {
        return new Promise(function(resolve, reject) {
            //duplicate the modal template and fill in the text
            var modal_warn = $('#warning-modal').clone();
            modal_warn.find('.modal-body p').html(text);
            modal_warn.find('.modal-title').html(title);
            modal_warn.modal('show');
            //wait for the user to click a button
            modal_warn.on('click', '.modal-button', function(e) {
                var result = $(e.target).data('dialog-result');
                //delete clone modal and resolve promise
                modal_warn.remove();
                if (result == 1)
                    resolve(true);
                else
                    resolve(false);
            });
        });
    }
</script>

<script>
    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
            checkValidTerminalStatus();
        })
        .listen('.terminal.downtime-state-changed', (e) => {
            LivePage.terminalDowntimeStateChangedHandler(e);
            updateDowntimesReasons();
        });

    const pageValidStatus = [3]; //Running only
    function checkValidTerminalStatus() {
        if (!pageValidStatus.includes(LivePage.terminalData.workCenter.status)) {
            location.reload();
            return;
        }
    }


    var submitting = false;

    var machineDowntimes = <?php echo $machineDowntimes ? json_encode($machineDowntimes) : '[]'; ?>;
    var humanDowntimes = <?php echo $humanDowntimes ? json_encode($humanDowntimes) : '[]'; ?>;
    $(() => {
        checkValidTerminalStatus();

        machineDowntimes.forEach(e => {
            LivePage.listenChanges(
                'live-downtime-timer', //class
                {
                    tag: 'by_id',
                    'downtime-id': e.id,
                    subtag: 'is_running'
                }, //Config
                (cfg, data, summary) => {
                    updateDowntimeState(e.id, data);
                }
            );
        });

        humanDowntimes.forEach(e => {
            LivePage.listenChanges(
                'live-downtime-timer', //class
                {
                    tag: 'by_id',
                    'downtime-id': e.id,
                    subtag: 'is_running'
                }, //Config
                (cfg, data, summary) => {
                    updateDowntimeState(e.id, data);
                }
            );
        });

        updateDowntimesReasons();
        LivePage.liveProduction.tick();
        // updateAllDowntimeState();
        autoRedirectDowntime.initialize(true);
    });


    function updateDowntimesReasons() {
        $('.machine-downtime').each((idx, e) => {
            //TODO: Check State, update if not same, update reason, update timer
            let downtimeId = $(e).data('id');
            if (!downtimeId)
                return;

            let activeDowntimeEvent = getActiveDowntimeEventByDowntimeId(downtimeId);

            let downtimeReasonElement = $(e).find('.downtime-reason');
            if (activeDowntimeEvent && activeDowntimeEvent.reason) {
                let reasonText = activeDowntimeEvent.user_input_reason ? activeDowntimeEvent.user_input_reason : activeDowntimeEvent.reason;
                downtimeReasonElement.html(reasonText);
                downtimeReasonElement.data('active-event', activeDowntimeEvent);
            } else {
                downtimeReasonElement.html('-');
                downtimeReasonElement.data('active-event', null);
            }

        });

        //Update All Human Downtime state
        $('.human-downtime').each((idx, e) => {
            //TODO: Check State, update if not same, update reason, update timer
            let downtimeId = $(e).data('id');
            if (!downtimeId)
                return;

            let activeDowntimeEvent = getActiveDowntimeEventByDowntimeId(downtimeId);

            let downtimeReasonElement = $(e).find('.downtime-reason');
            if (activeDowntimeEvent && activeDowntimeEvent.reason) {
                let reasonText = activeDowntimeEvent.user_input_reason ? activeDowntimeEvent.user_input_reason : activeDowntimeEvent.reason;

                downtimeReasonElement.html(reasonText);
                downtimeReasonElement.data('active-event', activeDowntimeEvent);
            } else {
                downtimeReasonElement.html('-');
                downtimeReasonElement.data('active-event', null);
            }
        });
    }

    function updateAllDowntimeState() {
        $('.downtime-item').each((idx, e) => {
            let id = $(e).data('id');
            let state = LivePage.liveProduction.currentSummary.downtimes.by_id[id];
            if (typeof(state) === 'undefined')
                state = 0;
            else
                state = state.is_running;

            console.log('updateAllDowntimeState', id, state);
            updateDowntimeState(id, state);
        });
    }

    function updateDowntimeState(downtimeId, state) {

        e = $(`.downtime-item[data-id="${downtimeId}"`);

        if (state) {
            $(e).find('.reason-button').prop('disabled', false);
            $(e).find('.set-clear-button').html('Clear');
            $(e).addClass('active');
        } else {
            $(e).find('.reason-button').prop('disabled', true);
            $(e).find('.set-clear-button').html('Set');
            $(e).removeClass('active');
            $(e).find('.downtime-reason').html('-');
        }

        return;
    }


    function getWorkCenterDowntimesByDowntimeId(id) {
        for (let i = 0; i < LivePage.terminalData.workCenterDowntimes.length; i++) {
            const workCenterDowntime = LivePage.terminalData.workCenterDowntimes[i];
            if (workCenterDowntime.downtime_id == id)
                return workCenterDowntime;
        }
        return null;
    }

    function getActiveDowntimeEventByDowntimeId(id) {

        for (let i = 0; i < LivePage.terminalData.activeDowntimeEvents.length; i++) {
            const downtimeEvent = LivePage.terminalData.activeDowntimeEvents[i];
            if (downtimeEvent.downtime_id == id)
                return downtimeEvent;
        }
        return null;
    }

    function getHumanDowntimeById(id) {
        for (let i = 0; i < LivePage.terminalData.humanDowntimes.length; i++) {
            const humanDowntime = LivePage.terminalData.humanDowntimes[i];
            if (humanDowntime.id == id)
                return humanDowntime;
        }
        return null;
    }



    function setHumanDowntime(sender) {
        if (submitting)
            return;
        submitting = true;
        let downtimeId = $(sender).data('downtime-id');
        let workCenterDowntime = getWorkCenterDowntimesByDowntimeId(downtimeId);
        let humanDowntime = getHumanDowntimeById(downtimeId);

        //TODO: Confirmation
        let confirmationText = "";

        if (workCenterDowntime.state) {
            confirmationText = `End human downtime '${humanDowntime.category}'?`;
        } else {
            confirmationText = `Set human downtime '${humanDowntime.category}'?`;
        }

        showConfirmationModal(confirmationText).then((result) => {
            if (!result) {
                submitting = false;
                return;
            } else {
                let newState = workCenterDowntime.state ? 0 : 1;

                let payload = {
                    _token: window.csrf.getToken(),
                    work_center_downtime_id: workCenterDowntime.id,
                    downtime_id: humanDowntime.id,
                    set_state: newState,
                };
                $.post("{{ route('terminal.downtime.set.human-downtime', [$plant->uid, $workCenter->uid]) }}", payload,
                    function(response, status, xhr) {
                        //result code
                        const RESULT_OK = 0;
                        const RESULT_INVALID_STATUS = -1;
                        const RESULT_INVALID_PARAMETERS = -2;

                        //TODO: display error message in modal
                        if (response.result === RESULT_OK) {
                            //update production line reject settings

                            //clear input
                            // updateRejectPayloads.forEach(e => {
                            //     if (e.production_line_id == response.data.production_line_id) {
                            //         //reset
                            //         e.add_maintenance = 0;
                            //         e.add_quality = 0;
                            //     }
                            // });

                            // updateProductionLineRejectSettings(response.data);

                        } else {
                            //alert(response.message);
                            //location.reload();
                        }
                    }).always(function() {
                    submitting = false;
                });
            }
        });
    }

    function callReasonModal(ref) {
        updateInputDowntimeId(ref);
        updateReasonSelections(ref);
    }

    function showReasonDetails(ref) {
        let modalElement = $('#reason-info-modal');

        modalElement.find('.modal-title').html($(ref).data('category'));

        let activeEvent = $(ref).data('active-event');

        if (!activeEvent)
            return;

        modalElement.find('.downtime-reason-info').each((index, element) => {
            if (activeEvent) {
                let tag = $(element).data('tag');
                // console.log(tag);
                $(element).val(activeEvent[tag]);
            } else
                $(element).val('');
        });
        modalElement.modal('show');
    }

    function updateInputDowntimeId(ref) {
        var downtime_id = $(ref).data('id');
        $('#reason-modal input[type=hidden]').val(downtime_id);
    }

    function updateReasonSelections(ref) {
        var downtimeID = $(ref).data('id');

        var downtimeLists = ($(ref).data('downtime-type-id') == 1 ? LivePage.terminalData.machineDowntimes : LivePage.terminalData.humanDowntimes);

        var activeDowntimeEvent = getActiveDowntimeEventByDowntimeId(downtimeID);

        var downtimeReasons = downtimeLists.find(x => x.id == downtimeID).downtime_reasons;
        $('#reason-selection').html('');
        $('#reason-selection').data('id', downtimeID);

        if (downtimeReasons.length > 0) {
            $('#reason-selection').append('<option disabled selected>Open this select menu</option>');
            downtimeReasons.forEach(function(reason) {
                let reasonSelected = (activeDowntimeEvent && activeDowntimeEvent.reason == reason.reason)
                $('#reason-selection').append(`<option data-enable-user-input="${reason.enable_user_input}" value="${reason.id}" ${reasonSelected?'selected':''}>${reason.reason}</option>`);
            });
        } else {
            $('#reason-selection').append('<option selected>No reasons available</option>');
        }
        updateReasonComment($('#reason-selection')[0]);
    }

    function updateReasonComment(ref) {
        var downtimeID = $(ref).data('id');
        var activeDowntimeEvent = getActiveDowntimeEventByDowntimeId(downtimeID);

        let userReason = (activeDowntimeEvent && activeDowntimeEvent.user_input_reason) ? activeDowntimeEvent.user_input_reason : '';

        var reason_comment = $('#reason-modal input[type=text]');
        reason_comment.val(userReason);
        if ($(ref).find(':selected').data('enable-user-input') == 1) {
            reason_comment.removeAttr('disabled');

        } else {
            reason_comment.attr('disabled', 'disabled');
        }
    }

    function submitReason() {
        var reason_comment = $('#reason-modal input[type=text]').val();
        var downtime_id = $('#reason-modal input[type="hidden"]').val();
        var downtime_reason_id = $('#reason-selection').val();

        onSetDowntimeReason(downtime_id, downtime_reason_id, reason_comment);
    }

    function onSetDowntimeReason(downtime_id, downtime_reason_id, user_input) {

        if (submitting)
            return;
        submitting = true;

        let tes = `downtime-id-${downtime_id}`;



        /*
        {
            production_id: <production_id>,
            downtime_id: <production_id>,
            downtime_reason_id: <downtime_reason_id>,
            user_input_reason: <user_input_reason>
        }
        */

        let payload = {
            _token: window.csrf.getToken(),
            downtime_id: downtime_id,
            downtime_reason_id: downtime_reason_id,
            user_input_reason: user_input
        };

        $.post("{{ route('terminal.downtime.set.downtime-reason', [$plant->uid, $workCenter->uid]) }}", payload,
            function(response, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings

                    //clear input
                    // updateRejectPayloads.forEach(e => {
                    //     if (e.production_line_id == response.data.production_line_id) {
                    //         //reset
                    //         e.add_maintenance = 0;
                    //         e.add_quality = 0;
                    //     }
                    // });

                    // updateProductionLineRejectSettings(response.data);

                } else {
                    //alert(response.message);
                    //location.reload();
                }
            }).always(function() {
            submitting = false;
            $('#reason-modal').modal('hide');
        });

    }
</script>
@endsection