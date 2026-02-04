<?php $pageTemplateUrl = route('realtime.oee.index', '__uid__'); ?>
@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation', ['dropMenuSelected' => 'OEE'])

@section('head')
    @parent

    <style>
        .chart-center-text {
            width: 100%;
            height: 100%;
            position: absolute;
            font-size: 300%;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
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

        {{-- part details & chart & horizontal bar & detailed oee data: AVAILABILITY & performance & quality --}}
        <div class="row" style="background-color: #fff">
            {{-- part details & chart & horizontal bar --}}
            <div class="col-12 col-md-4 mt-3">
                {{-- part details --}}
                <div class="d-flex justify-content-between" style="font-size:90%;">
                    <div class="part-container px-2">
                        <i class="fa-solid fa-tag big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text d-flex align-items-center">Part Name <i
                                    class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="name"></span>
                        </div>
                    </div>
                    <div class="part-container px-2">
                        <i class="fa-duotone fa-tags big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text d-flex align-items-center">Part Number <i
                                    class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="part_no"></span>
                        </div>
                    </div>
                </div>

                {{-- chart & horizontal bar --}}
                <div class="d-flex flex-column mt-3">
                    <div class="w-100 my-3" style="flex-grow:1; position:relative;">
                        <span class="text-center secondary-text"
                            style="width:100%; position:absolute; font-size:150%;">OEE</span>
                        <span
                            class="secondary-text format-chart-center-display-text chart-center-text current-production-line-data live-production-line-data"
                            data-tag="oee">-</span>
                        <canvas id="oee_chart"></canvas>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="secondary-text">AVAILABILITY</span>
                        <span
                            class="primary-text format-bar-display-text current-production-line-data live-production-line-data"
                            data-tag="availability">-</span>
                    </div>
                    <div style="position: relative;height: 10px;padding: 0 !important;">
                        <div class="status-bar-container">
                            <div id="bar-availability" class="d-flex justify-content-center align-items-center"
                                style="background-color: #f31414 ;">
                            </div>
                            <div id="bar-availability-balance" style="background-color: #f98a8a;">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column mt-3">
                        <div class="d-flex justify-content-between">
                            <span class="secondary-text">PERFORMANCE</span>
                            <span
                                class="primary-text format-bar-display-text current-production-line-data live-production-line-data"
                                data-tag="performance">-</span>
                        </div>
                        <div style="position: relative;height: 10px;padding: 0 !important;">
                            <div class="status-bar-container">
                                <div id="bar-performance" class="d-flex justify-content-center align-items-center"
                                    style="background-color: #ff7f00 ;">
                                </div>
                                <div id="bar-performance-balance" style="background-color: #ffbf80;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column mt-3">
                        <div class="d-flex justify-content-between">
                            <span class="secondary-text">QUALITY</span>
                            <span
                                class="primary-text format-bar-display-text current-production-line-data live-production-line-data"
                                data-tag="quality">-</span>
                        </div>
                        <div style="position: relative;height: 10px;padding: 0 !important;">
                            <div class="status-bar-container">
                                <div id="bar-quality" class="d-flex justify-content-center align-items-center"
                                    style="background-color: #33a02c ;">
                                </div>
                                <div id="bar-quality-balance" style="background-color: #99d096;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- detailed oee data: AVAILABILITY & performance & quality --}}
            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column h-100 justify-content-between">
                    {{-- availability --}}
                    <div class="card">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> AVAILABILITY
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value current-production-line-data live-runtime-timer"
                                                    data-tag="good" data-format="total_hours_floor">-</span>
                                                <span class="box-title">HRS</span>
                                            </div>
                                            <div class="main-box ms-3">
                                                <span class="box-value current-production-line-data live-runtime-timer"
                                                    data-tag="good" data-format="duration_minutes">-</span>
                                                <span class="box-title">Min</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            REAL OPERATING TIME
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span class="box-value current-production-line-data live-runtime-timer"
                                                    data-tag="plan" data-format="total_hours_floor">-</span>
                                                <span class="box-title">HRS</span>
                                            </div>
                                            <div class="main-box ms-3">
                                                <span class="box-value current-production-line-data live-runtime-timer"
                                                    data-tag="plan" data-format="duration_minutes">-</span>
                                                <span class="box-title">Min</span>
                                            </div>

                                        </div>
                                        <div class="title">
                                            PLANNED PRODUCTION TIME
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- performance --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> PERFORMANCE
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span
                                                    class="box-value current-production-line-data live-production-line-data"
                                                    data-tag="actual_output"></span>
                                                <span class="box-title">PCS</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            ACTUAL PRODUCTION
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span
                                                    class="box-value current-production-line-data live-production-line-data"
                                                    data-tag="standard_output"></span>
                                                <span class="box-title">PCS</span>
                                            </div>

                                        </div>
                                        <div class="title">
                                            STANDARD PRODUCTION
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- quality --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fa-solid fa-clipboard-check me-2"></i> QUALITY
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span
                                                    class="box-value current-production-line-data live-production-line-data"
                                                    data-tag="ok_count">-</span>
                                                <span class="box-title">PCS</span>
                                            </div>
                                        </div>
                                        <div class="title">
                                            TOTAL PART OK
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex">
                                            <div class="main-box">
                                                <span
                                                    class="box-value current-production-line-data live-production-line-data"
                                                    data-tag="actual_output">-</span>
                                                <span class="box-title">PCS</span>
                                            </div>

                                        </div>
                                        <div class="title">
                                            TOTAL PARTS PRODUCED
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-3">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="secondary-text"><u>OEE HOURLY TREND</u></h5>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="me-2" style="width:15px;   background-color: #80C3FF;"></div>
                                <span class="primary-text" style="font-size: 60%">STANDARD OEE</span>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="me-2" style="width:15px; background-color:#000080"></div>
                                <span class="primary-text" style="font-size: 60%">ACTUAL OEE</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="oee_trend_chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mt-5">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="secondary-text"><u>AVAILABILITY</u></h5>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="me-2" style="width:15px;   background-color: #80C3FF;"></div>
                                <span class="primary-text" style="font-size: 60%">STANDARD AVAILABILITY</span>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="me-2" style="width:15px; background-color:#000080"></div>
                                <span class="primary-text" style="font-size: 60%">ACTUAL AVAILABILITY</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="oee_availability_chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mt-5">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="secondary-text"><u>PERFORMANCE</u></h5>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="me-2" style="width:15px;   background-color: #80C3FF;"></div>
                                <span class="primary-text" style="font-size: 60%">STANDARD PERFORMANCE</span>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="me-2" style="width:15px; background-color:#000080"></div>
                                <span class="primary-text" style="font-size: 60%">ACTUAL PERFORMANCE</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="oee_performance_chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mt-5">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex justify-content-between">
                        <h5 class="secondary-text"><u>QUALITY</u></h5>
                        <div class="d-flex flex-column">
                            <div class="d-flex">
                                <div class="me-2" style="width:15px;   background-color: #80C3FF;"></div>
                                <span class="primary-text" style="font-size: 60%">STANDARD QUALITY</span>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="me-2" style="width:15px; background-color:#000080"></div>
                                <span class="primary-text" style="font-size: 60%">ACTUAL QUALITY</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 my-3" style="flex-grow:1">
                        <canvas id="oee_quality_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </template>
    <template id="template-inactive-production-line">
        <div class="row mt-3" style="background-color: #fff; height: 500px">
            <div class="d-flex justify-content-center align-items-center my-4"
                style="font-size: 2.5rem; font-weight: 400">
                No Production
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    @parent

    @include('snippets.live-production-scripts')

    {{-- websocket, populate template --}}
    <script>
        //Websocket
        Echo.channel('terminal.{{ $plant->uid }}.{{ $workCenter->uid }}')
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
                    'live-production-line-data', //class
                    setConfigToCurrentLine({
                        tag: 'oee'
                    }), //Config
                    updateOEEMainChart //callback
                ).listenChanges(
                    'live-production-line-data', //class
                    setConfigToCurrentLine({
                        tag: 'availability'
                    }), //Config
                    updateAvailabilityBar //callback
                ).listenChanges(
                    'live-production-line-data', //class
                    setConfigToCurrentLine({
                        tag: 'performance'
                    }), //Config
                    updatePerformanceBar //callback
                ).listenChanges(
                    'live-production-line-data', //class
                    setConfigToCurrentLine({
                        tag: 'quality'
                    }), //Config
                    updateQualityBar //callback
                ).listenChanges(
                    'live-production-line-hourly-data',
                    setConfigToCurrentLine({
                        tag: 'oee',
                    }), //Config
                    updateTrendOeeChart //callback
                ).listenChanges(
                    'live-production-line-hourly-data',
                    setConfigToCurrentLine({
                        tag: 'availability',
                    }), //Config
                    updateTrendAvailabilityChart //callback
                ).listenChanges(
                    'live-production-line-hourly-data',
                    setConfigToCurrentLine({
                        tag: 'performance',
                    }), //Config
                    updateTrendPerformanceChart //callback
                ).listenChanges(
                    'live-production-line-hourly-data',
                    setConfigToCurrentLine({
                        tag: 'quality',
                    }), //Config
                    updateTrendQualityChart //callback
                );
            // LivePage
        });

        function updateTrendOeeChart(config, data, summary) {
            updateTrendChart('oee', data, oee_trend_main_chart);
        }

        function updateTrendAvailabilityChart(config, data, summary) {
            updateTrendChart('availability', data, oee_trend_availability_chart);
        }

        function updateTrendPerformanceChart(config, data, summary) {
            updateTrendChart('performance', data, oee_trend_performance_chart);
        }

        function updateTrendQualityChart(config, data, summary) {
            updateTrendChart('quality', data, oee_trend_quality_chart);
        }

        function updateTrendChart(key, data, chart) {
            if (!chart)
                return;

            let labels = [];
            let chartData = [];

            data.forEach(e => {
                let localStart = liveClock.toPlantClock(e.start);
                // let localEnd = liveClock.toPlantClock(e.end);
                labels.push(`${localStart.format('hA')}-${localStart.add(1,'h').format('hA')}`); //e.g 8AM-9AM
                chartData.push(e[key] * 100);
            });


            chart.data.labels = labels;
            chart.data.datasets[0].data = chartData;
            chart.update();
        }
        var currentProductionLineDataConfig = [];

        function updateCurrentProductionLineConfigs(newProductionLineId) {
            currentProductionLineDataConfig.forEach(e => {
                e['production-line-id'] = newProductionLineId;
            });
        }

        function setConfigToCurrentLine(configData) {
            if (LivePage.tabCurrentProductionLine)
                configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

            currentProductionLineDataConfig.push(configData);
            return configData;
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

        var number_production_lines = null;

        function productionChanges(e) {

            if (number_production_lines !== null && number_production_lines !== e.production_lines.length) {
                number_production_lines = e.production_lines.length;
                populateTemplate(e.production_lines);
            } else if (number_production_lines === null) {
                number_production_lines = e.production_lines.length;
                populateTemplate(e.production_lines);
            }
        }

        var oee_trend_main_chart = null,
            oee_trend_availability_chart = null,
            oee_trend_performance_chart = null,
            oee_trend_quality_chart = null,
            oee_main_chart = null;

        function populateTemplate(production_lines) {
            let dashboardContainer = $('#dashboard-container');

            if (production_lines.length > 0) {
                dashboardContainer.html($('#template-active-production-line').html());
                

                populateTabLines(production_lines);

                dashboardContainer.find('.format-bar-display-text').data('render', function(e, value) {
                    return `${(value * 100).toFixed(2)} %`;
                });
                dashboardContainer.find('.format-chart-center-display-text').data('render', function(e, value) {
                    return `${(value * 100).toFixed(2)} %`;
                });
                // $('.format-chart-center-display-text').data('render', function(e, value) {
                //     return `${(value * 100).toFixed(0)} %`;
                // });

                oee_trend_main_config.initChart();
                oee_trend_availability_config.initChart();
                oee_trend_performance_config.initChart();
                oee_trend_quality_config.initChart();
                oee_main_config.initChart();

                oee_trend_main_chart = oee_trend_main_config.chart;
                oee_trend_availability_chart = oee_trend_availability_config.chart;
                oee_trend_performance_chart = oee_trend_performance_config.chart;
                oee_trend_quality_chart = oee_trend_quality_config.chart;
                oee_main_chart = oee_main_config.chart;

                LivePage.clearCallbackPrevValue();

                currentProductionLineChanged(production_lines[0]);

            } else {
                dashboardContainer.html($('#template-inactive-production-line').html());
            }
        }

        function populateTabLines(production_lines) {
            let production_line_container = $('#production-line-container');
            production_lines.forEach(function(e) {
                let liElement = $(
                    '<li class="nav-item" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="' +
                    e.id + '"></li>');
                let aElement = $('<a class="nav-link" data-production-line-id="' + e.id +
                    '" aria-current="page" href="#">LINE ' + e.line_no + '</a>');
                liElement.append(aElement);

                production_line_container.append(liElement);
            });
        }

        function updateOEEMainChart(config, value, summary) {
            if (oee_main_chart) {

                oee_main_chart.data.datasets[0].data[0] = value;
                oee_main_chart.data.datasets[0].data[1] = 1 - value;
                //if value between 0-30 then color is red
                if (value > 0.64) { //green
                    oee_main_chart.data.datasets[0].backgroundColor = ["#33a02c", "#99d096"];
                } else if (value < 0.65 && value > 0.3) { //orange
                    oee_main_chart.data.datasets[0].backgroundColor = ["#ff7f00", "#ffbf80"];
                } else { //red
                    oee_main_chart.data.datasets[0].backgroundColor = ["#f31414", "#F68788"];
                }
                oee_main_chart.update();
            }
        }

        function updateAvailabilityBar(config, value, summary) {
            let bar_availability = $('#bar-availability');
            let balance = $('#bar-availability-balance');

            if (bar_availability && balance) {
                let val = Math.round(value * 100);
                let bal = 100 - val;

                bar_availability.width(val + '%');
                balance.width(bal + '%');

                if (val > 64) { //green
                    bar_availability.css('background-color', '#33a02c');
                    balance.css('background-color', '#99d096');
                } else if (val < 65 && val > 30) { //orange
                    bar_availability.css('background-color', '#ff7f00');
                    balance.css('background-color', '#ffbf80');
                } else { //red
                    bar_availability.css('background-color', '#f31414');
                    balance.css('background-color', '#F68788');
                }
            }
        }

        function updatePerformanceBar(config, value, summary) {
            let bar_performance = $('#bar-performance');
            let balance = $('#bar-performance-balance');

            if (bar_performance && balance) {

                let val = Math.round(value * 100);
                let bal = 100 - val;

                bar_performance.width(val + '%');
                balance.width(bal + '%');

                if (val > 64) { //green
                    bar_performance.css('background-color', '#33a02c');
                    balance.css('background-color', '#99d096');
                } else if (val < 65 && val > 30) { //orange
                    bar_performance.css('background-color', '#ff7f00');
                    balance.css('background-color', '#ffbf80');
                } else { //red
                    bar_performance.css('background-color', '#f31414');
                    balance.css('background-color', '#F68788');
                }
            }
        }

        function updateQualityBar(config, value, summary) {
            let bar_quality = $('#bar-quality');
            let balance = $('#bar-quality-balance');

            if (bar_quality && balance) {
                let val = Math.round(value * 100);
                let bal = 100 - val;

                bar_quality.width(val + '%');
                balance.width(bal + '%');

                if (val > 64) { //green
                    bar_quality.css('background-color', '#33a02c');
                    balance.css('background-color', '#99d096');
                } else if (val < 65 && val > 30) { //orange
                    bar_quality.css('background-color', '#ff7f00');
                    balance.css('background-color', '#ffbf80');
                } else { //red
                    bar_quality.css('background-color', '#f31414');
                    balance.css('background-color', '#F68788');
                }
            }
        }
    </script>

    {{-- chart config --}}
    <script>
        var oee_trend_main_config = {
            chartCanvasID: 'oee_trend_chart',
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
                        type: 'line',
                        label: 'ACTUAL OEE',
                        borderColor: '#000080',
                        backgroundColor: 'linear-gradient(#d8d9dd, #000080);0',
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: '{{ $workCenter->threshold_oee * 100 }}',
                                    yMax: '{{ $workCenter->threshold_oee * 100 }}',
                                    borderColor: '#80C3FF',
                                    borderWidth: 2,
                                }
                            }
                        },
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');

                this.chart = new Chart(ctx, this.chartConfig);
            },
        };

        var oee_trend_availability_config = {
            chartCanvasID: 'oee_availability_chart',
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
                        type: 'line',
                        label: 'ACTUAL AVAILABILITY',
                        borderColor: '#000080',
                        backgroundColor: '#000080',
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: '{{ $workCenter->threshold_availability * 100 }}',
                                    yMax: '{{ $workCenter->threshold_availability * 100 }}',
                                    borderColor: '#80C3FF',
                                    borderWidth: 2,
                                }
                            }
                        },
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');

                this.chart = new Chart(ctx, this.chartConfig);
            },
        };

        var oee_trend_performance_config = {
            chartCanvasID: 'oee_performance_chart',
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
                        type: 'line',
                        label: 'ACTUAL PERFORMANCE',
                        borderColor: '#000080',
                        backgroundColor: '#000080',
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: '{{ $workCenter->threshold_performance * 100 }}',
                                    yMax: '{{ $workCenter->threshold_performance * 100 }}',
                                    borderColor: '#80C3FF',
                                    borderWidth: 2,
                                }
                            }
                        },
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');

                this.chart = new Chart(ctx, this.chartConfig);
            },
        };

        var oee_trend_quality_config = {
            chartCanvasID: 'oee_quality_chart',
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
                        type: 'line',
                        label: 'ACTUAL QUALITY',
                        borderColor: '#000080',
                        backgroundColor: '#000080',
                        fill: false,
                        cubicInterpolationMode: 'monotone',
                        tension: 0.4,
                        data: [],
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: '{{ $workCenter->threshold_quality * 100 }}',
                                    yMax: '{{ $workCenter->threshold_quality * 100 }}',
                                    borderColor: '#80C3FF',
                                    borderWidth: 2,
                                }
                            }
                        },
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');

                this.chart = new Chart(ctx, this.chartConfig);
            },
        };

        var oee_main_config = {
            chartCanvasID: 'oee_chart',
            chartConfig: {
                type: 'doughnut',
                data: {
                    // labels: ["Red", "Pink"],
                    datasets: [{
                        label: 'Average OEE',
                        data: [],
                        backgroundColor: ["#F31414", "#F68788"]
                    }]
                },
                options: {
                    rotation: 225, // start angle in degrees
                    circumference: 270, // sweep angle in degrees
                    cutout: '60%', // percentage of the chart that should be cut out of the middle
                    // responsive: false,
                    // maintainAspectRatio: false,
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            },
        };
    </script>
@endsection
