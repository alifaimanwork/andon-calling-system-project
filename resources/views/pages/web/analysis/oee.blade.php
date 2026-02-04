@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'OEE'])

@section('head')
@parent
<style>
    @media only screen and (max-width: 768px) {
        .chart-title {
            font-size: 65% !important;
        }
    }

    .table {
        background-color: #FFFFFF;
    }

    .chart-label-middle.mini-chart {
        top: calc(50%);
        font-size: 1.2rem;
    }

    @media only screen and (max-width: 1400px) {
        .chart-label-middle.mini-chart {
            font-size: 1rem;
        }
    }

    .chart-label-middle {
        position: absolute;
        top: calc(50% - 20px);
        left: 0;
        right: 0;
        text-align: center;
        font-size: 1.6rem;
        font-weight: bold;
        color: #000080;
    }

    .chart-label-middle span:nth-child(2) {
        font-size: 0.8rem;
        font-weight: 600;
    }



    .bar-active {
        transition-duration: 500ms;
    }

    .bar-availability {
        background-color: #f98a8a;
    }

    .bar-availability .bar-active {
        background-color: #f31414;
    }

    .bar-performance {
        background-color: #f98a8a;
    }

    .bar-performance .bar-active {
        background-color: #f31414;
        ;
    }

    .bar-quality {
        background-color: #f98a8a;
    }

    .bar-quality .bar-active {
        background-color: #f31414;
    }

    .box-header {
        display: flex;
        padding: 0.3rem;
        font-weight: bold;
        font-size: 1.2rem;
        color: #000080;
    }

    .table {
        background-color: #FFFFFF;
    }

    .table thead {
        background-color: #CB84A3;
        color: #FFFFFF;
    }

    .table thead th {
        font-weight: 500;
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
    <div class="container mb-3">
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

        <div class="row">
            <div class="col-12 col-md-6 p-1 h-100 mt-3">
                <div class="card">
                    <div class="box-header justify-content-center">
                        DAY & NIGHT SHIFT (%)
                    </div>
                    <div class="card-body">

                        <div class="d-flex justify-content-center align-items-center">
                            <div style="height: 52% ; width: 52%" class="position-relative">
                                <canvas id="average_oee"></canvas>
                                <div class="chart-label-middle d-flex flex-column">
                                    <span class="renderer-percentage analysis-oee-data" data-tag="average_oee"></span>
                                    <span>AVERAGE OEE</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text text-center">AVAILABILITY</span>
                                <span class="primary-text renderer-bar-text analysis-oee-data" data-tag="average_availability"></span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container bar-availability renderer-bar-progress analysis-oee-data" data-tag="average_availability">
                                    <div class="bar-active" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text text-center">PERFORMANCE</span>
                                <span class="primary-text renderer-bar-text analysis-oee-data" data-tag="average_performance"></span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container bar-performance renderer-bar-progress analysis-oee-data" data-tag="average_performance">
                                    <div class="bar-active" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="secondary-text text-center">QUALITY</span>
                                <span class="primary-text renderer-bar-text analysis-oee-data" data-tag="average_quality"></span>
                            </div>
                            <div style="position: relative;height: 10px;padding: 0 !important;">
                                <div class="status-bar-container bar-quality renderer-bar-progress analysis-oee-data" data-tag="average_quality">
                                    <div class="bar-active" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 p-1 flex-fill mt-3">
                <div class="d-flex justify-content-between gap-3 flex-column h-100">
                    <div class="card flex-fill">
                        <div class="box-header ms-3">
                            DAY SHIFT (%)
                        </div>
                        <div class="card-body py-0 d-flex align-items-center">
                            <div class="container p-0">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="day_average_oee"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_oee" data-shift-type-id="1"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">OEE</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="day_average_availability"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_availability" data-shift-type-id="1"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">AVAILABILITY</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="day_average_performance" class="position-relative"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_performance" data-shift-type-id="1"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">PERFORMANCE</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="day_average_quality"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_quality" data-shift-type-id="1"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">QUALITY</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card flex-fill">
                        <div class="box-header ms-3">
                            NIGHT SHIFT (%)
                        </div>
                        <div class="card-body py-0 d-flex align-items-center">
                            <div class="container p-0">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="night_average_oee"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_oee" data-shift-type-id="2"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">OEE</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="night_average_availability"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_availability" data-shift-type-id="2"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">AVAILABILITY</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="night_average_performance"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_performance" data-shift-type-id="2"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">PERFORMANCE</div>
                                    </div>
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <canvas id="night_average_quality"></canvas>
                                            <span class="chart-label-middle mini-chart renderer-percentage analysis-oee-data" data-tag="average_quality" data-shift-type-id="2"></span>
                                        </div>
                                        <div class="text-center secondary-text chart-title">QUALITY</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-3 p-1">
                <div class="card mobile-card">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body secondary-text" style="font-size: 2.5em;">
                        <form id="report-form" target="_blank" action="{{ route('analysis.oee.get.data',[ $plant->uid ]) }}" method="POST">
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

        </div>

        <div class="row mt-3">
            <div class="col-12 overflow-auto p-1">
                <table id="production-line-datatable" class="table nowrap table-striped text-wrap" style="width:100%; font-size:80%">
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

        $('.renderer-bar-text').data('render', (e, value, data) => {
            let rounded = Math.round(value * 100);
            return `${rounded} / 100`;
        });

        $('.renderer-bar-progress').data('render', (e, value, data) => {
            $(e).find('.bar-active').css('width', `${(value*100).toFixed(0)}%`);

            if (value > 0.64) { //green
                $(e).find('.bar-active').css('background-color', '#28a745');
                $(e).css('background-color', '#99d096');
                // chart_oee.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                $(e).find('.bar-active').css('background-color', '#ff7f00');
                $(e).css('background-color', '#ffbf80');
                // chart_oee.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                $(e).find('.bar-active').css('background-color', '#f31414');
                $(e).css('background-color', '#F68788');
                // chart_oee.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }

            return undefined;
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


        //Fetch data
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

            $.post("{{ route('analysis.oee.get.data',[ $plant->uid ]) }}", payload, function(response, status, xhr) {
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

            $('.analysis-oee-data').each((index, e) => {
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
                        title: 'A (%)',
                        data: 'availability',
                        render: function(data, type, row) {
                            return (data * 100).toFixed(0);
                        }
                    },
                    {
                        title: 'P (%)',
                        data: 'performance',
                        render: function(data, type, row) {
                            return (data * 100).toFixed(0);
                        }
                    },
                    {
                        title: 'Q (%)',
                        data: 'quality',
                        render: function(data, type, row) {
                            return (data * 100).toFixed(0);
                        }
                    },
                    {
                        title: 'OEE (%)',
                        data: 'oee',
                        render: function(data, type, row) {
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

            var dataMap = {
                average_oee: this.data.average_oee,
                day_average_oee: dayShiftData.average_oee,
                day_average_availability: dayShiftData.average_availability,
                day_average_performance: dayShiftData.average_performance,
                day_average_quality: dayShiftData.average_quality,

                night_average_oee: nightShiftData.average_oee,
                night_average_availability: nightShiftData.average_availability,
                night_average_performance: nightShiftData.average_performance,
                night_average_quality: nightShiftData.average_quality,
            };

            Object.entries(this.charts).forEach(([key, chart]) => {
                _this.updateSweepChart(chart, dataMap[key]);
            });
            return this;
        },
        updateSweepChart: function(chart, value) {
            chart.data.datasets[0].data[0] = value;
            chart.data.datasets[0].data[1] = 1 - value;
            if (value > 0.64) { //green
                chart.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
            } else if (value < 0.65 && value > 0.3) { //orange
                chart.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
            } else { //red
                chart.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
            }
            chart.update();
            return this;
        },
        chartOptions: {
            average_oee: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'Average OEE',
                        data: [12, 88],
                        backgroundColor: ["Red", "#f88989"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle

                }
            },
            day_average_oee: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [13, 87],
                        backgroundColor: ["Red", "#f88989"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            day_average_availability: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [16, 84],
                        backgroundColor: ["Red", "#f88989"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            day_average_performance: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [74, 26],
                        backgroundColor: ["Orange", "#ffbe7f"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            day_average_quality: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [89, 11],
                        backgroundColor: ["Green", "#98cf95"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            night_average_oee: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [14, 86],
                        backgroundColor: ["Red", "#f88989"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            night_average_availability: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [27, 73],
                        backgroundColor: ["Red", "#f88989"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            night_average_performance: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [61, 39],
                        backgroundColor: ["Orange", "#ffbe7f"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                }
            },
            night_average_quality: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'OEE',
                        data: [82, 18],
                        backgroundColor: ["Green", "#98cf95"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                },
            }
        },
        charts: {
            average_oee: undefined,

            day_average_oee: undefined,
            day_average_availability: undefined,
            day_average_performance: undefined,
            day_average_quality: undefined,

            night_average_oee: undefined,
            night_average_availability: undefined,
            night_average_performance: undefined,
            night_average_quality: undefined,
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