@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'PRODUCTIVITY'])

@section('head')
    @parent
    <style>
        .box-shadow {
            box-shadow: 6px 3px 5px #00000040;
            border-radius: 5px;
            border: 2px solid #00000020;
            border-top: 1px solid #00000020;
            border-left: 1px solid #00000020;
        }

        .elapsed-time-legend-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        .productivity-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        .productivity-info-grid .box-shadow:nth-child(1) {
            grid-column: 1/4;
        }

        @media only screen and (max-width: 768px) {
            .productivity-info-grid {
                grid-template-columns: 1fr 1fr;
            }

            .productivity-info-grid .box-shadow:nth-child(1) {
                grid-column: 1/2;
            }

        }

        .timer-label {
            font-weight: 500;
        }

        .elapsed-timer-label {
            color: #c62828;
            font-family: 'Roboto Mono', monospace;
        }
    </style>
@endsection

@section('body')
<main>
    @yield('drop-menu-navigation')
    <div class="container">
        @include('pages.web.realtime.components.work-center-header')

        <div id="dashboard-container">

        </div>

    </div>
</main>
@endsection

@section('templates')
    @parent
    <template id="template-active-production-line">

        {{-- tab : production lines --}}
        <div class="row">
            <div class="col-12 mt-3 px-0">
                <ul id="production-line-container" class="nav nav-tabs">

                </ul>
            </div>
        </div>

        {{-- part details & productivity & production elapsed time & hourly productivity --}}
        <div class="row" style="background-color: #fff">
            {{-- part details --}}
            <div class="col-12 mt-3">
                <div class="d-flex justify-content-between">
                    <div class="part-container px-2">
                        <i class="fa-solid fa-tag big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">PART NAME <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="name">-</span>
                        </div>
                    </div>
                    <div class="part-container px-2">
                        <i class="fa-duotone fa-tags big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">PART NUMBER <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="part_no">-</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- productivity & production elapsed time--}}
            <div class="row d-flex justify-content-between">
                {{-- productivity --}}
                <div class="col-12 col-md-6 mt-3">
                    <div class="primary-text px-2">
                        <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTIVITY
                    </div>
                    <hr>
                    <div class="productivity-info-grid">
                        <div class="box-shadow p-3">
                            <div class="grid-place-center">
                                <span class="secondary-text text-center current-production-line-data live-production-line-data format-text-pcs" data-tag="plan_quantity" style="font-size: 180%">-</span>
                                <span class="primary-text text-center">PLAN OUTPUT</span>
                            </div>
                        </div>

                        <div class="box-shadow p-3">
                            <div class="grid-place-center">
                                <span class="secondary-text text-center current-production-line-data live-production-line-data format-text-pcs" data-tag="standard_output" style="font-size: 180%">-</span>
                                <span class="primary-text text-center">STANDARD OUTPUT</span>
                            </div>
                        </div>
                        <div class="box-shadow p-3">
                            <div class="grid-place-center">
                                <span class="secondary-text text-center current-production-line-data live-production-line-data format-text-pcs" data-tag="actual_output" style="font-size: 180%">-</span>
                                <span class="primary-text text-center">ACTUAL OUTPUT</span>
                            </div>
                        </div>
                        <div class="box-shadow p-3">
                            <div class="grid-place-center">
                                <span class="secondary-text text-center current-production-line-data live-production-line-data format-text-pcs-variance" data-tag="variance" style="font-size: 180%">-</span>
                                <span class="primary-text text-center">VARIANCES</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- production elapsed time --}}
                <div class="col-12 col-md-6 mt-3">
                    <div class="primary-text px-2">
                        <i class="fa-solid fa-clipboard-check me-2"></i> PRODUCTION ELAPSED TIME
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <div class="p-3 border elapsed-time-legend-container">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width:15px; height:15px;background-color:#03941a"></div>
                                <span class="primary-text" style="font-size: 60%">RUNNING PRODUCTION</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width:15px; height:15px; background-color:#2830c6"></div>
                                <span class="primary-text" style="font-size: 60%">HUMAN DOWNTIME</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width:15px; height:15px; background-color:#c62828"></div>
                                <span class="primary-text" style="font-size: 60%">MACHINE DOWNTIME</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width:15px; height:15px; background-color:#800080"></div>
                                <span class="primary-text" style="font-size: 60%">BREAK</span>
                            </div>

                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width:15px; height:15px; background-color:#ffa000"></div>
                                <span class="primary-text" style="font-size: 60%">DIE CHANGE</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between timer-label mt-3">
                        <div>PLANNED DT: <span class="elapsed-timer-label live-downtime-timer" data-tag="plan" data-format="timer_full"></span></div>
                        <div>WORKING: <span class="elapsed-timer-label live-runtime-timer" data-tag="good" data-format="timer_full"></span></div>
                    </div>
                    <div class="mt-3" style="height:64px;">
                        <canvas class="mb-3" id="production_elapsed_time_chart"></canvas>
                    </div>
                    <div class="d-flex justify-content-between timer-label">
                        <div>UNPLAN DT: <span class="elapsed-timer-label live-downtime-timer" data-tag="unplan" data-format="timer_full"></span></div>
                        <div>REMAINING: <span class="elapsed-timer-label live-runtime-timer" data-tag="remaining" data-format="timer_full"></span></div>
                    </div>
                </div>
            </div>
            {{-- hourly productivity --}}
            <div class="row">
                <div class="col-12 col-md-12 mt-3">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between">
                            <div class="primary-text px-2">
                                <i class="fa-solid fa-clipboard-check me-2"></i> HOURLY PRODUCTIVITY
                            </div>
                            <div class="d-flex flex-row flex-md-column mt-3 mt-md-0">
                                <div class="d-flex">
                                    <div class="me-2" style="width:15px;   background-color: #800000;"></div>
                                    <span class="primary-text" style="font-size: 60%">PLAN OUTPUT</span>
                                </div>
                                <div class="d-flex ms-2 ms-md-0 mt-md-2">
                                    <div class="me-2" style="width:15px; background-color:#1B0C4D"></div>
                                    <span class="primary-text" style="font-size: 60%">ACTUAL OUTPUT</span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="w-100 my-3" style="flex-grow:1">
                            <canvas id="hourly_productivity_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </template>
    <template id="template-inactive-production-line">
        <div class="row mt-3" style="background-color: #fff; height: 500px">
            <div class="d-flex justify-content-center align-items-center my-4" style="font-size: 2.5rem; font-weight: 400">
                No Production
            </div>
        </div>
    </template>
