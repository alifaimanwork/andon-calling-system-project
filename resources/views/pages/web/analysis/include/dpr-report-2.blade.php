{{-- table  --}}
@section('head')
@parent
<style>
    /* bg-gray-custom */
    .bg-gray-custom {
        background-color: #ccccccec !important;
    }

    .bg-blue-custom {
        background-color: #b6dde8 !important;
    }

    .bg-pink-custom {
        background-color: #ff8dcf !important;
    }

    .bg-peach-custom {
        background-color: #fbd4b4 !important;
    }

    .bg-orange-custom {
        background-color: #ffa500 !important;
    }

    .bg-yellow-custom {
        background-color: #ffff00 !important;
    }

    .bg-light-yellow-custom {
        background-color: #ffff90 !important;
    }

    .font-weight-size {
        font-size: 1.2rem;
        font-weight: 900;
        margin-bottom: 15px;
    }

    .time-width {
        min-width: 100px !important;
    }

    .dpr table,
    .dpr tr,
    .dpr td,
    .dpr th {
        border: 2px solid black !important;
    }

    .dpr th {
        background-color: #ccccccec !important;
    }
</style>
@endsection

<table class="dpr table table-bordered bg-light text-center align-middle">
    <tbody>
        {{-- Header --}}
        <tr class="fw-bold">
            {{-- add image in table --}}
            <td rowspan="3" colspan="9">
                <img src="{{ asset('images/logo_smi_text.png') }}" alt="logo" width="100%" class="px-3">
            </td>
            <td rowspan="3" colspan="31" class="text-center fw-bold" style="font-size: 3em">ONLINE DAILY PRODUCTION REPORT</td>
            <td colspan="2" class="text-nowrap">Prepared by</td>
            <td colspan="2" class="text-nowrap">Checked by</td>
            <td colspan="2" class="text-nowrap">Approved by</td>
            <td colspan="2" class="text-nowrap">Document No</td>
            <td colspan="2" class="text-nowrap">-</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="2"></td>
            <td colspan="2" rowspan="2"></td>
            <td colspan="2" rowspan="2"></td>
            <td colspan="2" class="fw-bold">Revision No</td>
            <td colspan="2"></td>
        </tr>
        <tr class="fw-bold">
            <td colspan="2">Effetive Date</td>
            <td colspan="2"></td>
        </tr>

        {{-- Body --}}
        <tr class="fw-bold">
            <td colspan="2" class="bg-gray-custom">DATE</td>
            <td colspan="2" class="dpr-data" data-tag="date">-</td>
            <td colspan="6" class="bg-gray-custom">SHIFT</td>
            <td colspan="7" class="dpr-data" data-tag="shift">-</td>
            <td colspan="3">MANPOWER ATTENDANCE :</td>
            <td colspan="2" class="dpr-data" data-tag="man_power">-</td>
            <td colspan="18"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">Supervisor</td>
            <td colspan="2"></td>
        </tr>

        {{-- content header --}}
        <tr class="fw-bold">
            <td colspan="2" class="text-nowrap bg-gray-custom">WORK CENTER</td>
            <td colspan="2" class="dpr-data" data-tag="work_center">-</td>
            <td colspan="15"></td>
            <td colspan="3" rowspan="2"><span class="text-danger">PLANNED DOWNTIME </span> (MINUTES)</td>
            <td colspan="18" rowspan="2"><span class="text-danger">REJECTION (PIECES)</span></td>
            <td colspan="4"><span class="text-danger">UNPLANNED DOWNTIME</span></td>
            <td colspan="6"></td>
        </tr>

        <tr class="fw-bold">
            <td colspan="2" rowspan="2">TIME</td>
            <td colspan="2" rowspan="3">PLANNED PRODUCTION TIME</td>
            <td colspan="12">PRODUCTIVITY (PIECES)</td>
            <td colspan="3" rowspan="3">PROBLEM & CORRECTIVE ACTION</td>
            <td colspan="3">DOWNTIME (MINUTES)</td>
            <td rowspan="3" class="bg-orange-custom">TTL D/T (MINUTES)</td>
            <td colspan="6" rowspan="2">REAL OPERATING TIME (MINUTES)</td>
        </tr>

        <tr class="fw-bold">
            <td colspan="6" class="bg-pink-custom">STANDARD</td>
            <td colspan="6" class="bg-peach-custom">ACTUAL</td>
            <td rowspan="2" class="text-nowrap">DIE CHANGE</td>
            <td rowspan="2" class="text-nowrap bg-yellow-custom">BREAK</td>
            <td rowspan="2" class="text-nowrap">TOTAL</td>
            <td colspan="6" class="bg-blue-custom">SETTING</td>
            <td colspan="6" class="bg-blue-custom">PROCESS</td>
            <td colspan="6" class="bg-blue-custom">MATERIAL</td>
            <td rowspan="2" class="text-nowrap">DIE CHANGE</td>
            <td rowspan="2" class="text-nowrap">MACHINE</td>
            <td rowspan="2" class="text-nowrap">HUMAN</td>
        </tr>

        <tr id="pre-dpr-content" class="fw-bold">
            <td class="time-width">START</td>
            <td class="time-width">END</td>
            <td class="text-nowrap bg-pink-custom">Line 1</td>
            <td class="text-nowrap bg-pink-custom">Line 2</td>
            <td class="text-nowrap bg-pink-custom">Line 3</td>
            <td class="text-nowrap bg-pink-custom">Line 4</td>
            <td class="text-nowrap bg-pink-custom">Line 5</td>
            <td class="text-nowrap bg-pink-custom">Line 6</td>
            <td class="text-nowrap bg-peach-custom">Line 1</td>
            <td class="text-nowrap bg-peach-custom">Line 2</td>
            <td class="text-nowrap bg-peach-custom">Line 3</td>
            <td class="text-nowrap bg-peach-custom">Line 4</td>
            <td class="text-nowrap bg-peach-custom">Line 5</td>
            <td class="text-nowrap bg-peach-custom">Line 6</td>
            <td class="text-nowrap">Line 1</td>
            <td class="text-nowrap">Line 2</td>
            <td class="text-nowrap">Line 3</td>
            <td class="text-nowrap">Line 4</td>
            <td class="text-nowrap">Line 5</td>
            <td class="text-nowrap">Line 6</td>
            <td class="text-nowrap">Line 1</td>
            <td class="text-nowrap">Line 2</td>
            <td class="text-nowrap">Line 3</td>
            <td class="text-nowrap">Line 4</td>
            <td class="text-nowrap">Line 5</td>
            <td class="text-nowrap">Line 6</td>
            <td class="text-nowrap">Line 1</td>
            <td class="text-nowrap">Line 2</td>
            <td class="text-nowrap">Line 3</td>
            <td class="text-nowrap">Line 4</td>
            <td class="text-nowrap">Line 5</td>
            <td class="text-nowrap">Line 6</td>
            <td class="text-nowrap">Line 1</td>
            <td class="text-nowrap">Line 2</td>
            <td class="text-nowrap">Line 3</td>
            <td class="text-nowrap">Line 4</td>
            <td class="text-nowrap">Line 5</td>
            <td class="text-nowrap">Line 6</td>
        </tr>

        <tr>
            <td colspan="50"></td>
        </tr>
        <tr class="bg-gray-custom fw-bold">
            <td colspan="2">TOTAL</td>
            <td colspan="2" class="dpr-data" data-tag="total_runtime_plan">&nbsp;</td>
            <td class="dpr-data" data-tag="line_1_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_standard_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_1_actual_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_actual_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_actual_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_actual_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_actual_output">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_actual_output">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2" class="dpr-data" data-tag="total_downtime_plan_die_change">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_plan_break">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_plan">&nbsp;</td>
            <td class="dpr-data" data-tag="line_1_reject_1_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_reject_1_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_reject_1_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_reject_1_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_reject_1_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_reject_1_total">&nbsp;</td>

            <td class="dpr-data" data-tag="line_1_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_reject_3_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_1_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_reject_2_total">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_unplan_die_change">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_unplan_machine">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_unplan_human">&nbsp;</td>
            <td class="dpr-data" data-tag="total_downtime_unplan">&nbsp;</td>
            <td class="dpr-data" data-tag="line_1_total_runtime_good">&nbsp;</td>
            <td class="dpr-data" data-tag="line_2_total_runtime_good">&nbsp;</td>
            <td class="dpr-data" data-tag="line_3_total_runtime_good">&nbsp;</td>
            <td class="dpr-data" data-tag="line_4_total_runtime_good">&nbsp;</td>
            <td class="dpr-data" data-tag="line_5_total_runtime_good">&nbsp;</td>
            <td class="dpr-data" data-tag="line_6_total_runtime_good">&nbsp;</td>
        </tr>
    </tbody>
