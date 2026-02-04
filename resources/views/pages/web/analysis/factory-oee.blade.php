@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation',['dropMenuSelected' => 'FACTORY OEE'])
@section('head')
@parent

<style>
    .analysis-title-div {
        font-weight: 600;
        color: white;
        text-align: center;
        background-color: #000080;
    }

    .iposweb-font-row {
        font-weight: 500;
        color: #000080;
        text-align: center;
        font-size: 1.1rem;
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
    }

    .factory-oee-table {
        width: 100%;
        border: none;
        border-collapse: separate;
        border-spacing: 0.5rem;
        text-align: center;
        table-layout: fixed;
    }


    .factory-oee-table tr,
    .factory-oee-table th,
    .factory-oee-table td {
        border: none;
    }

    .factory-oee-table tbody td {
        font-weight: 500;
        color: #000080;
        text-align: center;
        font-size: 1.1rem;
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;

        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        border: 1px solid #dee2e6 !important;
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity)) !important;

    }

    .factory-oee-table thead th {
        font-weight: 600;
        color: white;
        background-color: #000080;
    }

    .factory-oee-table thead td {
        font-size: 0.9rem;
    }

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

<style>
    .grid-work-center-container {
        display: grid;
        grid-template-columns: repeat(9, 1fr);
        grid-gap: 0.5rem;
    }

    .grid-box-span {
        font-weight: 600;
        color: white;
        background-color: #000080;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .grid-box-value {
        font-weight: 500;
        color: #000080;
        text-align: center;
        font-size: 1.1rem;
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        border: 1px solid #dee2e6 !important;
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity));
    }

    .grid-box-span.muted {
        background-color: #c4c4c4 !important;
        color: #000080;
    }

    @media only screen and (max-width: 768px) {
        .grid-hide-small {
            display: none;
        }

        .grid-work-center-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-span-full-2 {
            grid-column: 1 / 3;
        }
    }

    @media only screen and (min-width: 768px) {
        .grid-hide-large {
            display: none;
        }
    }
</style>
@endsection

