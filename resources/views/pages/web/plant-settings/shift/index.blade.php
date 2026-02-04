@extends('layouts.app')
@include('utils.auto-toast')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'shift'])
@section('head')
@parent
@endsection

@section('body')
<style>
    /* change backgroud of disabled-shift */
    .disabled-shift {
        background-color: #fde0e0 !important;
    }

    .btn-warna {
        background-color: #0000e4;
    }
    .btn-warna:hover {
        background-color: #000080;
    }

</style>>
<main>
    <div class="container py-3">
        @yield('mobile-title')
        @include('components.web.change-plant-selector')
        @yield('tab-nav-bar')
        
        <h5 class="secondary-text mt-4">SHIFT</h5>
        <hr>
        <div class="my-2 text-end">
            <a href="{{ route('settings.shift.create',[ $plant->uid ]) }}" class="btn btn-action btn-warna"><i class="me-3 fa fa-file-plus"></i>CHANGE SHIFT</a>
        </div>

        {{-- WEEK table for shift --}}
        <div class="row">
            <div class="col-md-12">
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
                                        <th style="white-space: nowrap; width: 1%;">Enable</th>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">Over Time</th>
                                        <th class="text-center">End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="1">
                                        <td>Monday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="2">
                                        <td>Tuesday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="3">
                                        <td>Wednesday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="4">
                                        <td>Thursday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="5">
                                        <td>Friday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="6">
                                        <td>Saturday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                    <tr id="7">
                                        <td>Sunday</td>
                                        <td id="day_enabled" class=" text-center"></td>
                                        <td id="day_start" class="text-center"></td>
                                        <td id="day_over" class="text-center"></td>
                                        <td id="day_end" class="text-center"></td>
                                        
                                        <td id="night_enabled" class=" text-center"></td>
                                        <td id="night_start" class="text-center"></td>
                                        <td id="night_over" class="text-center"></td>
                                        <td id="night_end" class="text-center"></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
<script>

    $(document).ready(function(){
        var shifts = {!! json_encode($shifts->toArray()) !!};

        shifts.forEach(function(shift){

            if(shift.shift_type.name == 'Day'){
                if(shift.enabled){
                    //green icon enabled
                    $('#'+shift.day_of_week+' #day_enabled').html('<i class="fa fa-check-circle" style="color:green"></i>');
                }else{
                    //red icon disabled
                    $('#'+shift.day_of_week+' #day_enabled').html('<i class="fa fa-times-circle" style="color:red"></i>');
                    $('#'+shift.day_of_week+' #day_enabled').addClass('disabled-shift');

                    //add disabled class to row
                    $('#'+shift.day_of_week+' #day_start').addClass('disabled-shift');
                    $('#'+shift.day_of_week+' #day_over').addClass('disabled-shift');
                    $('#'+shift.day_of_week+' #day_end').addClass('disabled-shift');

                }
                $('#'+shift.day_of_week+' #day_start').html(shift.start_time.slice(0,-3));
                $('#'+shift.day_of_week+' #day_over').html(shift.over_time.slice(0,-3));
                $('#'+shift.day_of_week+' #day_end').html(shift.end_time.slice(0,-3));
            }
            else{
                if(shift.enabled){
                    //green icon enabled
                    $('#'+shift.day_of_week+' #night_enabled').html('<i class="fa fa-check-circle" style="color:green"></i>');
                }else{
                    //red icon disabled
                    $('#'+shift.day_of_week+' #night_enabled').html('<i class="fa fa-times-circle" style="color:red"></i>');
                    $('#'+shift.day_of_week+' #night_enabled').addClass('disabled-shift');

                    //add disabled class to row
                    $('#'+shift.day_of_week+' #night_start').addClass('disabled-shift');
                    $('#'+shift.day_of_week+' #night_over').addClass('disabled-shift');
                    $('#'+shift.day_of_week+' #night_end').addClass('disabled-shift');
                }
                $('#'+shift.day_of_week+' #night_start').html(shift.start_time.slice(0,-3));
                $('#'+shift.day_of_week+' #night_over').html(shift.over_time.slice(0,-3));
                $('#'+shift.day_of_week+' #night_end').html(shift.end_time.slice(0,-3));
            }

            
            
        });
    });

</script>
@endsection