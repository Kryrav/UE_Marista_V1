<?php
// TEST integración Fase SVC: controladores refactorizados contra BD real.
// Un caso por proceso (die()). Limpia sus filas de prueba (serie TEST-SVC-*).
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION['login'] = true; $_SESSION['idUser'] = 1;
$_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
$_SESSION['userData'] = ['idrol' => 1, 'nombre' => 'Test', 'id_persona' => 1];
$_SESSION['csrf_token'] = 'TOK';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
function ctrl($cls, $modelCls) {
  require_once "Controllers/$cls.php";
  $rc = new ReflectionClass($cls);
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new $modelCls());
  return $c;
}
$caso = $argv[1] ?? '';
if ($caso === 'g1') {
  // Tutores save vía servicio (estudiante scratch con matrícula para vínculo válido)
  $est = new EstudiantesModel();
  $ci = 'TEST-SVC-T1';
  echo 'alta: '.$est->insertEstudiante($ci, '', 'Nuevo', 'Svc', 'Tut', 'M', '', '', 'Dir', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null, 1)."\n";
  $eid = (int)$pdo->query("SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci'")->fetchColumn();
  $_POST = ['idPadre' => '0', 'listEstudiante' => (string)$eid, 'txtCiTutor' => 'TEST-SVC-TUT1', 'txtNombreTutor' => 'Tut', 'txtApellidoTutor' => 'Svc', 'txtCelTutor' => '70000011', 'txtEmailTutor' => 'tutsVC1@demo.bo', 'csrf_token' => 'TOK'];
  ob_start(); ctrl('Tutores', 'TutoresModel')->saveTutor(); echo ob_get_clean()."\n";
} elseif ($caso === 'g2') {
  $_POST = ['idCobro' => '0', 'txtTituloCobro' => 'Cuota TEST-SVC', 'txtDesPago' => 'd', 'listTipoPago' => 'Unico', 'intCuota' => '5', 'txtMonto' => '99.50', 'status' => '1'];
  ob_start(); ctrl('Cobros', 'CobrosModel')->saveCobro(); echo ob_get_clean()."\n";
} elseif ($caso === 'g3') {
  $_POST = ['idParalelo' => '0', 'listGrado' => '7', 'listNivel' => 'Primaria', 'listSigla' => 'Z', 'listTurno' => 'M', 'listTutorDocente' => '0', 'intCupo' => '25', 'status' => '1'];
  ob_start(); ctrl('Cursos', 'CursosModel')->saveParalelo(); echo ob_get_clean()."\n";
} elseif ($caso === 'g4') {
  $g = $pdo->query("SELECT gestion, inicio, fin, gestion_l, monto_pension, descripcion FROM gestion WHERE gestion=2026")->fetch(PDO::FETCH_ASSOC);
  $_POST = ['anio' => '2026', 'fechaInicio' => substr($g['inicio'], 0, 10), 'fechaFin' => substr($g['fin'], 0, 10), 'intPension' => (string)intval($g['monto_pension']), 'txtDescripcion' => $g['descripcion'], 'newG' => '0'];
  ob_start(); ctrl('Gestion', 'GestionModel')->insertGestion(); echo ob_get_clean()."\n";
} elseif ($caso === 'g5') {
  $_POST = ['txtEmail' => 'nadie@demo.bo', 'txtPassword' => 'x'];
  $_SERVER['REQUEST_METHOD'] = 'POST';
  ob_start(); ctrl('Login', 'LoginModel')->loginUser(); echo ob_get_clean()."\n";
} elseif ($caso === 'g6') {
  // Usuarios::getPensiones (vía PensionesModel corregido + inferencia unificada)
  $_POST = ['txtCiEstudiante' => '9100001'];
  ob_start(); ctrl('Usuarios', 'UsuariosModel')->getPensiones(); $out = ob_get_clean();
  $o = json_decode($out, true);
  echo 'status='.var_export($o['status'] ?? null, true).' tabla-bytes='.strlen($o['tabla'] ?? '')."\n";
} elseif ($caso === 'g7') {
  // Materias save vía servicio + duplicado
  $_POST = ['idMateria' => '0', 'txtArea' => 'Area SVC', 'txtCampo' => 'Materia SVC Unica', 'txtDescripcion' => 'd', 'listGrado' => '1', 'listNivel' => 'Primaria', 'horas' => '2', 'Status' => '1'];
  ob_start(); ctrl('Materias', 'MateriasModel')->insertNewMateria(); echo ob_get_clean()."\n";
} elseif ($caso === 'clean') {
  $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
  foreach (['TEST-SVC-T1', 'TEST-SVC-TUT1'] as $ci) {
    $q = $pdo->quote($ci);
    $pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
    $pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
    $eid = $pdo->query("SELECT e.id_estudiante FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q")->fetchColumn();
    if ($eid) { $pdo->exec("DELETE FROM padre WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE pa FROM padre pa INNER JOIN persona pp ON pa.id_persona=pp.id_persona WHERE pp.ci=$q"); $pdo->exec("DELETE FROM estudiante WHERE id_estudiante=".(int)$eid); }
    $pid = $pdo->query("SELECT id_persona FROM persona WHERE ci=$q")->fetchColumn();
    if ($pid) { $pdo->exec("DELETE FROM padre WHERE id_persona=".(int)$pid); $pdo->exec("DELETE FROM persona WHERE id_persona=".(int)$pid); }
  }
  $pdo->exec("DELETE FROM cobro WHERE nombre='Cuota TEST-SVC'");
  $pdo->exec("DELETE FROM paralelo WHERE sigla='Z' AND grado=7");
  $pdo->exec("DELETE FROM materia WHERE nombre_mat='Materia SVC Unica'");
  $pdo->exec("DELETE FROM gestion WHERE gestion=2099");
  $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
  echo 'left-persona='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-SVC-%'")->fetchColumn()."\n";
}
