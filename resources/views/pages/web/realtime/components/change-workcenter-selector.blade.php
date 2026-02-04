<div class="d-flex align-items-center">
    <div class="text-nowrap primary-text me-2">SELECT WORK CENTER</div>
    <div>
        <select class="form-select iposweb-selector" onchange="changeWorkCenter.changeWorkCenter(this);">
            @foreach($workCenters as $wc)
            <option value="{{$wc->uid}}" <?php echo ($wc->id == $workCenter->id ? 'selected' : ''); ?>>{{ $wc->name }}</option>
            @endforeach
        </select>
    </div>
</div>
@section('scripts')
@parent
<script>
    var changeWorkCenter = {
        urlTemplate: "{{ route(\Request::route()->getName(),[$plant->uid,'__uid__']) }}",
        requireConfirmation: false,
        setRequireConfirmation: function(required) {
            this.requireConfirmation = required;
        },
        currentWorkCenterUid: "{{ $workCenter->uid }}",
        targetWorkCenterUid: null,
        changeWorkCenter: function(sender) {
            this.targetWorkCenterUid = $(sender).val();
            if (this.currentWorkCenterUid == this.targetWorkCenterUid)
                return;

            if (this.requireConfirmation)
                $('#change-plant-selector-confirmation').modal('show');
            else
                this.changeWorkCenterNow();
        },
        changeWorkCenterNow: function() {
            window.location.href = this.urlTemplate.replace('__uid__', this.targetWorkCenterUid);
        }
    }
</script>

@endsection
@section('modals')
@parent
<div id="change-plant-selector-confirmation" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Plant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Any unsaved changes will be lost. Confirm change plant?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="changePlantSelector.changePlantNow()">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection