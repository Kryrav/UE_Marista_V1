<?php
// Hoja de recibo multi-formato: completo | carta (2 copias 1/2 carta) | termica (80mm) + QR
$pen = $data['recibo']['pension'];
$col = $data['recibo']['colegio'] ?: [];
$formato = $data['formato'] ?? '';
$nroFmt = str_pad((string)($pen['nro_recibo'] ?? 0), 6, '0', STR_PAD_LEFT);
$vUrl = $data['verify_url'] ?? '';
date_default_timezone_set('America/La_Paz');
$qrJs = BASE_URL . '/Assets/js/qrcode.min.js';

function bloqueCompleto($pen, $col, $nroFmt, $copia = '') {
ob_start(); ?>
  <div class="encabezado">
    <h2><?= htmlspecialchars($col['nombre_col'] ?? 'Unidad Educativa') ?></h2>
    <p><?= htmlspecialchars(trim(($col['nivel_educativo_col'] ?? '').' - '.($col['tipo_col'] ?? ''), ' -')) ?></p>
    <p><?= htmlspecialchars($col['direccion_col'] ?? '') ?> · <?= htmlspecialchars($col['telefono_col'] ?? '') ?> · <?= htmlspecialchars($col['correo_col'] ?? '') ?></p>
  </div>
  <h3 class="doc">RECIBO DE PAGO DE MENSUALIDAD N° <?= $nroFmt ?><?= $copia !== '' ? ' <small>['.$copia.']</small>' : '' ?></h3>
  <div class="meta">Fecha de emisión: <?= date('d/m/Y') ?>
    <?php if(!empty($pen['cajero_nombre'])){ ?> · Cajero: <?= htmlspecialchars($pen['cajero_nombre']) ?><?php } ?>
  </div>
  <table>
    <tr><th style="width:22%;">Recibido de</th><td><?= htmlspecialchars(trim(($pen['pagador_nombre'] ?? '').' '.($pen['pagador_apellido'] ?? ''))) ?></td><th style="width:18%;">CI / NIT</th><td><?= htmlspecialchars($pen['pagador_ci'] ?? '—') ?></td></tr>
    <tr><th>Parentesco</th><td><?= htmlspecialchars($pen['relacion_pagador'] ?? '—') ?></td><th>Fecha de pago</th><td><?= htmlspecialchars(substr($pen['fecha_pago'] ?? '', 0, 10)) ?></td></tr>
    <tr><th>Tipo de pago</th><td><?= htmlspecialchars($pen['tipo_pago'] ?? '—') ?></td><th>Cód. transacción</th><td><?= htmlspecialchars($pen['codigo'] ?? '—') ?></td></tr>
  </table>
  <table>
    <tr><th style="width:22%;">Estudiante</th><td><?= htmlspecialchars(trim(($pen['nombre_estudiante'] ?? '').' '.($pen['apellido_estudiante'] ?? ''))) ?></td><th style="width:18%;">CI</th><td><?= htmlspecialchars($pen['ci_estudiante'] ?? '—') ?></td></tr>
    <tr><th>Matrícula</th><td><?= htmlspecialchars($pen['id_matricula'] ?? '—') ?></td><th>Curso</th><td><?= htmlspecialchars(trim(($pen['grado_paralelo'] ?? '').' "' .($pen['sigla_paralelo'] ?? '').'"')) ?></td></tr>
    <tr><th>Mes</th><td><?= htmlspecialchars($pen['mes_pension'] ?? '—') ?></td><th>Gestión</th><td><?= htmlspecialchars($pen['gestion'] ?? '—') ?></td></tr>
  </table>
  <table>
    <thead><tr><th style="width:6%;">N°</th><th>Detalle</th><th class="num">Valor (Bs.)</th></tr></thead>
    <tbody><tr>
      <td>1</td>
      <td>Pago de la mensualidad de <?= htmlspecialchars($pen['mes_pension'] ?? '') ?>, gestión <?= htmlspecialchars($pen['gestion'] ?? '') ?>.</td>
      <td class="num"><?= number_format((float)($pen['monto_pagar'] ?? 0), 2) ?></td>
    </tr></tbody>
    <tfoot><tr><th colspan="2" style="text-align:right;">Total cancelado</th><th class="num">Bs. <?= number_format((float)($pen['monto_pagar'] ?? 0), 2) ?></th></tr></tfoot>
  </table>
  <div class="qrblock">
    <div class="qrimg"></div>
    <div class="qrtxt">Verifique este recibo escaneando el QR<br><small>N° <?= $nroFmt ?></small></div>
  </div>
  <p class="nota"><b>Nota:</b> El estudiante puede consultar sus mensualidades en "www.colmarista.com/perfil" con el usuario [<?= htmlspecialchars($pen['usuario_estudiante'] ?? '') ?>] y contraseña [CI].</p>
  <div class="firmas">
    <div>Firma del responsable<br><small>Nombre y sello</small></div>
    <div>Firma del apoderado<br><small>Nombre y CI</small></div>
  </div>
<?php return ob_get_clean(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recibo N° <?= $nroFmt ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 18px; }
  .toolbar { text-align: right; margin-bottom: 12px; }
  .toolbar button, .toolbar a.btn { background: #1a3b5d; color: #fff; border: none; padding: 9px 16px; border-radius: 6px; font-size: 12px; cursor: pointer; text-decoration: none; display: inline-block; margin-left: 6px; }
  .toolbar a.btn.alt { background: #7f8c8d; }
  <?php if($formato === 'termica'){ ?>
  /* Térmica 80mm */
  body { padding: 4px; font-size: 11px; }
  .termica { width: 72mm; margin: 0 auto; }
  .termica h2 { font-size: 14px; text-align: center; margin: 0 0 2px; }
  .termica .centro { text-align: center; }
  .termica hr { border: none; border-top: 1px dashed #333; margin: 6px 0; }
  .termica table { width: 100%; border-collapse: collapse; font-size: 11px; }
  .termica td { padding: 2px 0; vertical-align: top; }
  .termica .total { font-size: 14px; font-weight: bold; }
  .termica .qrimg { text-align: center; margin: 6px 0 2px; }
  .termica .qrimg img, .termica .qrimg canvas, .termica .qrimg table { margin: 0 auto; }
  @page { size: 80mm auto; margin: 3mm; }
  <?php }else{ ?>
  .hoja { max-width: 760px; margin: 0 auto; }
  .encabezado { text-align: center; border-bottom: 3px double #1a3b5d; padding-bottom: 10px; margin-bottom: 12px; }
  .encabezado h2 { margin: 0; color: #1a3b5d; font-size: 18px; }
  .encabezado p { margin: 2px 0; font-size: 11px; color: #555; }
  h3.doc { text-align: center; color: #1a3b5d; margin: 10px 0; font-size: 15px; }
  h3.doc small { color: #7f8c8d; }
  .meta { text-align: right; font-size: 11px; color: #555; margin-bottom: 8px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #999; padding: 5px 8px; text-align: left; vertical-align: top; }
  th { background: #1a3b5d; color: #fff; font-size: 11px; }
  td.num, th.num { text-align: right; }
  .qrblock { display: flex; align-items: center; gap: 12px; margin: 10px 0; padding: 8px; border: 1px dashed #999; border-radius: 8px; }
  .qrimg img, .qrimg canvas { width: 110px; height: 110px; }
  .qrtxt { font-size: 12px; }
  .firmas { display: flex; justify-content: space-between; margin-top: 36px; }
  .firmas div { width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
  .nota { font-size: 11px; margin-top: 10px; }
  .pie { margin-top: 14px; font-size: 10px; color: #777; text-align: center; }
  <?php if($formato === 'carta'){ ?>
  @page { size: letter; margin: 10mm; }
  .copia { page-break-inside: avoid; }
  .corte { border-top: 2px dashed #555; margin: 14px 0; position: relative; }
  .corte span { position: absolute; top: -9px; left: 10px; background: #fff; padding: 0 8px; font-size: 10px; color: #555; }
  <?php } ?>
  <?php } ?>
  @media print {
    body { padding: 0; }
    .toolbar { display: none; }
    .hoja { max-width: 100%; }
    <?php if($formato !== 'termica'){ ?>a { text-decoration: none; color: inherit; }<?php } ?>
  }
</style>
</head>
<body>

<div class="toolbar">
  <span style="font-size:12px;color:#555;">Formato:</span>
  <a class="btn<?= $formato === '' ? '' : ' alt' ?>" href="<?= BASE_URL ?>/Pensiones/recibos/<?= intval($pen['id_pensiones'] ?? $pen['id_pension'] ?? 0) ?>">Completo</a>
  <a class="btn<?= $formato === 'carta' ? '' : ' alt' ?>" href="<?= BASE_URL ?>/Pensiones/recibos/<?= intval($pen['id_pensiones'] ?? $pen['id_pension'] ?? 0) ?>/carta">½ carta ×2</a>
  <a class="btn<?= $formato === 'termica' ? '' : ' alt' ?>" href="<?= BASE_URL ?>/Pensiones/recibos/<?= intval($pen['id_pensiones'] ?? $pen['id_pension'] ?? 0) ?>/termica">Térmica 80mm</a>
  <button onclick="window.print()">Imprimir</button>
</div>

<?php if($formato === 'termica'){ ?>
<div class="termica">
  <h2><?= htmlspecialchars($col['nombre_col'] ?? 'Unidad Educativa') ?></h2>
  <div class="centro"><small><?= htmlspecialchars($col['direccion_col'] ?? '') ?> · <?= htmlspecialchars($col['telefono_col'] ?? '') ?></small></div>
  <hr>
  <div class="centro"><b>RECIBO N° <?= $nroFmt ?></b><br><small>Mensualidad <?= htmlspecialchars($pen['mes_pension'] ?? '') ?> / <?= htmlspecialchars($pen['gestion'] ?? '') ?></small></div>
  <hr>
  <table>
    <tr><td>Estudiante:</td><td><b><?= htmlspecialchars(trim(($pen['nombre_estudiante'] ?? '').' '.($pen['apellido_estudiante'] ?? ''))) ?></b></td></tr>
    <tr><td>CI:</td><td><?= htmlspecialchars($pen['ci_estudiante'] ?? '') ?></td></tr>
    <tr><td>Curso:</td><td><?= htmlspecialchars(trim(($pen['grado_paralelo'] ?? '').' "' .($pen['sigla_paralelo'] ?? '').'"')) ?></td></tr>
    <tr><td>Pagador:</td><td><?= htmlspecialchars(trim(($pen['pagador_nombre'] ?? '').' '.($pen['pagador_apellido'] ?? ''))) ?> (<?= htmlspecialchars($pen['relacion_pagador'] ?? '') ?>)</td></tr>
    <tr><td>Pago:</td><td><?= htmlspecialchars($pen['tipo_pago'] ?? '') ?> <?= htmlspecialchars($pen['codigo'] ?? '') ?></td></tr>
    <tr><td>Fecha:</td><td><?= htmlspecialchars(substr($pen['fecha_pago'] ?? '', 0, 16)) ?></td></tr>
    <tr><td class="total">TOTAL:</td><td class="total">Bs. <?= number_format((float)($pen['monto_pagar'] ?? 0), 2) ?></td></tr>
  </table>
  <?php if($vUrl !== ''){ ?>
  <div class="qrimg" data-qr="<?= htmlspecialchars($vUrl) ?>"></div>
  <div class="centro"><small>Verifique escaneando · N° <?= $nroFmt ?></small></div>
  <?php } ?>
  <hr>
  <div class="centro"><small><?= htmlspecialchars($col['nombre_col'] ?? '') ?> · Gracias por su pago</small></div>
</div>
<?php }else{ ?>
<div class="hoja">
  <div class="copia"><?= bloqueCompleto($pen, $col, $nroFmt, ($formato === 'carta' ? 'ORIGINAL' : '')) ?></div>
  <?php if($formato === 'carta'){ ?>
  <div class="corte"><span>✂ corte aquí — 2 copias por hoja carta</span></div>
  <div class="copia"><?= bloqueCompleto($pen, $col, $nroFmt, 'COPIA') ?></div>
  <?php } ?>
  <p class="pie">Recibo N° <?= $nroFmt ?> · Sistema de Gestión Marista</p>
</div>
<?php } ?>

<?php if($vUrl !== ''){ ?>
<script src="<?= $qrJs ?>"></script>
<script>
(function(){
  var url = <?= json_encode($vUrl) ?>;
  // QR grande del bloque completo/carta
  document.querySelectorAll('.qrblock .qrimg').forEach(function(el){
    try{ new QRCode(el, {text: url, width: 110, height: 110, correctLevel: QRCode.CorrectLevel.M}); }catch(e){}
  });
  // QR térmico
  document.querySelectorAll('.termica .qrimg').forEach(function(el){
    try{ new QRCode(el, {text: url, width: 140, height: 140, correctLevel: QRCode.CorrectLevel.M}); }catch(e){}
  });
})();
</script>
<?php } ?>
</body>
</html>
