<?php
// REVIEW Estudiantes — batería funcional por caso (die() obliga un caso por proceso).
// Uso: php test_rev_estudiantes.php t1|t2|t3|t4|t5|t6|t6b|clean
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
require_once 'Controllers/Estudiantes.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function ctrlEst() {
  $rc = new ReflectionClass('Estudiantes');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new EstudiantesModel());
  return [$c, $rc];
}
function baseSession() {
  $_SESSION['login'] = true;
  $_SESSION['idUser'] = 1;
  $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
  $_SESSION['userData'] = ['idrol' => 1, 'nombre' => 'Test'];
  $_SESSION['csrf_token'] = 'TOK';
}
$caso = $argv[1] ?? '';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-REV-01';

if ($caso === 't1') {
  [$c] = ctrlEst();
  $m = new ReflectionMethod('Estudiantes', 'validar'); $m->setAccessible(true);
  $o = (new ReflectionClass('Estudiantes'))->newInstanceWithoutConstructor();
  $cases = [
    'minima-ok' => [['txtCi' => '1', 'txtNombre' => 'A', 'txtApellido' => 'B', 'dateFNacimiento' => '2015-01-01'], null],
    'sin-ci' => [['txtNombre' => 'A', 'txtApellido' => 'B', 'dateFNacimiento' => '2015-01-01'], 'El C.I. es obligatorio.'],
    'email-malo' => [['txtCi' => '1', 'txtNombre' => 'A', 'txtApellido' => 'B', 'dateFNacimiento' => '2015-01-01', 'txtEmail' => 'x'], 'El email no tiene un formato válido.'],
    'cel-corto' => [['txtCi' => '1', 'txtNombre' => 'A', 'txtApellido' => 'B', 'dateFNacimiento' => '2015-01-01', 'txtCelular' => '123'], 'El celular debe tener entre 7 y 9 dígitos.'],
    'futura' => [['txtCi' => '1', 'txtNombre' => 'A', 'txtApellido' => 'B', 'dateFNacimiento' => '2099-01-01'], 'La fecha de nacimiento no puede ser futura.'],
  ];
  foreach ($cases as $n => [$in, $exp]) {
    $got = $m->invoke($o, $in, true);
    echo ($got === $exp ? 'OK' : "FALLO($got)")." $n\n";
  }
} elseif ($caso === 't2') {
  // alta mínima + matrícula inmediata vía controlador (auditoría uid=1)
  baseSession();
  [$c] = ctrlEst();
  $_POST = ['newStudent' => '1', 'csrf_token' => 'TOK', 'txtCi' => $ci, 'txtRUDE' => '', 'txtNombre' => 'Rev', 'txtApellido' => 'Test',
    'listSexEst' => 'M', 'dateFNacimiento' => '2015-05-01', 'txtCelular' => '', 'txtEmail' => '',
    'chkMatricular' => '1', 'listParaleloMat' => (string)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn(), 'listTipoMat' => 'Regular'];
  ob_start(); $c->setEstudiante(); $out = ob_get_clean();
  echo $out."\n";
  $row = $pdo->query("SELECT e.created_by, m.created_by mc, m.id_user, m.estado_inscripcion FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona LEFT JOIN matricula m ON m.id_estudiante=e.id_estudiante AND m.gestion=2026 WHERE p.ci=".$pdo->quote($ci))->fetch(PDO::FETCH_ASSOC);
  echo 'audit='.json_encode($row)."\n";
} elseif ($caso === 't3') {
  baseSession();
  [$c] = ctrlEst();
  ob_start(); $c->getEstudiantesAll(); $out = ob_get_clean();
  $a = json_decode($out, true);
  echo 'total='.count($a)."\n";
  $keys = ['foto','ci','legajo','nombre','apellido','curso_actual','tutores','email','cel','status_estudiante','options','curso_raw','estado_raw','legajo_flag'];
  $missing = array_diff($keys, array_keys($a[0] ?? []));
  echo 'keys='.($missing ? 'FALLO '.implode(',', $missing) : 'OK')."\n";
} elseif ($caso === 't4') {
  baseSession();
  $eid = (int)$pdo->query("SELECT e.id_estudiante FROM estudiante e WHERE e.status=1 LIMIT 1")->fetchColumn();
  [$c] = ctrlEst();
  ob_start(); $c->getEstudiante($eid); $out = ob_get_clean();
  $o = json_decode($out, true);
  echo 'ficha='.($o['status'] ? 'ok' : 'FALLO').' tutores='.count($o['ficha']['tutores'] ?? []).' mats='.count($o['ficha']['matriculas'] ?? []).' inc='.json_encode($o['ficha']['inclusion']['tiene_discapacidad'] ?? '?')."\n";
} elseif ($caso === 't5') {
  baseSession();
  [$c] = ctrlEst();
  $eid = (int)$pdo->query("SELECT m.id_estudiante FROM matricula m WHERE m.status=1 LIMIT 1")->fetchColumn();
  $_POST = ['idEstudiante' => (string)$eid, 'csrf_token' => 'TOK'];
  ob_start(); $c->delEstudiante(); $out = ob_get_clean();
  echo $out."\n";
} elseif ($caso === 't6') {
  // renders: historial + carnet + rezagados + comprobante(matricula) con datos reales (solo lectura)
  baseSession();
  [$c] = ctrlEst();
  $mid = (int)$pdo->query("SELECT id_matricula FROM matricula LIMIT 1")->fetchColumn();
  $eid = (int)$pdo->query("SELECT id_estudiante FROM estudiante WHERE status=1 LIMIT 1")->fetchColumn();
  foreach ([['historial', [$mid]], ['carnet', [$eid]], ['rezagados', []]] as [$mt, $args]) {
    ob_start(); $c->$mt(...$args); $html = ob_get_clean();
    echo "$mt bytes=".strlen($html)." print=". (strpos($html, 'window.print') !== false ? 'ok' : 'FALLO') ."\n";
  }
} elseif ($caso === 't6b') {
  baseSession();
  require_once 'Controllers/Matricula.php';
  $rc = new ReflectionClass('Matricula');
  $mc = $rc->newInstanceWithoutConstructor();
  $mp = $rc->getProperty('model'); $mp->setAccessible(true);
  $mp->setValue($mc, new MatriculaModel());
  $mid = (int)$pdo->query("SELECT id_matricula FROM matricula LIMIT 1")->fetchColumn();
  ob_start(); $mc->comprobante($mid); $html = ob_get_clean();
  echo 'comprobante bytes='.strlen($html).' print='.(strpos($html, 'window.print') !== false ? 'ok' : 'FALLO')."\n";
} elseif ($caso === 'clean') {
  $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
  $q = $pdo->quote($ci);
  $pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $eid = $pdo->query("SELECT e.id_estudiante FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q")->fetchColumn();
  if ($eid) { $pdo->exec("DELETE FROM estudiante_inclusion WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM padre WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM estudiante WHERE id_estudiante=".(int)$eid); }
  $pdo->exec("DELETE FROM persona WHERE ci=$q");
  $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
  echo 'left='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-REV-%'")->fetchColumn()."\n";
}
