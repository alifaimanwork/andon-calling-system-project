@extends('layouts.app')
@include('components.web.top-nav-bar')
@include('components.web.side-nav-bar',['menuActive' => 'realtime'])
@include('pages.web.realtime.components.drop-menu-navigation',['dropMenuSelected' => 'QUALITY'])

@section('head')
    @parent
@endsection

@section('body')
    <main>
        @yield('drop-menu-navigation')
        <div class="container">
            @include('pages.web.realtime.components.work-center-header')
    
            <div id="dashboard-container">
    
            </div>
    
        </div>
    </main>
@endsection

@section('templates')
    @parent
    <template id="template-active-production-line">
        {{-- tab : production lines --}}
        <div class="row">
            <div class="col-12 mt-3 px-0">
                <ul id="production-line-container" class="nav nav-tabs">

                </ul>
            </div>
        </div>

        {{-- part details --}}
        <div class="row" style="background-color: #fff">
            {{-- part details --}}
            <div class="col-12 col-md-6 mt-3">
                <div class="d-flex justify-content-between">
                    <div class="part-container px-2">
                        <i class="fa-solid fa-tag big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">PART NAME <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="name">-</span>
                        </div>
                    </div>
                    <div class="part-container px-2">
                        <i class="fa-duotone fa-tags big-icon"></i>
                        <div class="d-flex flex-column">
                            <span class="primary-text">PART NUMBER <i class="ms-3 fa-solid fa-caret-down"></i></span>
                            <span class="value current-production-line-data part-data" data-tag="part_no">-</span>
                        </div>
                    </div>
                </div>
                <div class="table-container mt-3">
                    <table class="table table-striped w-100 primary-text">
                        <tbody>
                            <tr>
                                <td>PLAN PRODUCTION</td>
                                <td class="current-production-line-data live-production-line-data format-text-pcs" data-tag="plan_quantity">-</td>
                            </tr>
                            <tr>
                                <td>ACTUAL PRODUCTION</td>
                                <td class="current-production-line-data live-production-line-data format-text-pcs" data-tag="actual_output">-</td>
                            </tr>
                            <tr>
                                <td>PART OK</td>
                                <td class="current-production-line-data live-production-line-data format-text-pcs" data-tag="ok_count">-</td>
                            </tr>
                            <tr>
                                <td>PART NG</td>
                                <td class="current-production-line-data live-production-line-data format-text-pcs" data-tag="reject_count">-</td>
                            </tr>
                            <tr>
                                <td>PENDING QUANTITY</td>
                                <td class="current-production-line-data live-production-line-data format-text-pcs" data-tag="pending_count">-</td>
                            </tr>
                            <tr>
                                <td>REJECT %</td>
                                <td class="current-production-line-data production-line-data format-text-percentage" data-tag="reject_percentage">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- reject chart --}}
            <div class="col-12 col-md-6 mt-3">
                <h5 class="secondary-text">REJECT (PCS)</h5>
                <div class="w-100 my-3" style="">
                    <canvas id="reject-chart"></canvas>
                </div>
            </div>
            <div class="col-12 mt-3" style="height:500px;">
                <h5 class="secondary-text">TOP 10 REJECT BY DEFECT PART (PCS)</h5>
                <div class="w-100 my-3" style="">
                    <canvas id="top-10-chart"></canvas>
                </div>
            </div>
        </div>
    </template>
    <template id="template-inactive-production-line">
        <div class="row mt-3" style="background-color: #fff; height: 500px">
            <div class="d-flex justify-content-center align-items-center my-4" style="font-size: 2.5rem; font-weight: 400">
                No Production
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    @parent
    @include('snippets.live-production-scripts')
    
    {{-- web socket, populate template --}}
    <script>

        //Websocket
        Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}')
            .listen('.terminal.data-updated', (e) => {
                LivePage.terminalDataUpdatedHandler(e);
            });

        $(() => {

            //Auth handle Tab
            LivePage.initializeProductionLineTab(function(e) {
                currentProductionLineChanged(e); // callback to init line tab
            })
            .listenAnyChanges(e => {
                productionChanges(e);
            }).listenChanges(
                'live-production-line-data',
                setConfigToCurrentLine({
                    tag: 'reject_count'
                }),
                updateChart
            );

            // Datatable
            $('#dtProductionPlanning').DataTable({
                dom: 'flrtp',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10', '25', '50', 'All']
                ],
                responsive: true,
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 1
                    },
                    {
                        responsivePriority: 9,
                        targets: -1
                    }
                ],
                scrollX: true,
            });

            $('#dt2').DataTable({
                dom: 'rt',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10', '25', '50', 'All']
                ],
                responsive: true,
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 1
                    },
                    {
                        responsivePriority: 9,
                        targets: -1
                    }
                ],
                scrollX: true,
            });
        });

        function updateChart(config, value, summary){

            let lineActive = $(`.nav-link.active`).data('production-line-id');
            let productionLines = summary.production_lines;

            productionLine = null;
            productionLines.forEach(e => {
                if(e['id'] == lineActive){
                    productionLine = e;
                }
            });

            if(productionLine == null){
                return;
            }
            let partRejectTypes = productionLine.part_data.part_reject_types;
            let rejectSummary = productionLine.reject_summary;
            updateRejectTypeChart(rejectSummary);
            updateTop10Chart(rejectSummary, partRejectTypes);
            console.log(config, value, summary);
        }

        function updateRejectTypeChart(rejectSummary){
            if(!rejectSummary){
                return;
            }
            
            if(rejectSummary && rejectChart){

                dataValues = [
                    (rejectSummary[1] ? rejectSummary[1]['total'] : 0),
                    (rejectSummary[2] ? rejectSummary[2]['total'] : 0),
                    (rejectSummary[3] ? rejectSummary[3]['total'] : 0),
                ];

                rejectChart.data.datasets[0].data = dataValues;
                rejectChart.update();
            }
        }

        function updateTop10Chart(rejectSummary, partRejectTypes){
            if(!rejectSummary || !partRejectTypes){
                return;
            }

            let allRejectTypes = [];

            Object.keys(rejectSummary).forEach(e => {
                
                Object.keys(rejectSummary[e]).forEach(f => {
                    if(f !== 'total'){
                        allRejectTypes.push({
                            id: f,
                            name: findRejectTypeName(partRejectTypes, f),
                            value: rejectSummary[e][f]
                        });
                    }
                });
            })

            console.log(allRejectTypes);
            allRejectTypes.sort( sorting );
            
            let dataLabels = [];
            let dataValues = [];
            
            for (let i = 0; i < 10; i++) {
                if(allRejectTypes[i]){
                    dataLabels.push(allRejectTypes[i].name);
                    dataValues.push(allRejectTypes[i].value);
                }
            }

            if(!top10Chart)
                return;

            top10Chart.data.labels = dataLabels;
            top10Chart.data.datasets[0].data = dataValues;
            top10Chart.update();

            console.log(dataLabels, dataValues);
        }

        function findRejectTypeName(partRejectTypes, reject_id){
            let rejectTypeName = '-';

            partRejectTypes.forEach(e => {
                if(e['id'] == reject_id){
                    rejectTypeName = e['name'];
                }
            });
            return rejectTypeName;
        }

        function sorting(a, b) {
            if (a.value < b.value) {
                return 1;
            }
            if (a.value > b.value) {
                return -1;
            }

            if(a.name < b.name){
                return -1;
            }

            if(a.name > b.name){
                return 1;
            }
            return 0;
        }

        function currentProductionLineChanged(e) {
            if (e) {
                $('.current-production-line-data').data('production-line-id', e.id);
                updateCurrentProductionLineConfigs(e.id);
                //Tab Data
                $('.nav-link').removeClass('active');
                $(`.nav-link[data-production-line-id="${e.id}"`).addClass('active');

                LivePage.updateLiveData();
            }
        }

        function setConfigToCurrentLine(configData) {
            if (LivePage.tabCurrentProductionLine)
                configData['production-line-id'] = LivePage.tabCurrentProductionLine.id;

            currentProductionLineDataConfig.push(configData);
            return configData;
        }

        var currentProductionLineDataConfig = [];
        function updateCurrentProductionLineConfigs(newProductionLineId) {
            currentProductionLineDataConfig.forEach(e => {
                e['production-line-id'] = newProductionLineId;
            });
        };

        var number_production_lines = null;
        function productionChanges(e) {

            if (number_production_lines !== null && number_production_lines !== e.production_lines.length) {
                number_production_lines = e.production_lines.length;
                populateTemplate(e.production_lines);
            } else if (number_production_lines === null) {
                number_production_lines = e.production_lines.length;
                populateTemplate(e.production_lines);
            }

        };

        var rejectChart = null,
            top10Chart = null;
        function populateTemplate(production_lines) {
            let dashboardContainer = $('#dashboard-container');

            if (production_lines.length > 0) {
                dashboardContainer.html($('#template-active-production-line').html());
                populateTabLines(production_lines);

                dashboardContainer.find('.format-text-pcs').data('render', function(e, value) {
                    return `${value} PCS`;
                });

                dashboardContainer.find('.format-text-percentage').data('render', function(e, value) {
                    value = value * 100;
                    return `${value.toFixed(2)} %`;
                });

                rejectChartConfig.initChart();
                top10ChartConfig.initChart();

                rejectChart = rejectChartConfig.chart;
                top10Chart = top10ChartConfig.chart;

                LivePage.clearCallbackPrevValue();
                
                currentProductionLineChanged(production_lines[0]);
            } else {
                dashboardContainer.html($('#template-inactive-production-line').html());
            }
        }

        function populateTabLines(production_lines) {
            let production_line_container = $('#production-line-container');
            production_lines.forEach(function(e) {
                let liElement = $('<li class="nav-item" onclick="LivePage.switchProductionLineTab(this)" data-production-line-id="' + e.id + '"></li>');
                let aElement = $('<a class="nav-link" data-production-line-id="' + e.id + '" aria-current="page" href="#">LINE ' + e.line_no + '</a>');
                liElement.append(aElement);

                production_line_container.append(liElement);
            });
        }

    </script>

    {{-- chart config --}}
    <script>
        var rejectChartConfig = {
            chartCanvasID: 'reject-chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: ['SETTING REJECT', 'PROCESS REJECT', 'MATERIAL REJECT'],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [{
                        type: 'bar',
                        label: 'Count',
                        backgroundColor: '#58006f',
                        borderColor: '#58006f',
                        data: [0, 0, 0],
                    }],
                },
                options: {
                    maintainAspectRatio: true,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

        var top10ChartConfig = {
            chartCanvasID: 'top-10-chart',
            chartConfig: {
                type: 'bar',
                data: {
                    labels: [],
                    animations: {
                        y: {
                            duration: 2000,
                            delay: 500,
                        },
                    },
                    datasets: [{
                        type: 'bar',
                        label: 'Count',
                        backgroundColor: '#ffa000',
                        borderColor: '#ffa000',
                        data: [],
                    }],
                },
                options: {
                    aspectRatio: 5,
                    responsive: true,
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            },
            initChart: function() {
                const ctx = document.getElementById(this.chartCanvasID).getContext('2d');
                this.chart = new Chart(ctx, this.chartConfig);
            }
        };

    </script>
@endsection




@section('modals')
    @parent
    <div>

    </div>
@endsection