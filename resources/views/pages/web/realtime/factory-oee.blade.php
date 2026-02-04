<?php $pageTemplateUrl = route('realtime.factory-oee.index', '__uid__'); ?>
@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar', ['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation', ['dropMenuSelected' => 'FACTORY OEE'])

@section('head')
    @parent

    <style>
        .iposweb-font-title {
            font-weight: 600;
            text-align: center;
            color: #000080;
        }

        .iposweb-font-row {
            font-weight: 500;
            color: #000080;
            text-align: center;
            font-size: 1.1rem;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }

        .work-center-state-running {
            background-color: #39FF14;
        }

        .work-center-state-human-downtime {
            background-color: #0000FF;
        }

        .work-center-state-machine-downtime {
            background-color: #FF073A;
        }

        .work-center-state-plan-die-change {
            background-color: #FFAD00;
        }

        .work-center-state-unplan-die-change {
            background-color: #0000FF;
        }

        .work-center-state-break {
            background-color: #b026ff;
        }

        .work-center-state-idle {
            background-color: #999999;
        }
    </style>

    <style>
        .grid-work-center-container {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr 1fr;
            grid-template-rows: 1fr;
            grid-gap: 0.5rem;
        }

        .grid-work-center {
            grid-column: 1 / 2;
        }

        .work-center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .grid-no-work-center {
            grid-column: 1 / 6;
        }

        @media only screen and (max-width: 768px) {
            .grid-hide-small {
                display: none;
            }

            .grid-work-center {
                grid-column: 1 / 3;
            }

            .grid-work-center-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-no-work-center {
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
            @include('pages.web.realtime.components.factory-header')
            @forelse($viewFactories as $factory)
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fa-regular fa-clipboard-check me-2"></i> {{ $factory->name }}
                    </div>
                    <div class="card-body">
                        <div class="grid-work-center-container">
                            <div class="iposweb-font-title grid-hide-small">
                                WORK CENTRE
                            </div>
                            <div class="iposweb-font-title grid-hide-small">
                                OEE
                            </div>
                            <div class="iposweb-font-title grid-hide-small">
                                AVAILABILITY
                            </div>
                            <div class="iposweb-font-title grid-hide-small">
                                PERFORMANCE
                            </div>
                            <div class="iposweb-font-title grid-hide-small">
                                QUALITY
                            </div>
                            @forelse($factory->workCenters as $workCenter)
                                <div class="iposweb-font-title grid-hide-large grid-work-center">
                                    WORK CENTRE
                                </div>
                                <table class="grid-work-center border bg-light shadow-sm">
                                    <tr>
                                        <td class=" iposweb-font-row">{{ $workCenter->name }}</td>
                                        <td class="work-center-state-color work-center-data"
                                            data-work-center-uid="{{ $workCenter->uid }}" data-tag="_status"
                                            style="width:1.2rem"></td>
                                    </tr>
                                </table>
                                <div class="work-center-details">
                                    <div class="iposweb-font-title grid-hide-large">
                                        OEE
                                    </div>
                                    <div class="border bg-light iposweb-font-row shadow-sm">
                                        <span class="live-production-data renderer-percentage" data-work-center-uid="{{ $workCenter->uid }}"
                                            data-tag="average_oee" ></span>
                                    </div>
                                </div>
                                <div class="work-center-details">
                                    <div class="iposweb-font-title grid-hide-large">
                                        AVAILABILITY
                                    </div>
                                    <div class="border bg-light iposweb-font-row shadow-sm">
                                        <span class="live-production-data renderer-percentage" data-work-center-uid="{{ $workCenter->uid }}"
                                            data-tag="average_availability"></span>
                                    </div>
                                </div>
                                <div class="work-center-details">
                                    <div class="iposweb-font-title grid-hide-large">
                                        PERFORMANCE
                                    </div>
                                    <div class="iposweb-font-row border bg-light shadow-sm ">
                                        <span class="live-production-data renderer-percentage" data-work-center-uid="{{ $workCenter->uid }}"
                                            data-tag="average_performance"></span>
                                    </div>
                                </div>
                                <div class="work-center-details">
                                    <div class="iposweb-font-title grid-hide-large">
                                        QUALITY
                                    </div>
                                    <div class="iposweb-font-row border bg-light shadow-sm ">
                                        <span class="live-production-data renderer-percentage" data-work-center-uid="{{ $workCenter->uid }}"
                                            data-tag="average_quality"></span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center my-4 grid-no-work-center">
                                    No Work Center
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            @empty
                <div class="text-center my-4">No Factory</div>
            @endforelse
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
    @include('snippets.multi-live-production-scripts')
    <script>
        /** Work Center Idle */
        const STATUS_IDLE = 0;
        /** Work Center Die Change */
        const STATUS_DIE_CHANGE = 1;
        /** Work Center First Product Confirmation */
        const STATUS_FIRST_CONFIRMATION = 2;
        /** Work Center Running */
        const STATUS_RUNNING = 3;

        /** No Downtime */
        const DOWNTIME_STATUS_NONE = 0;

        /** Unplanned Downtime: Human */
        const DOWNTIME_STATUS_UNPLAN_HUMAN = -1;
        /** Unplanned Downtime: Machine */
        const DOWNTIME_STATUS_UNPLAN_MACHINE = -2;
        /** Unplanned Downtime: Die-Change */
        const DOWNTIME_STATUS_UNPLAN_DIE_CHANGE = -3;

        /** Planned Downtime: Die-Change */
        const DOWNTIME_STATUS_PLAN_DIE_CHANGE = 3;
        /** Planned Downtime: Break */
        const DOWNTIME_STATUS_PLAN_BREAK = 4;
        $('.renderer-percentage').data('render', (e, value, data) => {              
                if (value == null || isNaN(value))
                    return '-';
                return `${(value * 100).toFixed(2)}%`;
            });

        $(() => {

            $('.work-center-state-color')
                .data('render', (element, value, summary) => {
                    /*
                    .work-center-state-running 
                    .work-center-state-human-downtime 
                    .work-center-state-machine-downtime 
                    .work-center-state-plan-die-change 
                    .work-center-state-unplan-die-change 
                    .work-center-state-break 
                    .work-center-state-idle 
                    */

                    $(element).removeClass('work-center-state-running');
                    $(element).removeClass('work-center-state-human-downtime');
                    $(element).removeClass('work-center-state-machine-downtime');
                    $(element).removeClass('work-center-state-plan-die-change');
                    $(element).removeClass('work-center-state-unplan-die-change');
                    $(element).removeClass('work-center-state-break');
                    $(element).removeClass('work-center-state-idle');

                    if (value.status == STATUS_IDLE) {
                        $(element).addClass('work-center-state-idle');
                    } else {
                        switch (value.downtime_state) {
                            case DOWNTIME_STATUS_NONE:
                                $(element).addClass('work-center-state-running');
                                break;
                            case DOWNTIME_STATUS_PLAN_BREAK:
                                $(element).addClass('work-center-state-break');
                                break;
                            case DOWNTIME_STATUS_PLAN_DIE_CHANGE:
                                $(element).addClass('work-center-state-plan-die-change');
                                break;
                            case DOWNTIME_STATUS_UNPLAN_HUMAN:
                            case DOWNTIME_STATUS_UNPLAN_DIE_CHANGE:
                                $(element).addClass('work-center-state-human-downtime');
                                break;
                            case DOWNTIME_STATUS_UNPLAN_MACHINE:
                                $(element).addClass('work-center-state-machine-downtime');
                                break;
                        }
                    }

                    return '';
                }) //set custom render
                .data('display-value', null); //reset display value

            LivePage.forceUpdate(); //force running liveUpdate
        })
    </script>
@endsection