</table>

<span class="font-weight-size">OVERALL RESULT :</span>
<table class="dpr table table-bordered bg-light text-center align-middle">
    <thead>
        <tr class="text-nowrap">
            <th>PRODUCTIVITY</th>
            <th>LINE 1</th>
            <th>LINE 2</th>
            <th>LINE 3</th>
            <th>LINE 4</th>
            <th>LINE 5</th>
            <th>LINE 6</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Production Order Number</td>
            <td class="dpr-data" data-tag="line_1_production_order_no"></td>
            <td class="dpr-data" data-tag="line_2_production_order_no"></td>
            <td class="dpr-data" data-tag="line_3_production_order_no"></td>
            <td class="dpr-data" data-tag="line_4_production_order_no"></td>
            <td class="dpr-data" data-tag="line_5_production_order_no"></td>
            <td class="dpr-data" data-tag="line_6_production_order_no"></td>
        </tr>
        <tr>
            <td>Part Number</td>
            <td class="dpr-data" data-tag="line_1_part_no"></td>
            <td class="dpr-data" data-tag="line_2_part_no"></td>
            <td class="dpr-data" data-tag="line_3_part_no"></td>
            <td class="dpr-data" data-tag="line_4_part_no"></td>
            <td class="dpr-data" data-tag="line_5_part_no"></td>
            <td class="dpr-data" data-tag="line_6_part_no"></td>
        </tr>
        <tr>
            <td>Part Name</td>
            <td class="dpr-data" data-tag="line_1_part_name"></td>
            <td class="dpr-data" data-tag="line_2_part_name"></td>
            <td class="dpr-data" data-tag="line_3_part_name"></td>
            <td class="dpr-data" data-tag="line_4_part_name"></td>
            <td class="dpr-data" data-tag="line_5_part_name"></td>
            <td class="dpr-data" data-tag="line_6_part_name"></td>
        </tr>
        <tr>
            <td>Cycle Time Target</td>
            <td class="dpr-data" data-tag="line_1_part_cycle_time"></td>
            <td class="dpr-data" data-tag="line_2_part_cycle_time"></td>
            <td class="dpr-data" data-tag="line_3_part_cycle_time"></td>
            <td class="dpr-data" data-tag="line_4_part_cycle_time"></td>
            <td class="dpr-data" data-tag="line_5_part_cycle_time"></td>
            <td class="dpr-data" data-tag="line_6_part_cycle_time"></td>
        </tr>
        <tr>
            <td>Planned Die Change</td>
            <td class="dpr-data" data-tag="line_1_plan_die_change"></td>
            <td class="dpr-data" data-tag="line_2_plan_die_change"></td>
            <td class="dpr-data" data-tag="line_3_plan_die_change"></td>
            <td class="dpr-data" data-tag="line_4_plan_die_change"></td>
            <td class="dpr-data" data-tag="line_5_plan_die_change"></td>
            <td class="dpr-data" data-tag="line_6_plan_die_change"></td>
        </tr>
        <tr>
            <td>Output (Quality Product)</td>
            <td class="dpr-data" data-tag="line_1_ok_count"></td>
            <td class="dpr-data" data-tag="line_2_ok_count"></td>
            <td class="dpr-data" data-tag="line_3_ok_count"></td>
            <td class="dpr-data" data-tag="line_4_ok_count"></td>
            <td class="dpr-data" data-tag="line_5_ok_count"></td>
            <td class="dpr-data" data-tag="line_6_ok_count"></td>
        </tr>
        <tr>
            <td>Total Reject ( Pieces )</td>
            <td class="dpr-data" data-tag="line_1_reject_count"></td>
            <td class="dpr-data" data-tag="line_2_reject_count"></td>
            <td class="dpr-data" data-tag="line_3_reject_count"></td>
            <td class="dpr-data" data-tag="line_4_reject_count"></td>
            <td class="dpr-data" data-tag="line_5_reject_count"></td>
            <td class="dpr-data" data-tag="line_6_reject_count"></td>
        </tr>
        <tr>
            <td>Total Output (Include Rej)</td>
            <td class="dpr-data" data-tag="line_1_actual_output"></td>
            <td class="dpr-data" data-tag="line_2_actual_output"></td>
            <td class="dpr-data" data-tag="line_3_actual_output"></td>
            <td class="dpr-data" data-tag="line_4_actual_output"></td>
            <td class="dpr-data" data-tag="line_5_actual_output"></td>
            <td class="dpr-data" data-tag="line_6_actual_output"></td>
        </tr>
        <tr>
            <td>Total Downtime ( Minutes )</td>
            <td class="dpr-data" data-tag="line_1_total_downtime_unplan"></td>
            <td class="dpr-data" data-tag="line_2_total_downtime_unplan"></td>
            <td class="dpr-data" data-tag="line_3_total_downtime_unplan"></td>
            <td class="dpr-data" data-tag="line_4_total_downtime_unplan"></td>
            <td class="dpr-data" data-tag="line_5_total_downtime_unplan"></td>
            <td class="dpr-data" data-tag="line_6_total_downtime_unplan"></td>
        </tr>
        <tr>
            <td>Pcs/Hr</td>
            <td class="dpr-data" data-tag="line_1_output_rate"></td>
            <td class="dpr-data" data-tag="line_2_output_rate"></td>
            <td class="dpr-data" data-tag="line_3_output_rate"></td>
            <td class="dpr-data" data-tag="line_4_output_rate"></td>
            <td class="dpr-data" data-tag="line_5_output_rate"></td>
            <td class="dpr-data" data-tag="line_6_output_rate"></td>
        </tr>
        <tr>
            <td>Pcs/m.hr</td>
            <td class="dpr-data" data-tag="line_1_output_manhour_rate"></td>
            <td class="dpr-data" data-tag="line_2_output_manhour_rate"></td>
            <td class="dpr-data" data-tag="line_3_output_manhour_rate"></td>
            <td class="dpr-data" data-tag="line_4_output_manhour_rate"></td>
            <td class="dpr-data" data-tag="line_5_output_manhour_rate"></td>
            <td class="dpr-data" data-tag="line_6_output_manhour_rate"></td>
        </tr>
        <tr>
            <td>Rejection %</td>
            <td class="dpr-data" data-tag="line_1_rejection"></td>
            <td class="dpr-data" data-tag="line_2_rejection"></td>
            <td class="dpr-data" data-tag="line_3_rejection"></td>
            <td class="dpr-data" data-tag="line_4_rejection"></td>
            <td class="dpr-data" data-tag="line_5_rejection"></td>
            <td class="dpr-data" data-tag="line_6_rejection"></td>
        </tr>
        <tr>
            <td>Rejection Target %</td>
            <td class="dpr-data" data-tag="line_1_reject_target"></td>
            <td class="dpr-data" data-tag="line_2_reject_target"></td>
            <td class="dpr-data" data-tag="line_3_reject_target"></td>
            <td class="dpr-data" data-tag="line_4_reject_target"></td>
            <td class="dpr-data" data-tag="line_5_reject_target"></td>
            <td class="dpr-data" data-tag="line_6_reject_target"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th>OEE RESULT</th>
            <th>LINE 1</th>
            <th>LINE 2</th>
            <th>LINE 3</th>
            <th>LINE 4</th>
            <th>LINE 5</th>
            <th>LINE 6</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="bg-light-yellow-custom">Availability</td>
            <td class="dpr-data" data-tag="line_1_availability"></td>
            <td class="dpr-data" data-tag="line_2_availability"></td>
            <td class="dpr-data" data-tag="line_3_availability"></td>
            <td class="dpr-data" data-tag="line_4_availability"></td>
            <td class="dpr-data" data-tag="line_5_availability"></td>
            <td class="dpr-data" data-tag="line_6_availability"></td>
        </tr>
        <tr>
            <td class="bg-light-yellow-custom">Performance</td>
            <td class="dpr-data" data-tag="line_1_performance"></td>
            <td class="dpr-data" data-tag="line_2_performance"></td>
            <td class="dpr-data" data-tag="line_3_performance"></td>
            <td class="dpr-data" data-tag="line_4_performance"></td>
            <td class="dpr-data" data-tag="line_5_performance"></td>
            <td class="dpr-data" data-tag="line_6_performance"></td>
        </tr>
        <tr>
            <td class="bg-light-yellow-custom">Quality</td>
            <td class="dpr-data" data-tag="line_1_quality"></td>
            <td class="dpr-data" data-tag="line_2_quality"></td>
            <td class="dpr-data" data-tag="line_3_quality"></td>
            <td class="dpr-data" data-tag="line_4_quality"></td>
            <td class="dpr-data" data-tag="line_5_quality"></td>
            <td class="dpr-data" data-tag="line_6_quality"></td>
        </tr>
        <tr>
            <td class="bg-light-yellow-custom">OEE</td>
            <td class="dpr-data" data-tag="line_1_oee"></td>
            <td class="dpr-data" data-tag="line_2_oee"></td>
            <td class="dpr-data" data-tag="line_3_oee"></td>
            <td class="dpr-data" data-tag="line_4_oee"></td>
            <td class="dpr-data" data-tag="line_5_oee"></td>
            <td class="dpr-data" data-tag="line_6_oee"></td>
        </tr>
