<?php headerAdmin($data);
$st = $data['stats'] ?? [];
$g = $st['gestion_activa']['gestion'] ?? '—';
$fmt = function($n){ return number_format((float)$n, 2); };
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/dashboard.css">
    <main class="app-content">
      <div class="app-title">
        <div><h1><i class="fa fa-dashboard"></i><?= $data['page_title'] ?></h1>
        <p class="text-muted">Gestión activa: <strong><?= htmlspecialchars($g) ?></strong> · Colegio Marista "Sagrados Corazones"</p></div>
        <ul class="app-breadcrumb breadcrumb"><li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li><li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard">Dashboard</a></li></ul>
      </div>

      <!-- KPIs principales -->
      <div class="row">
        <div class="col-md-3 col-sm-6 mb-3"><div class="widget-small primary"><i class="icon fa fa-graduation-cap fa-3x"></i><div class="info"><h4>Estudiantes</h4><p><b><?= $st['estudiantes'] ?? 0 ?></b><?php if(!empty($st['estudiantes_inactivos'])){ ?> <small>(<?= $st['estudiantes_inactivos'] ?> inactivos)</small><?php } ?></p></div></div></div>
        <div class="col-md-3 col-sm-6 mb-3"><div class="widget-small info"><i class="icon fa fa-users fa-3x"></i><div class="info"><h4>Tutores</h4><p><b><?= $st['tutores'] ?? 0 ?></b></p></div></div></div>
        <div class="col-md-3 col-sm-6 mb-3"><div class="widget-small warning"><i class="icon fa fa-id-card-o fa-3x"></i><div class="info"><h4>Matrículas <?= htmlspecialchars($g) ?></h4><p><b><?= $st['matriculas_gestion'] ?? 0 ?></b></p></div></div></div>
        <div class="col-md-3 col-sm-6 mb-3"><div class="widget-small danger"><i class="icon fa fa-money fa-3x"></i><div class="info"><h4>Cobrado <?= htmlspecialchars($g) ?> Bs.</h4><p><b><?= $fmt($st['cobrado_gestion'] ?? 0) ?></b></p></div></div></div>
      </div>

      <!-- Financiero -->
      <div class="row">
        <div class="col-md-8 mb-3">
          <div class="tile dash-chart">
            <h5>Cobro por mes <small class="text-muted">gestión <?= htmlspecialchars($g) ?> · Bs.</small></h5>
            <div class="dash-canvas"><canvas id="dashMeses"></canvas></div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="tile dash-chart">
            <h5>Estado <?= htmlspecialchars($g) ?></h5>
            <div class="dash-canvas"><canvas id="dashEstado"></canvas></div>
            <div class="men-legend" id="dashLegend"></div>
          </div>
        </div>
      </div>

      <!-- Operativo -->
      <div class="row">
        <div class="col-md-4 mb-3">
          <div class="tile">
            <h5><i class="fa fa-exclamation-triangle text-danger"></i> Morosidad <span class="badge badge-danger"><?= $st['mora_n'] ?? 0 ?></span></h5>
            <p class="mb-1">Total vencido: <b class="text-danger">Bs. <?= $fmt($st['mora_bs'] ?? 0) ?></b></p>
            <?php if(!empty($st['top_morosos'])){ ?>
            <div class="table-responsive"><table class="table table-sm">
              <thead><tr><th>Estudiante</th><th class="text-right">Cuotas</th><th class="text-right">Deuda</th></tr></thead>
              <tbody>
              <?php foreach($st['top_morosos'] as $m){ ?>
                <tr><td><?= htmlspecialchars($m['estudiante']) ?><br><small class="text-muted">CI <?= htmlspecialchars($m['ci']) ?></small></td><td class="text-right"><?= $m['cuotas'] ?></td><td class="text-right">Bs. <?= $fmt($m['deuda']) ?></td></tr>
              <?php } ?>
              </tbody>
            </table></div>
            <?php }else{ ?><p class="text-success mb-0"><i class="fa fa-check-circle"></i> Sin morosidad. ¡Excelente!</p><?php } ?>
            <a href="<?= base_url(); ?>/Pensiones" class="btn btn-danger btn-sm mt-2">Ver morosidad</a>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="tile">
            <h5><i class="fa fa-chalkboard-teacher"></i> Ocupación por paralelo</h5>
            <?php foreach(($st['ocupacion'] ?: []) as $o){
              $tot = intval($o['total_inscritos'] ?? 0); $cup = intval($o['cupo'] ?? 0);
              $pct = $cup > 0 ? min(100, round($tot/$cup*100)) : 0;
              $cls = $pct >= 100 ? 'bg-danger' : ($pct >= 85 ? 'bg-warning' : 'bg-success');
              $nom = trim(($o['nivel'] ?? '').' '.($o['grado'] ?? '').' "' .($o['sigla'] ?? '').'"');
            ?>
            <div class="mb-2"><div class="d-flex justify-content-between"><small><b><?= htmlspecialchars($nom) ?></b> · <?= htmlspecialchars($o['tutor'] ?? '') ?></small><small><?= $tot ?>/<?= $cup ?></small></div>
            <div class="progress" style="height:7px;"><div class="progress-bar <?= $cls ?>" style="width:<?= $pct ?>%"></div></div></div>
            <?php } ?>
            <a href="<?= base_url(); ?>/Cursos" class="btn btn-primary btn-sm mt-1">Ver cursos</a>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="tile">
            <h5><i class="fa fa-bell-o"></i> Pendientes operativos</h5>
            <div class="dash-alert"><i class="fa fa-users text-info"></i><span><b><?= $st['sin_tutores'] ?? 0 ?></b> estudiantes sin tutor asignado</span><a href="<?= base_url(); ?>/Tutores" class="btn btn-outline-info btn-sm">Asignar</a></div>
            <div class="dash-alert"><i class="fa fa-archive text-warning"></i><span><b><?= $st['sin_folio'] ?? 0 ?></b> legajos sin folio</span><a href="<?= base_url(); ?>/Estudiantes" class="btn btn-outline-warning btn-sm">Ver</a></div>
            <div class="dash-alert"><i class="fa fa-chalkboard-teacher text-secondary"></i><span><b><?= $st['sin_tutor_curso'] ?? 0 ?></b> paralelos sin tutor docente</span><a href="<?= base_url(); ?>/Cursos" class="btn btn-outline-secondary btn-sm">Ver</a></div>
            <div class="dash-alert"><i class="fa fa-address-book-o text-primary"></i><span><b><?= $st['docentes'] ?? 0 ?></b> docentes activos</span><a href="<?= base_url(); ?>/Docentes" class="btn btn-outline-primary btn-sm">Ver</a></div>
          </div>
        </div>
      </div>

      <!-- Últimos pagos -->
      <div class="row">
        <div class="col-md-12 mb-3">
          <div class="tile">
            <h5><i class="fa fa-money"></i> Últimos pagos registrados <a href="<?= base_url(); ?>/Pensiones" class="btn btn-info btn-sm float-right">Ir a mensualidades</a></h5>
            <div class="table-responsive"><table class="table table-sm table-hover">
              <thead><tr><th>Recibo</th><th>Estudiante</th><th>Mes</th><th>Gestión</th><th>Tipo</th><th class="text-right">Monto</th><th>Fecha</th></tr></thead>
              <tbody>
              <?php foreach(($st['ultimos_pagos'] ?: []) as $p){ ?>
                <tr><td><b><?= str_pad((string)$p['nro_recibo'], 6, '0', STR_PAD_LEFT) ?></b></td><td><?= htmlspecialchars($p['estudiante']) ?></td><td><?= htmlspecialchars($p['mes']) ?></td><td><?= htmlspecialchars($p['gestion']) ?></td><td><?= htmlspecialchars($p['tipo_pago'] ?? '') ?></td><td class="text-right">Bs. <?= $fmt($p['monto']) ?></td><td><small><?= htmlspecialchars(substr($p['fecha_reg_pago'] ?? '', 0, 16)) ?></small></td></tr>
              <?php } ?>
              <?php if(empty($st['ultimos_pagos'])){ ?><tr><td colspan="7" class="text-center text-muted">Sin pagos registrados.</td></tr><?php } ?>
              </tbody>
            </table></div>
          </div>
        </div>
      </div>
    </main>
<?php footerAdmin($data); ?>
<script src="<?= media(); ?>/js/chart.umd.min.js"></script>
<script>
window.DASH = {
  gestion: <?= json_encode($g) ?>,
  labels: <?= json_encode($st['serie_labels'] ?? []) ?>,
  cobrado: <?= json_encode($st['serie_cob'] ?? []) ?>,
  adeudado: <?= json_encode($st['serie_ade'] ?? []) ?>,
  dona: [<?= (float)($st['cobrado_gestion'] ?? 0) ?>, <?= max(0, (float)($st['adeudado_gestion'] ?? 0) - (float)($st['vencido_gestion'] ?? 0)) ?>, <?= (float)($st['vencido_gestion'] ?? 0) ?>]
};
</script>
