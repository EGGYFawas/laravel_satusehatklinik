@extends('dashboard.app')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">{{ now()->format('l, d F Y') }}</h1>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Selamat Datang di Modul Rawat Jalan,
                        <span><b>{{ auth()->user()->name }}</b></span>
                    </h3>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $jumlahPasienUmum }}</h3>
                            <p> Total Kunjungan Umum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $jumlahPasienBpjs }}</h3>
                            <p> Total Kunjungan BPJS</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $jumlahPasien }}</h3>
                            <p>Jumlah Pasien Baru</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>
            <!-- /.row (main row) -->

            <div class="row">
                <!-- /.col (LEFT) -->
                <div class="col-md-6">
                    <!-- BAR CHART -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users mx-1"></i> Kunjungan Pasien PerHari</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                                        class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col (RIGHT) -->

                <div class="col-md-6">
                    <!-- BAR CHART -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users mx-1"></i>Income PerBulan</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                                        class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartdiv"></div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>

            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- HTML -->

    <!-- /.content -->
@endsection

@push('scripts')
    <script>
        $(function() {
            //-------------
            //- BAR CHART -
            //-------------
            var barChartCanvas = $('#barChart').get(0).getContext('2d');
            var barChartData = {
                labels: ['11/01/2025', '12/01/2025', '13/01/2025', '14/01/2025', '15/01/2025', '16/01/2025'],
                datasets: [{
                        label: 'Jumlah Pasien Umum',
                        backgroundColor: '#007bff',
                        borderColor: '#00a65a',
                        data: [65, 59, 80, 81, 56, 55]
                    },
                    {
                        label: 'Jumlah Pasien BPJS',
                        backgroundColor: '#f39c12',
                        borderColor: '#f39c12',
                        data: [45, 39, 60, 71, 46, 45]
                    }
                ]
            };
            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };
            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            });
        });
    </script>

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <!-- Chart code -->
    <script>
        am5.ready(function() {

            // Create root element
            var root = am5.Root.new("chartdiv");

            // Set themes
            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            // Create chart
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "panX",
                wheelY: "zoomX",
                paddingLeft: 0,
                layout: root.verticalLayout
            }));

            // Sample data
            var data = [{
                    day: "Januari",
                    pendapatan: 1200000,
                    pengeluaran: 1100000
                },
                {
                    day: "Februari",
                    pendapatan: 1500000,
                    pengeluaran: 1300000
                },
                {
                    day: "Maret",
                    pendapatan: 2000000,
                    pengeluaran: 1800000
                },
                {
                    day: "April",
                    pendapatan: 2200000,
                    pengeluaran: 1900000
                },
                {
                    day: "Mei",
                    pendapatan: 2600000,
                    pengeluaran: 2200000
                }
            ];

            // Format numbers to "1,2 Jt" or "900 Ribu"
            function formatCurrency(value) {
                if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + " Jt";
                } else {
                    return (value / 1000).toFixed(0) + " Ribu";
                }
            }

            // Create axes
            var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "day",
                renderer: am5xy.AxisRendererY.new(root, {
                    inversed: true,
                    cellStartLocation: 0.1,
                    cellEndLocation: 0.9
                })
            }));

            yAxis.data.setAll(data);

            var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererX.new(root, {
                    strokeOpacity: 0.1,
                    minGridDistance: 50
                }),
                min: 0
            }));

            // Add series
            function createSeries(field, name) {
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueXField: field,
                    categoryYField: "day",
                    sequencedInterpolation: true,
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "[bold]{name}[/]\n{categoryY}: {valueX.formatNumber('#,###')}"
                    })
                }));

                series.columns.template.setAll({
                    height: am5.p100,
                    strokeOpacity: 0
                });

                // Add labels for values
                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationX: 1,
                        locationY: 0.5,
                        sprite: am5.Label.new(root, {
                            centerY: am5.p50,
                            text: "{valueX.formatCustom()}",
                            populateText: true
                        })
                    });
                });

                // Format data labels
                series.adapters.add("valueX", function(value) {
                    return formatCurrency(value);
                });

                series.data.setAll(data);
                series.appear();
                return series;
            }

            createSeries("pendapatan", "Pendapatan");
            createSeries("pengeluaran", "Pengeluaran");

            // Add legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            legend.data.setAll(chart.series.values);

            // Add cursor
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "zoomY"
            }));
            cursor.lineY.set("forceHidden", true);
            cursor.lineX.set("forceHidden", true);

            // Make chart animate on load
            chart.appear(1000, 100);

        }); // end am5.ready()
    </script>
@endpush
