<?php
// Verificación pública de identidad (QR del carnet). Sin datos sensibles.
$v = $data['valido'];
$e = $data['est'] ?? [];
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
  .badge { display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
  .okb { background: #d4edda; color: #155724; }
  .inab { background: #fef3cd; color: #856404; }
  .badb { background: #f8d7da; color: #721c24; }
  .pie { text-align: center; font-size: 11px; color: #999; padding: 0 18px 18px; }
</style>
</head>
<body>
<div class="card">
  <div class="head <?= $v ? 'ok' : 'bad' ?>">
    <h2><?= $v ? '✓ Estudiante verificado' : '✗ No válido' ?></h2>
    <p>Colegio Marista — credencial estudiantil</p>
  </div>
  <div class="body">
  <?php if($v){ ?>
    <table>
      <tr><td class="k">Estudiante</td><td><b><?= htmlspecialchars($e['nombre']) ?></b></td></tr>
      <tr><td class="k">CI</td><td><?= htmlspecialchars($e['ci']) ?></td></tr>
      <tr><td class="k">RUDE</td><td><?= htmlspecialchars($e['rude']) ?></td></tr>
      <?php if($e['folio'] !== null){ ?><tr><td class="k">Folio</td><td><?= str_pad((string)$e['folio'], 6, '0', STR_PAD_LEFT) ?></td></tr><?php } ?>
      <tr><td class="k">Estado</td><td><?= $e['estado'] == 1 ? '<span class="badge okb">ACTIVO</span>' : ($e['estado'] == 2 ? '<span class="badge inab">INACTIVO</span>' : '<span class="badge badb">INACTIVO</span>') ?></td></tr>
      <?php if(!empty($e['matricula'])){ ?><tr><td class="k">Matrícula</td><td>Gestión <?= htmlspecialchars($e['matricula']['gestion']) ?> · <?= htmlspecialchars($e['matricula']['curso'] ?? '') ?></td></tr><?php } ?>
    </table>
  <?php }else{ ?>
    <p style="text-align:center;">El código no corresponde a una credencial válida. Acérquese a secretaría.</p>
  <?php } ?>
  </div>
  <p class="pie">Verificación online · Colegio Marista</p>
</div>
</body>
</html>
