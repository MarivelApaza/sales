@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1 dashboard-card card-clientes">
                <div class="card-icon dashboard-icon bg-primary">
                    <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Clientes</h4>
                    </div>
                    <div class="card-body">
                        {{ $totales['clients'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1 dashboard-card card-productos">
                <div class="card-icon dashboard-icon bg-primary">
                    <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Productos</h4>
                    </div>
                    <div class="card-body">
                        {{ $totales['products'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1 dashboard-card card-compras">
                <div class="card-icon dashboard-icon bg-primary">
                    <i class="far fa-file"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Compras</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($montosTotal['compras'], 2) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1 dashboard-card card-ventas">
                <div class="card-icon dashboard-icon bg-primary">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Ventas</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($montosTotal['ventas'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if ($ventasPorSemana || $comprasPorSemana)
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Compras y Ventas por Semana</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasPorSemana" width="804" height="375" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        @endif

        @if ($ventas || $compras)
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Compras y Ventas por Mes</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasPorMes" width="804" height="375" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-12">
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('container', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Productos con Stock Bajo'
                },
                xAxis: {
                    categories: {!! json_encode($productos->pluck('producto')) !!}
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Stock'
                    }
                },
                legend: {
                    reversed: true
                },
                plotOptions: {
                    series: {
                        stacking: 'normal'
                    }
                },
                series: [{
                    name: 'Stock',
                    data: {!! json_encode($productos->pluck('stock')) !!}
                }]
            });

            if (document.getElementById('ventasPorSemana')) {
                ventasSemana()
            }

            //ventas
            var dataVenta = @json($ventas);
            var dataCompra = @json($compras);

            // Verifica si hay datos de ventas o compras
            var hayDatosVenta = dataVenta && Object.keys(dataVenta).length > 0;
            var hayDatosCompra = dataCompra && Object.keys(dataCompra).length > 0;
            var ctx = document.getElementById('ventasPorMes').getContext('2d');

// Gradiente para Ventas (café/verde-crema)
var gradientVentas = ctx.createLinearGradient(0, 0, 0, 400);
gradientVentas.addColorStop(0, 'rgba(122, 74, 46, 0.9)'); // café intenso
gradientVentas.addColorStop(0.5, 'rgba(122, 74, 46, 0.6)'); 

// Gradiente para Compras (azul suave / crema)
var gradientCompras = ctx.createLinearGradient(0, 0, 0, 400);
gradientCompras.addColorStop(0, 'rgba(255, 74, 74, 0.9)'); // azul intenso
gradientCompras.addColorStop(0.5, 'rgba(151, 78, 78, 0.6)');


            if (hayDatosVenta || hayDatosCompra) {
                var ctx = document.getElementById('ventasPorMes').getContext('2d');
                var datasets = [];

                // Sales data
                if (hayDatosVenta) {
                    Object.keys(dataVenta).forEach(function(year, index) {
                        datasets.push({
                        label: 'Ventas ' + year,
                        data: Object.values(dataVenta[year]),
                        backgroundColor: gradientVentas,
                        borderRadius: 6,
                        borderWidth: 1
                    });

                    });
                }

                // Purchases data
                if (hayDatosCompra) {
                    Object.keys(dataCompra).forEach(function(year, index) {
                        datasets.push({
                        label: 'Compras ' + year,
                        data: Object.values(dataCompra[year]),
                        backgroundColor: gradientCompras,
                        borderRadius: 6,
                        borderWidth: 1
                    });
                    });
                }

                var labels = hayDatosVenta ? Object.keys(dataVenta[Object.keys(dataVenta)[0]]) : (hayDatosCompra ?
                    Object.keys(
                        dataCompra[Object.keys(dataCompra)[0]]) : []);

                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                // Si no hay datos, puedes realizar alguna acción de manejo de error o simplemente mostrar un mensaje
                console.log('No hay datos disponibles para mostrar el gráfico.');
            }
        });

        function ventasSemana() {
            var ctx = document.getElementById('ventasPorSemana').getContext('2d');

            var ventasData = {!! json_encode($ventasPorSemana) !!};
            var comprasData = {!! json_encode($comprasPorSemana) !!};

            var labels = ventasData.map(function(item) {
                return item.diaEnEspanol;
            });

            var ventasValores = ventasData.map(function(item) {
                return item.total;
            });

            var comprasValores = comprasData.map(function(item) {
                return item.total;
            });

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Ventas por semana',
                            data: ventasValores,
                            backgroundColor: 'rgba(250, 187, 145, 1)',
                            borderColor: 'rgba(146, 112, 72, 1)',
                            borderWidth: 1,
                            borderRadius: 8
                        },
                        {
                            label: 'Compras por semana',
                            data: comprasValores,
                            backgroundColor: 'rgba(241, 157, 192, 1)',
                            borderColor: 'rgba(139, 57, 82, 1)',
                            borderWidth: 1,
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@stop
