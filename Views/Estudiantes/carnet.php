<?php
// Carnet estudiantil con QR (hoja imprimible, tamaño credencial CR80 horizontal)
$est = $data['ficha']['estudiante'];
$mat = $data['matricula'];
$foto = $data['foto_url'];
$ini = mb_strtoupper(mb_substr(trim($est['nombre'] ?? ''),0,1).mb_substr(trim($est['apellido'] ?? ''),0,1));
$qrJs = BASE_URL . '/Assets/js/qrcode.min.js';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carnet — <?= htmlspecialchars(trim(($est['nombre'] ?? '').' '.($est['apellido'] ?? ''))) ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 18px; background: #eee; }
  .toolbar { text-align: center; margin-bottom: 14px; }
  .toolbar button { background: #1a3b5d; color: #fff; border: none; padding: 9px 22px; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .carnet {
    width: 85.6mm; height: 54mm; margin: 0 auto;
    background: #fff; border-radius: 3mm; overflow: hidden;
    display: flex; border: 1px solid #bbb;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
  }
  .banda { width: 16mm; background: linear-gradient(180deg, #1a3b5d, #2c5282); color: #fff;
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2mm; padding: 2mm 1mm; }
  .banda .escudo { width: 11mm; height: 11mm; border-radius: 50%; background: #c4a35a; color: #1a3b5d;
    display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; }
  .banda small { font-size: 6px; text-align: center; line-height: 1.2; letter-spacing: .3px; }
  .cuerpo { flex: 1; padding: 2.5mm 3mm; display: flex; gap: 3mm; }
  .foto { width: 20mm; height: 24mm; border-radius: 1.5mm; object-fit: cover; border: 1px solid #999; background: #f4f7fa; flex: 0 0 auto; }
  .foto.ini { display: flex; align-items: center; justify-content: center; background: #1a3b5d; color: #fff; font-weight: 800; font-size: 16px; }
  .datos { flex: 1; min-width: 0; }
  .datos h3 { margin: 0 0 1mm; font-size: 11px; color: #1a3b5d; line-height: 1.15; }
  .datos p { margin: 0 0 .8mm; font-size: 8.5px; color: #333; }
  .datos p b { color: #1a3b5d; }
  .pie-card { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1mm; }
  .qr img, .qr canvas, .qr table { width: 17mm !important; height: 17mm !important; }
  .folio { font-size: 8px; color: #555; text-align: right; }
  .folio b { font-size: 11px; color: #1a3b5d; }
  .nota { max-width: 85.6mm; margin: 10px auto 0; font-size: 11px; color: #555; text-align: center; }
  @media print {
    body { background: #fff; padding: 0; }
    .toolbar, .nota { display: none; }
    .carnet { box-shadow: none; margin: 0; }
    @page { size: auto; margin: 8mm; }
  }
</style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Imprimir carnet</button></div>

<div class="carnet">
  <div class="banda">
    <div class="escudo">M</div>
    <small>COL. MARISTA<br>SS.CC.<br><?= htmlspecialchars($mat['gestion'] ?? '') ?></small>
  </div>
  <div class="cuerpo">
    <?php if($foto !== ''){ ?>
      <img class="foto" src="<?= htmlspecialchars($foto) ?>" alt="foto">
    <?php }else{ ?>
      <div class="foto ini"><?= htmlspecialchars($ini) ?></div>
    <?php } ?>
    <div class="datos">
      <h3><?= htmlspecialchars(trim(($est['nombre'] ?? '').' '.($est['apellido'] ?? ''))) ?></h3>
      <p><b>CI:</b> <?= htmlspecialchars($est['ci'] ?? '') ?> &nbsp; <b>RUDE:</b> <?= htmlspecialchars($est['rude'] ?? '') ?></p>
      <p><b>Curso:</b> <?= htmlspecialchars($mat['curso'] ?? '—') ?> &nbsp; <b>Folio:</b> <?= $est['folio_fisico'] !== null ? str_pad((string)$est['folio_fisico'], 6, '0', STR_PAD_LEFT) : '—' ?></p>
      <div class="pie-card">
        <div class="qr" data-qr="<?= htmlspecialchars($data['verify_url']) ?>"></div>
        <div class="folio">CARNET<br><b><?= htmlspecialchars($est['ci'] ?? '') ?></b><br>Escanee para verificar</div>
      </div>
    </div>
  </div>
</div>
<p class="nota">Recorte por el borde · El QR verifica la identidad en línea.</p>

<script src="<?= $qrJs ?>"></script>
<script>
(function(){
  document.querySelectorAll('.qr').forEach(function(el){
    try{ new QRCode(el, {text: el.getAttribute('data-qr'), width: 128, height: 128, correctLevel: QRCode.CorrectLevel.M}); }catch(e){}
  });
})();
</script>
</body>
</html>
