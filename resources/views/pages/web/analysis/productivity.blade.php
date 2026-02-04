@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation', ['dropMenuSelected' => 'PRODUCTIVITY'])
@section('head')
    @parent
    <style>
        .table {
            background-color: #FFFFFF;
        }

        .table thead {
            background-color: #CB84A3;
            color: #FFFFFF;
        }

        .table thead th {
            font-size: 14px;
            font-weight: 500;
        }

        .table th,
        .table td {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .table tbody {
            font-weight: 500;
            color: #575353;
        }
    </style>
@endsection

@section('body')
    <main>
        @yield('drop-menu-navigation')
        <div class="container">
            @yield('mobile-title')
            <div class="my-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start">
                <div class="mt-3">
                    @include('components.web.change-plant-selector')
                </div>
                @if (isset($workCenter, $workCenters))
                    <div class="ms-md-3 mt-3">
                        @include('pages.web.analysis.components.change-workcenter-selector')
                    </div>
                @endif
                <div class="ms-md-3 mt-3">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="text-nowrap me-2 primary-text">DATE RANGE</div>
                        <div>
                            <input type="text" class="form-control text-center" id="production-date"
                                style="color: #000080; background-color: #dddddd;">
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-12 col-md-4 mt-3">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTIVITY SUMMARY STATUS
                        </div>
                        <div class="card-body" style="color:white;">
                            <div class="d-flex justify-content-between mt-3">
                                <div class="flex-fill m-1 d-flex flex-column align-items-center p-3"
                                    style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040; border-top-left-radius: 10px;">
                                    <span class="primary-text "
                                        style="font-size: 80%; color: #787779; font-weight: 600;">PLAN OUTPUT</span>
                                    <span style="font-size: 200%; font-weight: 600; color: #000080"
                                        class="analysis-productivity-data" data-tag="total_standard_output">1190</span>
                                    <span style="font-size: 80%; color: #000080; font-weight: 600">PCS</span>
                                </div>
                                <div class="flex-fill m-1 d-flex flex-column align-items-center p-3"
                                    style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040;border-top-right-radius: 10px;">
                                    <span class="primary-text "
                                        style="font-size: 80%; font-weight: 600; color: #787779">ACTUAL OUTPUT</span>
                                    <span style="font-size: 200%; font-weight: 600; color: #000080"
                                        class="analysis-productivity-data" data-tag="total_actual_output">1190</span>
                                    <span style="font-size: 80%; color: #000080; font-weight: 600">PCS</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between p-2 m-1"
                                style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                                <span class="primary-text text-wrap w-50" style="color: #787779">
                                    PRODUCTIVITY PERCENTAGE (%)
                                </span>
                                <span class="align-self-center renderer-percentage  analysis-productivity-data"
                                    style="font-size: 200%; font-weight: 600;color: #000080" data-tag="performance">
                                    86 %
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card mobile-card mt-3">
                        <div class="card-header">
                            <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                        </div>
                        <div class="card-body secondary-text" style="font-size: 2.5em;">
                            <form id="report-form" target="_blank"
                                action="{{ route('analysis.productivity.get.data', [$plant->uid]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="date_start">
                                <input type="hidden" name="date_end">
                                <input type="hidden" name="work_center_uid">
                                <input type="hidden" name="format">
                                <button type="submit" class="blank-button px-1" onclick="pageData.download('download')">
                                    <i class="fa-light fa-file-spreadsheet"></i>
                                </button>
                                <button type="submit" class="blank-button px-1" onclick="pageData.download('print')">
                                    <i class="fa-solid fa-print"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 mt-3">
                    <div class="d-flex flex-column h-100">
                        <h5 class="secondary-text">PRODUCTIVITY - DAY SHIFT (PCS)</h5>
                        <div class="w-100 my-3" style="flex-grow:1">
                            <canvas id="day_productivity"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 mt-3">
                    <div class="d-flex flex-column h-100">
                        <h5 class="secondary-text">PRODUCTIVITY - NIGHT SHIFT (PCS)</h5>
                        <div class="w-100 my-3" style="flex-grow:1">
                            <canvas id="night_productivity"></canvas>
                        </div>


                    </div>
                </div>

                <div class="col-12 overflow-auto mt-3">
                    <table id="production-line-datatable" class="table nowrap table-hover mt-3 text-wrap"
                        style="width:100%; font-size:80%">
                    </table>
                </div>
            </div>




        </div>
    </main>
@endsection


@section('scripts')
    @parent
    <script>
        $(() => {
            if ((new URLSearchParams(window.location.search)).get('r')) {
                localStorage.clear();
            }
            //Renderer
            $('.renderer-percentage').data('render', (e, value, data) => {
                if (value == null || isNaN(value))
                    return '-';
                return `${(value * 100).toFixed(2)}%`;
            });

            //Date Range picker
            let datePicker = $('#production-date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                },
                startDate: localStorage['analysisStart'] ? localStorage['analysisStart'] : moment().format(
                    'YYYY-MM-DD'),
                endDate: localStorage['analysisEnd'] ? localStorage['analysisEnd'] : moment().format(
                    'YYYY-MM-DD'),
            }, function(start, end, label) {
                pageData.getData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                localStorage['analysisStart'] = start.format('YYYY-MM-DD');
                localStorage['analysisEnd'] = end.format('YYYY-MM-DD');
            });
            analysisPage.initialize();
            pageData.getData(datePicker.data('daterangepicker').startDate.format('YYYY-MM-DD'), datePicker.data(
                'daterangepicker').endDate.format('YYYY-MM-DD'));

            localStorage['analysisStart'] = datePicker.data('daterangepicker').startDate.format('YYYY-MM-DD');
            localStorage['analysisEnd'] = datePicker.data('daterangepicker').endDate.format('YYYY-MM-DD');
        });

        var pageData = {
            date_start: undefined,
            date_end: undefined,
            work_center_uid: undefined,
            getData: function(dateStart, dateEnd) {

                let payload = {
                    _token: window.csrf.getToken(),
                    work_center_uid: '{{ $workCenter->uid }}',
                    date_start: dateStart,
                    date_end: dateEnd,
                }
                this.date_start = dateStart;
                this.date_end = dateEnd;
                this.work_center_uid = '{{ $workCenter->uid }}';
                $.post("{{ route('analysis.productivity.get.data', [$plant->uid]) }}", payload, function(response,
                    status, xhr) {
                    //result code
                    const RESULT_OK = 0;
                    const RESULT_INVALID_STATUS = -1;
                    const RESULT_INVALID_PARAMETERS = -2;

                    //TODO: display error message in modal
                    if (response.result === RESULT_OK) {
                        analysisPage.data = response.data;
                        analysisPage.updateData();

                    } else {
                        alert(response.message);
                    }
                });
            },
            download: function(format) {
                $('#report-form').find('[name="date_start"]').val(this.date_start);
                $('#report-form').find('[name="date_end"]').val(this.date_end);
                $('#report-form').find('[name="work_center_uid"]').val(this.work_center_uid);
                $('#report-form').find('[name="format"]').val(format);
                // $('#report-form').submit();
            }
        }


        var analysisPage = {
            data: null,
            datatable: null,
            initialize: function() {
                this.initializeDatatable().initializeCharts();
                return this;
            },
            updateData: function() {
                let _this = this;

                $('.analysis-productivity-data').each((index, e) => {
                    let tag = $(e).data('tag');

                    let shiftTypeId = $(e).data('shift-type-id'); //set shift type id to select shift data

                    let renderer = $(e).data('render');
                    let data;
                    if (shiftTypeId) {
                        data = _this.data.shifts.find(e => {
                            return e.shift_type_id == shiftTypeId;
                        });
                    } else {
                        data = _this.data;
                    }

                    let val = '-';
                    if (data)
                        val = data[tag];

                    if (typeof(renderer) === 'function') {
                        val = renderer(e, val, data);
                    }

                    if (typeof(val) !== 'undefined')
                        $(e).html(val);
                });
                analysisPage.updateDataTable().updateCharts();

                return this;
            },
            /*
                <th>NO</th>
                <th>DATE</th>
                <th>SHIFT</th>
                <th>LINE</th>
                <th class="text-wrap" style="width: 50px;">PRODUCTION ORDER</th>
                <th>PART NUMBER</th>
                <th>PART NAME</th>
                <th class="text-wrap" style="width: 50px;">TOTAL WORKING HOURS</th>
                <th class="text-wrap" style="width: 50px;">TOTAL PLAN</th>
                <th class="text-wrap" style="width: 50px;">TOTAL STANDARD OUTPUT</th>
                <th class="text-wrap" style="width: 50px;">TOTAL ACTUAL OUTPUT</th>
                <th class="text-wrap" style="width: 50px;">PRODUCTIVITY (%)</th>
            */
            initializeDatatable: function() {
                this.datatable = $('#production-line-datatable').DataTable({
                    data: [],
                    columns: [{
                            title: 'NO',
                            data: 'no',
                        }, {
                            title: 'DATE',
                            data: 'shift_date',
                        }, {
                            title: 'SHIFT',
                            data: 'shift_type_id',
                            render: function(data, type, row) {
                                const shiftMap = {
                                    '1': 'Day',
                                    '2': 'Night'
                                };
                                return shiftMap[data];
                            }
                        },
                        {
                            title: 'LINE',
                            data: 'line_no',
                        }, {
                            title: 'PRODUCTION <br> ORDER',
                            data: 'order_no',
                        }, {
                            title: 'PART NUMBER',
                            data: 'part_no',
                        },
                        {
                            title: 'PART NAME',
                            data: 'part_name',
                        }, {
                            title: 'TOTAL<br>WORKING<br>HOURS',
                            data: 'runtimes_plan',
                            render: value => {
                                if (value == null || isNaN(value))
                                    return '-';
                                return `${(value / 3600).toFixed(2)}`;
                            }
                        },
                        {
                            title: 'TOTAL<br>PLAN',
                            data: 'plan_quantity'
                        },
                        {
                            title: 'TOTAL<br>STANDARD<br>OUTPUT',
                            data: 'standard_output'
                        },
                        {
                            title: 'TOTAL<br>ACTUAL<br>OUTPUT',
                            data: 'actual_output'
                        },
                        {
                            title: 'PRODUCTIVITY<br>(%)',
                            data: 'performance',
                            render: function(data, type, row) {
                                if (!data)
                                    data = 0;
                                return (data * 100).toFixed(0);
                            }
                        },
                    ],
                    dom: 'rtip',
                    scrollX: true,
                });
                return this;
            },
            updateDataTable: function() {
                let _this = this;
                if (!_this.datatable)
                    _this.initializeDatatable();

                _this.datatable.clear()
                _this.datatable.rows.add(_this.data.data);
                _this.datatable.draw();
                return this;
            },
            updateCharts: function() {
                let _this = this;

                let dayShiftData = this.data.shifts.find((e) => {
                    return e.shift_type_id == 1;
                });

                let nightShiftData = this.data.shifts.find((e) => {
                    return e.shift_type_id == 2;
                });

                const dayColors = [
                    '#6A042D',
                    '#A4114C',
                    '#B33D6C',
                    '#CC7095',
                    '#E1A2BB',
                    '#A3A3A3'
                ];
                const nightColors = [
                    '#1B0C4D',
                    '#223F6E',
                    '#28718F',
                    '#2C8AA0',
                    '#2FA3B0',
                    '#35D5D0'
                ];

                var dataMap = {
                    day_productivity: [dayShiftData.hourly_data, dayColors],
                    night_productivity: [nightShiftData.hourly_data, nightColors],
                };

                Object.entries(this.charts).forEach(([key, chart]) => {
                    _this.updateProductivityChart(key, chart, dataMap[key][0], dataMap[key][1]);
                });
                return this;
            },
            updateProductivityChart: function(chartId, chart, value, colorsets) {
                let labels = [];
                let datasets = [];
                //get min max

                let max = null;
                let min = null;

                Object.entries(value).forEach(([key, data]) => {
                    let keyVal = parseInt(key);
                    if (min == null) {
                        min = keyVal;
                        max = keyVal;
                    }
                    if (min > keyVal)
                        min = keyVal;
                    if (max < keyVal)
                        max = keyVal;


                });
                if (min != null) {
                    let datasetCount = max - min + 1;
                    Object.entries(value).forEach(([key, data]) => {
                        Object.entries(data.line_data).forEach(([lineNo, lineData]) => {
                            let dataset = datasets.find(e => {
                                return e.lineNo == lineNo;
                            });

                            if (!dataset) {
                                dataset = {
                                    lineNo: lineNo,
                                    label: `LINE ${lineNo}`,
                                    backgroundColor: colorsets[lineNo - 1],
                                    borderColor: colorsets[lineNo - 1],
                                    data: new Array(datasetCount).fill(0),
                                };
                                datasets.push(dataset);
                            };

                            dataset.data[key - min] += lineData.count;
                        });
                    });

                    for (let n = min; n <= max; n++) {
                        let h = n;
                        if (h > 24)
                            h -= 24;

                        let start = `${h}:00`;

                        let s = moment(start, 'H:mm');
                        let e = moment(start, 'H:mm').add(1, 'hours');

                        labels.push(`${s.format('ha')}-${e.format('ha')}`);
                    }
                }
                //construct blocks

                // Object.entries(value).forEach(([key, data]) => {
                //     let s = moment(data['start'], 'H:m');
                //     let e = moment(data['start'], 'H:m').add(1, 'hours');
                //     labels.push(`${s.format('Ha')}-${e.format('ha')}`);
                // });

                // console.log(datasets, labels);
                chart.data.labels = labels;
                chart.data.datasets = datasets;
                chart.update();

                if (chart.data.labels.length > 0) {
                    $(`#${chartId}`).removeClass('d-none');

                } else {
                    $(`#${chartId}`).addClass('d-none');
                }

                // console.log(labels);
                return this;
            },
            chartOptions: {
                day_productivity: {
                    type: 'bar',
                    data: {
                        labels: [],
                        animations: {
                            y: {
                                duration: 2000,
                                delay: 500,
                            },
                        },
                        datasets: [],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        elements: {
                            bar: {
                                borderWidth: 2,
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }

                },
                night_productivity: {
                    type: 'bar',
                    data: {
                        labels: [],
                        animations: {
                            y: {
                                duration: 2000,
                                delay: 500,
                            },
                        },
                        datasets: [],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        elements: {
                            bar: {
                                borderWidth: 2,
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }

                }
            },
            charts: {
                day_productivity: undefined,
                night_productivity: undefined,
            },
            initializeCharts: function() {
                let _this = this;
                Object.entries(_this.chartOptions).forEach(([key, option]) => {
                    var ctx = document.getElementById(key).getContext('2d');
                    _this.charts[key] = new Chart(ctx, option);
                });
                return this;
            }
        };
    </script>
@endsection


@section('modals')
    @parent
    <div>

    </div>
@endsection
