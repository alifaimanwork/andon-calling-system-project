@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'QUALITY'])

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

    .table tbody {
        font-weight: 500;
        color: #575353;
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
            @if(isset($workCenter,$workCenters))
            <div class="ms-md-3 mt-3">
                @include('pages.web.analysis.components.change-workcenter-selector')
            </div>
            @endif
            <div class="ms-md-3 mt-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="text-nowrap me-2 primary-text">DATE RANGE</div>
                    <div>
                        <input type="text" class="form-control text-center" id="production-date" style="color: #000080; background-color: #dddddd;">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-md-4 mt-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-check me-2"></i> REJECT SUMMARY STATUS
                    </div>
                    <div class="card-body" style="color:white;">
                        <div class="d-flex justify-content-between mt-3">
                            <div class="flex-fill m-1 d-flex flex-column align-items-center p-3" style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040; border-top-left-radius: 10px;">
                                <span style="font-size: 80%; font-weight: 600; color: #787779" class="primary-text">ACTUAL OUTPUT</span>
                                <span style="font-size: 200%; font-weight: 600; color: #000080" class="analysis-quality-data" data-tag="total_actual_output"></span>
                                <span style="font-size: 80%; color: #000080; font-weight: 600">PCS</span>
                            </div>
                            <div class="flex-fill m-1 d-flex flex-column align-items-center p-3" style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040;border-top-right-radius: 10px;">
                                <span style="font-size: 80%; font-weight: 600; color: #787779" class="primary-text">TOTAL REJECT</span>
                                <span style="font-size: 200%; font-weight: 600; color: #000080" class="analysis-quality-data" data-tag="total_reject_count"></span>
                                <span style="font-size: 80%; color: #000080; font-weight: 600">PCS</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between p-2 m-1" style="background-color: #f6e8ee;box-shadow: 0px 0px 4px #00000040;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                            <span class="primary-text text-wrap w-50">
                                REJECT PERCENTAGE (%)
                            </span>
                            <span style="font-size: 200%; font-weight: 600;color: #000080" class="align-self-center renderer-percentage analysis-quality-data" data-tag="reject_percentage">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card mobile-card mt-3">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body secondary-text" style="font-size: 2.5em;">
                        <form id="report-form" target="_blank" action="{{ route('analysis.quality.get.data', [$plant->uid]) }}" method="POST">
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

            <div class="col-12 col-md-4 d-flex justify-content-center align-items-center">
                <div>
                    <canvas id="defects"></canvas>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="d-flex flex-column h-100">
                    <h5 class="secondary-text">TOP 10 REJECT BY DEFECT PART (PCS)</h5>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="top10reject"></canvas>
                    </div>
                </div>
            </div>


            <div class="col-12 overflow-auto mt-3">
                <table id="production-line-datatable" class="table nowrap table-hover mt-3 text-wrap" style="width:100%; font-size:80%">
                </table>
            </div>

            <div class="col-12 overflow-auto mt-3">
                <table id="production-line-reject-datatable" class="table nowrap table-hover mt-3 text-wrap" style="width:100%; font-size:80%">
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
            startDate: localStorage['analysisStart'] ? localStorage['analysisStart'] : moment().format('YYYY-MM-DD'),
            endDate: localStorage['analysisEnd'] ? localStorage['analysisEnd'] : moment().format('YYYY-MM-DD'),
        }, function(start, end, label) {
            pageData.getData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            localStorage['analysisStart'] = start.format('YYYY-MM-DD');
            localStorage['analysisEnd'] = end.format('YYYY-MM-DD');
        });
        analysisPage.initialize();
        pageData.getData(datePicker.data('daterangepicker').startDate.format('YYYY-MM-DD'), datePicker.data('daterangepicker').endDate.format('YYYY-MM-DD'));

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

            $.post("{{ route('analysis.quality.get.data',[ $plant->uid ]) }}", payload, function(response, status, xhr) {
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
    };
    var analysisPage = {
        data: null,
        datatable: null,
        datatableRejectCount: null,
        initialize: function() {
            this.initializeDatatable()
                .initializeDatatableRejectCount()
                .initializeCharts();
            return this;
        },
        updateData: function() {
            let _this = this;

            $('.analysis-quality-data').each((index, e) => {
                let tag = $(e).data('tag');
                console.log(tag);
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
            analysisPage.updateDataTable()
                .updateDataTableRejectCount()
                .updateCharts();

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
            <th class="text-wrap" style="width: 50px;">TOTAL OUTPUT</th>
            <th class="text-wrap" style="width: 50px;">TOTAL REJECT</th>
            <th class="text-wrap" style="width: 50px;">SETTING REJECT (PCS)</th>
            <th class="text-wrap" style="width: 50px;">PROCESS REJECT (PCS)</th>
            <th class="text-wrap" style="width: 50px;">MATERIAL REJECT (PCS)</th>
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
                        title: 'PRODUCTION ORDER',
                        data: 'order_no',
                    }, {
                        title: 'PART NUMBER',
                        data: 'part_no',
                    },
                    {
                        title: 'PART NAME',
                        data: 'part_name',
                    }, {
                        title: 'TOTAL<br>OUTPUT',
                        data: 'actual_output'
                    },
                    {
                        title: 'TOTAL<br>REJECT',
                        data: 'reject_count'
                    },
                    {
                        title: 'SETTING<br>REJECT (PCS)',
                        data: 'reject_setting'
                    },
                    {
                        title: 'PROCESS<br>REJECT',
                        data: 'reject_process'
                    },
                    {
                        title: 'MATERIAL<br>REJECT',
                        data: 'reject_material'
                    }
                ],
                dom: 'rtip',
                scrollX: true,
            });
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
        */
        rejectsColumns: [],
        baseColumns: [{
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
                title: 'PRODUCTION ORDER',
                data: 'order_no',
            }, {
                title: 'PART NUMBER',
                data: 'part_no',
            },
            {
                title: 'PART NAME',
                data: 'part_name',
            }
        ],
        initializeDatatableRejectCount: function() {


            let merged = [];
            this.baseColumns.forEach(e => {
                merged.push({
                    'title': e.title,
                    'data': e.data,
                });
            });

            this.rejectsColumns.forEach(e => {
                merged.push({
                    'title': e.title,
                    'data': e.data,
                });
            });

            this.datatableRejectCount = $('#production-line-reject-datatable').DataTable({
                data: [],
                columns: merged,
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
        updateDataTableRejectCount: function() {
            let _this = this;

            if (this.datatableRejectCount)
                this.datatableRejectCount.destroy();

            $('#production-line-reject-datatable').html('');


            this.rejectsColumns.length = 0;

            this.data.defects.forEach(e => {
                _this.rejectsColumns.push({
                    title: e.name_2,
                    data: e.label
                });
            })


            _this.initializeDatatableRejectCount();


            _this.datatableRejectCount.clear()
            _this.datatableRejectCount.rows.add(_this.data.data);
            _this.datatableRejectCount.draw();
            return this;
        },
        updateCharts: function() {
            let _this = this;
            this.updateDefectChart(this.data.defects)
                .updateTop10Chart(this.data.defects)
            return this;
        },
        updateDefectChart(data) {
            let chart = this.charts.defects;

            let labels = [];
            let chartData = [];
            let backgroundColors = [];
            let borderColors = [];
            let colorGenerator = new ColorGenerator();



            let i = 0;
            data.forEach((e, index) => {
                labels.push(e.name_2);
                backgroundColors.push(colorGenerator.generateColor(index, 1));
                borderColors.push(colorGenerator.generateColor(index, 0.7));
                chartData.push(e.count);
            });


            chart.data.labels = labels;
            chart.data.datasets[0].data = chartData;
            chart.data.datasets[0].backgroundColor = backgroundColors;
            chart.data.datasets[0].borderColor = borderColors;


            chart.update();
            return this;
        },
        updateTop10Chart(data) {
            let chart = this.charts.top10reject;
            let labels = [];
            let chartData = [];
            let backgroundColors = [];
            let borderColors = [];
            let colorGenerator = new ColorGenerator();



            let i = 0;
            data.forEach((e, index) => {
                labels.push(e.name_2);
                backgroundColors.push(colorGenerator.generateColor(index, 1));
                borderColors.push(colorGenerator.generateColor(index, 1));
                chartData.push(e.count);
            });

            chart.data.labels = labels;
            chart.data.datasets[0].data = chartData;
            chart.data.datasets[0].backgroundColor = backgroundColors;
            chart.data.datasets[0].borderColor = borderColors;


            chart.update();
            return this;
        },
        chartOptions: {
            defects: {
                type: 'doughnut',
                data: {
                    datasets: [{
                        label: 'Rejects',
                        data: [],
                        backgroundColor: [],
                    }]
                },
                options: {
                    rotation: 0, // start angle in degrees
                    circumference: 360, // sweep angle in degrees

                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: "start"
                        }
                    }
                }
            },
            top10reject: {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        type: 'bar',
                        label: 'Count',
                        backgroundColor: [],
                        borderColor: [],
                        data: [],
                    }],
                },
                options: {
                    animations: {
                        y: {
                            duration: 1000,
                            delay: 0,
                        },
                        x: {
                            duration: 0
                        },
                        width: {
                            duration: 0
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 1,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }

            },

        },
        charts: {
            defects: undefined,
            top10reject: undefined,
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