@section('body')
<main>
    @yield('drop-menu-navigation')
    <div class="container">
        @yield('mobile-title')
        <div class="my-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start">
            <div class="mt-3">
                @include('components.web.change-plant-selector')
            </div>
            @if(count($plant->onPlantDb()->factories) > 0)
            <div class="ms-md-3 mt-3">
                @include('pages.web.analysis.components.change-factory-selector')
            </div>
            @endif
            <div class="ms-md-3 mt-3">
                {{-- date picker range --}}
                <div class="d-flex gap-3 align-items-center">
                    <label for="production-date" class="primary-text">DATE</label>
                    <input type="text" class="form-control text-center" id="production-date" style="color: #000080; background-color: #dddddd;">
                </div>
            </div>

        </div>

        @forelse($viewFactories as $_factory)
        <div class="w-100 mt-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa-regular fa-clipboard-check me-2"></i> {{ $_factory->name }}
                </div>
                <div class="card-body">
                    <div class="grid-work-center-container mt-3">
                        <div class="grid-hide-small grid-box-span" style="grid-column: 2 / 6">
                            DAY
                        </div>
                        <div class="grid-hide-small grid-box-span" style="grid-column: 6 / 10">
                            NIGHT
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            WORK CENTER
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            OEE
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            AVAILABILITY
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            PERFORMANCE
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            QUALITY
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            OEE
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            AVAILABILITY
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            PERFORMANCE
                        </div>
                        <div class="secondary-text grid-hide-small text-center">
                            QUALITY
                        </div>

                        @forelse($_factory->workCenters as $workCenter)
                        <div class="secondary-text grid-hide-large text-center grid-span-full-2">
                            WORK CENTER
                        </div>
                        <div class="grid-box-span grid-span-full-2 muted">
                            {{ $workCenter->name }}
                        </div>
                        <div class="grid-box-span grid-span-full-2 grid-hide-large">
                            DAY
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            OEE
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            AVAILABILITY
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_oee" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="1">
                            -
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_availability" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="1">
                            -
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            PERFORMANCE
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            QUALITY
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_performance" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="1">
                            -
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_quality" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="1">
                            -
                        </div>
                        <div class="grid-box-span grid-span-full-2 grid-hide-large">
                            NIGHT
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            OEE
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            AVAILABILITY
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_oee" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="2">
                            -
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_availability" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="2">
                            -
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            PERFORMANCE
                        </div>
                        <div class="secondary-text grid-hide-large text-center">
                            QUALITY
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_performance" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="2">
                            -
                        </div>
                        <div class="grid-box-value analysis-factory-oee-data renderer-percentage" data-tag="average_quality" data-work-center-uid="{{ $workCenter->uid }}" data-shift-type-id="2">
                            -
                        </div>
                        @empty
                        <div class="text-center" style="">
                            No Work Center
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center my-4">No Factory</div>
        @endforelse
        <div class="d-flex justify-content-start mt-3">
            <div class="card mobile-card mb-3">
                <div class="card-header">
                    <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                </div>
                <div class="card-body secondary-text" style="font-size: 2.5em;">
                    <form id="report-form" target="_blank" action="{{ route('analysis.factory-oee.get.data',[ $plant->uid, count($viewFactories) > 1? null:$viewFactories[0]->uid ?? null ]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="date">
                        <input type="hidden" name="format">
                        <button type="submit" class="blank-button px-1" onclick="pageData.download('download')">
                            <i class="fa-light fa-file-spreadsheet"></i>
                        </button>
                        <button type="submit" class="blank-button px-1" onclick="pageData.download('print')">
                            <i class="fa-solid fa-print"></i>
                        </button>
                    </form>
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
    $(() => {
        if ((new URLSearchParams(window.location.search)).get('r')) {
            localStorage.clear();
        }
        $('#production-date').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
            },
            singleDatePicker: true,
            showDropdowns: true,
            startDate: localStorage['analysisDate'] ? localStorage['analysisDate'] : moment().format('YYYY-MM-DD'),
            minDate: "2000-01-01",
            maxDate: moment().format('YYYY-MM-DD'),
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10)
        }, function(start, end, label) {
            pageData.getData(start.format('YYYY-MM-DD'));

            localStorage['analysisDate'] = start.format('YYYY-MM-DD');
        });

        //attach renderer
        $('.renderer-percentage').data('render', (e, value, data) => {
            if (value == null || isNaN(value))
                return '-';
            return `${(value * 100).toFixed(2)}%`;
        });

        pageData.getData($('#production-date').val());

    });
    var pageData = {
        date: undefined,
        getData: function(selectedDate) {

            let payload = {
                _token: window.csrf.getToken(),
                date: selectedDate
            };
            this.date = selectedDate;
            
            $.post("{{ route('analysis.factory-oee.get.data',[ $plant->uid, count($viewFactories) > 1? null:$viewFactories[0]->uid ?? null ]) }}", payload, function(response, status, xhr) {
                //result code
                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;
                const RESULT_INVALID_PARAMETERS = -2;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {
                    analysisPage.data = response.data;

                    analysisPage.updateData();
                } else {
                    alert(response.message);
                };
            });
        },
        download: function(format) {
            $('#report-form').find('[name="date"]').val(this.date);
            $('#report-form').find('[name="format"]').val(format);
            // $('#report-form').submit();
        }
    }



    var analysisPage = {
        data: null,
        updateData: function() {
            let _this = this;

            $('.analysis-factory-oee-data').each((index, e) => {
                let tag = $(e).data('tag');
                let workCenterUid = $(e).data('work-center-uid');
                let shiftTypeId = $(e).data('shift-type-id');

                let renderer = $(e).data('render');
                let dataRow = _this.findData(workCenterUid, shiftTypeId);
                let val = '-';
                if (dataRow)
                    val = dataRow[tag];

                if (typeof(renderer) === 'function') {
                    val = renderer(e, val, dataRow);
                }

                $(e).html(val);
            });
        },
        findData: function(workCenterUid, shiftTypeId) {
            if (!Array.isArray(this.data))
                return undefined;

            let data = this.data.find((e, index) => {
                return (e.work_center_uid == workCenterUid && e.shift_type_id == shiftTypeId);
            });

            return data
        },
    };
</script>

@endsection