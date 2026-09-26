<?php
// Hoja imprimible: comprobante de matrícula (standalone para impresión limpia)
// I3 (U-05). Reutiliza getHistorialMatricula: cabecera + pensiones + colegio + totales.
$cab = $data['hist']['cabecera'];
$tot = $data['hist']['totales'];
$col = $data['hist']['colegio'] ?: [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprobante de matrícula - <?= htmlspecialchars($cab['nombre'].' '.$cab['apellido']) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 18px; }
  .hoja { max-width: 760px; margin: 0 auto; border: 2px solid #1a3b5d; padding: 22px 26px; }
  .toolbar { text-align: right; margin-bottom: 12px; }
  .toolbar button { background: #1a3b5d; color: #fff; border: none; padding: 9px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .encabezado { text-align: center; border-bottom: 3px double #1a3b5d; padding-bottom: 10px; margin-bottom: 12px; }
  .encabezado h2 { margin: 0; color: #1a3b5d; font-size: 18px; }
  .encabezado p { margin: 2px 0; font-size: 11px; color: #555; }
  h3.doc { text-align: center; color: #1a3b5d; margin: 10px 0; font-size: 15px; letter-spacing: 1px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #999; padding: 5px 8px; text-align: left; }
  th { background: #eef3f8; color: #1a3b5d; font-size: 11px; width: 22%; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: bold; border: 1px solid #1a3b5d; }
  .nota { background: #fffbe6; border: 1px solid #d9b600; padding: 8px 10px; font-size: 11px; margin-bottom: 10px; }
  .firmas { display: flex; justify-content: space-between; margin-top: 46px; }
  .firmas div { width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
  .pie { margin-top: 14px; font-size: 10px; color: #777; text-align: center; }
  @media print {
    body { padding: 0; }
    .toolbar { display: none; }
    .hoja { max-width: 100%; border-width: 1px; }
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

  <h3 class="doc">COMPROBANTE DE MATRÍCULA — GESTIÓN <?= htmlspecialchars($cab['gestion']) ?></h3>

  <table>
    <tr><th>Estudiante</th><td><?= htmlspecialchars($cab['nombre'].' '.$cab['apellido']) ?></td><th>CI / RUDE</th><td><?= htmlspecialchars($cab['ci'].' / '.($cab['rude'] ?? '—')) ?></td></tr>
    <tr><th>Curso asignado</th><td><?= htmlspecialchars($cab['curso'] ?? '—') ?> (<?= htmlspecialchars($cab['turno'] ?? '') ?>)</td><th>Tipo</th><td><?= htmlspecialchars($cab['tipo']) ?></td></tr>
    <tr><th>Matrícula N°</th><td><?= htmlspecialchars($cab['id_matricula']) ?> del <?= htmlspecialchars(substr($cab['fecha_matricula'], 0, 10)) ?></td><th>Estado</th><td><span class="badge"><?= htmlspecialchars($cab['estado_inscripcion']) ?></span><?= !empty($cab['motivo_estado']) ? ' — '.htmlspecialchars($cab['motivo_estado']) : '' ?></td></tr>
    <tr><th>Folio legajo</th><td><?= $cab['folio_fisico'] !== null ? str_pad((string)$cab['folio_fisico'], 6, '0', STR_PAD_LEFT) : '—' ?></td><th>Contacto</th><td><?= htmlspecialchars(trim(($cab['cel'] ?? '').' / '.($cab['email'] ?? ''), ' /')) ?: '—' ?></td></tr>
  </table>

  <?php if(!empty($cab['plazo_documentos_hasta'])): ?>
  <div class="nota"><strong>Documentación pendiente</strong> hasta el <?= htmlspecialchars($cab['plazo_documentos_hasta']) ?> (30 días hábiles). El apoderado se compromete a completar: certificado de nacimiento, RUDE y solicitud.</div>
  <?php endif; ?>

  <table>
    <tr><th>Cuotas generadas</th><td><?= $tot['cuotas'] ?> (febrero a noviembre)</td><th>Total gestión</th><td>Bs. <?= number_format($tot['cobrado'] + $tot['deuda'], 2) ?></td></tr>
  </table>

  <div class="firmas">
    <div>Secretaría<br><small>Nombre, firma y sello</small></div>
    <div>Dirección<br><small>Nombre, firma y sello</small></div>
  </div>
  <div class="firmas">
    <div style="width:60%;margin:24px auto 0;">Apoderado (compromiso documental)<br><small>Nombre, CI y firma</small></div>
  </div>

  <p class="pie">Emitido el <?= htmlspecialchars($data['emision']) ?><?= $data['usuario_emisor'] !== '' ? ' por '.htmlspecialchars($data['usuario_emisor']) : '' ?> · Sistema de Gestión Marista</p>
</div>
</body>
</html>
