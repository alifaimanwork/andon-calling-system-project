@extends('layouts.terminal')
@include('components.commons.websocket')
@include('components.terminal.break-resume')
@include('components.terminal.numpad-modal')

@section('head')
    @parent
    <style>
        /* start fakhrul */
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

        .btn-terminal {
            font-size: 0.8rem;
            color: white;
            font-weight: bold;
            width: 13.5rem;
            height: 3rem;
            box-shadow: 1px 1px 7px black;
            display: flex;
            justify-content: center;
            align-items: center;
            /*border: 0.1rem solid #FFFFFF;*/

        }

        table {
            border-spacing: 0.1042rem;
        }

        .table td {
            padding-top: 1rem;
            padding-bottom: 1rem;
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

        .btn-manhour {
            height: 2rem;
            border: solid #9C9C9C;
            border-left: 0rem;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #414141;
        }

        .btn-manhourminus {
            border-top: 0px;
        }

        .btn-manhour:first-child {
            border-radius: 0px 0.15625rem 0px 0px;
        }

        .btn-manhour:last-child {
            border-radius: 0px 0px 0.15625rem 0px;
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

        .dc-custom-scrollbar {
            position: relative;
            /* height: 16.40625rem; */
            height: 18.9583rem;
            overflow: hidden;
        }

        .idc-custom-scrollbar {
            position: relative;
            /* height: 9.7396rem; */
            height: 15rem;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .table-wrapper-scroll-y {
            display: block;
        }

        .table-wrapperinfo-scroll-y {
            display: block;
            /* margin-top: 13px; */
        }

        td input[type="text"i] {
            padding: 0.05208rem 0.1042rem;
        }

        .input-mp {
            width: 4.6875rem;
            height: 4rem;
            border-style: solid;
            border-color: #9C9C9C;
            border-radius: 0.15625rem 0px 0px 0.15625rem;
            text-align: center;
            display: inline-block;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plus-custom {
            height: 23px;
            border: solid #9C9C9C;
            border-radius: 0px 3px 0px 0px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .minus-custom {
            height: 23px;
            border: solid #9C9C9C;
            border-radius: 0px 0px 3px 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top: 0px;
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
        <div class="d-flex flex-column">

            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    {{-- page-title --}}
                    <div class="d-flex flex-column">
                        <div class="content-title">DIE CHANGE</div>
                        <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                            <span>WORK CENTRE</span>
                            <i class='fa-duotone fa-caret-right'></i>
                            <span style="color: #000080;">{{ $workCenter->name }}</span>
                        </div>
                    </div>

                    {{-- act button --}}
                    <div class="d-flex gap-4 mt-3">

                        <button type="button" class="btn btn-terminal" style="background-color: #1F78B4" onclick="firstProductConfirmation()">
                                <div class="d-flex gap-2 align-items-center">
                                    <i style="font-size: 1.3rem; margin-right: 0.5rem" class="fa-duotone fa-cube"></i>
                                    <span style="letter-spacing: 0.03rem;"class="flex-fill">FIRST PRODUCT<br>CONFIRMATION</span>
                                </div>
                        </button>

                        <button type="button" class="btn btn-terminal" style="background-color: #FF3A4C" onclick="cancelAllPlanning()">
                            <div class="d-flex gap-2 align-items-center">
                                <i style="font-size: 1.3rem; letter-spacing: 0.5rem;" class="fa-regular fa-xmark"></i>
                                <span style="letter-spacing: 0.03rem;" class="flex-fill">CANCEL ALL<br>PLANNING</span>
                            </div>
                        </button>

                    </div>
                </div>

                {{-- countdown timer --}}
                <div class="d-flex gap-3">
                    @yield('terminal-break-button')

                    <div class="d-flex flex-column">
                        <div class="d-flex">
                            <div class="text-pst">Planned Setup Time</div>
                            <div class="countd-pst live-downtime-timer" data-tag="plan_die_change" data-format="timer_full"
                                data-process="countdown" data-countdown="{{ $production->setup_time }}">&nbsp;</div>
                        </div>
                        <div class="countu-pst flex-fill unplan-die-change live-downtime-timer" data-tag="unplan_die_change"
                            data-format="timer_full">&nbsp;</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- die change table --}}
        <div class="table-wrapper-scroll-y dc-custom-scrollbar mt-3">

            <table class="table nowrap table-hover text-wrap" style="width:100%; font-size:1rem; text-align: center;">
                <thead style="position:sticky; top: 0; background-color: #CD437A; color: white;">
                    <tr>
                        <th scope="col">NO</th>
                        <th scope="col">LINE</th>
                        <th scope="col">PRODUCTION NUMBER</th>
                        <th scope="col">PART NUMBER</th>
                        <th scope="col">PART NAME</th>
                        <th scope="col">PLAN OUTPUT</th>
                        <th scope="col">PLAN START</th>
                        <th scope="col">PLAN END</th>
                    </tr>
                </thead>
                <tbody style="background-color: #FFFFFF; color: #414141;">
                    @foreach ($productionLines as $productionLine)
                        <tr>
                            <td scope="row">{{ $loop->iteration }}</td>
                            <td>{{ $productionLine->productionOrder->line_no }}</td>
                            <td>{{ $productionLine->productionOrder->order_no }}</td>
                            <td>{{ $productionLine->productionOrder->pps_part_no }}</td>
                            <td>{{ $productionLine->productionOrder->pps_part_name }}</td>
                            <td>{{ $productionLine->plan_quantity }}</td>
                            <td>{{ $plant->getLocalDateTime($productionLine->productionOrder->plan_start)->format('Y-m-d H:i:s') }}
                            </td>
                            <td>{{ $plant->getLocalDateTime($productionLine->productionOrder->plan_finish)->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        {{-- die change information table --}}
        <div class="d-flex flex-column mt-3">
            <div class="content-title">DIE CHANGE INFORMATION</div>

            {{-- manpower --}}
            <div class="d-flex gap-3 align-items-center mt-2">
                <span style="color:#414141; font-weight:400">MAN POWER</span>

                <div class="d-flex">
                    <div id="die-info-man-power" class="input-mp" style="color: #414141; cursor:pointer" onclick="showNumpadModal(this, 'MAN POWER INFO', 0)">
                        {{ $dieChangeInfo->man_power ?? 0 }}
                    </div>
                    <div class="d-flex flex-column">
                        <button type="button" class="btn btn-manhour" onclick="addManHour()">+</button>
                        <button type="button" class="btn btn-manhour btn-manhourminus" onclick="minusManHour()">-</button>
                    </div>
                </div>
            </div>

            {{-- table die change information --}}
            <div class="table-wrapperinfo-scroll-y idc-custom-scrollbar mt-2">
                <table class="table nowrap table-hover mt-0 text-wrap"
                    style="max-height:9.7396rem;width:100%; font-size:1rem; text-align: center;">
                    <thead style="position:sticky; top: 0; background-color: #EFB45C; color: white;">
                        <tr>
                            <th style="width: 7.8125rem;" scope="col"></th>
                            @for ($n = 0; $n < $dieChangeInfo->lot_count; $n++)
                                <th scope="col">LOT {{ $n + 1 }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody style="background-color: #FFFFFF; color: #414141;">
                        <tr>
                            <th scope="row">COIL / BAR</th>
                            @for ($n = 0; $n < $dieChangeInfo->lot_count; $n++)
                                <td><input type="text" class="die-info-coil-bar" data-lot="{{ $n + 1 }}"
                                        style="width:6.25rem;border-width: 0.10416rem"
                                        value="{{ $dieChangeInfo->coil_bar[$n] ?? '' }}"></td>
                            @endfor
                        </tr>
                        <tr>
                            <th scope="row">CHILD PART</th>
                            @for ($n = 0; $n < $dieChangeInfo->lot_count; $n++)
                                <td><input type="text" class="die-info-child-part" data-lot="{{ $n + 1 }}"
                                        style="width:6.25rem;border-width: 0.10416rem"
                                        value="{{ $dieChangeInfo->child_part[$n] ?? '' }}"></td>
                            @endfor
                        </tr>
                        <tr>
                            <th scope="row">MATERIAL PART</th>
                            @for ($n = 0; $n < $dieChangeInfo->lot_count; $n++)
                                <td><input type="text" class="die-info-material-part" data-lot="{{ $n + 1 }}"
                                        style="width:6.25rem;border-width: 0.10416rem"
                                        value="{{ $dieChangeInfo->material_part[$n] ?? '' }}"></td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
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
                        <button type="button" style="background-color: #4949C0;"
                            class="btn p-2 modal-button dialog-result" data-dialog-result="1"
                            data-bs-dismiss="modal">YES</button>
                        <button type="button" style="background-color: #5B5A79"
                            class="btn p-2 modal-button dialog-result" data-dialog-result="0"
                            data-bs-dismiss="modal">NO</button>
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
        Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
            .listen('.terminal.data-updated', (e) => {
                LivePage.terminalDataUpdatedHandler(e);
                checkValidTerminalStatus();
            })
            .listen('.terminal.status-changed', function(e) {

                if (!pageValidStatus.includes(e.currentStatus))
                    location.reload();

            });

        const pageValidStatus = [1]; //Status Die change only
        function checkValidTerminalStatus() {
            if (!pageValidStatus.includes(LivePage.terminalData.workCenter.status)) {
                location.reload();
                return;
            }
        }
    </script>
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

        var dieChangeExpired = false;
        var dieChangeInfo = <?php echo json_encode($dieChangeInfo); ?>;
        $(() => {
            LivePage.listenAnyChanges(function(e) {
                if (e.downtimes.unplan_die_change.total && !dieChangeExpired) {
                    $('.unplan-die-change').addClass('blinking-countup');
                    dieChangeExpired = true;
                }
            });
        });

        function addManHour() {
            let manHour = Number.parseInt($('#die-info-man-power').html()) + 1;
            $('#die-info-man-power').html(manHour);
        }

        function minusManHour() {
            let manHour = Number.parseInt($('#die-info-man-power').html()) - 1;
            if (manHour <= 0)
                manHour = 0;
            $('#die-info-man-power').html(manHour);
        }

        function cancelAllPlanning() {
            //TODO: confirmation using modal
            text_die_change_cancel = 'CONFIRM TO CANCEL DIE CHANGE?';

            var modal_die_change_cancel = showConfirmationModal(text_die_change_cancel);

            modal_die_change_cancel.then((result) => {
                if (!result) {
                    return;
                } else {
                    $.post("{{ route('terminal.die-change.set.cancel-all-planning', [$plant->uid, $workCenter->uid]) }}", {
                        _token: window.csrf.getToken()
                    }, function(data, status, xhr) {
                        //result code
                        const RESULT_OK = 0;
                        const RESULT_INVALID_STATUS = -1;
                        const RESULT_INVALID_PARAMETERS = -2;

                        //TODO: display error message in modal
                        if (data.result === RESULT_OK) {
                            //canceled,try refresh page
                            location.reload();
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    });
                }
            })
        }

        function firstProductConfirmation() {
            // console.log('firstProductConfirmation');
            // return;
            let payload = prepareConfirmDieChangePayload();
            let validateResult = validatePayload(payload);

            if (!validateResult.valid) {
                //TODO: show alert using modal
                showWarningDialog(validateResult.errorTitle, validateResult.errorMessage).then((result) => {
                    if (!result) {
                        return;
                    } else {
                        text_die_change_cancel = 'PROCEED TO FIRST PRODUCT CONFIRMATION?';

                        var modal_die_change_cancel = showConfirmationModal(text_die_change_cancel);

                        modal_die_change_cancel.then((result) => {
                            if (!result) {
                                return;
                            } else {

                                $.post("{{ route('terminal.die-change.set.first-product-confirmation', [$plant->uid, $workCenter->uid]) }}",
                                    payload,
                                    function(data, status, xhr) {
                                        //result code
                                        const RESULT_OK = 0;
                                        const RESULT_INVALID_STATUS = -1;
                                        const RESULT_INVALID_PARAMETERS = -2;

                                        //TODO: display error message in modal
                                        if (data.result === RESULT_OK) {
                                            //canceled,try refresh page
                                            location.reload();
                                        } else {
                                            alert(data.message);
                                            location.reload();
                                        }
                                    });
                            }
                        });
                    }
                });
            } else {
                text_die_change_cancel = 'PROCEED TO FIRST PRODUCT CONFIRMATION?';

                var modal_die_change_cancel = showConfirmationModal(text_die_change_cancel);

                modal_die_change_cancel.then((result) => {
                    if (!result) {
                        return;
                    } else {

                        $.post("{{ route('terminal.die-change.set.first-product-confirmation', [$plant->uid, $workCenter->uid]) }}",
                            payload,
                            function(data, status, xhr) {
                                //result code
                                const RESULT_OK = 0;
                                const RESULT_INVALID_STATUS = -1;
                                const RESULT_INVALID_PARAMETERS = -2;

                                //TODO: display error message in modal
                                if (data.result === RESULT_OK) {
                                    //canceled,try refresh page
                                    location.reload();
                                } else {
                                    alert(data.message);
                                    location.reload();
                                }
                            });
                    }
                });
            }
        }

        function prepareConfirmDieChangePayload() {
            let payload = {
                _token: window.csrf.getToken(),
                man_power: Number.parseInt($('#die-info-man-power').html()),
                lot_count: dieChangeInfo.lot_count,
                coil_bar: [],
                child_part: [],
                material_part: []
            }

            //preAllocate
            let n = 0;
            for (n = 0; n < dieChangeInfo.lot_count; n++) {
                payload.coil_bar.push(null);
                payload.child_part.push(null);
                payload.material_part.push(null);
            }

            $('input.die-info-coil-bar').each((idx, e) => {
                payload.coil_bar[$(e).data('lot') - 1] = $(e).val();
            });

            $('input.die-info-child-part').each((idx, e) => {
                payload.child_part[$(e).data('lot') - 1] = $(e).val();
            });

            $('input.die-info-material-part').each((idx, e) => {
                payload.material_part[$(e).data('lot') - 1] = $(e).val();
            });
            return payload;
        }

        function validatePayload(payload) {

            let valid = false;
            let errorMessage = "";
            let errorTitle = "";

            // console.log('validating', payload);

            //client side validation
            if (!Number.isInteger(payload.man_power) || payload.man_power < 0)
                return {
                    valid: false,
                    errorMessage: 'Invalid Man Power Value',
                    errorTitle: 'Invalid Input',
                };
            if (dieChangeInfo.lot_count != payload.lot_count)
                return {
                    valid: false,
                    errorMessage: 'Invalid Parameters',
                    errorTitle: 'Invalid Input',
                };

            let n = 0;
            for (n = 0; n < dieChangeInfo.lot_count; n++) {
                if (payload.coil_bar[n] == null || payload.child_part[n] == null || payload.material_part[n] ==
                    null) {
                    return {
                        valid: false,
                        errorMessage: 'Invalid Parameter',
                        errorTitle: 'Invalid Input',
                    };
                }
            }

            return {
                valid: true,
                errorMessage: null,
                errorTitle: null,
            };
        }
    </script>
@endsection
