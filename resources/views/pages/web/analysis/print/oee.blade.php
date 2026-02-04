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
    </style>
@endsection
@section('body')
    <?php
    $dayShift = null;
    $nightShift = null;
    
    if (isset($data->data['shifts']) && is_array($data->data['shifts'])) {
        foreach ($data->data['shifts'] as $shift) {
            if ($shift['shift_type_id'] == \App\Models\ShiftType::DAY_SHIFT) {
                $dayShift = $shift;
            } elseif ($shift['shift_type_id'] == \App\Models\ShiftType::NIGHT_SHIFT) {
                $nightShift = $shift;
            }
        }
    }
    $shiftText = [
        \App\Models\ShiftType::DAY_SHIFT => 'Day',
        \App\Models\ShiftType::NIGHT_SHIFT => 'Night',
    ];
    
    $tableContents = [];
    $n = 1;
    if (isset($data->data['data']) && is_array($data->data['data'])) {
        foreach ($data->data['data'] as $item) {
            $row = [];
            $row[] = $n++;
            $row[] = $item->shift_date ?? '-';
            $row[] = $shiftText[$item->shift_type_id] ?? '-';
            $row[] = $item->line_no ?? '-';
            $row[] = $item->order_no ?? '-';
            $row[] = $item->part_no ?? '-';
            $row[] = $item->part_name ?? '-';
            $row[] = isset($item->availability) && is_numeric($item->availability) ? number_format($item->availability * 100, 0) : '-';
            $row[] = isset($item->performance) && is_numeric($item->performance) ? number_format($item->performance * 100, 0) : '-';
            $row[] = isset($item->quality) && is_numeric($item->quality) ? number_format($item->quality * 100, 0) : '-';
            $row[] = isset($item->oee) && is_numeric($item->oee) ? number_format($item->oee * 100, 0) : '-';
            $tableContents[] = $row;
        }
    }
    ?>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - OEE</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h2>Operational Analysis - OEE</h2>
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
                <thead>
                    <tr>
                        <th>Item\Shift</th>
                        <th>Day</th>
                        <th>Night</th>
                        <th>Overall</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold">Average OEE</td>
                        <td class="text-right font-mono">
                            {{ isset($dayShift['average_oee']) && is_numeric($dayShift['average_oee']) ? number_format($dayShift['average_oee'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($nightShift['average_oee']) && is_numeric($nightShift['average_oee']) ? number_format($nightShift['average_oee'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['average_oee']) && is_numeric($data->data['average_oee']) ? number_format($data->data['average_oee'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Average Availability</td>
                        <td class="text-right font-mono">
                            {{ isset($dayShift['average_availability']) && is_numeric($dayShift['average_availability']) ? number_format($dayShift['average_availability'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($nightShift['average_availability']) && is_numeric($nightShift['average_availability']) ? number_format($nightShift['average_availability'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['average_availability']) && is_numeric($data->data['average_availability']) ? number_format($data->data['average_availability'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Average Performance</td>
                        <td class="text-right font-mono">
                            {{ isset($dayShift['average_performance']) && is_numeric($dayShift['average_performance']) ? number_format($dayShift['average_performance'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($nightShift['average_performance']) && is_numeric($nightShift['average_performance']) ? number_format($nightShift['average_performance'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['average_performance']) && is_numeric($data->data['average_performance']) ? number_format($data->data['average_performance'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">Average Quality</td>
                        <td class="text-right font-mono">
                            {{ isset($dayShift['average_quality']) && is_numeric($dayShift['average_quality']) ? number_format($dayShift['average_quality'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($nightShift['average_quality']) && is_numeric($nightShift['average_quality']) ? number_format($nightShift['average_quality'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                        <td class="text-right font-mono">
                            {{ isset($data->data['average_quality']) && is_numeric($data->data['average_quality']) ? number_format($data->data['average_quality'] * 100, 2, '.', ',') : '-' }}
                            <span class="unit-pad text-left">
                                %
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="data-table table table-bordered table-striped table-sm text-sm my-2 text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Line</th>
                        <th>Production Order</th>
                        <th>Part Number</th>
                        <th>Part Name</th>
                        <th>A (%)</th>
                        <th>P (%)</th>
                        <th>Q (%)</th>
                        <th>OEE (%)</th>
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
                    <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - OEE</div>
                    <div class="text-smm font-mono flex-fill flexbase text-right">
                        {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
                </div>
                <table class="data-table table table-bordered table-striped table-sm text-sm my-2 text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Line</th>
                            <th>Production Order</th>
                            <th>Part Number</th>
                            <th>Part Name</th>
                            <th>A (%)</th>
                            <th>P (%)</th>
                            <th>Q (%)</th>
                            <th>OEE (%)</th>
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
    </script>
@endsection
