<?php
// Hoja imprimible: rezago escolar 2+ años para Comisión Técnica (standalone)
// I3 (N-03). $data['rows']: ci, nombre, apellido, curso, fnacimiento, edad, esperada, rezago, gestion, tutores.
$rows = $data['rows'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Rezago escolar — Comisión Técnica</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 18px; }
  .hoja { max-width: 900px; margin: 0 auto; }
  .toolbar { text-align: right; margin-bottom: 12px; }
  .toolbar button { background: #1a3b5d; color: #fff; border: none; padding: 9px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  h2.doc { text-align: center; color: #1a3b5d; font-size: 16px; margin: 6px 0; }
  p.sub { text-align: center; color: #555; font-size: 11px; margin: 0 0 10px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  th, td { border: 1px solid #999; padding: 5px 8px; text-align: left; font-size: 11px; }
  th { background: #1a3b5d; color: #fff; }
  td.num, th.num { text-align: center; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: bold; background: #f8d7da; color: #721c24; border: 1px solid #721c24; }
  .firmas { display: flex; justify-content: space-between; margin-top: 42px; }
  .firmas div { width: 30%; text-align: center; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
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

  <h2 class="doc">REZAGO ESCOLAR (2+ AÑOS) — COMISIÓN TÉCNICA</h2>
  <p class="sub">Estudiantes activos con desfase edad/grado ≥ 2 años · <?= count($rows) ?> caso(s) · Referencia: Inicial 4-5a, Primaria 1° = 6a</p>

  <table>
    <thead><tr><th>N°</th><th>CI</th><th>Apellidos y nombres</th><th>Curso actual</th><th>F. nac.</th><th class="num">Edad</th><th class="num">Esperada</th><th class="num">Rezago</th><th>Tutores</th></tr></thead>
    <tbody>
    <?php $n = 0; foreach($rows as $r): $n++; ?>
      <tr>
        <td class="num"><?= $n ?></td>
        <td><?= htmlspecialchars($r['ci']) ?></td>
        <td><?= htmlspecialchars($r['apellido'].' '.$r['nombre']) ?></td>
        <td><?= htmlspecialchars(trim(($r['nivel'] ?? '').' '.$r['grado'].' "'.$r['sigla'].'"')) ?> (<?= htmlspecialchars($r['gestion']) ?>)</td>
        <td><?= htmlspecialchars(substr($r['fnacimiento'], 0, 10)) ?></td>
        <td class="num"><?= (int)$r['edad'] ?></td>
        <td class="num"><?= (int)$r['esperada'] ?></td>
        <td class="num"><span class="badge">+<?= (int)$r['rezago'] ?>a</span></td>
        <td class="num"><?= (int)($r['tutores'] ?? 0) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if($n === 0): ?>
      <tr><td colspan="9" style="text-align:center;color:#777;">Sin casos de rezago en la gestión activa.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="firmas">
    <div>Dirección<br><small>Firma y sello</small></div>
    <div>Comisión Técnica<br><small>Firma y sello</small></div>
    <div>Regencia<br><small>Firma y sello</small></div>
  </div>

  <p class="pie">Emitido el <?= htmlspecialchars($data['emision']) ?><?= $data['usuario_emisor'] !== '' ? ' por '.htmlspecialchars($data['usuario_emisor']) : '' ?> · Sistema de Gestión Marista</p>
</div>
</body>
</html>
