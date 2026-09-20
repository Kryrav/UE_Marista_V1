<?php
    headerAdmin($data);
    $gests = $data['gestiones'] ?? [];
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/reportes.css">
<main class="app-content">
    <div class="app-title no-print">
        <div>
            <h1><i class="fa fa-file-text-o"></i> <?= $data['page_title'] ?></h1>
            <p class="text-muted">Información financiera y estadística para dirección · <button class="btn btn-secondary btn-sm" onclick="window.print()"><i class="fa fa-print"></i> Imprimir sección</button></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="<?= base_url(); ?>/Reportes"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <div class="print-head">
      <h2>Colegio Marista "Sagrados Corazones" — <span id="printTitulo">Reporte</span></h2>
      <p>Emitido: <?= date('d/m/Y H:i') ?> · <span id="printGestion"></span></p>
    </div>

    <div class="row">
      <div class="col-md-3 no-print">
        <div class="tile rep-menu">
          <div class="form-group">
            <label for="repGestion"><strong>Gestión</strong></label>
            <select id="repGestion" class="form-control form-control-sm">
              <?php foreach($gests as $gg){ ?><option value="<?= $gg['gestion'] ?>" <?= $gg['status']==1?'selected':'' ?>><?= $gg['gestion'] ?><?= $gg['status']==1?' (activa)':'' ?></option><?php } ?>
            </select>
          </div>
          <div class="nav flex-column nav-pills" role="tablist">
            <a class="nav-link active" data-toggle="pill" href="#rep-resumen" role="tab">Resumen financiero</a>
            <a class="nav-link" data-toggle="pill" href="#rep-mensual" role="tab">Mensual por mes</a>
            <a class="nav-link" data-toggle="pill" href="#rep-anual" role="tab">Anual comparativo</a>
            <a class="nav-link" data-toggle="pill" href="#rep-curso" role="tab">Por curso</a>
            <a class="nav-link" data-toggle="pill" href="#rep-cajero" role="tab">Por cajero</a>
            <a class="nav-link" data-toggle="pill" href="#rep-matricula" role="tab">Matrícula</a>
            <a class="nav-link" data-toggle="pill" href="#rep-estudiantes" role="tab">Estudiantes</a>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="tab-content">
          <div class="tab-pane fade show active" id="rep-resumen" role="tabpanel">
            <div class="tile"><h5>Resumen financiero <small class="text-muted gestion-lbl"></small></h5>
              <div class="row text-center" id="repResumenKpis"></div>
              <div class="men-canvas"><canvas id="repChartAnualMini"></canvas></div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-mensual" role="tabpanel">
            <div class="tile"><h5>Cobranza mensual <small class="text-muted gestion-lbl"></small></h5>
              <div class="table-responsive"><table class="table table-sm table-bordered w-100" id="tblMensual">
                <thead><tr><th>Mes</th><th class="text-right">Cuotas</th><th class="text-right">Pagadas</th><th class="text-right">Cobrado Bs.</th><th class="text-right">Adeudado Bs.</th><th class="text-right">Vencido Bs.</th></tr></thead>
                <tbody></tbody>
              </table></div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-anual" role="tabpanel">
            <div class="tile"><h5>Comparativo anual</h5>
              <div class="men-canvas"><canvas id="repChartAnual"></canvas></div>
              <div class="table-responsive mt-3"><table class="table table-sm table-bordered w-100" id="tblAnual">
                <thead><tr><th>Gestión</th><th class="text-right">Matrículas</th><th class="text-right">Cobrado Bs.</th><th class="text-right">Adeudado Bs.</th><th class="text-right">Vencido Bs.</th><th class="text-right">% cobro</th></tr></thead>
                <tbody></tbody>
              </table></div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-curso" role="tabpanel">
            <div class="tile"><h5>Cobranza por curso <small class="text-muted gestion-lbl"></small></h5>
              <div class="table-responsive"><table class="table table-sm table-bordered w-100" id="tblCurso">
                <thead><tr><th>Curso</th><th>Tutor</th><th class="text-right">Matr.</th><th class="text-right">Cobrado Bs.</th><th class="text-right">Adeudado Bs.</th></tr></thead>
                <tbody></tbody>
              </table></div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-cajero" role="tabpanel">
            <div class="tile"><h5>Cobros por cajero <small class="text-muted gestion-lbl"></small></h5>
              <div class="table-responsive"><table class="table table-sm table-bordered w-100" id="tblCajero">
                <thead><tr><th>Cajero</th><th class="text-right">Cobros</th><th class="text-right">Total Bs.</th></tr></thead>
                <tbody></tbody>
              </table></div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-matricula" role="tabpanel">
            <div class="tile"><h5>Matrícula <small class="text-muted gestion-lbl"></small></h5>
              <div class="row">
                <div class="col-md-7"><h6>Por paralelo</h6><div class="table-responsive"><table class="table table-sm table-bordered w-100" id="tblMatCur">
                  <thead><tr><th>Curso</th><th class="text-right">Cupo</th><th class="text-right">Inscritos</th><th class="text-right">Ocup. %</th></tr></thead><tbody></tbody>
                </table></div></div>
                <div class="col-md-5"><h6>Por tipo y sexo</h6><div id="matTipoSexo"></div></div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="rep-estudiantes" role="tabpanel">
            <div class="tile"><h5>Estadística de estudiantes</h5>
              <div class="row">
                <div class="col-md-6"><h6>Por estado y registro</h6><div id="estEstados"></div></div>
                <div class="col-md-6"><h6>Por sexo</h6><div class="men-canvas men-canvas-sm"><canvas id="repChartSexo"></canvas></div></div>
              </div>
              <h6 class="mt-3">Sin tutores (<span id="estSinTutN"></span>)</h6>
              <div class="table-responsive"><table class="table table-sm table-bordered w-100" id="tblSinTut">
                <thead><tr><th>Estudiante</th><th>CI</th></tr></thead><tbody></tbody>
              </table></div>
              <p class="text-muted">Legajos sin folio: <b id="estSinFolio"></b></p>
            </div>
          </div>
        </div>
      </div>
    </div>
</main>
<?php footerAdmin($data); ?>
<script src="<?= media(); ?>/js/chart.umd.min.js"></script>
