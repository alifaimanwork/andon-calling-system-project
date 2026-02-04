@extends('layouts.terminal')
@include('components.terminal.break-resume')
@include('components.terminal.numpad-modal')
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
        background-color: #000080 !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        opacity: 100%;
    }

    .pending-container {
        border: 0.1rem solid #626162 !important;
        background-color: #F8F8F9;
        /* height: 31.25rem; */
    }

    .pending-container span {
        font-size: 1rem;
        font-weight: bold;
        color: #5E5E5E;
    }

    .table-wrap-pending table {
        width: 100%;
        font-size: 1rem;
        text-align: center;
    }

    .table-wrap-pending table tbody tr td:first-child {
        background-color: #356771;
        color: white;
        font-weight: bold;
    }

    .table-wrap-pending table tbody tr td {
        background-color: #FFFFFF;
        color: #767577;
        font-weight: bold;
        width: 25rem;
    }

    .pending-input span:first-child {
        background-color: #356771;
        border: #2B2B2B 0.052083rem solid;
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pending-input span:nth-child(2) {
        text-align: center;
        background-color: white;
        border: #2B2B2B 0.052083rem solid;
        border-radius: 0.10416rem;
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 6rem;
        font-size: 1.5rem;
    }

    .flex-basis {
        flex-basis: 100%;
    }

    .button-input {
        border: #2B2B2B 0.052083rem solid;
        background-color: white;
        border-radius: 0;
        font-size: 1.5rem;
        height: 3rem;
        width: 3rem;
    }

    .btn-terminal {
        font-size: 0.9rem;
        color: white;
        font-weight: bold;
        width: 11rem;
        height: 3rem;
        box-shadow: 1px 1px 7px #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        /*border: 0.1rem solid #FFFFFF;*/
    }

    .btn:hover {
        color: black !important;
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

    .modal-button {
        color: white;
        font-weight: bold;
        font-size: 1rem;
        width: 8rem;
        height: 2.5rem;
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
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }
</style>
@endsection

@section('body')
<main class="px-3 py-2">
    {{-- fakhrul --}}
    {{-- page title, act button --}}
    <div class="d-flex flex-column">

        <div class="d-flex justify-content-between align-items-center">
            {{-- page-title --}}
            <div class="d-flex flex-column">
                <div class="content-title">PENDING</div>
                <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                    <span>WORK CENTRE</span>
                    <i class='fa-duotone fa-caret-right'></i>
                    <span style="color: #000080;">{{ $workCenter->name }}</span>
                </div>
            </div>


            {{-- act button --}}
            <div class="d-flex gap-4 mt-3">
                @yield('terminal-break-button')
            </div>

        </div>
    </div>

    {{-- line selection, pending container --}}
    <div class="d-flex flex-column mt-5">
        {{-- line selection --}}
        <ul class="nav nav-tabs">
            @foreach($productionLines as $productionLine)
            <li class="nav-item" type="button" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="{{ $productionLine->id }}">
                <a class="terminal-link" data-production-line-id="{{ $productionLine->id }}">LINE {{ $productionLine->line_no }}</a>
            </li>
            @endforeach
        </ul>

        {{-- pending container --}}
        <div class="d-flex flex-column p-4 pending-container">
            <span>PLEASE KEY IN PENDING PART QUANTITY AT THIS SECTION</span>
            <div class="d-flex justify-content-between mt-4">
                {{-- table pending --}}
                <div class="table-wrap-pending">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>PRODUCTION ORDER</td>
                                <td class="current-production-line-data production-order-data" data-tag="order_no"></td>
                            </tr>
                            <tr>
                                <td>PART NO</td>
                                <td class="current-production-line-data part-data" data-tag="part_no"></td>
                            </tr>
                            <tr>
                                <td>PART NAME</td>
                                <td class="current-production-line-data part-data" data-tag="name"></td>
                            </tr>
                            <tr>
                                <td>PENDING QUANTITY (PCS)</td>
                                <td class="current-production-line-data live-production-line-data" data-tag="pending_count"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- input pending --}}
                <div class="d-flex flex-column gap-3 justify-content-between align-items-center p-4 flex-fill">
                    <div class="d-flex pending-input flex-fill">
                        <span class="p-3 text-center">PENDING QUANTITY <br>(PCS)</span>
                        <span class="p-3 current-production-line-data uncommit-count" onclick="showNumpadModal(this, 'PENDING QUANTITY', 3)" style="cursor: pointer">0</span>
                        <div class="d-flex flex-column">
                            <button class="btn flex-basis button-input current-production-line-data" onclick="pendingUp(this)">+</button>
                            <button class="btn flex-basis button-input current-production-line-data" onclick="pendingDown(this)">-</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-terminal mt-3" style="background-color: #1C008A;" data-bs-toggle="modal" data-bs-target="#confirmation-modal">
                        <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem; margin-right: 0.5rem; color: #ffffff" class="fa-regular fa-circle-check"></i><span style="letter-spacing: 0.10417rem; color: #ffffff;" class="flex-fill">SUBMIT</span></div>
                    </button>

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