@endsection

@section('scripts')
@parent
@include('snippets.live-production-scripts')

{{-- web socket, populate template --}}
<script>
    var autoscale = true;
    var autoscaleMax = 0;
    var autoscaleMin = 0;
    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
        });

    $(() => {

        //Auth handle Tab
        LivePage.initializeProductionLineTab(function(e) {
                currentProductionLineChanged(e); // callback to init line tab
            })
            .listenAnyChanges(e => {
                productionChanges(e);
            }).listenChanges(
                'live-production-line-hourly-data',
                setConfigToCurrentLine({
                    tag: ['actual_output', 'standard_output'],
                }), //Config
                updateHourlyProductivityChart //callback
            ).listenChanges(
                'live-runtime-summary', {}, //Config
                updateProductionRuntimeSummary //callback
            );
    });

    /** No Downtime */
    const DOWNTIME_STATUS_NONE = 0;

    /** Unplanned Downtime: Human */
    const DOWNTIME_STATUS_UNPLAN_HUMAN = -1;
    /** Unplanned Downtime: Machine */
    const DOWNTIME_STATUS_UNPLAN_MACHINE = -2;
    /** Unplanned Downtime: Die-Change */
    const DOWNTIME_STATUS_UNPLAN_DIE_CHANGE = -3;

    /** Planned Downtime: Die-Change */
    const DOWNTIME_STATUS_PLAN_DIE_CHANGE = 3;
    /** Planned Downtime: Break */
    const DOWNTIME_STATUS_PLAN_BREAK = 4;
    const STATES_MAP = {
        "0": { //DOWNTIME_STATUS_NONE (RUNNING)
            color: '#03941a',
        },
        "-1": { //DOWNTIME_STATUS_UNPLAN_HUMAN
            color: '#2830c6',
        },
        "-2": { //DOWNTIME_STATUS_UNPLAN_MACHINE
            color: '#c62828',
        },
        "-3": { //DOWNTIME_STATUS_UNPLAN_DIE_CHANGE
            color: '#2830c6',
        },
        "3": { //DOWNTIME_STATUS_PLAN_DIE_CHANGE
            color: '#ffa000',
        },
        "4": { //DOWNTIME_STATUS_PLAN_BREAK
            color: '#800080',
        }
    };

    function updateProductionRuntimeSummary(config, data, summary) {
        if (!production_elapsed_time_chart)
            return;

        let dataRunning = [];
        let dataHumanDowntime = [];
        let dataMachineDowntime = [];
        let dataDieChange = [];
        let dataBreak = [];

        let lastState = null;
        let firstStart = null;
        let lastEnd = null;
        let count = 0;

        data.forEach(e => {
            count++;

            let localStart = liveClock.toPlantClock(e.time).format('HH:mm:ss');
            let stateName = '';

            if (!firstStart)
                firstStart = localStart;

            switch (lastState) {
                case DOWNTIME_STATUS_NONE:
                    dataRunning.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
                case DOWNTIME_STATUS_UNPLAN_HUMAN:
                    dataHumanDowntime.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
                case DOWNTIME_STATUS_UNPLAN_MACHINE:
                    dataMachineDowntime.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
                case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
                    dataHumanDowntime.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
                case DOWNTIME_STATUS_PLAN_DIE_CHANGE:
                    dataDieChange.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
                case DOWNTIME_STATUS_PLAN_BREAK:
                    dataBreak.push({
                        x: localStart,
                        y: 0,
                    });
                    break;
            }

            if (count !== data.length) {
                switch (e.state) {
                    case DOWNTIME_STATUS_NONE:
                        stateName = 'RUNNING';
                        dataRunning.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                    case DOWNTIME_STATUS_UNPLAN_HUMAN:
                        stateName = 'UNPLAN HUMAN DOWNTIME';
                        dataHumanDowntime.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                    case DOWNTIME_STATUS_UNPLAN_MACHINE:
                        stateName = 'UNPLAN MACHINE DOWNTIME';
                        dataMachineDowntime.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                    case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
                        stateName = 'UNPLAN DIE CHANGE';
                        dataHumanDowntime.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                    case DOWNTIME_STATUS_PLAN_DIE_CHANGE:
                        stateName = 'PLAN DIE CHANGE';
                        dataDieChange.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                    case DOWNTIME_STATUS_PLAN_BREAK:
                        stateName = 'PLAN BREAK';
                        dataBreak.push({
                            x: localStart,
                            y: 1,
                        });
                        break;
                }
            } else {
                lastEnd = localStart;
            }

            lastState = e.state;
        });

        production_elapsed_time_chart.data.datasets[0].data = dataRunning;
        production_elapsed_time_chart.data.datasets[1].data = dataHumanDowntime;
        production_elapsed_time_chart.data.datasets[2].data = dataMachineDowntime;
        production_elapsed_time_chart.data.datasets[3].data = dataDieChange;
        production_elapsed_time_chart.data.datasets[4].data = dataBreak;
        autoscaleMax = lastEnd;
        autoscaleMin = firstStart;

        if (autoscale) {
            production_elapsed_time_chart.options.scales.x.min = firstStart;
            production_elapsed_time_chart.options.scales.x.max = lastEnd;
        }

        production_elapsed_time_chart.update();

    }

    function updateHourlyProductivityChart(config, data, summary) {
        if (!hourly_productivity_chart)
            return;

        let labels = [];
        let dataActualOutput = [];
        let dataStandardOutput = [];

        data.forEach(e => {
            let localStart = liveClock.toPlantClock(e.start);

            labels.push(`${localStart.format('hA')}-${localStart.add(1,'h').format('hA')}`);
            dataActualOutput.push(e.actual_output);
            dataStandardOutput.push(e.standard_output);
        });

        hourly_productivity_chart.data.labels = labels;
        hourly_productivity_chart.data.datasets[0].data = dataActualOutput;
        hourly_productivity_chart.data.datasets[1].data = dataStandardOutput;
        hourly_productivity_chart.update();
    }

    function currentProductionLineChanged(e) {
        if (e) {
            $('.current-production-line-data').data('production-line-id', e.id);
            updateCurrentProductionLineConfigs(e.id);
            //Tab Data
            $('.nav-link').removeClass('active');
            $(`.nav-link[data-production-line-id="${e.id}"`).addClass('active');

            LivePage.updateLiveData();
        }
    }

    function setConfigToCurrentLine(configData) {
        if (LivePage.tabCurrentProductionLine)
            configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

        currentProductionLineDataConfig.push(configData);
        return configData;
    }

    var currentProductionLineDataConfig = [];

    function updateCurrentProductionLineConfigs(newProductionLineId) {
        currentProductionLineDataConfig.forEach(e => {
            e['production-line-id'] = newProductionLineId;
        });
    };

    var number_production_lines = null;

    function productionChanges(e) {

        if (number_production_lines !== null && number_production_lines !== e.production_lines.length) {
            number_production_lines = e.production_lines.length;
            populateTemplate(e.production_lines);
        } else if (number_production_lines === null) {
            number_production_lines = e.production_lines.length;
            populateTemplate(e.production_lines);
        }

    };

    var production_elapsed_time_chart = null,
        hourly_productivity_chart = null;

    function populateTemplate(production_lines) {
        let dashboardContainer = $('#dashboard-container');

        if (production_lines.length > 0) {
            dashboardContainer.html($('#template-active-production-line').html());
            populateTabLines(production_lines);

            $('.format-text-pcs').data('render', function(e, value) {
                return `${value} PCS`;
            });
            $('.format-text-pcs-variance').data('render', function(e, value) {
                let result = '';
                if (value < 0)
                    result = '<i class="fa-solid fa-caret-down text-danger"></i>&nbsp;'

                else if (value > 0)
                    result = '<i class="fa-solid fa-caret-up text-success"></i>&nbsp;'

                result += `${Math.abs(value)} PCS`;
                return result;
            });
            production_elapsed_time_config.initChart();
            hourly_productivity_config.initChart();

            production_elapsed_time_chart = production_elapsed_time_config.chart;
            hourly_productivity_chart = hourly_productivity_config.chart;

            LivePage.clearCallbackPrevValue();

            currentProductionLineChanged(production_lines[0]);
        } else {
            dashboardContainer.html($('#template-inactive-production-line').html());
        }
    }

    function populateTabLines(production_lines) {
        let production_line_container = $('#production-line-container');
        production_lines.forEach(function(e) {
            let liElement = $('<li class="nav-item" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="' + e.id + '"></li>');
            let aElement = $('<a class="nav-link" data-production-line-id="' + e.id + '" aria-current="page" href="#">LINE ' + e.line_no + '</a>');
            liElement.append(aElement);

            production_line_container.append(liElement);
        });
    }
