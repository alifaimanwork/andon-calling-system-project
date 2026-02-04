@extends('layouts.terminal')
@include('components.terminal.numpad-modal')


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

    .rework-container {
        background-color: #DEDEDE;
        border: #000000 0.052083rem solid;
        height: 43rem;
    }

    .rework-container span {
        font-size: 1rem;
        font-weight: bold;
        line-height: 1;
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

    .table-wrap-rework table {
        width: 100%;
        font-size: 1rem;
        text-align: center;
        background-color: white;
        display: block;
        max-height: 37.6rem;
    }

    .table-wrap-rework table thead td {
        position: sticky;
        top: 0;
    }

    .table-wrap-rework table thead th {
        position: sticky;
        top: 0;
        background-color: #356771;
        color: white;
        border: #2B2B2B 0.052083rem solid;
        text-align: center;
    }

    input[type="radio"] {
        accent-color: #356771;
        width: 1.5rem;
        height: 1.5rem;
    }

    .rework-input:first-child span {
        background-color: #52AF61;
    }

    .rework-input span {
        background-color: #FF4151;
    }

    .rework-input span:first-child {
        border: #2B2B2B 0.052083rem solid;
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
        line-height: 1.2;
    }

    .rework-input span:nth-child(2) {
        text-align: center;
        background-color: white;
        border: #2B2B2B 0.052083rem solid;
        border-radius: 0.10416rem;
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 6rem;
        font-size: 1.5rem;
        line-height: 1.2;
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
        box-shadow: 1px 1px 7px black;
        display: flex;
        align-items: center;
        justify-content: center;
        /*border: 0.1rem solid #FFFFFF;*/
    }

    .btn:hover {
        color: black !important;
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

    .modal-button {
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
        width: 8rem;
        height: 2.5rem;
        border-radius: 0.8rem;
    }

    .uncommit-ng-count,
    .uncommit-ok-count {
        cursor: pointer;
    }

    .modal-error-message {
        color: #FF4151;
    }
</style>
@endsection

@section('body')
<main class="px-3 py-2">
    {{-- fakhrul --}}
    {{-- page-title --}}
    <div class="d-flex flex-column">
        <div class="content-title">REWORK</div>
        <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
            <span>WORK CENTRE</span>
            <i class='fa-duotone fa-caret-right'></i>
            <span style="color: #000080;">{{ $workCenter->name }}</span>
        </div>
    </div>

    {{-- rework container --}}
    <div class="d-flex flex-column rework-container mt-5 p-3">
        <span>TICK THE CORRECT PRODUCTION ORDER NUMBER AND FILL IN THE OK & NG FOR REWORK PART<br>AND PRESS SUBMIT.(SELECT ONE PRODUCTION ORDER AT A TIME)</span>
        <div class="d-flex flex-fill justify-content-between mt-3">
            {{-- table rework --}}
            <div class="table-wrap-rework">
                <table id="pending-rework-table" class="table table-striped">
                </table>
            </div>

            {{-- rework input --}}
            <div class="d-flex flex-column justify-content-start align-items-center pe-5">
                <div class="d-flex rework-input mt-5">
                    <span class="p-3 text-center">OK QUANTITY <br>(PCS)</span>
                    <span class="p-3 selected-production-line-data uncommit-ok-count" onclick="showNumpadModal(this, 'REWORK COUNT', 4)" data-type="ok">0</span>
                    <div class="d-flex flex-column">
                        <button class="btn flex-basis button-input selected-production-line-data selected-input" onclick="okUp(this)">+</button>
                        <button class="btn flex-basis button-input selected-production-line-data selected-input" onclick="okDown(this)">-</button>
                    </div>
                </div>

                <div class="d-flex rework-input mt-5">
                    <span class="p-3 text-center">NG QUANTITY <br>(PCS)</span>
                    <span class="p-3 selected-production-line-data uncommit-ng-count" onclick="showNumpadModal(this, 'REWORK COUNT', 4)" data-type="ng">0</span>
                    <div class="d-flex flex-column">
                        <button class="btn flex-basis button-input selected-production-line-data selected-input" onclick="ngUp(this)">+</button>
                        <button class="btn flex-basis button-input selected-production-line-data selected-input" onclick="ngDown(this)">-</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-4 mt-5">
                    <button type="button" class="btn btn-terminal py-3" style="background-color: #1C008A; color:#ffffff;" data-bs-toggle="modal" data-bs-target="#confirmation-modal">
                        <div class="d-flex justify-content-center gap-2 align-items-center"><i style="font-size: 1rem" class="fa-regular fa-circle-check"></i><span style="letter-spacing: 0.10417rem;">SUBMIT</span></div>
                    </button>

                    <button type="button" class="btn btn-terminal py-3" style="background-color: #FFBD4D; color:#000000;" data-bs-toggle="modal" data-bs-target="#close-modal">
                        <div class="d-flex justify-content-center gap-2 align-items-center"><i style="font-size: 1rem" class="fa-regular fa-circle-exclamation-check"></i><span style="letter-spacing: 0.10417rem;">CLOSE</span></div>
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
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button selected-production-line-data" data-bs-dismiss="modal" onclick="submitRework(this)">YES</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal first product confirmation --}}
<div class="modal fade" id="close-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- icon --}}
                <i style="color:#eed202" class="fa-regular fa-triangle-exclamation"></i>

                {{-- message --}}
                <span>CONFIRM TO CLOSE PRODUCTION ORDER: <span class="selected-production-line-data production-line-data" data-tag="order_no"></span>?</span>

                {{-- button --}}
                <div class="d-flex justify-content-around w-100 mt-3">
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button selected-production-line-data" data-bs-dismiss="modal" onclick="submitClose(this)">YES</button>
                    <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lock-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="d-flex flex-column w-100 align-items-center p-4">
                {{-- icon --}}
                <i style="color:#eed202" class="fa-solid fa-lock"></i>
                {{-- message --}}
                <div class="d-flex flex-column">
                    <span class="terminal-title-text">Verification Required</span>
                    <div>
                        <label class="text-white">Password</label>
                        <input id="unlock-password" type="password" class="form-group">
                    </div>
                    <div id="unlock-password-error" class="modal-error-message text-center"></div>
                </div>

                {{-- button --}}
                <div class="d-flex justify-content-center w-100 mt-3">
                    <button type="button" style="background-color: #5B5A79;" class="btn p-2 modal-button mx-1" onclick="redirect()">CANCEL</button>
                    <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button mx-1" onclick="submitUnlockRework()">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@include('snippets.live-production-scripts')
@parent
<script>
    var lock = <?php echo $rework_lock ? 'true' : 'false'; ?>;
    /** Rework Status Open */
    const REWORK_STATUS_OPEN = 0;
    /** Work Center Die Change */
    const REWORK_STATUS_COMPLETED = 1;
    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
        })
        .listen('.terminal.downtime-state-changed', (e) => {
            LivePage.terminalDowntimeStateChangedHandler(e);
        })
        .listen('.terminal.rework-updated', terminalReworkUpdated);;


    function showWarningDialog(title, text) {
        let modal = $('#warning-modal');
        modal.find('.modal-body p').html(text);
        modal.find('.modal-title').html(title);
        modal.modal('show');
    }

    function terminalReworkUpdated(e) {
        if (!e.productionLine)
            return;
        if (e.productionLine.rework_status == REWORK_STATUS_OPEN) {

            if (productionLines[e.productionLine.id]) {
                Object.entries(productionLines[e.productionLine.id].data).forEach(([key, value]) => {
                    productionLines[e.productionLine.id].data[key] = e.productionLine[key];
                });
                updateTableOKNGCount();
            }
        } else {

            if (selectedProductionLine.data.id == e.productionLine.id)
                selectedProductionLine = null;

            pendingReworkDataTable.reload();
            updateSelectedInputDisabled();
        }
    }

    function updateSelectedInputDisabled() {

        if (selectedProductionLine == null) {
            $('.selected-input').prop('disabled', true);
        } else {
            $('.selected-input').prop('disabled', false);
        }

    }

    function redirect() {
        window.location.href = "{{ route('terminal.index',[ $plant->uid,$workCenter->uid ]) }}";
    }

    function updateTableOKNGCount() {
        $('.field-pending-ok').each((index, e) => {
            let productionLineId = $(e).data('production-line-id');
            if (!productionLineId || !productionLines[productionLineId])
                return;

            $(e).html(productionLines[productionLineId].data.pending_ok);
        });
        $('.field-pending-ng').each((index, e) => {
            let productionLineId = $(e).data('production-line-id');
            if (!productionLineId || !productionLines[productionLineId])
                return;

            $(e).html(productionLines[productionLineId].data.pending_ng);
        });
    }

    var productionLines = {};

    var selectedProductionLine = null;
    var uncommitRework = {};
    var plant = <?php echo json_encode($plant); ?>;
    $(() => {
        initializePendingReworkDataTable();
        updateSelectedInputDisabled();
        if (lock)
            showLockModal();

    });

    //Datatable Loader
    var pendingReworkDataTable = new DataTableLoader();

    function initializePendingReworkDataTable() {
        pendingReworkDataTable.initialize(
            "pending-rework-table",
            "{{ route('terminal.rework.get.pending-rework',[ $plant->uid, $workCenter->uid ]) }}",
            pendingReworkDataTableConfig,
            pendingReworkDataTableParameters);
    }

    function changeProductionLine(e) {

        let productionLineId = $(e).data('production-line-id');
        selectedProductionLine = productionLines[productionLineId];
        $('.selected-production-line-data').data('production-line-id', productionLineId);

        $('.production-line-data').each((index, elem) => {
            let tag = $(elem).data('tag');
            let productionLineId = $(elem).data('production-line-id');

            if (!productionLines[productionLineId] || !productionLines[productionLineId].data)
                return;

            let productionLineData = productionLines[productionLineId].data;
            $(elem).html(productionLineData[tag]);
        });
        updateUncommitCounter();
        updateSelectedInputDisabled();
    }

    var pendingReworkDataTableConfig = {
        rowCallback: function(row, data) {
            productionLines[data.id] = {
                row: row,
                data: data
            };
            $(row).find('.field-pending-ok').data('production-line-id', data.id);
            $(row).find('.field-pending-ng').data('production-line-id', data.id);

            let inputCheck = $(row).find('input');
            inputCheck.data('production-line-id', data.id);
            $(row).addClass('clickable')
                .click(function(e) {
                    inputCheck.prop("checked", true);
                    changeProductionLine(inputCheck);
                });
            inputCheck.change(e => {
                // changeProductionLine(e);
            });

            if (selectedProductionLine == null) {
                inputCheck.prop("checked", true);
                changeProductionLine(inputCheck);
            } else if (selectedProductionLine.id == data.id) {
                inputCheck.prop("checked", true);
                changeProductionLine(inputCheck);
            }


        },
        order: [
            [1, "asc"]
        ],
        dom: "<'row'<'col-sm-12'tr>>" + "<'row'<'d-flex justify-content-end align-items-center gap-3 mt-2'lp>>",
        scrollY: '37.6rem',
        scrollCollapse: true,
        columns: [{
                title: '',
                data: 'id',
                orderable: false,
                render: function(d) {
                    let check = $('<td><input type="radio" name="radio"></td>');

                    // check.addClass("pps-check");
                    return check.wrap("<div>").parent().html();
                },
                class: "table-cell-place-center"
            },
            {
                title: 'PRODUCTION ORDER',
                data: 'order_no'
            },
            {
                title: 'PART NUMBER',
                data: 'part_data',
                render: function(d) {
                    let part = JSON.parse(d);
                    return part.part_no;
                }
            },
            {
                title: 'PART NAME',
                data: 'part_data',
                render: function(d) {
                    let part = JSON.parse(d);
                    return part.name;
                }
            },
            {
                title: 'OK (PCS)',
                data: 'pending_ok',
                className: 'field-pending-ok',
            },
            {
                title: 'NG (PCS)',
                data: 'pending_ng',
                className: 'field-pending-ng',
            },
            {
                title: 'PENDING (PCS)',
                data: 'pending_count',
            }

        ]
    }

    var pendingReworkDataTableParameters = {
        _token: window.csrf.getToken()
    }
