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
    function autoFormat($item)
    {
        return is_numeric($item) ? number_format($item * 100, 2) . '%' : '-';
    }
    $tableContents = [];
    if (isset($data->factories)) {
        foreach ($data->factories as $factory) {
            $content = ['factory_name' => $factory->name];
    
            $rows = [];
    
            foreach ($factory->workCenters as $workCenter) {
                $workCenterData = $data->collectWorkCenterShiftData($workCenter->uid, null);
                $row = [];
                $row[] = $workCenter->name;
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::DAY_SHIFT]->average_oee ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::DAY_SHIFT]->average_availability ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::DAY_SHIFT]->average_performance ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::DAY_SHIFT]->average_quality ?? null);
    
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::NIGHT_SHIFT]->average_oee ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::NIGHT_SHIFT]->average_availability ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::NIGHT_SHIFT]->average_performance ?? null);
                $row[] = autoFormat($workCenterData[\App\Models\ShiftType::NIGHT_SHIFT]->average_quality ?? null);
    
                $rows[] = $row;
            }
            $content['rows'] = $rows;
            $tableContents[] = $content;
        }
    }
    ?>
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Factory OEE</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <h2>Operational Analysis - Factory OEE</h2>
            <table class="table table-sm table-borderless my-2">
                <tr>
                    <td class="text-right font-bold" style="width:1px;white-space: nowrap;">Plant :</td>
                    <td>{{ $data->plant->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-right font-bold">Date :</td>
                    <td>{{ $data->date ?? '-' }}</td>
                </tr>
            </table>

            @foreach ($tableContents as $content)
                <h4 class="mt-3">{{ $content['factory_name'] }}</h4>
                <table class="data-table table table-bordered table-striped table-sm text-sm my-2 text-center">
                    <thead>
                        {{-- <tr>
                            <th colspan="9" class="factory-name text-left">{{ $content['factory_name'] }}</th>
                        </tr> --}}
                        <tr>
                            <th rowspan="2">Work Center</th>
                            <th colspan="4">Day</th>
                            <th colspan="4">Night</th>
                        </tr>

                        <tr>

                            <th>OEE</th>
                            <th>Availability</th>
                            <th>Performance</th>
                            <th>Quality</th>

                            <th>OEE</th>
                            <th>Availability</th>
                            <th>Performance</th>
                            <th>Quality</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content['rows'] as $row)
                            <tr>
                                <td>{{ $row[0] }}</td>
                                <td>{{ $row[1] }}</td>
                                <td>{{ $row[2] }}</td>
                                <td>{{ $row[3] }}</td>
                                <td>{{ $row[4] }}</td>
                                <td>{{ $row[5] }}</td>
                                <td>{{ $row[6] }}</td>
                                <td>{{ $row[7] }}</td>
                                <td>{{ $row[8] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>
@endsection
