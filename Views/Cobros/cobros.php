<?php 
    headerAdmin($data); 
    getModal('modalCobros',$data);
?>
  <main class="app-content">    
      <div class="app-title">
        <div>
            <h1><i class="fas fa-user-tag"></i> <?= $data['page_title'] ?>
                <?php if($_SESSION['permisosMod']['w']){ ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" ><i class="fas fa-plus-circle"></i> Cobro</button>
                <button class="btn btn-primary" type="button" onclick="openModalPago();" ><i class="fas fa-plus-circle"></i> Nuevo Pago</button>
              <?php } ?>
            </h1>

        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Cobros"><?= $data['page_title'] ?></a></li>
        </ul>
      </div>
        <!-- Sector Imágenes -->
        <div class="row">
            <div class="col-md-8">
                <div class="tile">
                    <div class="tile-body">
                        <h4>Cobros: cuotas extra (aparte de mensualidad)</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered w-100" id="tableCobros">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tile">
                    <div class="tile-title">
                        <div class="container">
                            <figure class="highcharts-figure">
                                <div id="container"></div>
                                <p class="highcharts-description">
                                    
                                </p>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sector Cobros -->
        <div class="row">

        </div>
        <!-- Sector Últimos Cobros Realizados -->

        <div class="row">
            
            <div class="col-md-12">
                <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                    <h4>Últimos cobros Realizados:</h4>
                    <table class="table table-hover table-bordered" id="tableUsuarios">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Cobro</th>
                            <th>Monto Bs.</th>
                            <th>Fecha</th>
                            <th>Forma de pago:</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td>Luis Arce</td>
                            <td>Katacora</td>
                            <td>Cuota Cortinas</td>
                            <td>10</td>
                            <td>12-10-2024</td>
                            <td>Efectivo</td>
                            <td>
                        
                                <button class="btn btn-primary  btn-sm btnEditUsuario"  title="Ver Pago"><i class="far fa-eye"></i></button>
                                <button class="btn btn-secundary  btn-sm btnEditUsuario"  title="Imprimir Pago"><i class="fa fa-file-pdf-o"></i></button>
                                <button class="btn btn-danger  btn-sm btnEditUsuario"  title="Borrar Pago"><i class="fas far fa-trash-alt"></i></button>                       
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Carlos Pablito</td>
                            <td>Henández Vera</td>
                            <td>Cuota Cortinas</td>
                            <td>10</td>
                            <td>12-10-2024</td>
                            <td>Efectivo</td>
                            <td>
                        
                                <button class="btn btn-primary  btn-sm btnEditUsuario"  title="Ver Pago"><i class="far fa-eye"></i></button>
                                <button class="btn btn-secundary  btn-sm btnEditUsuario"  title="Imprimir Pago"><i class="fa fa-file-pdf-o"></i></button>
                                <button class="btn btn-danger  btn-sm btnEditUsuario"  title="Borrar Pago"><i class="fas far fa-trash-alt"></i></button>                       
                            </td>
                        
                            
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Juan Ramon</td>
                            <td>Quintana</td>
                            <td>Cuota Cortinas</td>
                            <td>10</td>
                            <td>12-10-2024</td>
                            <td>Efectivo</td>
                            <td>
                        
                                <button class="btn btn-primary  btn-sm btnEditUsuario"  title="Ver Pago"><i class="far fa-eye"></i></button>
                                <button class="btn btn-secundary  btn-sm btnEditUsuario"  title="Imprimir Pago"><i class="fa fa-file-pdf-o"></i></button>
                                <button class="btn btn-danger  btn-sm btnEditUsuario"  title="Borrar Pago"><i class="fas far fa-trash-alt"></i></button>                       
                            </td>
                            
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Evo</td>
                            <td>Morales Aima</td>
                            <td>Cuota Cortinas</td>
                            <td>10</td>
                            <td>12-10-2024</td>
                            <td>Efectivo</td>
                            <td>
                        
                                <button class="btn btn-primary  btn-sm btnEditUsuario"  title="Ver Pago"><i class="far fa-eye"></i></button>
                                <button class="btn btn-secundary  btn-sm btnEditUsuario"  title="Imprimir Pago"><i class="fa fa-file-pdf-o"></i></button>
                                <button class="btn btn-danger  btn-sm btnEditUsuario"  title="Borrar Pago"><i class="fas far fa-trash-alt"></i></button>                       
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Carlos Pablito</td>
                            <td>Henández Vera</td>
                            <td>Cuota Cortinas</td>
                            <td>10</td>
                            <td>12-10-2024</td>
                            <td>Efectivo</td>
                            <td>
                        
                                <button class="btn btn-primary  btn-sm btnEditUsuario"  title="Ver Pago"><i class="far fa-eye"></i></button>
                                <button class="btn btn-secundary  btn-sm btnEditUsuario"  title="Imprimir Pago"><i class="fa fa-file-pdf-o"></i></button>
                                <button class="btn btn-danger  btn-sm btnEditUsuario"  title="Borrar Pago"><i class="fas far fa-trash-alt"></i></button>                       
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title">
                        <div class="container">
                            <figure class="highcharts-figure">
                                <div id="container1"></div>
                                <p class="highcharts-description">
                                    
                                </p>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </main>
