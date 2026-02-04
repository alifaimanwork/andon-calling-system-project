@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation', ['dropMenuSelected' => 'SUMMARY'])
@section('head')
@parent
    <style>
        a.disabled {
            pointer-events: none;
            cursor: default;
        }
    </style>
@endsection

@section('body')
<main>
    @yield('drop-menu-navigation')
    <div class="container">
        @yield('mobile-title')
        <div class="my-2 d-flex flex-column flex-md-row justify-content-start justify-content-md-between align-items-md-center align-items-start">
            <div class="mt-3">
                @include('components.web.change-plant-selector')
            </div>
            <div class="mt-3">
                <div class="d-flex align-items-center">
                    <div class="text-nowrap me-2 primary-text">DATE RANGE</div>
                    <div>
                        <input type="text" class="form-control text-center" id="production-date" style="color: #000080; background-color: #dddddd;">
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-md-4 mt-3 pt-2">
                <div class="card">
                    <div class="card-header">
                        <i class="fa-regular fa-clipboard-check me-2"></i> PRODUCTION STATUS SUMMARY
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text analysis-summary-data" data-tag="total_standard_output">-</span>
                                    <span class="primary-text">PLAN OUTPUT</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text analysis-summary-data" data-tag="total_actual_output"></span>
                                    <span class="primary-text">ACTUAL OUTPUT</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text analysis-summary-data" data-tag="total_reject_count"></span>
                                    <span class="primary-text">TOTAL REJECT PART</span>
                                </div>
                                <span class="secondary-text align-self-end">PCS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text renderer-rounding-hours analysis-summary-data" data-tag="total_downtimes_unplan"></span>
                                    <span class="primary-text">TOTAL DOWNTIME</span>
                                </div>
                                <span class="secondary-text align-self-end">HRS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text renderer-rounding-hours analysis-summary-data" data-tag="total_runtimes_plan"></span>
                                    <span class="primary-text">TOTAL WORKING HOUR</span>
                                </div>
                                <span class="secondary-text align-self-end">HRS</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex flex-column">
                                    <span class="title-text secondary-text renderer-percentage analysis-summary-data" data-tag="average_oee"></span>
                                    <span class="primary-text">AVERAGE OEE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 mb-2 mobile-card">
                    <div class="card-header">
                        <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD / PRINT REPORT
                    </div>
                    <div class="card-body">
                        <div class="secondary-text mt-2">
                            <form id="report-form" target="_blank" action="{{ route('analysis.summary.get.data', [$plant->uid]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="date_start">
                                <input type="hidden" name="date_end">
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

            <div class="col-12 col-md-8 mt-3">
                <div class="d-flex flex-column h-100">
                    <div class="row flex-fill">
                        <div class="col-6 p-2">
                            <a href="javascript: void(0)" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #c7c7c7; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-calendar-lines-pen pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">OEE</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="javascript: void(0)" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #c7c7c7; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-duotone fa-forklift pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">PRODUCTIVITY</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="javascript: void(0)" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #c7c7c7; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-regular fa-badge-check pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">QUALITY</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="javascript: void(0)" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #c7c7c7; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-hourglass-clock pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">DOWNTIME</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="{{ route('analysis.dpr', $plant->uid) }}" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #3a5dd8; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-file-chart-column pb-2 align-self-center" style="font-size: 40px;"></i>
                                    <span style="font-size: 20px"> DAILY PRODUCTION REPORT (DPR)</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 p-2">
                            <a href="{{ route('analysis.calling', $plant->uid) }}" type="button" class="rounded p-2 h-100 w-100 d-flex flex-column justify-content-center" style="background-color: #000000; color: white; border:none; text-decoration: none">
                                <div class="align-self-center d-flex flex-column">
                                    <i class="fa-thin fa-calendar-lines-pen pb-2 align-self-center" style="font-size:40px;"></i>
                                    <span style="font-size: 120%">CALLING RECORD</span>
                                </div>
                            </a>
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
    $(() => {

        if ((new URLSearchParams(window.location.search)).get('r')) {
            localStorage.clear();
        }

        //Renderer
        $('.renderer-percentage').data('render', (e, value, data) => {
            if (value == null || isNaN(value))
                return '-';
            return `${(value * 100).toFixed(2)}%`;
        });

        $('.renderer-rounding-hours').data('render', (e, value, data) => {
            if (value == null || isNaN(value))
                return '-';

            return `${(value / 3600).toFixed(2)}`;
        });

        //Date Range picker
        let datePicker = $('#production-date').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
            },
            startDate: localStorage['analysisStart'] ? localStorage['analysisStart'] : moment().format(
                'YYYY-MM-DD'),
            endDate: localStorage['analysisEnd'] ? localStorage['analysisEnd'] : moment().format(
                'YYYY-MM-DD'),
        }, function(start, end, label) {
            pageData.getData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            localStorage['analysisStart'] = start.format('YYYY-MM-DD');
            localStorage['analysisEnd'] = end.format('YYYY-MM-DD');
        });
        analysisPage.initialize();
        pageData.getData(datePicker.data('daterangepicker').startDate.format('YYYY-MM-DD'), datePicker.data(
            'daterangepicker').endDate.format('YYYY-MM-DD'));

        localStorage['analysisStart'] = datePicker.data('daterangepicker').startDate.format('YYYY-MM-DD');
        localStorage['analysisEnd'] = datePicker.data('daterangepicker').endDate.format('YYYY-MM-DD');
    });
    var pageData = {
        date_start: undefined,
        date_end: undefined,
        getData: function(dateStart, dateEnd) {
            let payload = {
                _token: window.csrf.getToken(),
                date_start: dateStart,
                date_end: dateEnd,
            }
            this.date_start = dateStart;
            this.date_end = dateEnd;

            $.post("{{ route('analysis.summary.get.data', [$plant->uid]) }}", payload, function(response,
                status, xhr) {
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
                }
            });
        },
        download: function(format) {
            $('#report-form').find('[name="date_start"]').val(this.date_start);
            $('#report-form').find('[name="date_end"]').val(this.date_end);
            $('#report-form').find('[name="format"]').val(format);
            // $('#report-form').submit();
        }

    }


    var analysisPage = {
        data: null,
        datatable: null,
        datatableRejectCount: null,
        initialize: function() {
            return this;
        },
        updateData: function() {
            let _this = this;

            $('.analysis-summary-data').each((index, e) => {
                let tag = $(e).data('tag');
                console.log(tag);
                let shiftTypeId = $(e).data('shift-type-id'); //set shift type id to select shift data

                let renderer = $(e).data('render');
                let data;
                if (shiftTypeId) {
                    data = _this.data.shifts.find(e => {
                        return e.shift_type_id == shiftTypeId;
                    });
                } else {
                    data = _this.data;
                }

                let val = '-';
                if (data)
                    val = data[tag];

                if (typeof(renderer) === 'function') {
                    val = renderer(e, val, data);
                }

                if (typeof(val) !== 'undefined')
                    $(e).html(val);
            });

            return this;
        }
    };
</script>
@endsection