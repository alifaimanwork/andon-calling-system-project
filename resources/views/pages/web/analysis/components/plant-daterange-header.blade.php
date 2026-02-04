{{-- date picker range --}}
<div class="col-auto mx-5">
    {{-- date picker range --}}
    <div class="row">
        <div class="col-auto col-form-label">
            <label for="DateRangePicker">DATE RANGE <i class="fa fa-caret-right"></i></label>
        </div>
        <div class="col-auto">
            <input type="text" class="form-control text-center" id="DateRangePicker" style="color: #000080; background-color: #dddddd;">
        </div>
    </div>

</div>



@section('scripts')
@parent
<script>
    console.log('jQuery ' + $().jquery + ' Loaded');
    $(document).ready(function() {
        DateRangePicker();

    });
</script>
<script>
    function DateRangePicker() {
        $('#DateRangePicker').daterangepicker();
    }
</script>
@endsection