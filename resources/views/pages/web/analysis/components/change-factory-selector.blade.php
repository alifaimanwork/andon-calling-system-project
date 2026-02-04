<div class="d-flex align-items-center">
    <div class="text-nowrap me-2 primary-text">FACTORY</div>
    <div>
        <select class="form-select iposweb-selector" onchange="changeFactory.changeFactory(this);">
            <option value="">ALL</option>
            @foreach($factories as $_factory)
            <option value="{{$_factory->uid}}" <?php if (!is_null($factory)) {
                                                    echo ($_factory->id == $factory->id ? 'selected' : '');
                                                } ?>>{{ $_factory->name }}</option>
            @endforeach
        </select>
    </div>
</div>
@section('scripts')
@parent
<script>
    var changeFactory = {
        urlTemplate: "{{ route('analysis.factory-oee.index',[$plant->uid,'__uid__']) }}",

        currentFactoryUid: "{{ $factory->uid ?? null }}",
        targetFactoryUid: null,

        changeFactory: function(sender) {
            this.targetWorkCenterUid = $(sender).val();
            if (this.currentWorkCenterUid == this.targetWorkCenterUid)
                return;

            this.changeFactoryNow();
        },
        changeFactoryNow: function() {
            window.location.href = this.urlTemplate.replace('__uid__', this.targetWorkCenterUid);
        }
    }
</script>

@endsection