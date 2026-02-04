@extends('layouts.guest')

@section('body')
<div class="container">
    <div class="row">
        <div class="col-12 overflow-auto mt-5">
            <table id="dt2" class="table nowrap table-hover mt-3 text-wrap" style="font-size:70%">
                <thead>
                    <tr class="text-wrap" style="background-color: #cb84a2; color: white;">
                        <th>NO</th>
                        <th class="text-wrap" style="width: 230px;">FILENAME</th>
                        <th>SEC</th>
                        <th>STATUS</th>
                        <th>PLANT</th>
                        <th>FACTORY</th>
                        <th>LINE</th>
                        <th>ORDER NUMBER</th>
                        <th>PART NUMBER</th>
                        <th>PART NAME</th>
                        <th>PLAN START DATETIME</th>
                        <th>PLAN FINISH DATETIME</th>
                        <th>SHIFT</th>
                        <th class="text-wrap" style="width: 50px;">PLAN QUANTITY</th>
                        <th>UOM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n = 0; ?>
                    @foreach ($data as $key => $value)
                        @if($value != null)
                            <?php $n++; ?>
                            <tr>
                                <td>{{ $n }}</td>
                                <td>{{ $value->filename }}</td>
                                <td>{{ $value->seq }}</td>
                                <td>{{ $value->status }}</td>
                                <td>{{ $value->plant }}</td>
                                <td>{{ $value->factory }}</td>
                                <td>{{ $value->line }}</td>
                                <td>{{ $value->order_no }}</td>
                                <td>{{ $value->part_number }}</td>
                                <td>{{ $value->part_name }}</td>
                                <td>{{ $value->plan_start_datetime }}</td>
                                <td>{{ $value->plan_finish_datetime }}</td>
                                <td>{{ $value->shift }}</td>
                                <td>{{ $value->plan_quantity }}</td>
                                <td>{{ $value->uom }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