{{-- modal confirmation --}}
<div class="modal fade" id="confirmation-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- message --}}
                <span>Do you confirm</span> <br>
                <span>on the input data?</span>

                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button current-production-line-data" onclick="submitPending(this)" data-bs-dismiss="modal">YES</button>
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
<script>
    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
            checkValidTerminalStatus();
        }).listen('.terminal.downtime-state-changed', (e) => {
            LivePage.terminalDowntimeStateChangedHandler(e);
        });

    $(() => {
        LivePage.initializeProductionLineTab(function(e) {
            currentProductionLineChanged(e);
        }).listenChanges(
            'live-production-line-data', //class
            setConfigToCurrentLine({
                tag: 'reject_count',
            }),
            clearInput
        ).listenChanges(
            'live-production-line-data', //class
            setConfigToCurrentLine({
                tag: 'pending_count',
            }),
            clearInput
        );
        autoRedirectDowntime.initialize();
    });


    function setConfigToCurrentLine(configData) {
        if (LivePage.tabCurrentProductionLine)
            configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

        // currentProductionLineDataConfig.push(configData);
        return configData;
    }

    function clearInput() {
        let currentProductionLine = LivePage.tabCurrentProductionLine;
        let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count;

        let uncommitedPart = 0;
        if (uncommitPendingCount[currentProductionLine.id]) {
            uncommitedPart = uncommitPendingCount[currentProductionLine.id];
        }

        if (okPart - uncommitedPart < 0) {
            uncommitPendingCount[currentProductionLine.id] = 0;
            updateUncommitPendingCount();
        }
    }



    const pageValidStatus = [3]; //Running Only
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

        updateUncommitPendingCount();
    }
</script>

<script>
    var uncommitPendingCount = [];
    //Handle Uncommit Pending Counter
    function updateUncommitPendingCount() {
        $('.uncommit-count').each((index, e) => {
            let productionLineId = $(e).data('production-line-id');
            if (!productionLineId)
                return;

            let count = uncommitPendingCount[productionLineId];
            if (!count)
                count = 0;
            $(e).html(count);
        });
    }

    function pendingUp(sender) {
        console.log($(sender).data('production-line-id'));
        let productionLineId = $(sender).data('production-line-id');
        if (!productionLineId)
            return;

        let productionLine = LivePage.getProductionLineById(productionLineId);

        // let upperlimit = productionLine.ok_count - productionLine.pending_count;

        if (!uncommitPendingCount[productionLineId])
            uncommitPendingCount[productionLineId] = 0;

        //if (upperLimit > uncommitPendingCount[productionLineId]) //temp disable to test submit
        let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count;

        let uncommitedPart = uncommitPendingCount[productionLineId] + 1;

        if (okPart < uncommitedPart) {
            return;
        }

        uncommitPendingCount[productionLineId]++;

        updateUncommitPendingCount();
    }

    function pendingDown(sender) {
        let productionLineId = $(sender).data('production-line-id');
        if (!productionLineId)
            return;

        if (!uncommitPendingCount[productionLineId])
            uncommitPendingCount[productionLineId] = 0;


        if (uncommitPendingCount[productionLineId] > 0)
            uncommitPendingCount[productionLineId]--;

        updateUncommitPendingCount();
    }
</script>
<script>
    //Actions
    var submitting = false;

    function submitPending(sender) {

        let currentProductionLineId = $(sender).data('production-line-id');

        //submit pending on current page
        if (submitting)
            return;

        submitting = true;
        //prepare payload
        let payload = {
            _token: window.csrf.getToken(),
            production_line_id: currentProductionLineId,
            count: uncommitPendingCount[currentProductionLineId]
        };

        $.post("{{ route('terminal.pending.set.pending',[ $plant->uid, $workCenter->uid ]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings

                    //clear input
                    uncommitPendingCount[currentProductionLineId] = 0;
                    updateUncommitPendingCount();

                } else {
                    alert(response.message);
                    //location.reload();
                }
            }).always(function() {
            submitting = false;
        });

    }
</script>
@endsection