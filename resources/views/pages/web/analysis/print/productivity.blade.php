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
            height: 5cm;
        }
    </style>
@endsection
@section('body')
    <?php
    $dayShift = null;
    $nightShift = null;
    
    foreach ($data->data['shifts'] as $shift) {
        if ($shift['shift_type_id'] == \App\Models\ShiftType::DAY_SHIFT) {
            $dayShift = $shift;
        } elseif ($shift['shift_type_id'] == \App\Models\ShiftType::NIGHT_SHIFT) {
            $nightShift = $shift;
        }
    }
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
        $row[] = isset($item->plan_quantity) && is_numeric($item->plan_quantity) ? $item->plan_quantity : '-';
        $row[] = isset($item->standard_output) && is_numeric($item->standard_output) ? $item->standard_output : '-';
        $row[] = isset($item->actual_output) && is_numeric($item->actual_output) ? $item->actual_output : '-';
        $row[] = isset($item->performance) && is_numeric($item->performance) ? number_format($item->performance * 100, 0) : '-';
        $tableContents[] = $row;
    }
    
    ?>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Productivity</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h2>Operational Analysis - Productivity</h2>
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
                        <td class="font-bold">Total Plan Output</td>
                        <td class="text-right font-mono">
                            {{ $data->data['total_standard_output'] ?? '-' }}
                            <span class="unit-pad text-left">
                                PCS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Total Actual Output</td>
                        <td class="text-right font-mono">
                            {{ $data->data['total_actual_output'] ?? '-' }}
                            <span class="unit-pad text-left">
                                PCS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Productivity</td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['performance']) && is_numeric($data->data['performance']) ? number_format($data->data['performance'] * 100, 2) : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
            <h4>Productivity - Day Shift (PCS)</h4>
            <div class="chart-container">
                <canvas id="day_productivity">
            </div>
            <h4>Productivity - Night Shift (PCS)</h4>
            <div class="chart-container">
                <canvas id="night_productivity">
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
                        <th>Total Plan</th>
                        <th>Total Standard Output</th>
                        <th>Total Actual Output</th>
                        <th>Productivity (%)</th>
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
                    <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Productivity</div>
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
                            <th>Total Plan</th>
                            <th>Total Standard Output</th>
                            <th>Total Actual Output</th>
                            <th>Productivity (%)</th>
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
            var dataElement = $('.data-table tbody')[0];
            var page = $('.page')
            var pageContainer = $('.page-container');


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
        }
    </script>
@endsection