</table>

<span class="font-weight-size">MATERIAL INFORMATION :</span>

<table class="dpr table table-bordered bg-light text-center align-middle">
    <thead>
        <tr>
            <th></th>
            <th>LOT 1</th>
            <th>LOT 2</th>
            <th>LOT 3</th>
            <th>LOT 4</th>
            <th>LOT 5</th>
            <th>LOT 6</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Coil/Bar</td>
            <td class="dpr-data" data-tag="die_change_info_lot_1_coil_bar"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_2_coil_bar"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_3_coil_bar"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_4_coil_bar"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_5_coil_bar"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_6_coil_bar"></td>
        </tr>
        <tr>
            <td>Child Part</td>
            <td class="dpr-data" data-tag="die_change_info_lot_1_child_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_2_child_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_3_child_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_4_child_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_5_child_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_6_child_part"></td>
        </tr>
        <tr>
            <td>Material Part</td>
            <td class="dpr-data" data-tag="die_change_info_lot_1_material_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_2_material_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_3_material_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_4_material_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_5_material_part"></td>
            <td class="dpr-data" data-tag="die_change_info_lot_6_material_part"></td>
        </tr>
    </tbody>
</table>

@section('templates')
@parent
<template id="template-dpr-content-row">
    <tr class="dpr-content">
        <td rowspan="2" class="dpr-data" data-tag="row___ROWLINE___start_time">&nbsp;</td>
        <td rowspan="2" class="dpr-data" data-tag="row___ROWLINE___end_time">&nbsp;</td>
        <td colspan="2" rowspan="2" class="dpr-data" data-tag="row___ROWLINE___total_runtime_plan">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___standard_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___actual_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___actual_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___actual_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___actual_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___actual_output">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___actual_output">&nbsp;</td>
        <td colspan="3" rowspan="2">&nbsp;</td>
        <td rowspan="2" class="dpr-data" data-tag="row___ROWLINE___total_downtime_plan_die_change">&nbsp;</td>
        <td rowspan="2" class="bg-yellow-custom dpr-data" data-tag="row___ROWLINE___total_downtime_plan_break">0</td>
        <td rowspan="2" class="dpr-data" data-tag="row___ROWLINE___total_downtime_plan">0</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_1_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_1_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_1_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_1_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_1_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_1_total">&nbsp;</td>

        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_3_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_2_total">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_die_change">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_machine">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_human">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___total_runtime_good">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___total_runtime_good">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___total_runtime_good">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___total_runtime_good">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___total_runtime_good">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___total_runtime_good">&nbsp;</td>
    </tr>

    <tr class="dpr-content bg-gray-custom">
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___standard_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___actual_output_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_1_total_accumulate">&nbsp;</td>

        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_3_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___reject_2_total_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_die_change_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_machine_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_human_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="row___ROWLINE___total_downtime_unplan_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_1_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_2_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_3_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_4_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_5_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
        <td class="dpr-data" data-tag="line_6_row___ROWLINE___total_runtime_good_accumulate">&nbsp;</td>
    </tr>
</template>
@endsection