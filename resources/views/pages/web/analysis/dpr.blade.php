@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'analysis'])
@include('pages.web.analysis.components.drop-menu-navigation', ['dropMenuSelected' => 'DPR'])
@section('head')
@parent
{{-- style transform-table-scale --}}
<style>
    .transform-table-scale {
        transform: scale(1);
        transform-origin: top left;
        /* padding: 1rem; */

    }

    .dpr-page-container {
        height: calc(100vh - 95px - 1rem);
    }

    .scrollable-div-container {
        max-height: 100%;

    }

    .scrollable-container {
        width: 100%;
        height: 100%;
        overflow: auto;
    }

    .content-position {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    .no-drop {
        cursor: no-drop;
    }

    .pointer {
        cursor: pointer;
    }
</style>
@endsection

@section('body')
<main>
    @yield('drop-menu-navigation')
    <div class="container-fluid px-5 dpr-page-container d-flex flex-column">
        @yield('mobile-title')

        <div class="d-flex flex-wrap">
            {{-- plant input --}}
            <div class="mt-3">
                @include('components.web.change-plant-selector')
            </div>

            {{-- work center input --}}
            <div class="d-flex align-items-center mt-3 ms-md-3">
                <div class="text-nowrap primary-text me-2">Work Center</div>
                <div>
                    <select class="form-select iposweb-selector workcenter-selector" style="color: #000080; background-color: #dddddd;">
                        @foreach ($workCenters as $wc)
                            <option value="{{ $wc->uid }}" @if($wc->uid == $workCenter->uid) selected @endif>{{ $wc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- shift input --}}
            <div class="d-flex align-items-center mt-3 ms-md-3">
                <div class="text-nowrap primary-text me-2">SHIFT</div>
                <div>
                    <select class="form-select iposweb-selector shifttype-selector" style="color: #000080; background-color: #dddddd;">
                        @foreach ($shiftTypes as $shiftType)
                            <option value="{{ $shiftType->id }}">{{ $shiftType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- date input --}}
            <div class="d-flex align-items-center mt-3 ms-md-3">
                <div class="text-nowrap primary-text me-2">Date</div>
                <div>
                    <input id="date-pick" type="text" class="form-control text-center datepick-selecter" style="color: #000080; background-color: #dddddd;">
                </div>
            </div>

            {{-- production input --}}
            <div class="d-flex align-items-center mt-3 ms-md-3">
                <div class="text-nowrap primary-text me-2">Production</div>
                <div>
                    <select class="form-select iposweb-selector production-selector" style="color: #000080; background-color: #dddddd;">
                        <option disabled>No Production</option>
                    </select>
                </div>
            </div>

        </div>


        <div class="d-flex">
            <div class="mobile-card card mt-3">
                <div class="card-header">
                    <i class="fa-light fa-file-invoice me-2"></i> DOWNLOAD DPR REPORT
                </div>
                <div class="card-body">
                    <div class="d-flex secondary-text mt-2" style="font-size: 40px;">
                        <div onclick="downloadExcel();" class="no-drop excel-download-btn"><i class="fa-light fa-file-spreadsheet px-2"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- include dpr-report --}}
        {{-- overflow-scroll --}}

        <div class="flex-fill card mt-3" style="max-height:100%;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-center no-dpr-prod" style="height: 100%;">
                    <div class="text-nowrap primary-text me-2">
                        <h3>No Production Selected</h3>
                    </div>
                </div>


                {{-- transform scale --}}
                <div class="scrollable-container position-relative view-dpr-prod d-none">
                    <div class="transform-table-scale content-position" style="font-size: 12pt">
                        @include('pages.web.analysis.include.dpr-report-2')
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
    var plant = <?php echo json_encode($plant); ?>;

    var dprData = "";
    $('.shifttype-selector').val(localStorage['dprShift'] ? localStorage['dprShift'] : '1');


    $(() => {
        if((new URLSearchParams(window.location.search)).get('r'))
        {
            localStorage.clear();
        }
        $('#date-pick').daterangepicker({
            //local format
            startDate: localStorage['analysisDate'] ? localStorage['analysisDate'] : moment().format('YYYY-MM-DD'),
            locale: {
                format: 'YYYY-MM-DD',
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: "2000-01-01",
            maxDate: moment().format('YYYY-MM-DD'),
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'), 10)
        });

        populateDprSheet();

        localStorage['dprShift'] = $('.shifttype-selector').val();
        localStorage['analysisDate'] = $('.datepick-selecter').val();
    });

    function regenerateRows() {
        $('.dpr-content').empty();
        let rowCount = dprData._row_count;
        if (!rowCount || rowCount < 12)
            rowCount = 12;

        for (let n = rowCount - 1; n >= 0; n--) {
            let row = $($('#template-dpr-content-row').html());
            row.find('.dpr-data').each((idx, e) => {
                $(e).data('tag', $(e).data('tag').replace('__ROWLINE__', n));
            });

            row.insertAfter($('#pre-dpr-content'));
        }

    }

    function populateDprSheet() {

        regenerateRows();

        $('.dpr-data').each((index, element) => {
            let tag = $(element).data('tag');
            if (!tag)
                return;

            if (dprData[tag] == null)
                $(element).html('&nbsp;');
            else
                $(element).html(dprData[tag]);
        });

    }

    $('.workcenter-selector').on('change', () => {
        // getProductions();
        let urlTemplate = '{{ route("analysis.dpr", [ $plant->uid, "__workCenter__" ]) }}';
        let target = $('.workcenter-selector').val();

        if (!target)
            return;

        window.location.href = urlTemplate.replace('__workCenter__', target);
    });

    $('.shifttype-selector').on('change', () => {
        getProductions();
        localStorage['dprShift'] = $('.shifttype-selector').val();
    });

    $('.datepick-selecter').on('change', () => {
        getProductions();
        localStorage['dprDate'] = $('.datepick-selecter').val();
    });

    $('.production-selector').on('change', () => {
        getDprPopulate();
    });

    function getDprPopulate() {
        //prepare payload
        let payload = {
            _token: window.csrf.getToken(),
            work_center_uid: $('.workcenter-selector').val(),
            production_id: $('.production-selector').val(),
        };

        $.post("{{ route('analysis.dpr.get.dprdata', [$plant->uid]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {

                    console.log("Respone DPR :", response.data);
                    let productions = response.data;
                    dprData = response.data;

                    $('.no-dpr-prod').addClass('d-none');
                    $('.view-dpr-prod').removeClass('d-none');

                    //excel-download-btn remove class no-drop addclass pointer
                    $('.excel-download-btn').removeClass('no-drop');
                    $('.excel-download-btn').addClass('pointer');

                    populateDprSheet();

                } else {
                    alert(response.message);
                }
            }).always(function() {});
    }

    function getProductions() {
        $('.no-dpr-prod').removeClass('d-none');
        $('.view-dpr-prod').addClass('d-none');

        //excel-download-btn remove class no-drop addclass pointer
        $('.excel-download-btn').addClass('no-drop');
        $('.excel-download-btn').removeClass('pointer');

        //prepare payload
        let payload = {
            _token: window.csrf.getToken(),
            work_center_uid: $('.workcenter-selector').val(),
            date: $('.datepick-selecter').val(),
            shift_type_id: $('.shifttype-selector').val()
        };

        $.post("{{ route('analysis.dpr.get.productions', [$plant->uid]) }}", payload,
            function(response, status, xhr) {

                const RESULT_OK = 0;
                const RESULT_INVALID_STATUS = -1;

                //TODO: display error message in modal
                if (response.result === RESULT_OK) {

                    console.log("Respone Prod:", response.data);
                    let productions = response.data;

                    let productionSelector = $('.production-selector');
                    productionSelector.empty();
                    //for loop through productions
                    productionSelector.append('<option disabled selected>Select Production</option>');
                    for (let i = 0; i < productions.length; i++) {
                        let production = productions[i];
                        let started_at = moment(production.started_at).tz(plant.time_zone).format(
                            'YYYY-MM-DD HH:mm');

                        productionSelector.append('<option value="' + production.id + '">' + (i + 1).toFixed(0) +
                            " - " + started_at + '</option>');
                    }
                    if (productions.length < 1) {
                        productionSelector.empty();
                        productionSelector.append('<option disabled selected>No Production</option>');
                    }

                    productions.forEach(production => {
                        //TODO: populate dpr-report
                    });

                } else {
                    alert(response.message);
                    //location.reload();
                }
            }).always(function() {});
    }

    function downloadExcel() {

        //open link in new tab  
        var url = "{{ route('analysis.dpr.export', [$plant->uid,'__work_center_uid__','__production_id__']) }}";

        //if view-dpr-prod is not hidden, then download excel
        if (!$('.view-dpr-prod').hasClass('d-none')) {
            url = url.replace('__work_center_uid__', $('.workcenter-selector').val());
            url = url.replace('__production_id__', $('.production-selector').val());

            window.open(url, '_blank');
        }
    }
</script>
@endsection