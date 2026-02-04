@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'work-center'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
@parent
<style>
    /* TODO: Responsive grid layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr 1fr 1fr 1fr;
        row-gap: 1em;
        column-gap: 1em;
    }

    .workcenter-form-cell {
        grid-column-start: 1;
        grid-column-end: 3;
        grid-row-start: 1;
        grid-row-end: 2;
    }

    .treshold-form-cell {
        grid-column-start: 3;
        grid-column-end: 5;
        grid-row-start: 1;
        grid-row-end: 2;
    }

    .path-form-cell {
        grid-column-start: 1;
        grid-column-end: 3;
        grid-row-start: 2;
        grid-row-end: 3;
    }

    .downtime-form-cell {
        grid-column-start: 3;
        grid-column-end: 5;
        grid-row-start: 2;
        grid-row-end: 3;
    }

    .layout-form-cell {
        grid-column-start: 1;
        grid-column-end: 5;
        grid-row-start: 3;
        grid-row-end: 4;
    }


    .submit-button-cell {
        grid-column-start: 2;
        grid-column-end: 3;
        grid-row-start: 5;
        grid-row-end: 6;
    }

    .form-label {
        font-weight: 500;
    }

    .btn-warna {
        background-color: #0000e4;
    }
    .btn-warna:hover {
        background-color: #000080;
    }
</style>
@endsection

@section('body')
<main>
    <div class="container pt-4">
        {{-- @include('components.web.change-plant-selector') --}}
        @yield('tab-nav-bar')
        <h5 class="secondary-text mt-4">NEW WORK CENTER</h5>
        <hr>
        <form method="post" action="{{ route('settings.work-center.store',[ $plant->uid]) }}" id="wc_form">
            @csrf

            <div class="row px-3">

                {{-- WORK CENTER INFORMATION --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>WORK CENTER INFORMATION</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label for="wc_factory_id" class="form-label">FACTORY <span class="text-danger">*</span></label>
                                    <select name="wc_factory_id" id="wc_factory_id" class="form-control" required>
                                        <option value="" disabled>Select Factory</option>
                                        @foreach($factories as $factory)
                                        <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('wc_factory_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="wc_name" class="form-label">WORK CENTER <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="wc_name" id="wc_name" placeholder="Work Center Name" value="{{ old('wc_name') }}" required>
                                    @error('wc_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="wc_code" class="form-label">CODE ID <span class="text-danger">*</span></label>
                                    <input type="text" name="wc_code" id="wc_code" class="form-control" placeholder="Code ID" value="{{ old('wc_code') }}" required>
                                    @error('wc_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="wc_line" class="form-label">NUMBER OF LINE <span class="text-danger">*</span></label>
                                    <select name="wc_line" id="wc_line" class="form-select" required>
                                        <option selected disabled>SELECT HOW MANY LINE</option>
                                        @foreach ( $dashboard_layouts as $layout )
                                            @if ( old('wc_line') != $layout->capacity )
                                                <option value="{{ $layout->capacity }}">{{ $layout->capacity }} Lines</option>
                                            @else
                                                <option value="{{ $layout->capacity }}" selected>{{ $layout->capacity }} Lines</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('wc_line')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- enabled active, inactive --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span class="text-danger">*</span></label>
                                    <select name="enabled" id="enabled" class="form-select" required>
                                        <option value="1" @if ( old('enabled')==1 ) selected @endif>Active</option>
                                        <option value="0" @if ( old('enabled')==0 ) selected @endif>Inactive</option>
                                    </select>
                                    @error('enabled')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="wc_break" class="form-label">BREAK SCHEDULE <span class="text-danger">*</span></label>
                                    <select name="wc_break" id="wc_break" class="form-select" required>
                                        <option value="" selected disabled>SELECT BREAK SCHEDULES</option>
                                        @foreach ( $break_schedules as $break_schedule )
                                            @if ( old('wc_break') != $break_schedule->id )
                                                <option value="{{ $break_schedule->id }}" @if(!$break_schedule->enabled) class='text-danger' @endif>{{ $break_schedule->name }}</option>
                                            @else
                                                <option value="{{ $break_schedule->id }}"  @if(!$break_schedule->enabled) class='text-danger' @endif selected>{{ $break_schedule->name }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                    @error('wc_break')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DASHBOARD LAYOUT --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>DASHBOARD LAYOUT</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                {{-- view image --}}
                                <img id="img-layout-preview" src="{{ asset('images/work_center/no-image.png') }}" alt="dashboard-layout" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- THRESHOLD SETTING --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>THRESHOLD SETTING</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="threshold_oee_target" class="form-label">OEE TARGET (%) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="100" class="form-control" name="threshold_oee_target" id="threshold_oee_target" placeholder="OEE TARGET PERCENTAGE" value="85" required>
                                    @error('threshold_oee_target')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="threshold_performance_target" class="form-label">PERFORMANCE TARGET (%) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="100" name="threshold_performance_target" id="threshold_performance_target" class="form-control" placeholder="PERFORMANCE TARGET PERCENTAGE" value="85" required>
                                    @error('threshold_performance_target')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="threshold_availability_target" class="form-label">AVAILABILITY TARGET (%) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="100" name="threshold_availability_target" id="threshold_availability_target" class="form-control" placeholder="AVAILABILITY TARGET PERCENTAGE" value="85" required>
                                    @error('threshold_availability_target')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="threshold_quality_target" class="form-label">QUALITY TARGET (%) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" max="100" name="threshold_quality_target" id="threshold_quality_target" class="form-control" placeholder="QUALITY TARGET PERCENTAGE" value="85" required>
                                    @error('threshold_quality_target')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BREAK CONFIGURATION? & PATH CONFIGURATION --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>PATH CONFIGURATION</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                {{-- GR OK File Path --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_gr_ok" class="form-label">GR OK FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_gr_ok" id="path_gr_ok" placeholder="Write CSV File path" value="{{ old('path_gr_ok') }}" required>
                                    @error('path_gr_ok')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- GR NG File Path --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_gr_ng" class="form-label">GR NG FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_gr_ng" id="path_gr_ng" placeholder="Write CSV File path" value="{{ old('path_gr_ng') }}" required>
                                    @error('path_gr_ng')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- ETT10 File Path --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_ett10" class="form-label">ETT10 FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_ett10" id="path_ett10" placeholder="Write CSV File path" value="{{ old('path_ett10') }}" required>
                                    @error('path_ett10')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- ETT20 File Path --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_ett20" class="form-label">ETT20 FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_ett20" id="path_ett20" placeholder="Write CSV File path" value="{{ old('path_ett20') }}" required>
                                    @error('path_ett20')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- GR QI --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_gr_qi" class="form-label">GR QI FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_gr_qi" id="path_gr_qi" placeholder="Write CSV File path" value="{{ old('path_gr_qi') }}" required>
                                    @error('path_gr_qi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- PPS File Path --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_pps" class="form-label">PPS FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_pps" id="path_pps" placeholder="Write CSV File path" value="{{ old('path_pps') }}" required>
                                    @error('path_pps')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- RW OK --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_rw_ok" class="form-label">RW OK FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_rw_ok" id="path_rw_ok" placeholder="Write CSV File path" value="{{ old('path_rw_ok') }}" required>
                                    @error('path_rw_ok')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- RW NG --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="path_rw_ng" class="form-label">RW NG FILE PATH <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="path_rw_ng" id="path_rw_ng" placeholder="Write CSV File path" value="{{ old('path_rw_ng') }}" required>
                                    @error('path_rw_ng')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- DOWNTIME --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>DOWNTIME</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                {{-- MACHINE TOOLING --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <div class="p-2">
                                        <h6 style="border-bottom: 2px solid #575353">MACHINE / TOOLING <span class="text-danger">*</span></h6>
                                        <div class="row form-check">
                                            @foreach ($downtime_tools as $downtime_tool)
                                                @if ($downtime_tool->enabled == 1)
                                                    <div class="col-12">
                                                        <input class="form-check-input check-downtime check-downtime-machine" type="checkbox" value="{{ $downtime_tool->id }}" id="downtime_{{ $downtime_tool->id }}" data-name="{{ $downtime_tool->category }}"><label class="form-check-label" for="downtime_{{ $downtime_tool->id }}">{{ $downtime_tool->category }}</label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mt-4">
                                    <div class="p-2">
                                        <h6 style="border-bottom: 2px solid #575353">HUMAN DOWNTIME <span class="text-danger">*</span></h6>
                                        <div class="row form-check">
                                            @foreach ($downtime_humans as $downtime_human)
                                                @if ($downtime_human->enabled == 1)
                                                    <div class="col-12">
                                                        <input class="form-check-input check-downtime check-downtime-human" type="checkbox" value="{{ $downtime_human->id }}" id="downtime_{{ $downtime_human->id }}"><label class="form-check-label" for="downtime_{{ $downtime_human->id }}">{{ $downtime_human->category }}</label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OPC TAG --}}
                <div class="p-2 col-12 col-md-6 opc-tag">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>OPC TAG CONFIGURATION</h5>
                        </div>
                        <div class="card-body p-0">
                            {{-- select input for 'die change tag' and 'break tag' --}}
                            <div class="row">
                                <div class="col-12 col-md-6 my-2">
                                    <label for="die_change_tag" class="form-label text-decoration-underline">DIE CHANGE TAG <span class="text-danger">*</span></label>
                                    {{-- select foreach '$opc_tags' --}}
                                    <select class="form-control" name="die_change_tag" id="die_change_tag" required>
                                        <option value="0" selected disabled>No OPC Tag</option>
                                        @foreach ($opc_tags as $opc_tag)
                                            @if (old('die_change_tag') == $opc_tag->id)
                                                <option value="{{ $opc_tag->id }}" selected>{{ $opc_tag->tag }}</option>
                                            @else
                                                <option value="{{ $opc_tag->id }}">{{ $opc_tag->tag }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('die_change_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 my-2">
                                    <label for="break_tag" class="form-label text-decoration-underline">BREAK TAG <span class="text-danger">*</span></label>
                                    {{-- select foreach '$opc_tags' --}}
                                    <select class="form-control" name="break_tag" id="break_tag" required>
                                        <option value="0" selected disabled>No OPC Tag</option>
                                        @foreach ($opc_tags as $opc_tag)
                                            @if (old('break_tag') == $opc_tag->id)
                                                <option value="{{ $opc_tag->id }}" selected>{{ $opc_tag->tag }}</option>
                                            @else
                                                <option value="{{ $opc_tag->id }}">{{ $opc_tag->tag }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('break_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 my-2">
                                    <label for="human_downtime_tag" class="form-label text-decoration-underline">HUMAN DOWNTIME TAG<span class="text-danger">*</span></label>
                                    {{-- select foreach '$opc_tags' --}}
                                    <select class="form-control" name="human_downtime_tag" id="human_downtime_tag" required>
                                        <option value="0" selected disabled>No OPC Tag</option>
                                        @foreach ($opc_tags as $opc_tag)
                                            @if (old('human_downtime_tag') == $opc_tag->id)
                                                <option value="{{ $opc_tag->id }}" selected>{{ $opc_tag->tag }}</option>
                                            @else
                                                <option value="{{ $opc_tag->id }}">{{ $opc_tag->tag }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('human_downtime_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 my-2">
                                    <label for="on_production_tag" class="form-label text-decoration-underline">ON PRODUCTION TAG<span class="text-danger">*</span></label>
                                    {{-- select foreach '$opc_tags' --}}
                                    <select class="form-control" name="on_production_tag" id="on_production_tag" required>
                                        <option value="0" selected disabled>No OPC Tag</option>
                                        @foreach ($opc_tags as $opc_tag)
                                            @if (old('on_production_tag') == $opc_tag->id)
                                                <option value="{{ $opc_tag->id }}" selected>{{ $opc_tag->tag }}</option>
                                            @else
                                                <option value="{{ $opc_tag->id }}">{{ $opc_tag->tag }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('on_production_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-md-6 my-2">
                                    {{-- part number tag --}}
                                    <label for="part_number_tag" class="form-label text-decoration-underline">PART NUMBER TAG <span class="text-danger">*</span></label>
                                    <input type="hidden" name="selectedPartNumbers" id="selectedPartNumbers" value="{{ old('selectedPartNumbers') }}">
                                    <div class="part_number_tag"><h6 class="text-danger" id="no-item-part-number">No Line Selected</h6></div>
                                    @error('part_number_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 my-2">
                                    {{-- count up tag --}}
                                    <label for="count_up_tag" class="form-label text-decoration-underline">COUNT UP TAG <span class="text-danger">*</span></label>
                                    <input type="hidden" name="selectedCountUps" id="selectedCountUps" value="{{ old('selectedCountUps') }}">
                                    <div class="count_up_tag"><h6 class="text-danger" id="no-item-count-up">No Line Selected</h6></div>
                                    @error('count_up_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <hr>
                                <div class="col-12 mt-2">
                                    <label for="count_up_tag" class="form-label text-decoration-underline">DOWNTIME TAG <span class="text-danger">*</span></label>
                                    <input type="hidden" name="selectedDowntimes" id="selectedDowntimes" value="{{ old('selectedDowntimes') }}">
                                    <div class="row" id="opc_selection_box">
                                        {{-- No Item Selected --}}
                                        <div class="col-12 col-md-6 mt-2">
                                            <div id="error-opc">
                                                @error('opc_tag_error')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <h6 class="text-danger" id="no-item-opc">No Downtime Selected</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BUTTON --}}
                <div class="col-12 my-2">
                    <div class=" text-end">
                        <a href="{{ route('settings.work-center.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                        <button type="submit" onclick="generate_json()" class="btn btn-action btn-warna">SUBMIT</button>
                    </div>
                </div>

            </div>

        </form>
    </div>
</main>
@endsection

@section('scripts')
@parent
<script>
    var opc_tags = <?php echo json_encode($opc_tags->toArray()); ?>;

    //TODO: Guard variable
    $(() => {
        page.initialize();

    });


    var page = {
        _token: "{{ csrf_token() }}",

        initialize: function() {
            return this;
        },
    }

    //function. text from work center name to lowercase and change spaces to '-' , ignore special character
    var element = document.getElementById('wc_name');
    element.addEventListener('input', function() {
        var text = this.value;
        text = text.toLowerCase();
        text = text.replace(/\s/g, '-');
        text = text.replace(/[^a-z0-9-]/g, '');
        document.getElementById('wc_code').value = text;
    });

    function appendOpcTagOptions(element,lineRow,oldValues)
        {
            //oldValues [{ tag_id: "15", line_row: 1 },...]
            opc_tags.forEach(e =>{
                let selected = false;
                oldValues.forEach(old =>{
                    if(e.id == old.tag_id && old.line_row == lineRow){
                        selected = true;
                    }
                });
                if(selected)
                    element.append(`<option value="${e.id}" selected>${e.tag}</option>`);
                else
                    element.append(`<option value="${e.id}">${e.tag}</option>`);
            });
        }

    //on load 
    $(document).ready(function() {
        var wc_line = $('#wc_line').val();

        if (wc_line == null) {
            $('#img-layout-preview').attr('src', `{{ asset("images/work_center/") }}/no-image.png`);
        } else {
            $('#img-layout-preview').attr('src', `{{ asset("images/work_center/") }}/layout-preview-${wc_line}.png`);

            $('.part_number_tag').html('');
            $('.count_up_tag').html('');

            let oldPartTag = $('#selectedPartNumbers').val();
            var part_tag = [];
            if(oldPartTag)
                part_tag = JSON.parse(oldPartTag);

            let oldCountUpTag = $('#selectedCountUps').val();
            var countUpTag = [];
            if(oldCountUpTag)
                countUpTag = JSON.parse(oldCountUpTag);

            for(var i = 0; i < wc_line; i++){
                let selectElementPartNumber = $(`<select class="form-control mb-2 part-number-selected" name="part_number_tag_${i}" id="part_number_tag_${i}" required>
                            <option value="0" selected disabled>No OPC Tag</option></select>`);
                            appendOpcTagOptions(selectElementPartNumber,i+1,part_tag);
                $('.part_number_tag').append($(`<div class="form-group"></div>`).append(selectElementPartNumber));

                    let selectElementCountUp = $(`<select class="form-control mb-2 count-up-selected" name="count_up_tag_${i}" id="count_up_tag_${i}" required>
                            <option value="0" selected disabled>No OPC Tag</option></select>`);
                            appendOpcTagOptions(selectElementCountUp,i+1,countUpTag);
                $('.count_up_tag').append($(`<div class="form-group"></div>`).append(selectElementCountUp));
            };
        }
    });

    //on wc_line change, get value from wc_line and set img-layout-preview src to wc_line_image
    $('#wc_line').change(function() {
        var wc_line = $('#wc_line').val();
        $('#img-layout-preview').attr('src', `{{ asset("images/work_center/") }}/layout-preview-${wc_line}.png`);

        $('.part_number_tag').html('');
        $('.count_up_tag').html('');

        let oldPartTag = $('#selectedPartNumbers').val();
        var part_tag = [];
        if(oldPartTag)
            part_tag = JSON.parse(oldPartTag);

        let oldCountUpTag = $('#selectedCountUps').val();
        var countUpTag = [];
        if(oldCountUpTag)
            countUpTag = JSON.parse(oldCountUpTag);

        for(var i = 0; i < wc_line; i++){
            let selectElementPartNumber = $(`<select class="form-control mb-2 part-number-selected" name="part_number_tag_${i}" id="part_number_tag_${i}" required>
                        <option value="0" selected disabled>No OPC Tag</option></select>`);
                        appendOpcTagOptions(selectElementPartNumber,i+1,part_tag);
            $('.part_number_tag').append($(`<div class="form-group"></div>`).append(selectElementPartNumber));

                let selectElementCountUp = $(`<select class="form-control mb-2 count-up-selected" name="count_up_tag_${i}" id="count_up_tag_${i}" required>
                        <option value="0" selected disabled>No OPC Ta</option></select>`);
                        appendOpcTagOptions(selectElementCountUp,i+1,countUpTag);
            $('.count_up_tag').append($(`<div class="form-group"></div>`).append(selectElementCountUp));
        };
    });


    function getActiveTagIdByTag(serverId, tag) {
        var opc_tags = <?php echo json_encode($opc_tags->toArray());?>;
        let id = null;
        opc_tags.forEach(opcTag => {
            if (opcTag.id == serverId) {
                id = opcTag.id;
            }
        });

        return id;
    }

    function getWorkcenterDowntime(downtimeId) {

        var downtime_selected = <?php echo json_encode(old('selectedDowntimes'));?>;
        let result = null;

        // downtime_selected json to toArray
        downtime_selected = JSON.parse(downtime_selected);

        if (downtime_selected != null) {
            downtime_selected.forEach(workCenterDowntime => {
                if (workCenterDowntime.downtime_id == downtimeId)
                    result = workCenterDowntime;
            });
        }

        return result;
    }

    //call this function on change or reload
    function get_checked_item(event) {
        var count = $('input.check-downtime-machine:checked').length;
        if (count == 0) {
            $('#no-item-opc').show();
            $('#error-opc').hide();
        }
        if (count > 0) {
            $('#no-item-opc').hide();
            $('#error-opc').show();
        }

        if (this.checked) {

            //create select box in opc_selection_box id 
            var opc_selection_box = document.getElementById('opc_selection_box');

            //create div with class mt-2
            var div = document.createElement('div');
            div.setAttribute('class', 'col-12 col-md-12 mt-2');
            div.setAttribute('id', 'div_box_' + $(this).val());
            opc_selection_box.appendChild(div);

            var label = document.createElement('label');
            label.setAttribute('class', 'form-control');
            label.setAttribute('for', 'opc_tag_' + $(this).val());
            var label_text = document.createTextNode($(this).data('name'));
            div.appendChild(label_text);

            var select_box = document.createElement('select');
            select_box.setAttribute('class', 'form-control mb-2 ');
            select_box.setAttribute('id', 'opc_tag_' + $(this).val());
            select_box.setAttribute('data-id', $(this).val());
            select_box.setAttribute('placeholder', 'OPC TAG');
            select_box.setAttribute('required', 'required');
            div.appendChild(select_box);

            //create option for selection box with for each data from {{ $opc_tags }}
            var opc_tags = <?php echo json_encode($opc_tags->toArray()); ?>;

            var option = document.createElement('option');
            option.setAttribute('value', '0');
            option.setAttribute('selected', 'selected');
            // option.setAttribute('disabled', 'disabled');
            option.innerHTML = 'No OPC Tag';
            select_box.appendChild(option);

            opc_tags.forEach(function(opc_tag) {
                var option = document.createElement('option');
                option.setAttribute('value', opc_tag.id);
                //option.setAttribute('selected', 'selected');
                option.innerHTML = opc_tag.tag;
                select_box.appendChild(option);
            });

            let workCenterDowntime = getWorkcenterDowntime($(this).val());
            if (workCenterDowntime) {
                let selectedOpcId = getActiveTagIdByTag(workCenterDowntime.opc_tag_id);
                if (selectedOpcId)
                    $(select_box).val(selectedOpcId);
            }
        } else {
            //remove div with id div_box_$(this).val()
            var div = document.getElementById('div_box_' + $(this).val());
            div.remove();
        }
    }

    $(document).ready(function() {
        var downtime_selected = <?php echo json_encode(old('selectedDowntimes')); ?>;

        // downtime_selected json to toArray
        downtime_selected = JSON.parse(downtime_selected);

        // downtime_selected to toArray

        if (downtime_selected != null) {
            console.log(downtime_selected);
            downtime_selected.forEach(function(downtime) {
                $('#downtime_' + downtime.downtime_id).prop('checked', true);
            });
        }

        //for each checked "downtime_machine_[]" , call get_checked_item function
        $('input.check-downtime-machine:checked').each(function() {
            get_checked_item.call(this, event);
        });

        $('input.check-downtime-machine').change(function() {
            get_checked_item.call(this, event);
        });
    });

    //submit function
    function generate_json() {
        //var opc_tag_json = [];
        var selectDowntimes = [];
        $('.check-downtime-machine').each((index, element) => {
            if ($(element).is(':checked')) {
                var downtimeId = $(element).val();
                var opcTagId = $('#opc_tag_' + downtimeId).val();

                let selectDowntime = {
                    downtime_id: downtimeId,
                    opc_tag_id: opcTagId
                }
                selectDowntimes.push(selectDowntime);
            }
        });

        $('.check-downtime-human').each((index, element) => {
            if ($(element).is(':checked')) {
                var downtimeId = $(element).val();
                var opcTagId = null;

                let selectDowntime = {
                    downtime_id: downtimeId,
                    opc_tag_id: opcTagId
                }
                selectDowntimes.push(selectDowntime);
            }
        });
        $('#selectedDowntimes').val(JSON.stringify(selectDowntimes));

        var selectedPartNumber = [];
        $('.part-number-selected').each((index, element) => {
            console.log('selected val:',element.value,' info:',index);

            let select_part_data ={
                tag_id: element.value,
                line_row: index+1,
            }

            selectedPartNumber.push(select_part_data);
        });
        $('#selectedPartNumbers').val(JSON.stringify(selectedPartNumber));

        var selectedCountUp = [];
        $('.count-up-selected').each((index, element) => {
            
            let select_count_up_data ={
                tag_id: element.value,
                line_row: index+1,
            }

            selectedCountUp.push(select_count_up_data);
        });
        $('#selectedCountUps').val(JSON.stringify(selectedCountUp));

        return false;
    }


// // on submit stop form submit
//     $('#wc_form').submit(function(e) {
//         e.preventDefault();
//         // generate_json();
//         this.submit();
//     });
</script>
@endsection