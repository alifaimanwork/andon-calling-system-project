@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'settings'])
@include('pages.web.plant-settings.components.tab-nav-bar',['tabActive' => 'part'])
@include('templates.search-field-text')
@include('templates.search-field-select')
@section('head')
    @parent
    <style>
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
        @yield('mobile-title')
        {{-- @include('components.web.change-plant-selector') --}}
        @yield('tab-nav-bar')
        <div class="row mt-3">
            <h5 class="col-6 secondary-text">EDIT PART</h5>
            <div class="col-6 submit-button-cell text-end">
                {{-- form delete button --}}
                <form action="{{ route('settings.part.destroy',[ $plant->uid , $part->id ]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">DELETE RECORD</button>
                </form>
            </div>
        </div>
        <hr>
        <form method="post" action="{{ route('settings.part.update',[ $plant->uid , $part->id ]) }}">
            @method('PUT')
            @csrf

            <div class="row px-3">
                <!-- Data Form -->
                {{-- part information --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>PART INFORMATION</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                {{-- part number --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="part_no" class="form-label">PART NUMBER <span class="text-danger">*</span></label>
                                    <input type="text" name="part_no" class="form-control @error('part_no') is-invalid @enderror" id="part_no" placeholder="PART NUMBER" value="{{ $part->part_no }}" required>
                                    @error('part_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- part name --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="part_name" class="form-label">PART NAME <span class="text-danger">*</span></label>
                                    <input type="text" name="part_name" class="form-control @error('part_name') is-invalid @enderror" id="part_name" placeholder="PART NAME" value="{{ $part->name }}" required>
                                    @error('part_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- setup time --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="setup_time" class="form-label">SETUP TIME (IN SECONDS) <span class="text-danger">*</span></label>
                                    <input type="text" name="setup_time" class="form-control @error('setup_time') is-invalid @enderror" id="setup_time" placeholder="SETUP TIME (IN SECONDS)" value="{{ $part->setup_time }}" required>
                                    @error('setup_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- cycle time --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="cycle_time" class="form-label">CYCLE TIME (IN SECONDS) <span class="text-danger">*</span></label>
                                    <input type="text" name="cycle_time" class="form-control @error('cycle_time') is-invalid @enderror" id="cycle_time" placeholder="CYCLE TIME (IN SECONDS)" value="{{ $part->cycle_time }}" required>
                                    @error('cycle_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- packaging --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="packaging" class="form-label">PACKAGING <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="packaging" class="form-control @error('packaging') is-invalid @enderror" id="packaging" placeholder="PACKAGING" value="{{ $part->packaging }}" required>
                                    @error('packaging')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- reject target --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="reject_target" class="form-label">REJECT TARGET <span class="text-danger">*</span></label>
                                    <input type="number" min="0" name="reject_target" class="form-control" id="reject_target" placeholder="REJECT TARGET (%)" value="{{ $part->reject_target * 100 }}" required>
                                    @error('reject_target')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- work center --}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="work_center" class="form-label">WORK CENTER <span class="text-danger">*</span></label>
                                    <select name="work_center" id="work_center" onclick="updateLineList()" onchange="updateLineList()" class="form-control" required>
                                        @foreach ($workCenters as $workCenter)
                                            @if ($workCenter->id == $part->work_center_id)
                                                <option value="{{ $workCenter->id }}" selected>{{ $workCenter->name }}</option>
                                            @else
                                                <option value="{{ $workCenter->id }}">{{ $workCenter->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                {{-- line number--}}
                                <div class="col-12 col-md-6 mt-3">
                                    <label for="line_number" class="form-label">LINE NUMBER <span class="text-danger">*</span></label>
                                    <select name="line_number" id="line_number" class="form-select" required>
                                        @if ($part->line_no != null)
                                            <option value="{{ $part->line_no }}" selected>Line {{ $part->line_no }}</option>
                                        @else
                                            <option value="" disabled selected>Select Line Number</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- side --}}
                                <div class="col-12 mt-3">
                                    <label for="side" class="form-label">SIDE <span class="text-danger">*</span></label>
                                    <select name="side" id="" class="form-select @error('side') is-invalid @enderror" id="side" required>
                                        @isset($sides)
                                            @foreach ( $sides as $side )
                                                <option value="{{ $side }}" {{ ($side == $part->side)?'selected':'' }}>{{ $side }}</option>
                                            @endforeach
                                        @else
                                            <option disabled>No Option Found</option>
                                        @endisset
                                    </select>
                                    @error('side')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- opc part id --}}
                                <div class="col-12 mt-3">
                                    <label for="opc_part_id" class="form-label">OPC PART ID <span class="text-danger">*</span></label>
                                    <input type="text" name="opc_part_id" class="form-control @error('opc_part_id') is-invalid @enderror" id="opc_part_id" placeholder="OPC PART ID" value="{{ $part->opc_part_id }}" required>
                                    @error('opc_part_id')
                                        <div class="text-danger">{{ $message }}</div>                                
                                    @enderror
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                {{-- reject types --}}
                <div class="p-2 col-12 col-md-6">
                    <div class="card p-3 h-100">
                        <div class="card-title">
                            <h5>REJECT TYPE</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="row">
                                @foreach ( $reject_groups as $group )
                                    <div class="col-12 col-md-4 mt-3">
                                        <h6 style="border-bottom: 2px solid #575353">{{ strtoupper($group->name) }} <span class="text-danger">*</span></h6>

                                        @foreach ( $reject_types as $types )
                                            @if ($types->reject_group_id == $group->id && $types->enabled == 1)
                                                <div class="form-check">
                                                    @if ( $part_reject_types->contains($types->id) )
                                                        <input id="reject_{{ $types->id }}" class="form-check-input" type="checkbox" name="reject_types[]" value="{{ $types->id }}" id="reject_{{ $group->id }}_{{ $types->id }}" {{ old('reject_types') == $types->id ? 'checked' : '' }} checked>
                                                    @else
                                                        <input id="reject_{{ $types->id }}" class="form-check-input" type="checkbox" name="reject_types[]" value="{{ $types->id }}"id="reject_{{ $group->id }}_{{ $types->id }}" {{ old('reject_types') == $types->id ? 'checked' : '' }}>
                                                    @endif
                                                    <label class="form-check-label" for="reject_{{ $types->id }}">{{ $types->name }}</label>
                                                </div>
                                            @endif
                                            @if ($types->reject_group_id == $group->id && $types->enabled == 0)
                                                @if ( $part_reject_types->contains($types->id) )
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="reject_types[]" value="{{ $types->id }}" id="reject_{{ $group->id }}_{{ $types->id }}" {{ old('reject_types') == $types->id ? 'checked' : '' }} checked>
                                                        <label class="form-check-label text-danger" for="{{ $types->id }}">{{ $types->name }}</label>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    {{-- back --}}
                    <a href="{{ route('settings.part.index',[ $plant->uid ]) }}" class="btn btn-secondary">CANCEL</a>
                    <button type="submit" class="btn btn-action btn-warna">UPDATE</button>
                </div>
            </div>

        </form>

    </div>


</main>
@endsection

@section('modals')
@parent
<!-- Upload Temp Profile Picture -->
<div class="modal fade" id="manage-profile-picture" tabindex="-1" aria-labelledby="manage-profile-picture-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manage-profile-picture-label">Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                TODO: Set temporary profile picture for new user
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>

var workCenters = @json($workCenters);
    //TODO: Guard variable


function updateLineList() {
    let workCenterId = $('#work_center').val();

    let maxLine = 0;
    //get workcenter
    workCenters.forEach(e => {
        if (e.id == workCenterId) {
            //update
            maxLine = e.production_line_count;
        }
    });

    let prevLine = $('#line_number').val();
    

    let count_line = 0;
    $('#line_number').children().each(function() {
        //count line not disabled
        if (!$(this).prop('disabled')) {
            count_line++;
        }
    });

    $('#line_number').empty();

    if(count_line == 0){
        $('#line_number').append(`<option value="" selected disabled>WORK CENTER NOT SELECTED</option>`);
    }else{
        $('#line_number').append(`<option value="" selected disabled>SELECT LINE</option>`);
    }

    for (let i = 1; i <= maxLine; i++) {
        if (prevLine == i)
            $('#line_number').append(`<option value="${i}" selected>Line ${i}</option>`);
        else
            $('#line_number').append(`<option value="${i}">Line ${i}</option>`);
    }
};

var page = {
    _token: "{{ csrf_token() }}",

    initialize: function() {
        return this;
    },
}

    //onload
$(document).ready(function() {
    CheckLocked();
    updateLineList();
});

function CheckLocked() {

    var reject_types = @json($reject_types->toArray());
    //get all checkboxes
    var checkboxes = $('input[type=checkbox]');

    //loop through all reject types
    reject_types.forEach(e => {
        //get checkbox
        var checkbox = $('#reject_' + e.id);
        //check if locked
        
        if (e.locked == 1) {
            //disable checkbox
            checkbox.prop('disabled', true);
            checkbox.prop('checked', true);

            //add hidden input
            //$('#reject_' + e.id).after(`<input type="hidden" name="reject_types[]" value="${e.id}">`);
        }
        
    });
}
</script>
@endsection