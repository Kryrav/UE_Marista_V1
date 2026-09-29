<?php
// GOLDEN Fase 4: captura salidas de Dashboard/Reportes ANTES de deduplicar SQL.
// Uso: php test_golden_finanzas.php capture|compare
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
$modo = $argv[1] ?? 'capture';
$file = __DIR__.'/golden_finanzas.json';
if ($modo === 'capture') {
  $d = new DashboardModel();
  $r = new ReportesModel();
  $out = [
    'stats' => $d->stats(),
    'resumen' => $r->resumenFinanciero(2026),
    'mensual' => $r->mensual(2026),
    'anual' => $r->anual(),
    'porCurso' => $r->porCurso(2026),
    'matriculaStats' => $r->matriculaStats(2026),
    'estudiantesStats' => $r->estudiantesStats(),
  ];
  file_put_contents($file, json_encode($out, JSON_UNESCAPED_UNICODE));
  echo 'captured '.strlen(json_encode($out))." bytes\n";
} else {
  $old = json_decode(file_get_contents($file), true);
  $d = new DashboardModel();
  $r = new ReportesModel();
  $now = [
    'stats' => $d->stats(),
    'resumen' => $r->resumenFinanciero(2026),
    'mensual' => $r->mensual(2026),
    'anual' => $r->anual(),
    'porCurso' => $r->porCurso(2026),
    'matriculaStats' => $r->matriculaStats(2026),
    'estudiantesStats' => $r->estudiantesStats(),
  ];
  $a = json_encode($old, JSON_UNESCAPED_UNICODE);
  $b = json_encode($now, JSON_UNESCAPED_UNICODE);
  if ($a === $b) { echo "GOLDEN OK (".strlen($b)." bytes idénticos)\n"; exit(0); }
  // diff por sección
  foreach ($now as $k => $v) {
    $x = json_encode($old[$k] ?? null, JSON_UNESCAPED_UNICODE);
    $y = json_encode($v, JSON_UNESCAPED_UNICODE);
    echo ($x === $y ? 'OK   ' : 'DIF  ').$k."\n";
  }
  exit(1);
}
