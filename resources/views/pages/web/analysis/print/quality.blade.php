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

        .chart-defect-container {
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
    
        $row[] = $item->actual_output ?? '-';
        $row[] = $item->reject_count ?? '-';
        $row[] = $item->reject_setting ?? '-';
        $row[] = $item->reject_process ?? '-';
        $row[] = $item->reject_material ?? '-';
    
        $tableContents[] = $row;
    }
    
    ?>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Quality</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h2>Operational Analysis - Quality</h2>
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
                        <td class="font-bold">Actual Output</td>
                        <td class="text-right font-mono">
                            {{ $data->data['total_actual_output'] ?? '-' }}
                            <span class="unit-pad text-left">
                                PCS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Total Reject</td>
                        <td class="text-right font-mono">
                            {{ $data->data['total_reject_count'] ?? '-' }}
                            <span class="unit-pad text-left">
                                PCS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Reject Percentage</td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['reject_percentage']) && is_numeric($data->data['reject_percentage']) ? number_format($data->data['reject_percentage'] * 100, 2) : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>

            <div class="chart-centering">
                <div class="chart-defect-container">
                    <canvas id="defects">
                </div>
            </div>
            <h4>Top 10 Reject By Defect Part (PCS)</h4>
            <div class="chart-container">
                <canvas id="top10reject">
            </div>
        </div>
    </div>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Quality</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h4>Defects</h4>
            <table class="data-table table table-bordered table-striped table-sm text-smm my-2 text-center">
                <thead>
                    <tr>
                        <th>Reject Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data->data['defects'] ) && is_array($data->data['defects'] ))
                    @foreach ($data->data['defects'] as $defect)
                        <tr>
                            <td>{{ $defect['name_2'] ?? '-' }}</td>
                            <td>{{ $defect['count'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Quality</div>
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

                        <th>Total Output</th>
                        <th>Total Reject</th>
                        <th>Setting Reject</th>
                        <th>Process Reject</th>
                        <th>Material Reject</th>
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
                    <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Quality</div>
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

                            <th>Total Output</th>
                            <th>Total Reject</th>
                            <th>Setting Reject</th>
                            <th>Process Reject</th>
                            <th>Material Reject</th>
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
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 10
                                    },
                                    padding: 4,
                                }
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
        }
    </script>
@endsection
