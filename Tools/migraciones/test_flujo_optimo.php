<?php
// TEST FLUJO-ÓPTIMO 1,2,5: preview + alta con idMatricula (un caso por proceso por die()).
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
require_once 'Controllers/Matricula.php';
require_once 'Controllers/Estudiantes.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION['login'] = true; $_SESSION['idUser'] = 1;
$_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
$_SESSION['userData'] = ['idrol' => 1, 'nombre' => 'Test'];
$_SESSION['csrf_token'] = 'TOK';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$caso = $argv[1] ?? '';
$par = (string)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();

function ctrlMat() {
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new MatriculaModel());
  return $c;
}
if ($caso === 'p1') {
  $_GET = ['ci' => 'CI-NUEVO-99', 'gestion' => '2026', 'paralelo' => $par, 'tipo' => 'Regular', 'estado' => 'Confirmado'];
  ctrlMat()->preview();
} elseif ($caso === 'p2') {
  $ci = $pdo->query("SELECT p.ci FROM persona p JOIN estudiante e ON e.id_persona=p.id_persona WHERE e.status=1 LIMIT 1")->fetchColumn();
  $_GET = ['ci' => $ci, 'gestion' => '2026', 'paralelo' => $par, 'tipo' => 'Regular', 'estado' => 'Inscrito'];
  ctrlMat()->preview();
} elseif ($caso === 'p3') {
  // duplicado: CI con matrícula 2026 existente
  $ci = $pdo->query("SELECT p.ci FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE m.gestion=2026 LIMIT 1")->fetchColumn();
  $_GET = ['ci' => $ci, 'gestion' => '2026', 'paralelo' => $par, 'tipo' => 'Regular', 'estado' => 'Inscrito'];
  ctrlMat()->preview();
} elseif ($caso === 'p4') {
  $ci = 'TEST-FLUJO-01';
  $_POST = ['newStudent' => '1', 'csrf_token' => 'TOK', 'txtCi' => $ci, 'txtRUDE' => '', 'txtNombre' => 'Flujo', 'txtApellido' => 'Test',
    'listSexEst' => 'M', 'dateFNacimiento' => '2015-05-01', 'txtCelular' => '', 'txtEmail' => '',
    'chkMatricular' => '1', 'listParaleloMat' => $par, 'listTipoMat' => 'Regular'];
  $rc = new ReflectionClass('Estudiantes');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new EstudiantesModel());
  $c->setEstudiante();
} elseif ($caso === 'r1') {
  // primera matrícula vía rematricular (alta previa sin chkMatricular)
  $ci2 = 'TEST-FLUJO-02';
  $est = new EstudiantesModel();
  echo 'alta2: '.$est->insertEstudiante($ci2, '', 'Nuevo', 'Flujo', 'Dos', 'M', '', '', 'Dir', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci2, PASSWORD_DEFAULT), 1, null, null, null, null, null, 1)."\n";
  require_once 'Controllers/Matricula.php';
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  $_SESSION['login'] = true; $_SESSION['idUser'] = 1;
  $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
  $_SESSION['userData'] = ['idrol' => 1];
  $_SESSION['csrf_token'] = 'TOK';
  $_POST = ['ci' => $ci2, 'gestion' => '2026', 'paralelo' => $par, 'tipo' => 'Regular', 'csrf_token' => 'TOK'];
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new MatriculaModel());
  $c->rematricular();
} elseif ($caso === 'r2') {
  // CI inexistente -> rechazo claro
  require_once 'Controllers/Matricula.php';
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  $_SESSION['login'] = true; $_SESSION['idUser'] = 1;
  $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
  $_SESSION['userData'] = ['idrol' => 1];
  $_SESSION['csrf_token'] = 'TOK';
  $_POST = ['ci' => 'CI-FANTASMA-00', 'gestion' => '2026', 'paralelo' => $par, 'tipo' => 'Regular', 'csrf_token' => 'TOK'];
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new MatriculaModel());
  $c->rematricular();
} elseif ($caso === 'clean') {  $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
  foreach (['TEST-FLUJO-01', 'TEST-FLUJO-02'] as $ci) {
    $q = $pdo->quote($ci);
    $pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
    $pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
    $eid = $pdo->query("SELECT e.id_estudiante FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q")->fetchColumn();
    if ($eid) { $pdo->exec("DELETE FROM estudiante_inclusion WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM padre WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM estudiante WHERE id_estudiante=".(int)$eid); }
    $pdo->exec("DELETE FROM persona WHERE ci=$q");
  }
  $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
  echo 'left='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-FLUJO-%'")->fetchColumn()."\n";
}
