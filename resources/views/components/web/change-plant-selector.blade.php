<?php $_plants = \App\Models\User::getCurrent()->getAccessiblePlants(); //get accessible plant 
?>

<div class="d-flex align-items-center mt-2">
    <div class="text-nowrap primary-text me-2 mt-2">PLANT</div>
    <div class="mt-2">
        <select class="form-select iposweb-selector" onchange="changePlantSelector.changePlant(this);">
            @foreach($_plants as $_plant)
            <option value="{{$_plant->uid}}" <?php echo ($_plant->id == $plant->id ? 'selected' : ''); ?>>{{ $_plant->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@section('scripts')
@parent
<script>
    var changePlantSelector = {
        urlTemplate: "{{ $pageTemplateUrl ?? route(\Request::route()->getName(),'__uid__') }}",
        requireConfirmation: false,
        setRequireConfirmation: function(required) {
            this.requireConfirmation = required;
        },
        currentPlantUid: "{{ $plant->uid }}",
        targetPlantUid: null,
        changePlant: function(sender) {
            this.targetPlantUid = $(sender).val();
            if (this.currentPlantUid == this.targetPlantUid)
                return;

            if (this.requireConfirmation)
                $('#change-plant-selector-confirmation').modal('show');
            else
                this.changePlantNow();
        },
        changePlantNow: function() {

            window.location.href = this.urlTemplate.replace('__uid__', this.targetPlantUid);
            
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