</script>
<script>
    function okUp(sender) {
        let productionLineId = $(sender).data('production-line-id');
        if (!uncommitRework[productionLineId])
            uncommitRework[productionLineId] = {
                ok: 0,
                ng: 0
            };
        //TODO: max limit
        let uncommitPart = 1;
        uncommitPart += uncommitRework[productionLineId].ok;
        uncommitPart += uncommitRework[productionLineId].ng;

        let pendingPart = (productionLines[productionLineId] ? productionLines[productionLineId].data.pending_count - productionLines[productionLineId].data.pending_ok - productionLines[productionLineId].data.pending_ng : 0);

        if (pendingPart - uncommitPart < 0) {
            return;
        }
        uncommitRework[productionLineId]['ok']++;

        updateUncommitCounter();
    }

    function okDown(sender) {
        let productionLineId = $(sender).data('production-line-id');
        if (!uncommitRework[productionLineId])
            uncommitRework[productionLineId] = {
                ok: 0,
                ng: 0
            };

        if (uncommitRework[productionLineId]['ok'] > 0)
            uncommitRework[productionLineId]['ok']--;

        updateUncommitCounter();
    }

    function ngUp(sender) {
        let productionLineId = $(sender).data('production-line-id');
        if (!uncommitRework[productionLineId])
            uncommitRework[productionLineId] = {
                ok: 0,
                ng: 0
            };
        //TODO: max limit
        let uncommitPart = 1;
        uncommitPart += uncommitRework[productionLineId].ok;
        uncommitPart += uncommitRework[productionLineId].ng;

        let pendingPart = (productionLines[productionLineId] ? productionLines[productionLineId].data.pending_count - productionLines[productionLineId].data.pending_ok - productionLines[productionLineId].data.pending_ng : 0);

        if (pendingPart - uncommitPart < 0) {
            return;
        }
        uncommitRework[productionLineId]['ng']++;

        updateUncommitCounter();
    }

    function ngDown(sender) {
        let productionLineId = $(sender).data('production-line-id');
        if (!uncommitRework[productionLineId])
            uncommitRework[productionLineId] = {
                ok: 0,
                ng: 0
            };

        if (uncommitRework[productionLineId]['ng'] > 0)
            uncommitRework[productionLineId]['ng']--;

        updateUncommitCounter();
    }

    function updateUncommitCounter() {
        $('.uncommit-ng-count').each((index, e) => {
            let productionLineId = $(e).data('production-line-id');

            let count = 0;
            if (uncommitRework[productionLineId] && uncommitRework[productionLineId]['ng'])
                count = uncommitRework[productionLineId]['ng'];
            $(e).html(count);
        });
        $('.uncommit-ok-count').each((index, e) => {
            let productionLineId = $(e).data('production-line-id');

            let count = 0;
            if (uncommitRework[productionLineId] && uncommitRework[productionLineId]['ok'])
                count = uncommitRework[productionLineId]['ok'];
            $(e).html(count);
        });
    }