<?php footerAdmin($data); ?>


<script>
(function (H) {
    H.seriesTypes.pie.prototype.animate = function (init) {
        const series = this,
            chart = series.chart,
            points = series.points,
            {
                animation
            } = series.options,
            {
                startAngleRad
            } = series;

        function fanAnimate(point, startAngleRad) {
            const graphic = point.graphic,
                args = point.shapeArgs;

            if (graphic && args) {

                graphic
                    // Set inital animation values
                    .attr({
                        start: startAngleRad,
                        end: startAngleRad,
                        opacity: 1
                    })
                    // Animate to the final position
                    .animate({
                        start: args.start,
                        end: args.end
                    }, {
                        duration: animation.duration / points.length
                    }, function () {
                        // On complete, start animating the next point
                        if (points[point.index + 1]) {
                            fanAnimate(points[point.index + 1], args.end);
                        }
                        // On the last point, fade in the data labels, then
                        // apply the inner size
                        if (point.index === series.points.length - 1) {
                            series.dataLabelsGroup.animate({
                                opacity: 1
                            },
                            void 0,
                            function () {
                                points.forEach(point => {
                                    point.opacity = 1;
                                });
                                series.update({
                                    enableMouseTracking: true
                                }, false);
                                chart.update({
                                    plotOptions: {
                                        pie: {
                                            innerSize: '50%',
                                            borderRadius: 8
                                        }
                                    }
                                });
                            });
                        }
                    });
            }
        }

        if (init) {
            // Hide points on init
            points.forEach(point => {
                point.opacity = 0;
            });
        } else {
            fanAnimate(points[0], startAngleRad);
        }
    };
}(Highcharts));

Highcharts.chart('container', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'Pagos Realizados por curso',
        align: 'left'
    },
    subtitle: {
        text: 'Marista "SSCC"',
        align: 'left'
    },
    tooltip: {
        headerFormat: '',
        pointFormat:
            '<span style="color:{point.color}">\u25cf</span> ' +
            '{point.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            borderWidth: 2,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b><br>{point.percentage}%',
                distance: 20
            }
        }
    },
    series: [{
        // Disable mouse tracking on load, enable after custom animation
        enableMouseTracking: false,
        animation: {
            duration: 1500
        },
        colorByPoint: true,
        data: [{
            name: 'Enero - Febrero ',
            y: 20.00
        }, {
            name: 'Marzo - Abril ',
            y: 18.6
        }, {
            name: 'Mayo - Junio',
            y: 15.4
        }, {
            name: 'Julio - Agosto',
            y: 26.0
        }, {
            name: 'Septiembre - Octubre',
            y: 10.0
        }, {
            name: 'Noviembre - Diciembre',
            y: 10.0
        }
        ]
    }]
});


</script>

<script>
    // PARA OTRA IMAGEN -----------------------------------------------------------------------------------------
    Highcharts.chart('container1', {

title: {
    text: 'Pagos por año',
    align: 'left'
},

subtitle: {
    text: 'NARA SERVICES.',
    align: 'left'
},

yAxis: {
    title: {
        text: 'Monto Bs.'
    }
},

xAxis: {
    accessibility: {
        rangeDescription: 'Range: 2012 to 2022'
    }
},

legend: {
    layout: 'vertical',
    align: 'right',
    verticalAlign: 'middle'
},

plotOptions: {
    series: {
        label: {
            connectorAllowed: false
        },
        pointStart: 2018
    }
},

series: [{
    name: 'Mensualidades',
    data: [
        431, 486, 654, 811, 112, 142
       
    ]
},{
    name: 'Pagos',
    data: [
        451, 386, 254, 511, 112, 242
       
    ]
}

],

responsive: {
    rules: [{
        condition: {
            maxWidth: 500
        },
        chartOptions: {
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
            }
        }
    }]
}

});

</script>

    