@section('templates')
@parent
<template id="template-search-field-select">
    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 pb-2">
        <div class="input-group flex-nowrap">
            <div class="input-group-text rounded-0">
                <input class="form-check-input mt-0 search-field-enable" type="checkbox">
            </div>
            <div class="form-floating flex-fill">
                <select class="form-select rounded-0 search-field"></select>
                <label class="search-field-label text-nowrap">&nbsp;</label>
            </div>
        </div>
    </div>
</template>
@endsection