</script>
<script>
    //Action
    var submitting = false;

    function showLockModal() {
        let modal = $('#lock-modal');
        modal.modal('show');
    }

    function submitUnlockRework(sender) {
        let pass = $('#unlock-password').val();
        if (!pass || pass.length == 0)
            return;

        let payload = {
            _token: window.csrf.getToken(),
            password: pass
        };

        $.post("{{ route('terminal.rework.set.unlock',[ $plant->uid, $workCenter->uid ]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;
                const RESULT_RESTRICTED = -3;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings
                    lock = false;
                    $('#lock-modal').modal('hide');
                } else {
                    $('#unlock-password-error').html(response.message);
                    //location.reload();
                }
            }).always(function() {
            submitting = false;
        });
    }

    function submitRework(sender) {
        if (lock)
            return;

        let currentProductionLineId = $(sender).data('production-line-id');

        //submit pending on current page
        if (submitting)
            return;

        submitting = true;


        //prepare payload
        /*
        {
            production_line_id: <production_line_id>
            ok_count: <ok count>
            ng_count: <ng count>
        }
        */
        let okCount = 0;
        if (uncommitRework[currentProductionLineId] && uncommitRework[currentProductionLineId]['ok'])
            okCount = uncommitRework[currentProductionLineId]['ok'];

        let ngCount = 0;
        if (uncommitRework[currentProductionLineId] && uncommitRework[currentProductionLineId]['ng'])
            ngCount = uncommitRework[currentProductionLineId]['ng'];

        if (ngCount <= 0 && okCount <= 0) { //nothing to submit, abort
            submitting = false;
            return;
        }

        let payload = {
            _token: window.csrf.getToken(),
            production_line_id: currentProductionLineId,
            ok_count: okCount,
            ng_count: ngCount
        };

        $.post("{{ route('terminal.rework.set.rework',[ $plant->uid, $workCenter->uid ]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings

                    //clear input
                    if (uncommitRework[currentProductionLineId]) {
                        uncommitRework[currentProductionLineId]['ok'] = 0;
                        uncommitRework[currentProductionLineId]['ng'] = 0;
                    }
                    updateUncommitCounter();

                } else {
                    alert(response.message);
                    //location.reload();
                }
            }).always(function() {
            submitting = false;
        });

    }

    function submitClose(sender) {

        if (lock)
            return;

        let currentProductionLineId = $(sender).data('production-line-id');

        //submit pending on current page
        if (submitting)
            return;

        submitting = true;


        //prepare payload
        /*
        {
            production_line_id: <production_line_id>
        }
        */
        let payload = {
            _token: window.csrf.getToken(),
            production_line_id: currentProductionLineId,
        };

        $.post("{{ route('terminal.rework.set.close',[ $plant->uid, $workCenter->uid ]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    //update production line reject settings

                    //clear input
                    if (uncommitRework[currentProductionLineId]) {
                        uncommitRework[currentProductionLineId]['ok'] = 0;
                        uncommitRework[currentProductionLineId]['ng'] = 0;
                    }
                    updateUncommitCounter();


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