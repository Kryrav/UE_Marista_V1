<?php
    headerAdmin($data);
    getModal('modalPensiones',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/mensualidades.css">
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fas fa-money-bill-wave"></i> <?= $data['page_title'] ?>
            <?php if($_SESSION['permisosMod']['w']){ ?>
                <button class="btn btn-primary" type="button" onclick="openModalPago();" ><i class="fas fa-plus-circle"></i> Nuevo pago</button>
            <?php } ?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Pensiones"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <!-- KPIs -->
    <div class="row" id="kpiMensualidades">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="widget-small primary">
                <i class="icon fa fa-check-circle fa-3x"></i>
                <div class="info"><h4>Cobrado <span id="kpiGestionLbl">gestión</span></h4><p><b id="kpiCobrado">Bs. 0</b></p></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="widget-small warning">
                <i class="icon fa fa-clock-o fa-3x"></i>
                <div class="info"><h4>Adeudado <span id="kpiGestionLbl2">gestión</span></h4><p><b id="kpiAdeudado">Bs. 0</b></p></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="widget-small danger">
                <i class="icon fa fa-exclamation-triangle fa-3x"></i>
                <div class="info"><h4>Vencido (morosidad)</h4><p><b id="kpiVencido">Bs. 0</b><br><small id="kpiVencidas"></small></p></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="widget-small info">
                <i class="icon fa fa-history fa-3x"></i>
                <div class="info"><h4>Cobrado histórico</h4><p><b id="kpiHist">Bs. 0</b></p></div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="tile men-chart">
                <h5>Estado de mensualidades <small class="text-muted" id="chartDonaSub"></small></h5>
                <div class="men-canvas"><canvas id="chartEstado"></canvas></div>
                <div class="men-legend" id="legendEstado"></div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="tile men-chart">
                <h5>Cobro por mes <small class="text-muted" id="chartBarrasSub"></small></h5>
                <div class="men-canvas men-canvas-bar"><canvas id="chartMeses"></canvas></div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills tile mb-0" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn nav-link mx-1 px-4 active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><i class="fa fa-search"></i> Consultar estudiante</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn nav-link mx-1 px-4" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><i class="fa fa-check-circle"></i> Cobradas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn nav-link mx-1 px-4" id="pills-mora-tab" data-toggle="pill" data-target="#pills-mora" type="button" role="tab" aria-controls="pills-mora" aria-selected="false"><i class="fa fa-exclamation-triangle"></i> Morosidad <span class="badge badge-danger" id="badgeMora">0</span></button>
        </li>
    </ul>

    <div class="tab-content tile" id="pills-tabContent">
        <!-- Consultar -->
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-center">Estado de mensualidades del estudiante</h4>
                    <p><strong>CI del estudiante: </strong><span id="celCiEstudiante">*</span><br>
                    <strong>Nombre completo: </strong><span id="celNombreEstudiante"></span></p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="PensionesEstudianteCi">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Gestión</th>
                                    <th>Curso</th>
                                    <th>Mes</th>
                                    <th>Vence</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Recibo</th>
                                    <th>Operaciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaPensionesEstudiante">
                                <tr><td colspan="9" class="text-center text-muted">Use «Nuevo pago» e ingrese el CI para consultar.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cobradas -->
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            <h4>Últimas mensualidades cobradas</h4>
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100" id="tablePensionesPagadas">
                    <thead>
                    <tr>
                        <th>Recibo</th>
                        <th>CI</th>
                        <th>Matrícula</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Curso</th>
                        <th>Gestión</th>
                        <th>Mes</th>
                        <th>Cajero</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Morosidad -->
        <div class="tab-pane fade" id="pills-mora" role="tabpanel" aria-labelledby="pills-mora-tab">
            <h4>Mensualidades vencidas <small class="text-muted">(pendientes pasada su fecha de vencimiento)</small></h4>
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100" id="tableMorosidad">
                    <thead>
                    <tr>
                        <th>CI</th>
                        <th>Estudiante</th>
                        <th>Curso</th>
                        <th>Gestión</th>
                        <th>Mes</th>
                        <th>Venció</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>
<script src="<?= media(); ?>/js/chart.umd.min.js"></script>
