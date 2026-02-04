<div class="row my-2 d-flex justify-content-between align-items-center">
    {{-- if $headerSelect exist --}}
    @if(!isset($headerSelect))
    <?php $headerSelect = null; ?>
    @endif

    @if ($headerSelect == 'DPR')
    <div class="col-auto">
        {{-- date picker --}}
        <div class="row">
            <div class="col-auto col-form-label">
                <label>SHIFT <i class="fa fa-caret-right"></i></label>
            </div>
            <div class="col-auto">
                <select class="form-control" id="ShiftSelect" style="color: #000080; background-color: #dddddd;">
                    <option value="day">DAY</option>
                    <option value="night">NIGHT</option>
                </select>
            </div>
        </div>

    </div>
    @endif

    <div class="col-auto">
        {{-- date picker --}}
        <div class="row">
            <div class="col-auto col-form-label">
                <label for="DatePicker">DATE <i class="fa fa-caret-right"></i></label>
            </div>
            <div class="col-auto">
                <input type="text" class="form-control text-center" id="DatePicker" style="color: #000080; background-color: #dddddd;">
            </div>
        </div>

    </div>

    @if ($headerSelect == 'DPR')
    <div class="col-auto">
        {{-- date picker --}}
        <div class="row">
            <div class="col-auto col-form-label">
                <label>PO <i class="fa fa-caret-right"></i></label>
            </div>
            <div class="col-auto">
                <input type="text" class="form-control text-center" style="color: #000080; background-color: #dddddd;">
            </div>
        </div>

    </div>
    @endif

</div>

@section('scripts')
@parent
<script>
    console.log('jQuery ' + $().jquery + ' Loaded');
    $(document).ready(function() {
        DatePicker();

    });
</script>
<script>
    function DatePicker() {
        $('#DatePicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

    }
</script>
@endsection