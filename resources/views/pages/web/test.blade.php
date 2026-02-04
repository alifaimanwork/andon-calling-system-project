@extends('layouts.guest')

@section('body')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            Test Date Range Picker
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="testDateRangePicker" class="form-label">DateRange Picker Test</label>
                <input type="text" class="form-control" id="testDateRangePicker">
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <i class="fa-duotone fa-alicorn"></i> Test Card
        </div>
        <div class="card-body">

            Test card body
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Launch demo modal
            </button>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            Chart Test
        </div>
        <div class="card-body">
            <canvas id="testChart" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="card my-4">
        <div class="card-header">
            Datatable Test
        </div>
        <div class="card-body">
            <table id="testTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            Pisang
                        </th>
                        <th>
                            Emas
                        </th>
                        <th>
                            Dibawa
                        </th>
                        <th>
                            Belayar
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            1
                        </td>
                        <td>
                            Masak
                        </td>
                        <td>
                            Sebiji
                        </td>
                        <td>
                            Di atas
                        </td>
                        <td>
                            Peti
                        </td>
                    </tr>
                    <tr>
                        <td>
                            2
                        </td>
                        <td>
                            Hutang
                        </td>
                        <td>
                            Emas
                        </td>
                        <td>
                            Boleh
                        </td>
                        <td>
                            Dibayar
                        </td>
                    </tr>
                    <tr>
                        <td>
                            3
                        </td>
                        <td>
                            Hutang
                        </td>
                        <td>
                            Budi
                        </td>
                        <td>
                            Dibawa
                        </td>
                        <td>
                            Mati
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('modals')
@parent

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    console.log('jQuery ' + $().jquery + ' Loaded');

    $(document).ready(function() {
        testDateRangePicker();
        testChartJs();
        testDatatable();

    });
</script>
<script>
    function testDateRangePicker() {
        $('#testDateRangePicker').daterangepicker();
    }
</script>
<script>
    const chartLabels = ['Pisang', 'Emas', 'Dibawa', 'Belayar'];
    const CHART_COLORS = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };
    const CHART_COLORS_FILL = {
        red: 'rgba(255, 99, 132, 0.5)',
        orange: 'rgba(255, 159, 64, 0.5)',
        yellow: 'rgba(255, 205, 86, 0.5)',
        green: 'rgba(75, 192, 192, 0.5)',
        blue: 'rgba(54, 162, 235, 0.5)',
        purple: 'rgba(153, 102, 255, 0.5)',
        grey: 'rgba(201, 203, 207, 0.5)'
    };
    const data = {
        labels: ['January', 'February', 'March', ],
        datasets: [{
            data: [3, 2, 5]
        }]
    };
    const chartData = {
        labels: chartLabels,
        datasets: [{
                label: 'Masak',
                data: [5, 1, 3, 7],
                borderColor: CHART_COLORS.red,
                backgroundColor: CHART_COLORS_FILL.red,
            },
            {
                label: 'Sebiji',
                data: [6, 5, 1, 3],
                borderColor: CHART_COLORS.blue,
                backgroundColor: CHART_COLORS_FILL.blue,
            },
            {
                label: 'Di Atas',
                data: [3, 2, 6, 8],
                borderColor: CHART_COLORS.green,
                backgroundColor: CHART_COLORS_FILL.green,
            },
            {
                label: 'Peti',
                data: [0, 1, 2, 3],
                borderColor: CHART_COLORS.orange,
                backgroundColor: CHART_COLORS_FILL.orange,
            }

        ]
    };
    const annotation = {
        type: 'box',
        backgroundColor: 'rgba(0, 0, 0, 0.2)',
        borderWidth: 1,
        borderColor: '#F27173',
        yMin: 30,
        yMax: 80,
        xMax: 2,
        xMin: 5,
        label: {
            enabled: true,
            // content: Utils.getImage(),
            width: 150,
            height: 150,
            position: 'center'
        }
    };
    const config = {
        plugins: [annotationPlugin],
        type: 'line',
        data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                annotation: {
                    annotations: {
                        annotation
                    }
                }
            }
        }
    };

    const chartConfig = {

        plugins: [ChartDataLabels, annotationPlugin],
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                annotation: {
                    annotations: {
                        box1: {
                            // Indicates the type of annotation
                            type: 'box',
                            xMin: 1,
                            xMax: 2,
                            yMin: 50,
                            yMax: 70,
                            backgroundColor: 'rgba(255, 99, 132, 0.25)'
                        }
                    }
                },
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js with Datalabels Plugin'
                }
            }
        },
    };

    function testChartJs() {
        const ctx = document.getElementById('testChart').getContext('2d');
        const testChart = new Chart(ctx, chartConfig);

    }
</script>

<script>
    function testDatatable() {
        $('#testTable').DataTable();
    }
</script>

@endsection