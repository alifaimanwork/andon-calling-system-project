@extends('layouts.print')
@section('head')
    @parent
    <style>
        .unit-pad {
            display: inline-block;
            min-width: 2em;
        }

        .flexbase {
            flex-basis: 1%;
        }

        .no-data-cell {
            font-style: italic;
        }

        .chart-container {
            height: 8cm;
        }

        .chart-downtime-container {
            width: 10cm;
        }

        .chart-centering {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('body')
    <?php
    
    $shiftText = [
        \App\Models\ShiftType::DAY_SHIFT => 'Day',
        \App\Models\ShiftType::NIGHT_SHIFT => 'Night',
    ];
    
    $tableContents = [];
    $n = 1;
    foreach ($data->data['data'] as $item) {
        $row = [];
        $row[] = $n++;
        $row[] = $item->shift_date ?? '-';
        $row[] = $shiftText[$item->shift_type_id] ?? '-';
        $row[] = $item->line_no ?? '-';
        $row[] = $item->order_no ?? '-';
        $row[] = $item->part_no ?? '-';
        $row[] = $item->part_name ?? '-';
    
        $row[] = isset($item->runtimes_plan) && is_numeric($item->runtimes_plan) ? number_format($item->runtimes_plan / 3600, 2) : '-';
        $row[] = isset($item->downtimes_unplan) && is_numeric($item->downtimes_unplan) ? number_format($item->downtimes_unplan / 3600, 2) : '-';
        $row[] = isset($item->downtimes_unplan_die_change) && is_numeric($item->downtimes_unplan_die_change) ? number_format($item->downtimes_unplan_die_change / 3600, 2) : '-';
        $row[] = isset($item->downtimes_unplan_machine) && is_numeric($item->downtimes_unplan_machine) ? number_format($item->downtimes_unplan_machine / 3600, 2) : '-';
        $row[] = isset($item->downtimes_unplan_human) && is_numeric($item->downtimes_unplan_human) ? number_format($item->downtimes_unplan_human / 3600, 2) : '-';
    
        $row[] = isset($item->downtime_percentage) && is_numeric($item->downtime_percentage) ? number_format($item->downtime_percentage * 100, 1) . '%' : '-';
        $tableContents[] = $row;
    }
    
    ?>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Downtime</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h2>Operational Analysis - Downtime</h2>
            <table class="table table-sm table-borderless my-2">
                <tr>
                    <td class="text-right font-bold" style="width:1px;white-space: nowrap;">Plant :</td>
                    <td>{{ $data->plant->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-right font-bold" style="width:1px;white-space: nowrap;">Work Center :</td>
                    <td>{{ $data->workCenter->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-right font-bold">Date :</td>
                    <td>{{ $data->dateStart ?? '-' }} - {{ $data->dateEnd ?? '-' }}</td>
                </tr>
            </table>
            <table class="table table-bordered table-striped table-sm text-m my-2">
                <tbody>
                    <tr>
                        <td class="font-bold">Total Working Time</td>
                        <td class="text-right font-mono">
                            {{ (isset($data->data['total_runtimes_plan']) && is_numeric($data->data['total_runtimes_plan']) ? number_format($data->data['total_runtimes_plan'] / 3600, 2) : '-') ?? '-' }}
                            <span class="unit-pad text-left">
                                HRS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Total Downtime</td>
                        <td class="text-right font-mono">
                            {{ (isset($data->data['total_downtimes_unplan']) && is_numeric($data->data['total_downtimes_unplan']) ? number_format($data->data['total_downtimes_unplan'] / 3600, 2) : '-') ?? '-' }}
                            <span class="unit-pad text-left">
                                HRS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Downtime Percentage</td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['downtime_percentage']) && is_numeric($data->data['downtime_percentage']) ? number_format($data->data['downtime_percentage'] * 100, 2) : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>

            <div class="chart-centering">
                <div class="chart-downtime-container">
                    <canvas id="downtimes">
                </div>
            </div>
            <h4>Top 10 Downtime By Breakdown (MIN)</h4>
            <div class="chart-container">
                <canvas id="top10downtime">
            </div>
        </div>
    </div>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Downtime</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h4>Downtimes</h4>
            <table class="data-table table table-bordered table-striped table-sm text-smm my-2 text-center">
                <thead>
                    <tr>
                        <th>Downtime Type</th>
                        <th>Duration (minutes)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->data['downtimes'] as $downtime)
                        <tr>
                            <td>{{ $downtime['name'] ?? '-' }}</td>
                            <td>{{ isset($downtime['duration']) && is_numeric($downtime['duration']) ? number_format($downtime['duration'] / 60, 0) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Downtime</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <table class="data-table table table-bordered table-striped table-sm text-smm my-2 text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Line</th>
                        <th>Production Order</th>
                        <th>Part Number</th>
                        <th>Part Name</th>

                        <th>Total Working Hours</th>
                        <th>Total Downtime</th>
                        <th>Unplan Die-Change</th>
                        <th>Machine Downtime</th>
                        <th>Human Downtime</th>
                        <th>Downtime %</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('templates')
    @parent
    <template id="table-body">
        <tr>
            <td class="text-right"></td>
            <td class="nowrap"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </template>
    <template id="table-body-nodata">
        <tr>
            <td class="no-data-cell" colspan="11">No Production</td>
        </tr>
    </template>
    <template id="new-data-page">
        <div class="page">
            <div class="page-container">
                <div class="d-flex">
                    <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                    <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Downtime</div>
                    <div class="text-smm font-mono flex-fill flexbase text-right">
                        {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
                </div>
                <table class="data-table table table-bordered table-striped table-sm text-smm my-2 text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Line</th>
                            <th>Production Order</th>
                            <th>Part Number</th>
                            <th>Part Name</th>

                            <th>Total Working Hours</th>
                            <th>Total Downtime</th>
                            <th>Unplan Die-Change</th>
                            <th>Machine Downtime</th>
                            <th>Human Downtime</th>
                            <th>Downtime %</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </template>
@endsection
@section('scripts')
    @parent
    <script src="{{ asset(mix('js/app.js')) }}"></script>
    <script>
        var data = @json($tableContents);

        $(() => {
            generateDataTable();
            chart.initializeCharts().updateCharts();
        });

        function generateDataTable() {
            var page = $($('.page')[$('.page').length - 1]);
            dataElement = page.find('.data-table tbody');
            pageContainer = page.find('.page-container');


            for (i = 0; i < data.length; i++) {
                const e = data[i];
                let rowElement = $($('#table-body').html());
                rowElement.find('td').each((idx, td) => {
                    $(td).html(e[idx]);
                });

                dataElement.append(rowElement[0]);
                //measure min
                if (page.height() - pageContainer.height() < 120) {
                    let newPage = $($('#new-data-page').html());
                    $('body').append(newPage);
                    let pages = $('.page');

                    page = $(pages[pages.length - 1]);
                    dataElement = page.find('.data-table tbody');
                    pageContainer = page.find('.page-container');

                }
            }
            if (data.length <= 0) {
                let rowElement = $($('#table-body-nodata').html());
                dataElement.append(rowElement[0]);
            }

            //page numbering
            let pageNos = $('.page-no');
            if (pageNos.length > 1) {
                pageNos.each((idx, e) => {
                    $(e).html(`Page: ${idx+1} / ${pageNos.length}`);
                })
            }

        }



        var chart = {
            data: @json($data->data),
            updateCharts: function() {
                let _this = this;
                this.updateDowntimeChart(this.data.downtimes)
                    .updateTop10Chart(this.data.downtimes)
                return this;
            },
            updateDowntimeChart(data) {
                let chart = this.charts.downtimes;

                let labels = [];
                let chartData = [];
                let backgroundColors = [];
                let borderColors = [];
                let coolColorGenerator = new ColorGenerator();
                coolColorGenerator.baseColor = coolColorGenerator.coolColor;

                let hotColorGenerator = new ColorGenerator();
                hotColorGenerator.baseColor = hotColorGenerator.hotColor;


                let hotIndex = 0;
                let coolIndex = 0;

                data.forEach((e, index) => {
                    labels.push(e.name);
                    if (e.type == 1) //machine
                    {
                        backgroundColors.push(hotColorGenerator.generateColor(hotIndex, 1));
                        borderColors.push(hotColorGenerator.generateColor(hotIndex, 0.7));
                        hotIndex++;
                    } else if (e.type == 2) //human
                    {
                        backgroundColors.push(coolColorGenerator.generateColor(coolIndex, 1));
                        borderColors.push(coolColorGenerator.generateColor(coolIndex, 0.7));
                        coolIndex++;
                    } else { //die change
                        backgroundColors.push('rgba(255,152,0,1)');
                        borderColors.push('rgba(255,152,0,0.7)');
                        coolIndex++;
                    }
                    chartData.push(e.duration / 60);
                });


                chart.data.labels = labels;
                chart.data.datasets[0].data = chartData;
                chart.data.datasets[0].backgroundColor = backgroundColors;
                chart.data.datasets[0].borderColor = borderColors;


                chart.update();
                return this;
            },
            updateTop10Chart(data) {
                let chart = this.charts.top10downtime;

                let labels = [];
                let chartData = [];
                let backgroundColors = [];
                let borderColors = [];
                let coolColorGenerator = new ColorGenerator();
                coolColorGenerator.baseColor = coolColorGenerator.coolColor;

                let hotColorGenerator = new ColorGenerator();
                hotColorGenerator.baseColor = hotColorGenerator.hotColor;


                let hotIndex = 0;
                let coolIndex = 0;

                data.forEach((e, index) => {
                    labels.push(e.name);
                    if (e.type == 1) //machine
                    {
                        backgroundColors.push(hotColorGenerator.generateColor(hotIndex, 1));
                        borderColors.push(hotColorGenerator.generateColor(hotIndex, 0.7));
                        hotIndex++;
                    } else if (e.type == 2) //human
                    {
                        backgroundColors.push(coolColorGenerator.generateColor(coolIndex, 1));
                        borderColors.push(coolColorGenerator.generateColor(coolIndex, 0.7));
                        coolIndex++;
                    } else { //die change
                        backgroundColors.push('rgba(255,152,0,1)');
                        borderColors.push('rgba(255,152,0,0.7)');
                        coolIndex++;
                    }
                    chartData.push(e.duration / 60);
                });

                chart.data.labels = labels;
                chart.data.datasets[0].data = chartData;
                chart.data.datasets[0].backgroundColor = backgroundColors;
                chart.data.datasets[0].borderColor = borderColors;


                chart.update();
                return this;
            },
            chartOptions: {
                downtimes: {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            label: 'Downtimes',
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
                top10downtime: {
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
                downtimes: undefined,
                top10downtime: undefined,
            },
            initializeCharts: function() {
                let _this = this;
                Object.entries(_this.chartOptions).forEach(([key, option]) => {
                    var ctx = document.getElementById(key).getContext('2d');
                    _this.charts[key] = new Chart(ctx, option);
                });
                return this;
            }
        }
    </script>
@endsection
