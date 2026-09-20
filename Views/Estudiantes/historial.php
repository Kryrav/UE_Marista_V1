<?php
// Hoja imprimible: historial de pagos por matrícula (standalone para impresión limpia)
$cab = $data['hist']['cabecera'];
$pens = $data['hist']['pensiones'];
$col = $data['hist']['colegio'] ?: [];
$tot = $data['hist']['totales'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de pagos - Matrícula <?= htmlspecialchars($cab['id_matricula']) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 18px; }
  .hoja { max-width: 760px; margin: 0 auto; }
  .toolbar { text-align: right; margin-bottom: 12px; }
  .toolbar button { background: #1a3b5d; color: #fff; border: none; padding: 9px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .encabezado { text-align: center; border-bottom: 3px double #1a3b5d; padding-bottom: 10px; margin-bottom: 12px; }
  .encabezado h2 { margin: 0; color: #1a3b5d; font-size: 18px; }
  .encabezado p { margin: 2px 0; font-size: 11px; color: #555; }
  h3.doc { text-align: center; color: #1a3b5d; margin: 10px 0; font-size: 15px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #999; padding: 5px 8px; text-align: left; }
  th { background: #1a3b5d; color: #fff; font-size: 11px; }
  td.num, th.num { text-align: right; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: bold; }
  .pagado { background: #d4edda; color: #155724; border: 1px solid #155724; }
  .pendiente { background: #f8d7da; color: #721c24; border: 1px solid #721c24; }
  tr.pend td { background: #fff8f0; }
  tfoot th { background: #f0f0f0; color: #222; }
  .totales td { font-weight: bold; }
  .firmas { display: flex; justify-content: space-between; margin-top: 42px; }
  .firmas div { width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
  .pie { margin-top: 14px; font-size: 10px; color: #777; text-align: center; }
  @media print {
    body { padding: 0; }
    .toolbar { display: none; }
    .hoja { max-width: 100%; }
  }
</style>
</head>
<body>
<div class="hoja">
  <div class="toolbar"><button onclick="window.print()">Imprimir</button></div>

  <div class="encabezado">
    <h2><?= htmlspecialchars($col['nombre_col'] ?? 'Unidad Educativa') ?></h2>
    <p><?= htmlspecialchars(trim(($col['nivel_educativo_col'] ?? '').' - '.($col['tipo_col'] ?? ''), ' -')) ?></p>
    <p><?= htmlspecialchars($col['direccion_col'] ?? '') ?> · <?= htmlspecialchars($col['telefono_col'] ?? '') ?> · <?= htmlspecialchars($col['correo_col'] ?? '') ?></p>
  </div>

  <h3 class="doc">HISTORIAL DE PAGOS — MATRÍCULA N° <?= htmlspecialchars($cab['id_matricula']) ?></h3>

  <table>
    <tr><th style="width:22%;">Estudiante</th><td><?= htmlspecialchars($cab['nombre'].' '.$cab['apellido']) ?></td><th style="width:18%;">CI / RUDE</th><td><?= htmlspecialchars($cab['ci'].' / '.$cab['rude']) ?></td></tr>
    <tr><th>Curso</th><td><?= htmlspecialchars($cab['curso'] ?? '—') ?> (<?= htmlspecialchars($cab['turno'] ?? '') ?>)</td><th>Gestión</th><td><?= htmlspecialchars($cab['gestion']) ?> · <?= htmlspecialchars($cab['tipo']) ?></td></tr>
    <tr><th>Folio legajo</th><td><?= $cab['folio_fisico'] !== null ? str_pad((string)$cab['folio_fisico'], 6, '0', STR_PAD_LEFT) : '—' ?></td><th>Inscripción</th><td><?= htmlspecialchars($cab['estado_inscripcion']) ?> (<?= htmlspecialchars(substr($cab['fecha_matricula'], 0, 10)) ?>)</td></tr>
  </table>

  <table>
    <thead><tr><th style="width:6%;">N°</th><th>Mes</th><th class="num">Monto (Bs.)</th><th>Estado</th><th>Tipo pago</th><th>Fecha pago</th></tr></thead>
    <tbody>
    <?php $n = 0; foreach($pens as $r): $n++; $ok = ($r['estado_pago'] == 1); ?>
      <tr class="<?= $ok ? '' : 'pend' ?>">
        <td><?= $n ?></td>
        <td><?= htmlspecialchars($r['mes']) ?></td>
        <td class="num"><?= number_format((float)$r['monto'], 2) ?></td>
        <td><span class="badge <?= $ok ? 'pagado' : 'pendiente' ?>"><?= $ok ? 'Pagado' : 'Pendiente' ?></span></td>
        <td><?= htmlspecialchars($r['tipo_pago'] ?? '—') ?></td>
        <td><?= $r['fecha_reg_pago'] ? htmlspecialchars(substr($r['fecha_reg_pago'], 0, 10)) : '—' ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th colspan="2">Totales (<?= $tot['cuotas'] ?> cuotas)</th><th class="num"><?= number_format($tot['cobrado'] + $tot['deuda'], 2) ?></th><th colspan="3">Pagado Bs. <?= number_format($tot['cobrado'], 2) ?> (<?= $tot['pagadas'] ?>) · Adeudado Bs. <?= number_format($tot['deuda'], 2) ?> (<?= $tot['pendientes'] ?>)</th></tr>
    </tfoot>
  </table>

  <div class="firmas">
    <div>Firma del responsable<br><small>Nombre y sello</small></div>
    <div>Firma del apoderado<br><small>Nombre y CI</small></div>
  </div>

  <p class="pie">Emitido el <?= htmlspecialchars($data['emision']) ?><?= $data['usuario_emisor'] !== '' ? ' por '.htmlspecialchars($data['usuario_emisor']) : '' ?> · Sistema de Gestión Marista</p>
</div>
</body>
</html>
