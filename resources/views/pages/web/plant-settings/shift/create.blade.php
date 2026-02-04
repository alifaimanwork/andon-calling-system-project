@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'shift'])
@section('head')
@parent

    <style>
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
    <div class="container py-3">
        @yield('mobile-title')
        @include('components.web.change-plant-selector')
        @yield('tab-nav-bar')
        
        <h5 class="secondary-text mt-4">CHANGE SHIFT</h5>
        <hr>

        {{-- WEEK table for shift --}}
        <div class="row">
            <div class="col-md-12">
                @error('error')
                    <div class="my-2 alert alert-danger">{{ $message }}</div>
                @enderror
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mt-3">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="white-space: nowrap; width: 1%;">Day of Week</th>
                                        <th class="text-center" colspan="4"><i class="fa fa-sun"></i> Day Shift</th>
                                        <th class="text-center" colspan="4"><i class="fa fa-moon"></i>Night Shift</th>
                                    </tr>
                                    <tr>
                                        <th style="white-space: nowrap; width: 1%;">Enable</th>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">Over Time</th>
                                        <th class="text-center">End Time</th>
                                        {{-- <th class="text-center">Duration (Min.)</th> --}}
                                        <th style="white-space: nowrap; width: 1%;">Enable</th>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">Over Time</th>
                                        <th class="text-center">End Time</th>
                                        {{-- <th class="text-center">Duration (Min.)</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="1">
                                        <td>Monday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="2">
                                        <td>Tuesday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="3">
                                        <td>Wednesday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="4">
                                        <td>Thursday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="5">
                                        <td>Friday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="6">
                                        <td>Saturday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                    <tr id="7">
                                        <td>Sunday</td>
                                        <td id="day_enabled" class="form-control-lg text-center"></td>
                                        <td id="day_start"></td>
                                        <td id="day_over"></td>
                                        <td id="day_end"></td>
                                        {{-- <td id="day_duration"></td> --}}
                                        
                                        <td id="night_enabled" class="form-control-lg text-center"></td>
                                        <td id="night_start"></td>
                                        <td id="night_over"></td>
                                        <td id="night_end"></td>
                                        {{-- <td id="night_duration"></td> --}}
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.shift.update',[ $plant->uid ]) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="shift_data" id="shift_data" value="">
                    <div class="text-end mt-2">
                        <a href="{{ route('settings.shift.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                        <button type="submit" id="update" class="btn btn-action btn-warna">UPDATE</button>
                    </div>
                </form>


            </div>
        </div>

    </div>
</main>
@endsection

@section('modals')
@parent
<div>

</div>
@endsection
@section('scripts')
@parent
<script src="{{ asset('js/jquery-clock-timepicker.js') }}"></script>
<script>

    $(document).ready(function(){
        var shifts = {!! json_encode($shifts->toArray()) !!};

        shifts.forEach(function(shift){
            if(shift.shift_type.name == 'Day'){
                if(shift.enabled){
                    //checkbox is checked
                    $('#'+shift.day_of_week+' #day_enabled').html('<input class="form-check-input" onchange="checkbox_change(this);" type="checkbox" checked>');

                    //day_start time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_start').html('<input type="text"  class="form-control clock-picker" value="'+shift.start_time.slice(0,-3)+'" required>');

                    //day_over time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_over').html('<input type="text"  class="form-control clock-picker" value="'+shift.over_time.slice(0,-3)+'" required>');

                    //day_end time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_end').html('<input type="text"  class="form-control clock-picker" value="'+shift.end_time.slice(0,-3)+'" required>');

                    //duration day night
                    // $('#'+shift.day_of_week+' #day_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');
                    // $('#'+shift.day_of_week+' #night_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');

                    

                }else{
                    //red icon disabled
                    $('#'+shift.day_of_week+' #day_enabled').html('<input class="form-check-input" onchange="checkbox_change(this);" type="checkbox">');

                    //day_start time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_start').html('<input type="text"  class="form-control clock-picker" value="'+shift.start_time.slice(0,-3)+'" required>');

                    //day_over time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_over').html('<input type="text"  class="form-control clock-picker" value="'+shift.over_time.slice(0,-3)+'" required>');

                    //day_end time spinner hours and minutes
                    $('#'+shift.day_of_week+' #day_end').html('<input type="text"  class="form-control clock-picker" value="'+shift.end_time.slice(0,-3)+'" required>');

                    //duration day night
                    // $('#'+shift.day_of_week+' #day_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');
                    // $('#'+shift.day_of_week+' #night_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');

                }
                // $('#'+shift.day_of_week+' #day_start').html(shift.start_time.slice(0,-3));
                // $('#'+shift.day_of_week+' #day_end').html(shift.end_time.slice(0,-3));
            }
            else{
                if(shift.enabled){
                    ///checkbox is checked
                    $('#'+shift.day_of_week+' #night_enabled').html('<input class="form-check-input" onchange="checkbox_change(this);" type="checkbox" checked>');

                    //night_start time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_start').html('<input type="text"  class="form-control clock-picker" value="'+shift.start_time.slice(0,-3)+'" required>');

                    //night_over time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_over').html('<input type="text"  class="form-control clock-picker" value="'+shift.over_time.slice(0,-3)+'" required>');

                    //night_end time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_end').html('<input type="text"  class="form-control clock-picker" value="'+shift.end_time.slice(0,-3)+'" required>');

                    //duration day night
                    // $('#'+shift.day_of_week+' #night_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');
                    // $('#'+shift.day_of_week+' #day_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');

                }else{
                    //red icon disabled
                    $('#'+shift.day_of_week+' #night_enabled').html('<input class="form-check-input" onchange="checkbox_change(this);" type="checkbox">');

                    //night_start time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_start').html('<input type="text"  class="form-control clock-picker" value="'+shift.start_time.slice(0,-3)+'" required>');

                    //night_over time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_over').html('<input type="text"  class="form-control clock-picker" value="'+shift.over_time.slice(0,-3)+'" required>');

                    //night_end time spinner hours and minutes
                    $('#'+shift.day_of_week+' #night_end').html('<input type="text"  class="form-control clock-picker" value="'+shift.end_time.slice(0,-3)+'" required>');

                    //duration day night
                    // $('#'+shift.day_of_week+' #night_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');
                    // $('#'+shift.day_of_week+' #day_duration').html('<div class="text-center"><label >'+shift.duration/60+'</label></div>');

                }
                // $('#'+shift.day_of_week+' #night_start').html(shift.start_time.slice(0,-3));
                // $('#'+shift.day_of_week+' #night_end').html(shift.end_time.slice(0,-3));
            }
        });

        //find all inputs and set clock picker
        $('.clock-picker').clockTimePicker({required:true});

    });


    // function checkbox_change(value){
    //     //get parent id
    //     var day_of_week = $(value).parent().parent().attr('id');
    //     var shift_type = $(value).parent().attr('id');
    //     //split shift_type _enabled 
    //     var shift_type_split = shift_type.split('_');
    //     shift_type = shift_type_split[0];

    //     if(value.checked){


    //         //enable time spinner start
    //         $('#'+day_of_week+' #'+shift_type+'_start :input').prop('disabled', false);
    //         // $('#'+day_of_week+' #'+shift_type+'_start').prop('disabled', false);

    //         //enable time spinner over
    //         $('#'+day_of_week+' #'+shift_type+'_over :input').prop('disabled', false);
    //         // $('#'+day_of_week+' #'+shift_type+'_over').prop('disabled', false);

    //         //enable time spinner end
    //         $('#'+day_of_week+' #'+shift_type+'_end :input').prop('disabled', false);
    //         // $('#'+day_of_week+' #'+shift_type+'_end').prop('disabled', false);
    //     }
    //     else{

    //         //disable time spinner start
    //         $('#'+day_of_week+' #'+shift_type+'_start :input').prop('disabled', true);
    //         // $('#'+day_of_week+' #'+shift_type+'_start #minutes').prop('disabled', true);

    //         //disable time spinner over
    //         $('#'+day_of_week+' #'+shift_type+'_over :input').prop('disabled', true);
    //         // $('#'+day_of_week+' #'+shift_type+'_over #minutes').prop('disabled', true);

    //         //disable time spinner end
    //         $('#'+day_of_week+' #'+shift_type+'_end :input').prop('disabled', true);
    //         // $('#'+day_of_week+' #'+shift_type+'_end #minutes').prop('disabled', true);
    //     }
    // }


    function validateShift(){
        var checkboxes = $('input[type=checkbox]');
        var checkbox_values = [];

        error = 0;

        checkboxes.each(function(){

            let data_value ={
                day_of_week: $(this).parent().parent().attr('id'),
                shift_type: $(this).parent().attr('id').split('_')[0],
                start_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_start :input').val(),
                over_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_over :input').val(),
                end_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_end :input').val(),
                enabled: $(this).is(':checked')
            }
            
            checkbox_values.push(data_value);

            if(data_value['start_time'] == '' || data_value['end_time'] == ''){
                console.log('empty');
                error = -1; //empty shift

                return false;
            }
            
            });

        //check if shift is overlapping
        if(error == 0){
            var shift_overlapping = check_overlapping(checkbox_values);
            if(shift_overlapping == true){
                error = -2; //overlapping shift
            }
        }

        if(error == 0 && checkbox_values.length > 0){
            //save shift
            
            
        }
        else{
            if(error == -1){
                alert('Please fill in all shift times');
            }
            else if(error == -2){
                alert('Shift times are overlapping');
            }else{
                alert('Please select at least one shift');
            }
        }

        if (error != 0){
            validate_result = false;
        }else{
            validate_result = true;
        }

        return [validate_result, checkbox_values, error];
    }

    function check_overlapping(checkbox_values){


        // var shift_overlapping = false;
        // var shift_overlapping_index = [];

        // checkbox_values.forEach(function(shift){

        //     console.log(shift);
            
        //     if(shift['shift_type'] == 'day'){
        //         var start_time_minutes = shift['start_time'];
        //         var end_time_minutes = shift['end_time'];
        //     }
        //     else{
        //         var start_time_minutes = shift['start_time'];
        //         var end_time_minutes = shift['end_time'];
        //     }
            
        // });
        
        //TODO: check if shift is overlapping FRONTEND
        
        return true;
        //return shift_overlapping;
    }

    function convert_time_to_minutes(time){
        var time_split = time.split(':');
        var time_minutes = parseInt(time_split[0])*60 + parseInt(time_split[1]);
        return time_minutes;
    }

    //submit as JSON
    $('#update').click(function(e){

        //let check_valid = validateShift();
        let check_valid = [true, 'hello'];

        console.log('update' , check_valid[1], check_valid[2]);

        if(check_valid[0]){
            //get all checkbox values
            var checkboxes = $('input[type=checkbox]');
            var checkbox_values = [];
            checkboxes.each(function(){

                let data_value ={
                    day_of_week: $(this).parent().parent().attr('id'),
                    shift_type: $(this).parent().attr('id').split('_')[0],
                    start_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_start :input').val(),
                    over_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_over :input').val(),
                    end_time: $(this).parent().parent().find('#'+$(this).parent().attr('id').split('_')[0]+'_end :input').val(),
                    enabled: $(this).is(':checked')
                }
                    checkbox_values.push(data_value);
                });
            $('#shift_data').val(JSON.stringify(checkbox_values));
            console.log("JSON: "+JSON.stringify(checkbox_values));
            $(this).submit();
        }else{
            e.preventDefault();
        }

    
        
        //write JSON to hidden input
        

    });
    
    $('#submit_data').click(function(e){
        
        
    });


    function leadingZeros(input) {
        if(!isNaN(input.value) && input.value.length === 1) {
            input.value = '0' + input.value;
        }
    }
    
//TODO: Check masa supaya tak bertindih dengan shift yang lain

</script>
@endsection