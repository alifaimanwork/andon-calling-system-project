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
    </style>
@endsection
@section('body')
    <div class="page">
        <div class="page-container">
            <div class="d-flex">
                <div class="text-smm font-mono flex-fill flexbase page-no"></div>
                <div class="text-smm font-mono flex-fill flexbase text-center">Operational Analysis - Summary</div>
                <div class="text-smm font-mono flex-fill flexbase text-right">
                    {{ $data->plant->getLocalDateTime()->format('Y-m-d H:i:s') }}</div>
            </div>
            <h2>Operational Analysis - Summary</h2>
            <table class="table table-sm table-borderless my-2">
                <tr>
                    <td class="text-right font-bold" style="width:1px;white-space: nowrap;">Plant :</td>
                    <td>{{ $data->plant->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-right font-bold">Date :</td>
                    <td>{{ $data->dateStart ?? '-' }} - {{ $data->dateEnd ?? '-' }}</td>
                </tr>
            </table>
            <table class="table table-bordered table-striped">
                <tr>
                    <td class="font-bold">Total Plan Output</td>
                    <td class="text-right font-mono">

                        {{ isset($data->data['total_standard_output']) && is_numeric($data->data['total_standard_output']) ? number_format($data->data['total_standard_output'], 0, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            PCS
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Total Actual Output</td>
                    <td class="text-right font-mono">
                        {{ isset($data->data['total_actual_output']) && is_numeric($data->data['total_actual_output']) ? number_format($data->data['total_actual_output'], 0, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            PCS
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Total Reject Part</td>
                    <td class="text-right font-mono">
                        {{ isset($data->data['total_reject_count']) && is_numeric($data->data['total_reject_count']) ? number_format($data->data['total_reject_count'], 0, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            PCS
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Total Downtime</td>
                    <td class="text-right font-mono">
                        {{ isset($data->data['total_downtimes_unplan']) && is_numeric($data->data['total_downtimes_unplan']) ? number_format($data->data['total_downtimes_unplan'], 2, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            HRS
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Total Working Hour</td>
                    <td class="text-right font-mono">
                        {{ isset($data->data['total_runtimes_plan']) && is_numeric($data->data['total_runtimes_plan']) ? number_format($data->data['total_runtimes_plan'], 2, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            HRS
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">Average OEE</td>
                    <td class="text-right font-mono">
                        {{ isset($data->data['average_oee']) && is_numeric($data->data['average_oee']) ? number_format($data->data['average_oee'] * 100, 2, '.', ',') : '-' }}
                        <span class="unit-pad text-left">
                            %
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
