<?php
// TEST I2-addenda: regla de gestión activa (un caso por proceso porque el controlador hace die()).
// Uso: php test_m02b_gestion.php caso1|caso2|caso3|caso4   (crea/limpia filas TEST-GACT)
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
require_once 'Controllers/Matricula.php';

$caso = $argv[1] ?? '';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function nuevaMatriculaController(array $session, array $post) {
  $_SESSION = $session;
  $_POST = $post;
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $prop = $rc->getProperty('model');
  $prop->setAccessible(true);
  $prop->setValue($c, new MatriculaModel());
  ob_start();
  try { $c->insertNewMatricula(); } catch (Throwable $t) {}
  // insertNewMatricula termina con die(); si llega aquí, no hubo die (no debería pasar)
  return ob_get_clean();
}
function rematController(array $session, array $post) {
  $_SESSION = $session;
  $_POST = $post;
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $prop = $rc->getProperty('model');
  $prop->setAccessible(true);
  $prop->setValue($c, new MatriculaModel());
  ob_start();
  try { $c->rematricular(); } catch (Throwable $t) {}
  return ob_get_clean();
}

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-GACT-01';

if ($caso === 'setup') {
  $est = new EstudiantesModel();
  $r = $est->insertEstudiante($ci, '', 'Nuevo', 'Gact', 'Test', 'M', '', '', 'Dir', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null);
  echo "setup alta: $r\n";
  exit(0);
}
if ($caso === 'clean') {
  $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
  $q = $pdo->quote($ci);
  $pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $pdo->exec("DELETE e FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $pdo->exec("DELETE FROM persona WHERE ci=$q");
  $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
  echo "clean ok\n";
  exit(0);
}

$base = ['intGestion' => '2025', 'listParalelos' => '1', 'listTipoEstudiante' => 'Regular', 'listStateInscripcion' => 'Confirmado', 'newG' => '1', 'txtCi' => $ci, 'idMatricula' => '0'];
$sesNoAdmin = ['login' => true, 'permisosMod' => ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1], 'userData' => ['idrol' => 4]];
$sesAdmin = ['login' => true, 'permisosMod' => ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1], 'userData' => ['idrol' => 1]];

if ($caso === 'caso1') { // no-admin fuera de activa -> bloqueo
  echo nuevaMatriculaController($sesNoAdmin, $base)."\n";
} elseif ($caso === 'caso2') { // admin sin motivo -> bloqueo
  echo nuevaMatriculaController($sesAdmin, $base)."\n";
} elseif ($caso === 'caso3') { // admin con motivo -> ok + motivo guardado
  $post = $base; $post['motivoRectificacion'] = 'Prueba rectificación 2025';
  echo nuevaMatriculaController($sesAdmin, $post)."\n";
  $row = $pdo->query("SELECT estado_inscripcion, motivo_estado FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci' AND m.gestion=2025")->fetch(PDO::FETCH_ASSOC);
  echo 'row2025='.json_encode($row, JSON_UNESCAPED_UNICODE)."\n";
} elseif ($caso === 'caso4') { // remat no-admin fuera de activa -> bloqueo (2025 ya existe tras caso3; usa 2024)
  echo rematController($sesNoAdmin, ['ci' => $ci, 'gestion' => '2024', 'paralelo' => '1', 'tipo' => 'Regular'])."\n";
}
