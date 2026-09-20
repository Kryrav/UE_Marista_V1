<?php
// Hoja imprimible directa: nómina del curso (auto-imprime al abrir)
$c = $data['lista']['curso'];
$est = $data['lista']['estudiantes'];
$nivel = trim($c['nivel'] ?? '');
$titulo = (strcasecmp($nivel, 'Inicial') == 0)
    ? ('Nivel Inicial "' . ($c['sigla'] ?? '') . '"')
    : ($nivel . ' ' . ($c['grado'] ?? '') . ' "' . ($c['sigla'] ?? '') . '"');
$varones = 0; $mujeres = 0;
foreach($est as $e){ if(($e['sexo'] ?? 'M') === 'F'){ $mujeres++; } else { $varones++; } }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nómina <?= htmlspecialchars($titulo) ?> <?= intval($data['gestion']) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 18px; }
  .hoja { max-width: 760px; margin: 0 auto; }
  .toolbar { text-align: right; margin-bottom: 12px; }
  .toolbar button { background: #1a3b5d; color: #fff; border: none; padding: 9px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .encabezado { text-align: center; border-bottom: 3px double #1a3b5d; padding-bottom: 8px; margin-bottom: 10px; }
  .encabezado h2 { margin: 0; color: #1a3b5d; font-size: 17px; }
  .encabezado p { margin: 2px 0; font-size: 11px; color: #555; }
  h3.doc { text-align: center; color: #1a3b5d; margin: 8px 0; font-size: 14px; }
  table.info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  table.info th, table.info td { border: 1px solid #999; padding: 4px 8px; text-align: left; font-size: 11px; }
  table.info th { background: #1a3b5d; color: #fff; width: 16%; }
  table.nomina { width: 100%; border-collapse: collapse; }
  table.nomina th, table.nomina td { border: 1px solid #999; padding: 4px 6px; font-size: 11px; }
  table.nomina thead th { background: #1a3b5d; color: #fff; }
  table.nomina td.num { text-align: center; }
  td.firma { min-width: 130px; }
  .totales { margin-top: 8px; font-size: 11px; }
  .firmas { display: flex; justify-content: space-between; margin-top: 40px; }
  .firmas div { width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
  .pie { margin-top: 12px; font-size: 10px; color: #777; text-align: center; }
  @media print {
    body { padding: 0; }
    .toolbar { display: none; }
    .hoja { max-width: 100%; }
    table.nomina { page-break-inside: auto; }
    table.nomina tr { page-break-inside: avoid; }
  }
</style>
</head>
<body onload="window.print()">
<div class="hoja">
  <div class="toolbar"><button onclick="window.print()">Imprimir</button></div>

  <div class="encabezado">
    <h2>Colegio Marista "Sagrados Corazones"</h2>
    <p>Roboré · Santa Cruz · Bolivia</p>
  </div>

  <h3 class="doc">NÓMINA DE ESTUDIANTES — GESTIÓN <?= intval($data['gestion']) ?></h3>

  <table class="info">
    <tr><th>Curso</th><td><?= htmlspecialchars($titulo) ?></td><th>Turno</th><td><?= htmlspecialchars($c['turno'] ?? '—') ?></td></tr>
    <tr><th>Tutor</th><td><?= htmlspecialchars($c['tutor'] ?? 'Sin asignación') ?></td><th>Inscritos</th><td><?= count($est) ?> / <?= intval($c['cupo'] ?? 0) ?> (Varones: <?= $varones ?> · Mujeres: <?= $mujeres ?>)</td></tr>
  </table>

  <table class="nomina">
    <thead><tr><th style="width:5%;">N°</th><th>Apellidos y nombres</th><th style="width:14%;">CI</th><th style="width:14%;">RUDE</th><th style="width:6%;">Sexo</th><th style="width:22%;">Firma</th></tr></thead>
    <tbody>
    <?php $n = 0; foreach($est as $e): $n++; ?>
      <tr>
        <td class="num"><?= $n ?></td>
        <td><?= htmlspecialchars(trim(($e['apellido'] ?? '').' '.($e['nombre'] ?? ''))) ?></td>
        <td><?= htmlspecialchars($e['ci'] ?? '') ?></td>
        <td><?= htmlspecialchars($e['rude'] ?? '') ?></td>
        <td class="num"><?= htmlspecialchars($e['sexo'] ?? '') ?></td>
        <td class="firma"></td>
      </tr>
    <?php endforeach;
    if($n === 0){ echo '<tr><td colspan="6" style="text-align:center;color:#777;">Sin estudiantes inscritos.</td></tr>'; } ?>
    </tbody>
  </table>

  <div class="firmas">
    <div>Firma del tutor<br><small>Nombre y sello</small></div>
    <div>Firma de dirección<br><small>Nombre y sello</small></div>
  </div>

  <p class="pie">Emitido el <?= htmlspecialchars($data['emision']) ?> · Sistema de Gestión Marista</p>
</div>
</body>
</html>
