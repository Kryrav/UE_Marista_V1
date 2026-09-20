<?php
// Página pública de verificación de recibos (sin template admin)
$v = $data['valido'];
$r = $data['recibo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($data['page_tag']) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 20px; background: #f4f7fa; color: #222; }
  .card { max-width: 520px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,.12); }
  .head { padding: 18px; text-align: center; color: #fff; }
  .head.ok { background: linear-gradient(135deg, #1a3b5d, #2c5282); }
  .head.bad { background: linear-gradient(135deg, #7b241c, #c0392b); }
  .head h2 { margin: 0 0 4px; font-size: 20px; }
  .head p { margin: 0; opacity: .9; font-size: 13px; }
  .body { padding: 18px; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  td { padding: 7px 4px; border-bottom: 1px solid #eee; }
  td.k { color: #777; width: 40%; }
  .monto { font-size: 22px; font-weight: 800; color: #1a3b5d; text-align: center; margin: 10px 0; }
  .badge { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
  .okb { background: #d4edda; color: #155724; }
  .badb { background: #f8d7da; color: #721c24; }
  .pie { text-align: center; font-size: 11px; color: #999; padding: 0 18px 18px; }
</style>
</head>
<body>
<div class="card">
  <div class="head <?= $v ? 'ok' : 'bad' ?>">
    <h2><?= $v ? '✓ Recibo válido' : '✗ No válido' ?></h2>
    <p>Colegio Marista — verificación de pago<?= $v ? (' · Recibo N° ' . str_pad((string)$data['nro'], 6, '0', STR_PAD_LEFT)) : '' ?></p>
  </div>
  <div class="body">
  <?php if($v){ ?>
    <table>
      <tr><td class="k">Estudiante</td><td><b><?= htmlspecialchars($r['estudiante']) ?></b> (CI <?= htmlspecialchars($r['ci_estudiante']) ?>)</td></tr>
      <tr><td class="k">Mensualidad</td><td><?= htmlspecialchars($r['mes']) ?> · Gestión <?= htmlspecialchars($r['gestion']) ?> · <?= htmlspecialchars($r['curso'] ?? '') ?></td></tr>
      <tr><td class="k">Tipo de pago</td><td><?= htmlspecialchars($r['tipo_pago'] ?? '—') ?> · <?= htmlspecialchars(substr($r['fecha_reg_pago'] ?? '', 0, 10)) ?></td></tr>
      <tr><td class="k">Estado</td><td><span class="badge okb">PAGADO</span></td></tr>
    </table>
    <div class="monto">Bs. <?= number_format((float)$r['monto'], 2) ?></div>
  <?php }else{ ?>
    <p style="text-align:center;">El código no corresponde a un recibo válido. Verifique el N° de recibo o acérquese a secretaría.</p>
  <?php } ?>
  </div>
  <p class="pie">Verificación online · <?= htmlspecialchars($r['colegio'] ?? 'Colegio Marista') ?></p>
</div>
</body>
</html>