</script>

{{-- chart config --}}
<script>
    var hourly_productivity_config = {
        chartCanvasID: 'hourly_productivity_chart',
        chartConfig: {
            type: 'line',
            data: {
                labels: [],
                animations: {
                    y: {
                        duration: 2000,
                        delay: 500,
                    },
                },
                datasets: [{
                        type: 'bar',
                        label: 'ACTUAL OUTPUT',
                        borderColor: '#1B0C4D',
                        backgroundColor: '#1B0C4D',
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'STANDARD OUTPUT',
                        borderColor: '#800000',
                        backgroundColor: '#800000',
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                        order: 1
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                }
            }
        },
        initChart: function() {
            const ctx = document.getElementById(this.chartCanvasID).getContext('2d');

            this.chart = new Chart(ctx, this.chartConfig);
        },
    };
    var lastClick = 0;
    var timeFormat = 'HH:mm:ss';
    var production_elapsed_time_config = {
        chartCanvasId: 'production_elapsed_time_chart',
        chartConfig: {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                        label: 'RUNNING PRODUCTION',
                        backgroundColor: '#03941a',
                        borderColor: '#03941a',
                        fill: true,
                        borderWidth: 0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    },
                    {
                        label: 'HUMAN DOWNTIME',
                        backgroundColor: '#2830c6',
                        borderColor: '#2830c6',
                        fill: true,
                        borderWidth: 0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    }, {
                        label: 'MACHINE DOWNTIME',
                        backgroundColor: '#c62828',
                        borderColor: '#c62828',
                        fill: true,
                        borderWidth: 0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    },
                    {
                        label: 'DIE CHANGE',
                        backgroundColor: '#ffa000',
                        borderColor: '#ffa000',
                        fill: true,
                        borderWidth: 0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    },
                    {
                        label: 'BREAK',
                        backgroundColor: '#800080',
                        borderColor: '#800080',
                        fill: true,
                        borderWidth: 0,
                        stepped: true,
                        pointRadius: 0,
                        data: []
                    },
                ]
            },
            options: {
                onClick(e) {
                    let current = (new Date()).getTime();
                    if ((current - lastClick) < 400) {
                        autoscale = true;
                        production_elapsed_time_chart.options.scales.x.min = autoscaleMin;
                        production_elapsed_time_chart.options.scales.x.max = autoscaleMax;
                        production_elapsed_time_chart.update();
                    }
                    lastClick = current;
                },

                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'time',
                        ticks: {
                            autoSkip: true,
                            autoSkipPadding: 50,
                            fontColor: 'black',
                            maxRotation: 0,
                            minRotation: 0
                        },
                        min: '00:00:00',
                        max: '24:00:00',
                        time: {
                            parser: timeFormat,
                            tooltipFormat: 'HH:mm:ss'
                        },
                        scaleLabel: {
                            display: false,
                            labelString: 'Time'
                        },
                        gridLines: {
                            display: true,
                            color: "#ecc94b"
                        },
                    },
                    y: {
                        display: false,
                        gridLines: {
                            drawOnChartArea: false,
                            display: false,
                        },
                        min: 0,
                        max: 1,

                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    zoom: {
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'x',
                            onZoomComplete: function(e) {
                                autoscale = false;
                                e.chart.render();
                            }
                        },
                        pan: {
                            enabled: true,
                            // pan options and/or events
                            mode: 'x'
                        },

                    }
                }
            }
        },
        initChart: function() {
            const ctx = document.getElementById(this.chartCanvasId).getContext('2d');
            this.chart = new Chart(ctx, this.chartConfig);
        }
    };
</script>
@endsection

@section('modals')
@parent
<div>

</div>
@endsection