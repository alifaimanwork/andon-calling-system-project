@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'DOWNTIME'])

@section('head')
@parent
    <style>
        .machine-downtime .downtime-active {
            background-color: #c62828 !important;
        }

        .human-downtime .downtime-active {
            background-color: #303f9f !important;
        }

        .downtime-indicator {
            background-color: #e1e1e1;
            flex: 1;
        }

        .downtime-item-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1em;
        }

        @media only screen and (max-width: 990px) {

            .downtime-item-container {
                grid-template-columns: 1fr;
            }
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
    <div class="row mb-3" style="background-color: #fff">
        {{-- downtime details --}}
        <div class="col-12 col-md-6 mt-3 my-0 my-md-3">
            <div class="card-header">
                <i class="fa-solid fa-clipboard-check me-2"></i> DOWNTIME
            </div>
            <div class="table-container mt-3">
                <table class="table table-striped w-100 primary-text">
                    <tbody>
                        <tr>
                            <td>START TIME</td>
                            <td class="local-timestamp-render production-data" data-tag="started_at"></td>
                        </tr>
                        <tr>
                            <td>CURRENT TIME</td>
                            <td class='live-clock' data-clock="plant" data-format="YYYY-MM-DD HH:mm:ss"></td>
                        </tr>
                        <tr>
                            <td>TOTAL WORKING</td>
                            <td class="summary-runtime-render live-runtime-timer" data-tag="plan"></td>
                        </tr>
                        <tr>
                            <td>TOTAL DOWNTIME</td>
                            <td class="summary-runtime-render live-downtime-timer" data-tag="unplan"></td>
                        </tr>
                        <tr>
                            <td>DOWNTIME %</td>
                            <td><span class="live-production-data render-variance-percentage" data-tag="average_availability"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-3 my-0 my-md-3">
            <h5 class="secondary-text">DOWNTIME (MIN)</h5>
            <div class="w-100 my-3">
                <canvas id="downtime-chart"></canvas>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-3">
            <div class="d-flex flex-column">
                <div class="downtime-container justify-content-between px-2">
                    <h4 class="secondary-text my-auto">MACHINE DOWNTIME</h4>
                    <div class="timebox">
                        <i class="fa-solid fa-database my-auto" style="font-size:25px"></i>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_machine" data-format="total_hours_floor">00</span>
                            <span class="time-unit">HRS</span>
                        </div>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_machine" data-format="duration_minutes">00</span>
                            <span class="time-unit">MINS</span>
                        </div>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_machine" data-format="duration_seconds">00</span>
                            <span class="time-unit">SECS</span>
                        </div>
                    </div>
                </div>

                <div class="downtime-item-container machine-downtime mt-3">
                    @foreach($machineDowntimes as $machineDowntime)

                    <div class="w-100 d-flex">
                        <div class="downtime-indicator live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $machineDowntime->id }}" data-subtag="is_running" style="flex:1; min-width: 30px">
                        </div>
                        <div class="mx-2" style="flex:4;border: 1px solid black">
                            <div class="p-1" style="font-weight: 700">
                                {{ $machineDowntime->category }}
                            </div>
                        </div>
                        <div class="" style="flex:1;border: 1px solid black">
                            <div class="p-1 live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $machineDowntime->id }}" data-format="timer_full" style="font-weight: 700">

                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
                <div class="downtime-container justify-content-between px-2 mt-3">
                    <h4 class="secondary-text my-auto">HUMAN DOWNTIME</h4>
                    <div class="timebox">
                        <i class="fa-solid fa-database my-auto" style="font-size:25px"></i>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_human" data-format="total_hours_floor">00</span>
                            <span class="time-unit">HRS</span>
                        </div>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_human" data-format="duration_minutes">00</span>
                            <span class="time-unit">MINS</span>
                        </div>
                        <div class="px-2">
                            <span class="time-value live-downtime-timer" data-tag="unplan_human" data-format="duration_seconds">00</span>
                            <span class="time-unit">SECS</span>
                        </div>
                    </div>
                </div>

                <div class="downtime-item-container human-downtime mt-3">
                    @foreach($humanDowntimes as $humanDowntime)
                    <div class="w-100 d-flex">
                        <div class="downtime-indicator live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $humanDowntime->id }}" data-subtag="is_running" style="flex:1; min-width: 15px">
                        </div>
                        <div class="mx-2" style="flex:4;border: 1px solid black">
                            <div class="p-1" style="font-weight: 700">
                                {{ $humanDowntime->category }}
                            </div>
                        </div>
                        <div class="" style="flex:1;border: 1px solid black">
                            <div class="p-1 live-downtime-timer" data-tag="by_id" data-downtime-id="{{ $humanDowntime->id }}" data-format="timer_full" style="font-weight: 700">

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="col-12 col-md-6 mt-3">
            <div class="d-flex flex-column h-100">
                <h5 class="secondary-text">TOP 10 DOWNTIME BY BREAKDOWN (MIN)</h5>
                <div class="w-100 my-3" style="flex-grow:1">
                    <canvas id="top10down-chart"></canvas>
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
    //Websocket
    Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
        .listen('.terminal.data-updated', (e) => {
            LivePage.terminalDataUpdatedHandler(e);
        })
        .listen('.terminal.downtime-state-changed', (e) => {
            LivePage.terminalDowntimeStateChangedHandler(e);
        });


    function downtimeStateChanged(e) {
        LivePage.terminalData.workCenterDowntimes = e.workCenterDowntimes;
        LivePage.terminalData.activeDowntimeEvents = e.activeDowntimeEvents;
        updateDowntimesState();
    }

    function updateDowntimesState() {
        //downtime state updated
        console.log("updateDowntimesState", LivePage.terminalData.workCenterDowntimes, LivePage.terminalData.activeDowntimeEvents);
    }

    function attachRenderer() {
        $('.downtime-indicator').data('render', function(e, data, summary) {
            if (data)
                $(e).addClass('downtime-active');
            else
                $(e).removeClass('downtime-active');

            return '';
        });
        $('.summary-runtime-render').data('render', function(e, data, summary) {

            let hrs = Math.floor(data / 3600); // 1 hour = 3600 seconds
            let mins = Math.floor(data / 60 - hrs * 60); // 1 minute = 60 seconds, subtract hours * 60 minutes
            let secs = Math.floor(data - hrs * 3600 - mins * 60); // seconds, subtract hours * 60 minutes * 60 seconds

            let result = '';

            if (hrs > 0){
                result += (hrs < 10 ? '0' : '') + `${hrs} HRS `;
            }

            if (mins > 0){
                result += (mins < 10 ? '0' : '') + `${mins} MINS `;
            }

            if (secs > 0){
                result += (secs < 10 ? '0' : '') + `${secs} SECS `;
            }

            return result;

        });

        $('.local-timestamp-render').data('render', function(e, data, summary) {

            return liveClock.toPlantClock(data).format('YYYY-MM-DD HH:mm:ss');
        });
    }
    $(() => {
        //Auth handle Tab
        LivePage.listenAnyChanges(e => {
            productionChanges(e);
        }).listenChanges('live-downtime-timer', {
            tag: 'unplan',
        }, updateDowntimeCharts);

    });

    function updateDowntimeCharts(cfg, data, summary) {
        //Update downtime minute chart

        updateDowntimeMinChart(summary.downtimes);
        updateTop10Downtime(summary.downtimes.by_id)
    };

    function getDowntimeById(downtimeId) {
        for (let i = 0; i < LivePage.terminalData.machineDowntimes.length; i++) {
            const downtime = LivePage.terminalData.machineDowntimes[i];
            if (downtime.id == downtimeId)
                return downtime;
        }
        for (let i = 0; i < LivePage.terminalData.humanDowntimes.length; i++) {
            const downtime = LivePage.terminalData.humanDowntimes[i];
            if (downtime.id == downtimeId)
                return downtime;
        }

        return undefined;
    }

    function updateDowntimeMinChart(downtimes) {

        let dieChangeTotal = 0;
        if (downtimes && downtimes.unplan_die_change && downtimes.unplan_die_change.total)
            dieChangeTotal = downtimes.unplan_die_change.total;

        let machineTotal = 0;
        if (downtimes && downtimes.unplan_machine && downtimes.unplan_machine.total)
            machineTotal = downtimes.unplan_machine.total;

        let humanTotal = 0;
        if (downtimes && downtimes.unplan_human && downtimes.unplan_human.total)
            humanTotal = downtimes.unplan_human.total;
        // console.log(downtimes);

        if (downtimeChart) {
            downtimeChart.data.datasets[0].data = [dieChangeTotal / 60, machineTotal / 60, humanTotal / 60];
            downtimeChart.update();
        }

    }
    const DowntimeTypeColors = {
        "1": "#c62828",
        "2": "#303f9f"
    };

    function updateTop10Downtime(downtimesById) {
        if (!downtimesById) {
            if (!!top10DownChart) {
                top10DownChart.data.datasets[0].data.length = 0;
                top10DownChart.data.labels.length = 0
                top10DownChart.data.datasets[0].backgroundColor.length = 0;
                top10DownChart.data.datasets[0].borderColor.length = 0;
                top10DownChart.update();
            }
            return;
        }
        let datasets = [];

        Object.entries(downtimesById).forEach(([downtimeId, downtime]) => {

            let downtimeInfo = getDowntimeById(downtimeId);
            if (!downtimeInfo)
                return;

            datasets.push({
                label: downtimeInfo.category,
                color: DowntimeTypeColors[downtimeInfo.downtime_type_id],
                value: downtime.total / 60
            });
        });
        datasets.sort(function(a, b) {
            return b.value - a.value;
        });

        if (top10DownChart) {
            let data = [];
            let labels = [];
            let colors = [];
            top10DownChart.data.datasets[0].data.length = 0;
            top10DownChart.data.labels.length = 0;
            top10DownChart.data.datasets[0].backgroundColor.length = 0;

            datasets.forEach(e => {
                data.push(e.value);
                labels.push(e.label);
                colors.push(e.color);


            });

            top10DownChart.data.datasets[0].data = data; //.push(e.value);
            top10DownChart.data.labels = labels; //.push(e.label);
            top10DownChart.data.datasets[0].backgroundColor = colors; //.push(e.color);
            top10DownChart.data.datasets[0].borderColor = colors; //.push(e.color);

            top10DownChart.update();
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
    };

    var downtimeChart;
    var top10DownChart;

    function populateTemplate(production_lines) {
        let dashboardContainer = $('#dashboard-container');

        if (production_lines.length > 0) {
            dashboardContainer.html($('#template-active-production-line').html());
            dashboardContainer.find('.render-variance-percentage').data('render', (e, value, data) => {
                
                if (value == null || isNaN(value))
                    return '-';

                return `${((1-parseFloat(value))*100).toFixed(2)}%`;
            });
            downtimeChartConfig.initChart();
            top10DownChartConfig.initChart();

            downtimeChart = downtimeChartConfig.chart;
            top10DownChart = top10DownChartConfig.chart;

            attachRenderer();

            LivePage.clearCallbackPrevValue();
            LivePage.updateLiveData();
        } else {
            dashboardContainer.html($('#template-inactive-production-line').html());
        }
    }
</script>

{{-- chart config --}}
<script>
    var downtimeChartConfig = {
        chartCanvasID: 'downtime-chart',
        chartConfig: {
            type: 'bar',
            data: {
                labels: ['DIE CHANGE', 'MACHINE DOWNTIME', 'HUMAN DOWNTIME'],
                animations: {
                    y: {
                        duration: 2000,
                        delay: 500,
                    },
                },
                datasets: [{
                    type: 'bar',
                    label: 'Count',
                    backgroundColor: ['#ffa000', '#c62828', '#303f9f'],
                    borderColor: ['#ffa000', '#c62828', '#303f9f'],
                    data: [30, 68, 30],
                }],
            },
            options: {
                maintainAspectRatio: true,
                responsive: true,
                elements: {
                    bar: {
                        borderWidth: 2,
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        },
        initChart: function() {
            const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
            this.chart = new Chart(ctx, this.chartConfig);
        }
    };

    var top10DownChartConfig = {
        chartCanvasID: 'top10down-chart',
        chartConfig: {
            type: 'bar',
            data: {
                labels: ['EMERGENCY', 'WIP WAITING', 'AUTOLOADER #1', 'ROBOT #1', 'ROBOT #2', 'LIFTER #1', 'PRESS #1', 'OTHER', 'AUTOLOADER #2', 'LIFTER #1'],
                animations: {
                    y: {
                        duration: 0,
                        delay: 0,
                    },
                },
                datasets: [{
                    type: 'bar',
                    label: 'Count',
                    backgroundColor: ['#c62828', '#303f9f', '#c62828', '#c62828', '#c62828', '#c62828', '#c62828', '#303f9f', '#c62828', '#c62828'],
                    borderColor: ['#c62828', '#303f9f', '#c62828', '#c62828', '#c62828', '#c62828', '#c62828', '#303f9f', '#c62828', '#c62828'],
                    data: [33, 25, 21, 20, 10, 10, 10, 5, 4, 3],
                }],
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
                        display: false
                    }
                }
            }
        },
        initChart: function() {
            const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
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