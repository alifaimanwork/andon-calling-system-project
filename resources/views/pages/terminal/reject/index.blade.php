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
    }

    .terminal-link.active {
        color: white !important;
        background-color: #000078 !important;
        border-color: #dee2e6 #dee2e6 #fff !important;
        opacity: 100%;
    }

    .reject-container {
        border: 0.1rem solid #626162 !important;
        background-color: #EDEDED;
        height: 39rem;
    }

    .part-detail {
        font-weight: bold;
        line-height: 1;
        background-color: white;
        padding-left: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        padding-right: 1.5rem !important;
        padding-top: 1rem !important;
        border-radius: 0% 9px 9px 0%;
        font-size: 0.7rem;
    }

    .part-detail span {
        line-height: 1.2;
    }

    .part-detail span:last-child {
        color: #9B003E;
    }

    .btn-terminal {
        font-size: 0.9rem;
        color: white;
        font-weight: bold;
        width: 11rem;
        height: 3rem;
        box-shadow: 1px 1px 7px #000000;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 0.5rem;
        /*border: 0.1rem solid #FFFFFF;*/
    }

    .btn:hover {
        color: white !important;
    }

    .part-icon {
        position: absolute;
        border-radius: 50%;
        width: 1.7rem;
        height: 1.7rem;
        background: #000078;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #FFFFFF;
        margin-left: 0.833rem;
        margin-top: -1.1458rem;
    }

    .flex-basis {
        flex-basis: 100%;
    }

    .reject-value {
        text-align: center;
        background-color: #BBBABB;
        border: #BBBABB 0.052083rem solid;
        border-radius: 0.10416rem;
        height: 1.8229rem;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 2.6041rem;
    }

    .reject-input .reject-input-value {
        background-color: white;
        width: 4.1666rem;
        height: 1.8229rem;
        border: #BBBABB 0.052083rem solid;
        border-radius: 0.10416rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .reject-input button {
        width: 1.8229rem;
        height: 1.8229rem;
    }

    .reject-title {
        width: 11.9791rem;
        font-size: 0.9rem;
    }

    input[type="text"i] {
        padding: 0.05208rem 0.1042rem;
    }

    .reject-input button {
        padding-top: 0.05208rem;
        padding-right: 0.3125rem;
        padding-bottom: 0.05208rem;
        padding-left: 0.3125rem;
        border-top-width: 0.1041rem;
        border-right-width: 0.1041rem;
        border-bottom-width: 0.1041rem;
        border-left-width: 0.1041rem
    }

    .modal-button {
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
        width: 8rem;
        height: 2.5rem;
        border-radius: 0.8rem;
    }
</style>

{{-- style reject type container -> grid --}}
<style>
    .reject-type-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-column-gap: 2rem;
    }

    .reject-type-category {
        background-color: white;
        font-weight: bold;
        height: 29rem;
        font-size: 0.7rem;
        position: relative;
    }

    .reject-list::-webkit-scrollbar {
        width: 0.21rem;
    }

    .reject-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .reject-list::-webkit-scrollbar-thumb {
        background: #888;
    }

    .reject-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .reject-icon {
        position: absolute;
        /* height: 45px; */
        background: #000078;
        color: #FFFFFF;
        margin-left: 0.8333rem;
        margin-top: -1.1458rem;
        border-radius: 0 0.3rem 0.3rem 0;
    }

    .reject-icon i {
        margin-bottom: auto !important;
        margin-top: auto !important;
        font-size: 1rem;
    }

    .reject-list {
        font-size: 0.9rem;
        height: 26.5625rem;
        overflow-y: auto;
    }

    .reject-list-item {
        padding-bottom: 0.5rem !important;
        padding-top: 0.5rem !important;
        display: grid;
        grid-template-columns: 3fr 1fr 2fr;
        grid-column-gap: 0.5rem;
    }

    .reject-total-container {
        border-top: #BBBABB 0.052083rem solid;
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

    .reject-input-value {
        cursor: pointer;
    }
</style>
@endsection

@section('body')
<main class="px-3 py-2">
    {{-- fakhrul --}}

    <div class="d-flex justify-content-between align-items-center">
        {{-- page-title --}}
        <div class="d-flex flex-column">
            <div class="content-title">REJECT</div>
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

    {{-- line selection, reject container --}}
    <div class="d-flex flex-column mt-3">
        {{-- line selection --}}
        <ul class="nav nav-tabs">
            @foreach($productionLines as $productionLine)
            <li class="nav-item">
                <div role="button" class="terminal-link {{ ($loop->index == 0) ?'active':'' }}" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="{{ $productionLine->id }}">LINE {{ $productionLine->line_no }}</div>
            </li>
            @endforeach
        </ul>

        {{-- reject container --}}
        <div class="d-flex flex-column p-4 reject-container">
            {{-- part detail --}}
            <div class="d-flex justify-content-between w-100" style="margin-bottom: 0.3rem">
                <div class="d-flex gap-5 mt-2">
                    <div class="d-flex flex-column" style="position: relative">
                        <div class="part-icon">
                            <i class="fa-solid fa-file-spreadsheet"></i>
                        </div>
                        <div class="part-detail">
                            <span>PART NUMBER:</span> <br>
                            <span class="current-production-line-data part-data" data-tag="part_no"></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column" style="position: relative">
                        <div class="part-icon">
                            <i class="fa-regular fa-file-signature"></i>
                        </div>
                        <div class="part-detail">
                            <span>PART NAME:</span> <br>
                            <span class="current-production-line-data part-data" data-tag="name"></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column" style="position: relative">
                        <div class="part-icon">
                            <i class="fa-solid fa-table-tree"></i>
                        </div>
                        <div class="part-detail">
                            <span>PRODUCTION ORDER:</span> <br>
                            <span class="current-production-line-data production-order-data" data-tag="order_no"></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column" style="position: relative">
                        <div class="part-icon">
                            <i class="fa-solid fa-calculator-simple"></i>
                        </div>
                        <div class="part-detail">
                            <span>GRAND TOTAL:</span> <br>
                            <span class="current-production-line-data production-line-data" data-tag="reject_count"></span>
                        </div>

                    </div>
                </div>

                <button type="button" class="btn btn-terminal" style="background-color: #1C008A" data-bs-toggle="modal" data-bs-target="#confirmation-modal">
                    <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem; margin-right: 0.5rem" class="fa-regular fa-circle-check"></i><span style="letter-spacing: 0.10417rem;" class="flex-fill">CONFIRM</span></div>
                </button>
            </div>

            {{-- reject types --}}
            <div class="reject-type-container mt-5">
                {{-- reject type: setting --}}
                <div class="d-flex gap-2 flex-column reject-type-category">
                    <div class="reject-icon d-flex gap-2 py-1 px-3">
                        <i class="fa-regular fa-gear"></i>
                        <span>SETTING REJECT</span>
                    </div>

                    <div class="reject-list flex-fill px-3 mt-1" data-reject-group-id="1">
                    </div>

                    {{-- total --}}
                    <div class="d-flex p-2 reject-total-container">
                        <div class="reject-title my-auto">
                            TOTAL (PCS)
                        </div>
                        <div class="reject-value my-auto flex-fill current-production-line-data reject-group-count" data-reject-group-id="1">
                            0
                        </div>
                    </div>
                </div>
                {{-- reject type: process --}}
                <div class="d-flex gap-2 flex-column reject-type-category">
                    <div class="reject-icon d-flex gap-2 py-1 px-3">
                        <i class="fa-regular fa-gear"></i>
                        <span>PROCESS REJECT</span>
                    </div>

                    <div class="reject-list flex-fill px-3 mt-1" data-reject-group-id="3">
                    </div>

                    {{-- total --}}
                    <div class="d-flex p-2 reject-total-container">
                        <div class="reject-title my-auto">
                            TOTAL (PCS)
                        </div>
                        <div class="reject-value my-auto flex-fill current-production-line-data reject-group-count" data-reject-group-id="3">
                            0
                        </div>
                    </div>
                </div>
                {{-- reject type: material --}}
                <div class="d-flex gap-2 flex-column reject-type-category">
                    <div class="reject-icon d-flex gap-2 py-1 px-3">
                        <i class="fa-regular fa-gear"></i>
                        <span>MATERIAL REJECT</span>
                    </div>

                    <div class="reject-list flex-fill px-3 mt-1" data-reject-group-id="2">

                    </div>

                    {{-- total --}}
                    <div class="d-flex p-2 reject-total-container">
                        <div class="reject-title my-auto">
                            TOTAL (PCS)
                        </div>
                        <div class="reject-value my-auto flex-fill current-production-line-data reject-group-count" data-reject-group-id="2">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>
@endsection

@section('templates')
@parent
<template id="template-reject-list-item">
    <div class="reject-list-item">
        <div class="reject-title my-auto reject-item-field" data-tag="name">
        </div>
        <div class="reject-value my-auto current-production-line-data reject-item-count">
        </div>
        <div class="reject-input d-flex">
            <button class="reject-item-button-sub"><i class="fa-solid fa-minus"></i></button>
            <div class="reject-input-value uncommit-reject-count">
            </div>
            <button class="reject-item-button-add"><i class="fa-solid fa-plus"></i></button>
        </div>
    </div>
</template>
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
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button" data-bs-dismiss="modal" onclick="submitReject()">YES</button>
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
    Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
            checkValidTerminalStatus();
        }).listen('.terminal.downtime-state-changed', (e) => {
            LivePage.terminalDowntimeStateChangedHandler(e);
        });;
    /** Work Center Idle */
    const STATUS_IDLE = 0;
    /** Work Center Die Change */
    const STATUS_DIE_CHANGE = 1;
    /** Work Center First Product Confirmation */
    const STATUS_FIRST_CONFIRMATION = 2;
    /** Work Center Running */
    const STATUS_RUNNING = 3;

    const pageValidStatus = [3]; //Running only
    function checkValidTerminalStatus() {
        if (!pageValidStatus.includes(LivePage.terminalData.workCenter.status)) {
            location.reload();
            return;
        }
    }

    var pageData = {
        uncommitRejectData: {}
    }
    var submitting = false;

    function setConfigToCurrentLine(configData) {
        if (LivePage.tabCurrentProductionLine)
            configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

        // currentProductionLineDataConfig.push(configData);
        return configData;
    }

    $(() => {
        LivePage.initializeProductionLineTab(function(e) {
            updateRejectList();
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

    function currentProductionLineChanged(e) {
        $('.current-production-line-data').data('production-line-id', e.id);
        //updateCurrentProductionLineConfigs(e.id);

        $('.terminal-link').removeClass('active');
        $(`.terminal-link[data-production-line-id="${e.id}"`).addClass('active');
        LivePage.updateLiveData();
    }



    function generateRejectListItemElement(rejectItemDetail) {


        let rejectItem = $($('#template-reject-list-item').html());

        let currentProductionLine = LivePage.tabCurrentProductionLine;

        rejectItem
            .data('reject-type-id', rejectItemDetail.id)
            .data('reject-group-id', rejectItemDetail.reject_group_id)
            .data('production-line-id', currentProductionLine.id)
            .find('.reject-item-field').each((idx, e) => {
                $(e).html(rejectItemDetail[$(e).data('tag')]);
            });

        rejectItem.find('button.reject-item-button-sub')
            .data('reject-type-id', rejectItemDetail.id)
            .data('reject-group-id', rejectItemDetail.reject_group_id)
            .data('production-line-id', currentProductionLine.id)
            .click((e) => {
                let rejectTypeId = $(e.currentTarget).data('reject-type-id');
                let currentProductionLineId = $(e.currentTarget).data('production-line-id');

                //subtract data
                if (!pageData.uncommitRejectData[currentProductionLineId])
                    pageData.uncommitRejectData[currentProductionLineId] = {};

                if (!pageData.uncommitRejectData[currentProductionLineId][rejectTypeId])
                    pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;

                if (pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] <= 0)
                    pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;
                else
                    pageData.uncommitRejectData[currentProductionLineId][rejectTypeId]--;

                updateUncommitedRejectCount();
                //console.log('sub:', e, rejectTypeId, pageData.uncommitRejectData[currentProductionLineId][rejectTypeId]);
            });

        rejectItem.find('button.reject-item-button-add')
            .data('reject-type-id', rejectItemDetail.id)
            .data('reject-group-id', rejectItemDetail.reject_group_id)
            .data('production-line-id', currentProductionLine.id)
            .click((e) => {
                let rejectTypeId = $(e.currentTarget).data('reject-type-id');
                let currentProductionLineId = $(e.currentTarget).data('production-line-id');

                //Add data
                if (!pageData.uncommitRejectData[currentProductionLineId])
                    pageData.uncommitRejectData[currentProductionLineId] = {};

                if (!pageData.uncommitRejectData[currentProductionLineId][rejectTypeId])
                    pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;

                if (pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] <= 0)
                    pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;

                let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count;

                let otherUncommitted = 1;
                Object.entries(pageData.uncommitRejectData[currentProductionLineId]).forEach(([key, value]) => {
                    otherUncommitted += value;
                });

                if (okPart - otherUncommitted < 0) {
                    return;
                }

                pageData.uncommitRejectData[currentProductionLineId][rejectTypeId]++;

                updateUncommitedRejectCount();
                //console.log('add:', e, rejectTypeId, pageData.uncommitRejectData[currentProductionLineId][rejectTypeId]);

            });

        rejectItem.find('.reject-item-count').data('reject-type-id', rejectItemDetail.id);

        rejectItem.find('.reject-input-value')
            .data('reject-type-id', rejectItemDetail.id)
            .data('reject-group-id', rejectItemDetail.reject_group_id)
            .data('production-line-id', currentProductionLine.id)
            .click((e) => {
                console.log('click:', $(e.currentTarget));
                showNumpadModal($(e.currentTarget), 'REJECT COUNT', 2);
                
            });
        return rejectItem;
    }

    function updateRejectList() {

        //clear list item
        $('.reject-list').html('');

        let currentProductionLine = LivePage.tabCurrentProductionLine;
        if (!currentProductionLine)
            return;

        currentProductionLine.part_data.part_reject_types.forEach(rejectTypeDetail => {
            if (!rejectTypeDetail.enabled)
                return;

            $(`.reject-list[data-reject-group-id="${rejectTypeDetail.reject_group_id}"`).append(generateRejectListItemElement(rejectTypeDetail));
        });
        updateUncommitedRejectCount();
    }

    function updateUncommitedRejectCount() {
        $('.reject-list-item').each((idx, e) => {

            let productionLineId = $(e).data('production-line-id');
            let rejectTypeId = $(e).data('reject-type-id');
            let rejectGroupId = $(e).data('reject-group-id');

            if (!rejectTypeId)
                return;

            //Uncommit Element
            let uncommitElement = $(e).find('.uncommit-reject-count');
            let uncommitValue = 0;
            if (pageData.uncommitRejectData[productionLineId] && pageData.uncommitRejectData[productionLineId][rejectTypeId])
                uncommitValue = pageData.uncommitRejectData[productionLineId][rejectTypeId];

            uncommitElement.html(uncommitValue);

        });
    }

    function submitReject() {
        //submit reject on current page
        if (submitting)
            return;
        submitting = true;

        let currentProductionLine = LivePage.tabCurrentProductionLine;
        if (!currentProductionLine) {
            submitting = false;
            return;
        }


        //prepare payload
        let payload = {
            _token: window.csrf.getToken(),
            production_line_id: currentProductionLine.id,
            data: [],
        };

        Object.entries(pageData.uncommitRejectData[currentProductionLine.id]).forEach(([rejectTypeId, count]) => {
            payload.data.push({
                reject_type_id: rejectTypeId,
                count: count
            });
        });



        $.post("{{ route('terminal.reject.set.reject',[ $plant->uid, $workCenter->uid ]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings

                    //clear input
                    pageData.uncommitRejectData[currentProductionLine.id] = [];
                    updateUncommitedRejectCount();

                } else {
                    alert(response.message);
                    //location.reload();
                }
            }).always(function() {
            submitting = false;
        });

    }

    function clearInput() {
        let currentProductionLine = LivePage.tabCurrentProductionLine;

        let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count;

        let otherUncommitted = 0;
        if (pageData.uncommitRejectData[currentProductionLine.id]) {
            Object.entries(pageData.uncommitRejectData[currentProductionLine.id]).forEach(([key, value]) => {
                otherUncommitted += value;
            });
        }

        if (okPart - otherUncommitted < 0) {
            pageData.uncommitRejectData[currentProductionLine.id] = [];
            updateUncommitedRejectCount();
        }
    }
</script>
@endsection