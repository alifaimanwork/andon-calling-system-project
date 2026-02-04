@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'CALLING'])

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
        font-size: 11px;
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
                        <i class="fa-solid fa-clipboard-check me-2"></i> CALLING TOTAL SUMMARY
                    </div>
                    <div class="card-body" style="color:white;">
                        <div class="d-flex justify-content-around p-2 mt-2 mx-1" style="background-color: #f6e8ee;box-shadow: 0px 0px 2px #00000040;border-top-left-radius: 10px; border-top-right-radius: 10px;">
                            <span class="primary-text text-wrap w-50">
                                LEADER</br>(hrs)
                            </span>
                            <span style="font-size: 200%; font-weight: 600;color: #000080" class="align-self-center renderer-rounding-hours analysis-calling-data" data-tag="leader_hours">
                            </span>
                        </div>
                        <div class="d-flex justify-content-around p-2 m-1" style="background-color: #f6e8ee;box-shadow: 0px 0px 2px #00000040;border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
                            <span class="primary-text text-wrap w-50">
                                MAINTENANCE</br>(hrs)
                            </span>
                            <span style="font-size: 200%; font-weight: 600;color: #000080" class="align-self-center renderer-rounding-hours analysis-calling-data" data-tag="maintenance_hours">
                            </span>
                        </div>
                        <div class="d-flex justify-content-around p-2 m-1" style="background-color: #f6e8ee;box-shadow: 0px 0px 2px #00000040;border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
                            <span class="primary-text text-wrap w-50">
                                LOGISTIC</br>(hrs)
                            </span>
                            <span style="font-size: 200%; font-weight: 600;color: #000080" class="align-self-center renderer-rounding-hours analysis-calling-data" data-tag="logistic_hours">
                            </span>
                        </div>
                        <div class="d-flex justify-content-around p-2 m-1" style="background-color: #f6e8ee;box-shadow: 0px 0px 2px #00000040;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                            <span class="primary-text text-wrap w-50">
                                QC CHECK</br>(hrs)
                            </span>
                            <span style="font-size: 200%; font-weight: 600;color: #000080" class="align-self-center renderer-rounding-hours analysis-calling-data" data-tag="qc_check_hours">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mobile-card card mt-3">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body secondary-text" style="font-size: 2.5em;">
                        <form id="report-form" target="_blank" action="{{ route('analysis.calling.get.data', [$plant->uid]) }}" method="POST">
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

            <div class="col-12 col-md-1 d-flex justify-content-center align-items-center">
                <div>
                    <canvas id="callings"></canvas>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="d-flex flex-column h-100">
                    <h5 class="secondary-text mt-4">TOP 4 CALLING BY BREAKDOWN (MIN)</h5>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="top4calling"></canvas>
                    </div>
                </div>
            </div>           
            
            <div class="col-12 overflow-auto mt-3">
                <table id="production-line-datatable" class="table nowrap table-hover mt-3 text-wrap" style="width:100%; font-size:80%">
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

        $('.renderer-rounding-hours').data('render', (e, value, data) => {
            if (value == null || isNaN(value))
                return '-';
            return `${(value).toFixed(2)}`; //into 2 decimal places
        });

        // Date Range picker
        var datePicker = $('#production-date').daterangepicker({
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

            console.log('Fetching data with payload:', payload);

            $.post("{{ route('analysis.calling.get.data', [$plant->uid]) }}", payload, function(response, status, xhr) {
                const RESULT_OK = 0;

                if (response.result === RESULT_OK) {
                    console.log('Data received:', response.data);
                    analysisPage.data = response.data.data; // Adjusted to match the new structure
                    analysisPage.summary = response.data.summary; // Added to handle summary data
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
        summary: null, // Added to store summary data
        datatable: null,
        initialize: function() {
            this.initializeDatatable()
                .initializeCharts();
            return this;
        },
        updateData: function() {
            let _this = this;

            // Update summary data
            $('.analysis-calling-data').each((index, e) => {
                let tag = $(e).data('tag');
                let val = '-';
                if (_this.summary && _this.summary[tag] !== undefined) {
                    val = _this.summary[tag];
                }
                let renderer = $(e).data('render');
                if (typeof(renderer) === 'function') {
                    val = renderer(e, val);
                }
                if (typeof(val) !== 'undefined')
                    $(e).html(val);
            });

            analysisPage.updateDataTable()
                .updateCharts();

            return this;
        },
        initializeDatatable: function() {
            this.datatable = $('#production-line-datatable').DataTable({
                data: [],
                columns: [
                    { title: 'NO', data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { title: 'DATE', data: 'shift_date' },
                    { title: 'SHIFT', data: 'shift_type_id', render: function(data, type, row) {
                        const shiftMap = { '1': 'Day', '2': 'Night' };
                        return shiftMap[data];
                    }},
                    { title: 'LINE', data: 'line_no' },
                    { title: 'PRODUCTION ORDER', data: 'order_no' },
                    { title: 'PART NUMBER', data: 'part_no' },
                    { title: 'PART NAME', data: 'part_name' },
                    { title: 'TOTAL CALLING', data: 'total_calling', render: value => {
                        if (value == null || isNaN(value)) return '-';
                        return `${(value / 3600).toFixed(3)}`;
                    }},
                    { title: 'LEADER CALLING', data: 'leader_calling', render: value => {
                        if (value == null || isNaN(value)) return '-';
                        return `${(value / 3600).toFixed(3)}`;
                    }},
                    { title: 'MAINTENANCE CALLING', data: 'maintenance_calling', render: value => {
                        if (value == null || isNaN(value)) return '-';
                        return `${(value / 3600).toFixed(3)}`;
                    }},
                    { title: 'LOGISTIC CALLING', data: 'logistic_calling', render: value => {
                        if (value == null || isNaN(value)) return '-';
                        return `${(value / 3600).toFixed(3)}`;
                    }},
                    { title: 'QC CHECK CALLING', data: 'qc_check_calling', render: value => {
                        if (value == null || isNaN(value)) return '-';
                        return `${(value / 3600).toFixed(3)}`;
                    }},
                ],
                dom: 'rtip',
                scrollX: true,
            });
            return this;
        },
        // Update this part to ensure data is correctly added to the table
        updateDataTable: function() {
            let _this = this;
            if (!_this.datatable)
                _this.initializeDatatable();

            _this.datatable.clear();
            if (_this.data && _this.data.length > 0) {
                _this.datatable.rows.add(_this.data);
            }
            _this.datatable.draw();
            return this;
        },
        updateCharts: function() {
            let _this = this;
            if (!_this.data || _this.data.length === 0) {
                console.log('No data available for updating calling chart.');
                return this;
            }
            //this.updateCallingChart(this.data)
                //.updateTop4Chart(this.data);
            this.updateTop4Chart(this.data);
            return this;
        },
        //updateCallingChart(data) {
            //let chart = this.charts.callings;

            //if (!data || data.length === 0) {
                //console.log('No data available for updating calling chart.');
                //return this;
            //}

            //let labels = [];
            //let chartData = [];
            //let backgroundColors = [];
            //let borderColors = [];
            //let coolColorGenerator = new ColorGenerator();
            //coolColorGenerator.baseColor = coolColorGenerator.coolColor;

            //let hotColorGenerator = new ColorGenerator();
            //hotColorGenerator.baseColor = hotColorGenerator.hotColor;

            //let hotIndex = 0;
            //let coolIndex = 0;

            //data.forEach((e, index) => {
                //labels.push(e.name);
                //if (e.type == 1) // machine
                //{
                    //backgroundColors.push(hotColorGenerator.generateColor(hotIndex, 1));
                    //borderColors.push(hotColorGenerator.generateColor(hotIndex, 0.7));
                    //hotIndex++;
                //} else if (e.type == 2) // human
                //{
                    //backgroundColors.push(coolColorGenerator.generateColor(coolIndex, 1));
                    //borderColors.push(coolColorGenerator.generateColor(coolIndex, 0.7));
                    //coolIndex++;
                //} else { // die change
                    //backgroundColors.push('rgba(255,152,0,1)');
                    //borderColors.push('rgba(255,152,0,0.7)');
                    //coolIndex++;
                //}
                //chartData.push(e.duration / 60);
            //});

            //chart.data.labels = labels;
            //chart.data.datasets[0].data = chartData;
            //chart.data.datasets[0].backgroundColor = backgroundColors;
            //chart.data.datasets[0].borderColor = borderColors;

            //chart.update();
            //return this;
        //},
        updateTop4Chart(data) {
            let chart = this.charts.top4calling;

            if (!data || data.length === 0) {
                console.log('No data available for updating top 4 chart.');
                return this;
            }

            let callingTypes = {
                'Leader Calling': 0,
                'Maintenance Calling': 0,
                'Logistic Calling': 0,
                'QC Check Calling': 0
            };

            data.forEach((item) => {
                callingTypes['Leader Calling'] += item.leader_calling / 60; // seconds to minutes
                callingTypes['Maintenance Calling'] += item.maintenance_calling / 60; // seconds to minutes
                callingTypes['Logistic Calling'] += item.logistic_calling / 60; // seconds to minutes
                callingTypes['QC Check Calling'] += item.qc_check_calling / 60; // seconds to minutes
            });

            // Convert the callingTypes object into an array of { type, duration } objects
            let top4Data = Object.keys(callingTypes).map(type => ({
                type: type,
                duration: callingTypes[type]
            }));

            // Sort the data by duration in descending order
            top4Data.sort((a, b) => b.duration - a.duration);

            // Take the top 4 items
            top4Data = top4Data.slice(0, 4);

            let labels = top4Data.map(item => item.type);
            let chartData = top4Data.map(item => parseFloat(item.duration.toFixed(3))); // Ensure the data is accurate to 3 decimal places

            let backgroundColors = [];
            let borderColors = [];
            let colorGenerator = new ColorGenerator();

            for (let i = 0; i < chartData.length; i++) {
                let color = colorGenerator.generateColor(i, 1);
                backgroundColors.push(color);
                borderColors.push(colorGenerator.generateColor(i, 0.7));
            }

            chart.data.labels = labels;
            chart.data.datasets[0].data = chartData;
            chart.data.datasets[0].backgroundColor = backgroundColors;
            chart.data.datasets[0].borderColor = borderColors;

            chart.update();
            return this;
        },
        chartOptions: {
            callings: {
                type: 'doughnut',
                data: {
                    datasets: [{
                        label: 'Callings',
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
            top4calling: {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        type: 'bar',
                        label: 'Duration',
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
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Calling Type'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Duration (min)'
                            }
                        }
                    },
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
            callings: undefined,
            top4calling: undefined,
        },
        initializeCharts: function() {
            let _this = this;
            Object.entries(_this.chartOptions).forEach(([key, option]) => {
                var ctx = document.getElementById(key).getContext('2d');
                if (ctx) {
                    _this.charts[key] = new Chart(ctx, option);
                } else {
                    console.error(`Element with ID '${key}' not found.`);
                }
            });
            return this;
        }
    };

    // ColorGenerator class for generating colors
    class ColorGenerator {
        constructor() {
            this.baseColor = [255, 99, 132]; // Base color (red)
        }

        // Generate a color based on the index
        generateColor(index, alpha = 1) {
            const [r, g, b] = this.baseColor;
            const colorFactor = index * 40;
            return `rgba(${(r + colorFactor) % 256}, ${(g + colorFactor) % 256}, ${(b + colorFactor) % 256}, ${alpha})`;
        }
    }
</script>
@endsection

@section('modals')
@parent
<div>
</div>
@endsection
