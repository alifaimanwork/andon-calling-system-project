@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'break-schedules'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
        .form-label {
            font-weight: 500;
        }

        .btn-addrow {
            background-color: #E3E3F1;
            color: #000000;
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
        @yield('mobile-title')
        {{-- @include('components.web.change-plant-selector') --}}
        @yield('tab-nav-bar')
        <h5 class="secondary-text mt-4" style="text-transform: uppercase">ADD NEW BREAK SCHEDULES</h5>
        <hr>
        <form method="post" action="{{ route('settings.break-schedule.store',[ $plant->uid ]) }}">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6 mt-3">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label for="name" class="form-label">SCHEDULE NAME <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="Schedule Name" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-12 mt-3">
                                    <label for="enabled" class="form-label">STATUS <span class="text-danger">*</span></label>
                                    <div class="m-2 my-0 d-flex gap-3">
                                        <div class="form-check">
                                            <input type="radio" name="enabled" id="enabled" value="1" class="form-check-input" checked>
                                            <label for="enabled" class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="enabled" id="enabled" value="0" class="form-check-input">
                                            <label for="enabled" class="form-check-label">Inactive</label>
                                        </div>
                                    </div>
                                    @error('enabled')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <input type="hidden" name="schedule_data" id="schedule_data">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="1">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">MONDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="1-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="1" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="2">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">TUESDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="2-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="2" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="3">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">WEDNESDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="3-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="3" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="4">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">THURSDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="4-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="4" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="5">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">FRIDAY</label>                       
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="5-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="5" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="6">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">SATURDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="6-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="6" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3 data-schedule-cell" data-dow="7">
                    <!-- Data Form -->
                    <div class="card h-100">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-12 col-md-12 mt-3">
                                    <label class="form-label">SUNDAY</label>                        
                                </div>
                            </div>
                            {{-- table add start and end time --}}
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="7-content">
                                        <tr id="none">
                                            <td colspan="4" class="text-center">No data available in table</td>                                                  
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="form-label text-danger error-msg" id="error-msg"></label>
                            {{-- add new row button --}}
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm btn-addrow" id="7" onclick="add_row(this);" ><i class="fa fa-plus"></i> Add Row</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('settings.break-schedule.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                    <button type="submit" class="btn btn-action btn-warna" id="submit_data">SUBMIT</button>
                </div>
            </div>
        </form>
    </div>


</main>
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/jquery-clock-timepicker.js') }}"></script>
<script>
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

    function validateSchedule()
    {
        let valid = true;

        var schedules = {
            'data': {
                '1': [],
                '2': [],
                '3': [],
                '4': [],
                '5': [],
                '6': [],
                '7': [],
            }
        };
        $('.data-schedule-cell').each((idxSchedule,scheduleElement) => {
            let schedule = [];
            let dow = $(scheduleElement).data('dow');

            let tbody = $(scheduleElement).find('tbody');
            if(!tbody)
                return;

            let errorCodes = {};
            $(tbody).find('tr').each((idxRow,rowElement) => {
                let noElement = $(rowElement).find('td:nth-child(1)');
                let startElement = $(rowElement).find('td:nth-child(2) input');
                let endElement = $(rowElement).find('td:nth-child(3) input');
                if(!startElement[0] || !endElement[0])
                    return;

                let no = $(noElement[0]).html();
                
                let validateResult = validateStartEnd(startElement,endElement);
                let errorCode = validateResult[0];
                if(!errorCode)
                {
                    
                    let rowData = {
                    'table_id': dow,
                    'row_id': no,
                    'start_time': validateResult[1][0],
                    'end_time': validateResult[1][1],
                    };
                    schedule.push(rowData);
                }
                errorCodes[no] = errorCode;
                
                if(errorCode)
                {
                    $(rowElement).addClass('bg-danger');
                }
                else
                {
                    $(rowElement).removeClass('bg-danger');
                }


            });


            let errorMsg = '';
            Object.entries(errorCodes).forEach(([no,errorCode]) => {
                if(errorCode)
                {
                    errorMsg += `No ${no} : ${getErrorMessage(errorCode)}<br>`;
                }
            });
            if(errorMsg)
            {
                $(scheduleElement).find('.error-msg').html(errorMsg);
                valid = false;
            }
            else
            {
                $(scheduleElement).find('.error-msg').html('');
            }

            schedules['data'][dow] = schedule;
        });

        
        return [valid,schedules];
    }

    function getErrorMessage(errorCode)
    {
        switch(errorCode)
        {
            case -1:
                return 'Required';
            case -2:
                return 'Start time must be less than end time';
            default:
                return '';
        }
    }
    function validateStartEnd(startElement,endElement)
    {
        let startTime = startElement.val();
        let endTime = endElement.val();
        //Logic to validate start time and end time

        if(!startTime || !endTime)
            return [-1, null]; //Required
            
        if(startTime == "24:00"){
            startTime = "00:00";
        }
        if(endTime == "00:00"){
            endTime = "24:00";
        }

        //compare end time with start time
        if(startTime > endTime || startTime == endTime)
        {
            return [-2, null]; //End time must be greater than start time
        }

        return [0, [startTime,endTime]];
    }

    function add_row(e){
        //get table id
        var table_id = $(e).attr('id');
        
        var test_val = validateSchedule();
        if(test_val[0]== true || row_id == 0){
            $(e).parent().parent().parent().find('#none').remove();
        }

        //get row count
        var row_id = $('#'+table_id+'-content tr').length + 1;
        var row_html = '<tr id="' + row_id + '" >' +
            '<td>' + row_id + '</td>' +
            '<td><input type="text" id="start_time"  class="form-control clock-picker" value="00:00" required></td>' +
            '<td><input type="text" id="end_time"  class="form-control clock-picker" value="00:00" required></td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" id="'+table_id+'" onclick="remove_row(this);"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';

        if(test_val[0]== true || row_id == 0){
            $('#'+table_id+'-content').append(row_html);
        }
        else{
            validateSchedule();
        }
        $('.clock-picker').clockTimePicker({required:true});
    }

    function remove_row(e){
        //delete row from table
        $(e).parent().parent().remove();

        //if row is empty, add none row
        var table_id = $(e).attr('id');
        if($('#'+table_id+'-content tr').length == 0){
            var row_html = '<tr id="none">' +
                '<td colspan="4" class="text-center">No data available in table</td>' +
                '</tr>';
            $('#'+table_id+'-content').append(row_html);
        }
        validateSchedule();
    }


    //submit json data
    $('#submit_data').click(function(e){
        let check_valid = validateSchedule();

        if(check_valid[0]){
            let json_data = JSON.stringify(check_valid[1]);
            $('#schedule_data').val(json_data);
            $(this).submit();
            
        }else{
            e.preventDefault();
        }
    });

//on load page, check if any table has row bg-danger
$(document).ready(function(){
    var schedule_data = {!! json_encode(old('schedule_data')) !!} ;

    console.log(schedule_data);
    if(schedule_data != null){
        //from json to array
        var schedule_data = JSON.parse(schedule_data);

        //foreach schedule data
        var old_data = Object.values(schedule_data['data']);

        old_data.forEach(function(data){
            data.forEach(function(data_row , index){
                //add row to table
                var row_id = index + 1;
                var start_time = data_row['start_time'];
                var end_time = data_row['end_time'];
                var table_id = data_row['table_id'];


                //remove none row from table
                $('#'+table_id+'-content tr#none').remove();

                //add new row to table
                var row_html = '<tr id="' + row_id + '" >' +
                    '<td>' + row_id + '</td>' +
                    '<td><input type="text" id="start_time"  class="form-control clock-picker" value="'+start_time+'"></td>' +
                    '<td><input type="text" id="end_time"  class="form-control clock-picker"  value="'+end_time+'"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" id="'+table_id+'" onclick="remove_row(this);"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                $('#'+table_id+'-content').append(row_html);

            })
        });
    }

    $('.clock-picker').clockTimePicker({required:true});
});

</script>
@endsection