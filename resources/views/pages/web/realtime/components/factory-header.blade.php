@section('head')
    @parent
    <style>
        .iposweb-flag-ico {
            width: 100px;
            /* shadow */
            box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.75);
        }

        .local-clock-container {
            /* content vertically center */
            display: flex;
            align-items: center;
            /* content horizontally center */
            justify-content: center;

            padding: 0.5rem;
            background-color: rgb(180, 180, 180);
        }

        .live-clock-flag {
            font-size: 2rem;
            color: #000080;
            font-weight: 600;
        }
    </style>
@endsection

<div class="row my-2 d-flex flex-column flex-md-row justify-content-between align-items-center">

    <div class="col mt-3">
        @include('components.web.change-plant-selector')
    </div>
    @if(count($plant->onPlantDb()->factories) > 0)
        <div class="col mt-3">
            @include('pages.web.realtime.components.change-factory-selector')
        </div>
    @endif
    <div class="col-auto mt-3 mx-5">
        <div class="local-clock-container row">
            <div class="col">
                <img class="iposweb-flag-ico" src="{{ $plant->getRegionFlag() }}">
            </div>
            <div class="col">
                <div class="font-mono live-clock live-clock-flag" data-clock="plant" data-format="HH:mm"></div>
                <div class="">{{ $plant->getTimezoneText() }}</div>
            </div>
        </div>
    </div>

</div>