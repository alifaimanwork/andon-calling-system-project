@extends('layouts.terminal')
@include('components.commons.websocket')
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

        .btn {
            font-size: 1rem;
            color: white;
            font-weight: bold;
        }

        .btn-terminal {
            font-size: 0.9rem;
            color: white;
            font-weight: bold;
            width: 13rem;
            height: 3rem;
            box-shadow: 1px 1px 7px black;
            display: flex;
            justify-content: center;
            align-items: center;
            /*border: 0.1rem solid #FFFFFF;*/
        }

        .table-container {
            overflow-x: hidden;
        }

        .table thead {
            background-color: #CD437A;
            color: #FFFFFF;
        }

        .dataTables_scroll {
            width: 100%;
        }

        .dataTables_scroll,
        .dataTables_length,
        .dataTables_paginate {
            font-size: 0.8rem !important;
        }

        .table {
            width: 100% !important;
        }

        .table td {
            padding-top: 1rem;
            padding-bottom: 1rem;
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
@endsection

@section('body')
    <main class="px-3 py-2">

        <div class="d-flex flex-column">
            {{-- page title --}}
            <div class="d-flex flex-column">
                <div class="content-title">PRODUCTION PLANNING</div>
                <div style="vertical-align: middle;" class="d-flex align-items-center gap-2 content-sub-title">
                    <span>WORK CENTRE</span>
                    <i class='fa-duotone fa-caret-right'></i>
                    <span style="color: #000080;">{{ $workCenter->name }}</span>
                </div>
            </div>

            {{-- act button --}}
            <div class="d-flex gap-4 mt-3">
                <button class="btn btn-terminal" style="background-color: #FF8041" onclick="refreshAll()"><i
                        class="fa-duotone fa-arrows-rotate" style="margin-right: 0.5rem;"></i> REFRESH PLANNING</button>
                <button type="button" class="btn btn-terminal" style="background-color: #1F78B4"
                    onclick="startDieChange()"><i class="fa-light fa-wrench style=" style="margin-right: 0.5rem;"></i> START
                    DIE CHANGE</button>
            </div>
            <div class="d-flex gap-4 mt-3">
                <div class="d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" onclick="clearSelection()"></i>Clear Selection</button>
                    <div id="selectionText" class="ms-3 text-muted fst-italic">0 selected</div>
                </div>

                <div class="d-flex flex-fill justify-content-end">
                    <div class="d-flex align-items-center me-3">
                        <div class="text-nowrap primary-text me-2">SHIFT</div>
                        <div>
                            <select id="shift" class="form-select iposweb-selector shifttype-selector"
                                style="color: #9b003e; background-color: #dddddd;" onchange="updateShiftParameter()">
                                @foreach ($shiftTypes as $shiftType)
                                    <option value="{{ $shiftType->id }}"
                                        {{ $currentShift ? ($currentShift->shift_type_id == $shiftType->id ? 'selected' : ''):'' }}>
                                        {{ $shiftType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-nowrap primary-text me-2">PRODUCTION DATE</div>
                        <div>
                            <input type="text" class="form-control text-center" id="production-date"
                                style="color: #9b003e; background-color: #dddddd;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- production planning --}}
            <div class="table-container mt-3">
                <table id="pps-table" class="table">
                </table>
            </div>

            {{-- incomplete production --}}
            <div class="d-flex flex-column mt-3">
                <div class="d-flex gap-2 text-danger align-items-center">
                    <i class="fa-regular fa-circle-exclamation"></i>
                    <span style="font-weight: bold"><u>INCOMPLETE PRODUCTION</u></span>
                </div>
                <div class="table-container mt-1">
                    <table id="production-order-table" class="table table-striped">
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('modals')
    @parent
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
                        <button type="button" style="background-color: #4949C0;" class="btn p-2 modal-button dialog-result"
                            data-dialog-result="1" data-bs-dismiss="modal">YES</button>
                        <button type="button" style="background-color: #5B5A79" class="btn p-2 modal-button dialog-result"
                            data-dialog-result="0" data-bs-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('templates')
    @parent
    <template id="simple-checkbox">
        <input class="form-check-input m-0 p-0" type="checkbox" value="">
    </template>
@endsection


@section('scripts')
    @parent
    <script>
        //Websocket
        const pageValidStatus = [0];
        Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
            .listen('.terminal.status-changed', function(e) {

                if (!pageValidStatus.includes(e.currentStatus))
                    location.reload();

            });
    </script>

    <script>
        // Call Modal Dialog with custom text
        function showConfirmationModal(message) {
            return new Promise(function(resolve, reject) {
                // //console.log(message);
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

        //Page workflow
        function startDieChange() {
            //validate selection,
            //show confirmation,
            //post form data & redirect to die change page

            // text with html tags <br> : CONFIRM TO <br> START DIE CHANGE
            let text_die_change = 'CONFIRM TO <br> START DIE CHANGE';
            let text_line_not_assigned = 'NOT ALL LINE ASSIGNED,<br>CONTINUE DIE CHANGE?';
            var workCenter = <?php echo json_encode($workCenter); ?>;

            //Validate all
            let selectionRecords = selectedProductionOrders; //fetchAllCheckedProductionOrder();

            const maxLine = workCenter.production_line_count;

            let lineNos = [];
            let valid = true;
            let errorMessage = null;
            let errorTitle = null;

            //TODO: Localization error message
            if (selectionRecords.length <= 0) {
                valid = false;
                errorMessage = "No PPS / production order selected";
                errorTitle = "Error";
            } else {
                selectionRecords.forEach(e => {
                    if (lineNos.includes(e.line_no)) {
                        //duplicate sequence number (line)
                        valid = false;
                        errorMessage = "Duplicate Line Number";
                        errorTitle = "Error";
                    } else if (e.line_no > maxLine) {
                        //sequence number > production line count at work center (PPS error?)
                        valid = false;
                        errorMessage = "Invalid Line Number";
                        errorTitle = "Error";
                    }

                    lineNos.push(e.line_no);
                });
            }

            if (lineNos.length != maxLine) {
                if(lineNos.length > maxLine)
                    text_line_not_assigned = 'TOO MANY LINE ASSIGNED,<br>CONTINUE DIE CHANGE?';
                //console.log('Continue die change : line problem');
                var modal_die_change = showConfirmationModal(text_die_change);

                modal_die_change.then((result) => {
                    if (!result) {
                        //console.log('Die change : User cancelled');
                        return;
                    } else {
                        var confirm_line = showConfirmationModal(text_line_not_assigned);
                        confirm_line.then(function(result) {
                            if (!result) {
                                return;
                            } else {
                                if (!valid) {
                                    showWarningDialog(errorTitle, errorMessage);
                                    return;
                                }
                                //console.log('Line Assigned : User confirmed');
                                sendStartDieChange(selectionRecords,false);
                            }
                        });

                    }
                });
            } else {
                //console.log('Continue die change : allowed');
                var modal_die_change = showConfirmationModal(text_die_change);
                modal_die_change.then((result) => {
                    if (!result) {
                        //console.log('Die change : User cancelled');
                        return;
                    } else {
                        if (!valid) {
                            showWarningDialog(errorTitle, errorMessage);
                            return;
                        }
                        //console.log('Continue die change  : User confirmed');
                        sendStartDieChange(selectionRecords,false);

                    }
                });
            }
        }

        function sendStartDieChange(selectionRecords, forced = false)
        {
            $.post("{{ route('terminal.production-planning.set.start-die-change', [$plant->uid, $workCenter->uid]) }}",
                prepareStartDieChangePayload(selectionRecords, forced),
                function(data, status, xhr) {
                    //result code
                    const RESULT_OK = 0;
                    const RESULT_INVALID_STATUS = -1;
                    const RESULT_INVALID_PARAMETERS = -2;
                    const RESULT_RESTRICTED = -3;
                    //TODO: display error message in modal
                    if (data.result === RESULT_OK) {
                        //die change started,try refresh page
                        location.reload();
                    } 
                    else if(data.result === RESULT_RESTRICTED) {
                        //Restricted, ask force continue
                        message = data.message.replaceAll("\r\n",'<br>');
                        showWarningDialog("Error", message);
                        /*
                        message = message.concat("<br>Ignore wrong part ID?")
                        var modal_die_change = showConfirmationModal(message);
                        modal_die_change.then(
                            (resolve) => { 
                                if(resolve)
                                    sendStartDieChange(selectionRecords,true); 
                                }
                        );
                        */
                    }
                    else {
                        showWarningDialog("Error", data.message);
                        // location.reload();
                    }
                });
        }

        function showWarningDialog(title, text) {
            let modal = $('#warning-modal');
            modal.find('.modal-body p').html(text);
            modal.find('.modal-title').html(title);
            modal.modal('show');
        }

        function prepareStartDieChangePayload(selectedRecords,forced = false) {
            let production_orders = [];

            selectedRecords.forEach(function(e) {
                production_orders.push(e.id);
            });

            return {
                _token: window.csrf.getToken(),
                production_orders: production_orders,
                forced: forced ? 1 :0
            }
        }
    </script>

    <script>
        $(() => {
            $('#production-date').daterangepicker({
                //local format
                startDate: moment().format('YYYY-MM-DD'),
                locale: {
                    format: 'YYYY-MM-DD',
                },
                singleDatePicker: true,
                showDropdowns: false,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10)
            }, function(start, end, label) {
                updateProductionDateParameter(start.format('YYYY-MM-DD'));
            });
            updateShiftParameter(false);
            updateProductionDateParameter($('#production-date').val(), false);

            initializePpsDataTable();
            initializeProductionOrderDataTable();

        });

        var plant = <?php echo json_encode($plant); ?>;
        var workCenter = <?php echo json_encode($workCenter); ?>;
        var selectedProductionOrders = []; //selected



        function clearSelection() {
            selectedProductionOrders.length = 0;
            $('.production-order-checkbox').prop('checked', false);
            updateSelectedText();
        }
        //Handles
        function refreshAll() {
            ppsDataTable.reload();
            productionOrderDataTable.reload();
        }

        function fetchAllCheckedProductionOrder() {
            let selectedOrders = [];

            //collect from pps list
            $('.pps-check:checked').each((idx, e) => {
                selectedOrders.push($(e).data('record'));
            });

            //collect from pending production order
            $('.production-order-check:checked').each((idx, e) => {
                selectedOrders.push($(e).data('record'));
            });
            return selectedOrders;
        }

        function updateSelectedText() {
            //console.log(selectedProductionOrders.length);
            $('#selectionText').html(`${selectedProductionOrders.length} selected`);
        }

        //-- PPS Datatable --//
        var ppsDataTable = new DataTableLoader();

        function initializePpsDataTable() {
            ppsDataTable.initialize(
                "pps-table",
                "{{ route('terminal.production-planning.get.pps', [$plant->uid, $workCenter->uid]) }}",
                ppsDataTableConfig,
                ppsDataTableParameters);
        }

        function updateProductionDateParameter(date, fetchData = true) {
            ppsDataTableParameters.filters.production_date = date;
            productionOrderDataTableParameters.filters.production_date = date;
            if (fetchData) {
                ppsDataTable.reload();
                productionOrderDataTable.reload();
            }
        }

        function updateShiftParameter(fatchData = true) {
            ppsDataTableParameters.filters.shift_type_id = $('#shift').val();
            productionOrderDataTableParameters.filters.shift_type_id = $('#shift').val();
            if (fatchData) {
                ppsDataTable.reload();
                productionOrderDataTable.reload();
            }
        }
        var ppsDataTableConfig = {
            rowCallback: function(row, data) {

                if (data.part_id == null) {
                    let checkboxContainer = $(row).find('input').parent();
                    checkboxContainer.html('');
                    checkboxContainer.append('<i class="fa-solid fa-triangle-exclamation text-danger"></i>');
                    $(row).addClass('clickable').click(function(e) {
                        showWarningDialog('Missing Part',
                            `Part "<strong>${data.pps_part_no}</strong>" not found!`);
                    });
                } else {

                    let inputCheck = $(row).find('input');
                    inputCheck.addClass("production-order-checkbox");

                    inputCheck.data('record', data);
                    $(row).addClass('clickable')
                        .click(function(e) {
                            inputCheck.attr("checked", !inputCheck.attr("checked"));

                            //add into record if checked,
                            if (inputCheck.attr("checked")) {
                                inputCheck.prop("checked", true);
                                selectedProductionOrders.push(data);
                            } else {
                                inputCheck.prop("checked", false);
                                selectedProductionOrders.splice(selectedProductionOrders.indexOf(data), 1);
                            }
                            updateSelectedText();
                        });

                    //check if selectedProductionOrders contains this record, if so, check it
                    selectedProductionOrders.forEach(function(e) {
                        if (e.id == data.id) {
                            inputCheck.attr("checked", true);
                        }
                    });


                }
            },
            order: [
                [1, "asc"]
            ],
            dom: "<'row'<'col-sm-12'tr>>" + "<'row'<'d-flex justify-content-end align-items-center gap-3 mt-2'lp>>",
            scrollY: '14rem',
            scrollCollapse: true,
            columns: [{
                    title: '',
                    data: 'id',
                    orderable: false,
                    render: function(d) {
                        let check = $($(`#simple-checkbox`).html());
                        check.val(d);
                        check.addClass("pps-check");
                        return check.wrap("<div>").parent().html();
                    },
                    class: "table-cell-place-center"
                },
                {
                    title: 'NO',
                    data: '_no'
                },
                {
                    title: 'SEQUENCE',
                    data: 'pps_seq'
                },
                {
                    title: 'LINE NO',
                    data: 'line_no',
                },
                {
                    title: 'PRODUCTION ORDER',
                    data: 'order_no',
                },
                {
                    title: 'PART NUMBER',
                    data: 'pps_part_no',
                },
                {
                    title: 'PART NAME',
                    data: 'pps_part_name',
                },
                {
                    title: 'PLAN START',
                    data: 'plan_start',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).tz(plant.time_zone).format(
                            'YYYY-MM-DD HH:mm:ss'); //convert to local time
                    }
                },
                {
                    title: 'PLAN FINISH',
                    data: 'plan_finish',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).tz(plant.time_zone).format(
                            'YYYY-MM-DD HH:mm:ss'); //convert to local time
                    }
                },
                {
                    title: 'PLAN OUTPUT',
                    data: 'plan_quantity',
                },
                {
                    title: 'SHIFT',
                    data: 'pps_shift',
                },
                {
                    title: 'UNIT',
                    data: 'unit_of_measurement',
                },
                {
                    data: 'part_id',
                    visible: false
                }

            ]
        }


        var ppsDataTableParameters = {
            _token: window.csrf.getToken(),
            filters: {
                shift_type_id: null,
                production_date: null,
            }

        }

        //-- Production Order Datatable --//
        var productionOrderDataTable = new DataTableLoader();

        function initializeProductionOrderDataTable() {
            productionOrderDataTable.initialize(
                "production-order-table",
                "{{ route('terminal.production-planning.get.production-order', [$plant->uid, $workCenter->uid]) }}",
                productionOrderDataTableConfig,
                productionOrderDataTableParameters);
        }
        var productionOrderDataTableConfig = {
            rowCallback: function(row, data) {
                if (data.part_id == null) {
                    let checkboxContainer = $(row).find('input').parent();
                    checkboxContainer.html('');
                    checkboxContainer.append('<i class="fa-solid fa-triangle-exclamation text-danger"></i>');
                    $(row).addClass('clickable').click(function(e) {
                        showWarningDialog('Missing Part',
                            `Part "<strong>${data.pps_part_no}</strong>" not found!`)
                    });
                } else {

                    let inputCheck = $(row).find('input');
                    inputCheck.addClass("production-order-checkbox");
                    inputCheck.data('record', data);
                    $(row).addClass('clickable')
                        .click(function(e) {
                            inputCheck.attr("checked", !inputCheck.attr("checked"));


                            //add into record if checked,
                            if (inputCheck.attr("checked")) {
                                inputCheck.prop("checked", true);
                                selectedProductionOrders.push(data);
                            } else {
                                inputCheck.prop("checked", false);
                                selectedProductionOrders.splice(selectedProductionOrders.indexOf(data), 1);
                            }
                            updateSelectedText();
                        });

                    //check if selectedProductionOrders contains this record, if so, check it
                    selectedProductionOrders.forEach(function(e) {
                        if (e.id == data.id) {
                            inputCheck.attr("checked", true);
                        }
                    });

                }
            },
            dom: "<'row'<'col-sm-12'tr>>" + "<'row'<'d-flex justify-content-end align-items-center gap-3 mt-2'lp>>",
            scrollY: '9rem',
            scrollCollapse: true,
            order: [
                [1, "asc"]
            ],
            columns: [{
                    title: '',
                    data: 'id',
                    orderable: false,
                    render: function(d) {
                        let check = $($(`#simple-checkbox`).html());
                        check.val(d);
                        check.addClass("production-order-check");
                        return check.wrap("<div>").parent().html();
                    }
                },
                {
                    title: 'NO',
                    data: '_no'
                },
                {
                    title: 'SEQUENCE',
                    data: 'pps_seq'
                },
                {
                    title: 'LINE NO',
                    data: 'line_no'
                },
                {
                    title: 'PRODUCTION ORDER',
                    data: 'order_no',
                },
                {
                    title: 'PART NUMBER',
                    data: 'pps_part_no',
                },
                {
                    title: 'PART NAME',
                    data: 'pps_part_name',
                },
                {
                    title: 'PLAN START',
                    data: 'plan_start',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).tz(plant.time_zone).format(
                            'YYYY-MM-DD HH:mm:ss'); //convert to local time
                    }
                },
                {
                    title: 'PLAN FINISH',
                    data: 'plan_finish',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).tz(plant.time_zone).format(
                            'YYYY-MM-DD HH:mm:ss'); //convert to local time
                    }
                },
                {
                    title: 'PLAN OUTPUT',
                    data: 'plan_quantity',
                },
                {
                    title: 'ACTUAL OUTPUT',
                    data: 'actual_output',
                },
                {
                    title: 'SHIFT',
                    data: 'pps_shift',
                },
                {
                    title: 'UNIT',
                    data: 'unit_of_measurement',
                },
                {
                    data: 'part_id',
                    visible: false
                }
            ]
        }

        var productionOrderDataTableParameters = {
            _token: window.csrf.getToken(),
            filters: {
                shift_type_id: null,
                production_date: null,
                status: 1
            }
        }
    </script>
@endsection
