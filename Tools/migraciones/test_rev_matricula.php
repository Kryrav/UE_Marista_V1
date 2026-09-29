<?php
// REVIEW Matrícula — batería por caso (die() obliga un caso por proceso).
// Uso: php test_rev_matricula.php setup|n1|n2|n3|n4a|n4b|n5|n6|n7a|n7b|n8|clean
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
require_once 'Controllers/Matricula.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function ctrlMat() {
  $rc = new ReflectionClass('Matricula');
  $c = $rc->newInstanceWithoutConstructor();
  $p = $rc->getProperty('model'); $p->setAccessible(true);
  $p->setValue($c, new MatriculaModel());
  return $c;
}
function ses($idrol) {
  $_SESSION['login'] = true; $_SESSION['idUser'] = 1;
  $_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
  $_SESSION['userData'] = ['idrol' => $idrol, 'nombre' => 'Test'];
  $_SESSION['csrf_token'] = 'TOK';
}
$caso = $argv[1] ?? '';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-RMAT-01';
$par = (int)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();
$newPost = ['newG' => '1', 'idMatricula' => '0', 'txtCi' => $ci, 'intGestion' => '2026', 'listParalelos' => (string)$par,
  'listTipoEstudiante' => 'Regular', 'txtFolio' => '', 'listStateInscripcion' => 'Confirmado', 'csrf_token' => 'TOK'];

if ($caso === 'setup') {
  $est = new EstudiantesModel();
  echo 'alta: '.$est->insertEstudiante($ci, '', 'Nuevo', 'Rmat', 'Test', 'M', '', '', 'Dir', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null, 1)."\n";
} elseif ($caso === 'n1') {
  ses(4); $_POST = $newPost;
  ob_start(); ctrlMat()->insertNewMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n2') {
  ses(4); $_POST = $newPost; $_POST['intGestion'] = '2025';
  ob_start(); ctrlMat()->insertNewMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n3') {
  ses(1); $_POST = $newPost; $_POST['intGestion'] = '2025'; $_POST['motivoRectificacion'] = 'Prueba REV';
  ob_start(); ctrlMat()->insertNewMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n4a') {
  ses(1);
  $id = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci' AND m.gestion=2025")->fetchColumn();
  $_POST = ['newG' => '0', 'idMatricula' => (string)$id, 'intGestion' => '2025', 'listParalelos' => (string)$par, 'listTipoEstudiante' => 'Regular', 'txtFolio' => '', 'listStateInscripcion' => 'Trasladado', 'motivoEstado' => '', 'csrf_token' => 'TOK'];
  ob_start(); ctrlMat()->insertNewMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n4b') {
  ses(1);
  $id = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci' AND m.gestion=2025")->fetchColumn();
  $_POST = ['newG' => '0', 'idMatricula' => (string)$id, 'intGestion' => '2025', 'listParalelos' => (string)$par, 'listTipoEstudiante' => 'Regular', 'txtFolio' => '', 'listStateInscripcion' => 'Trasladado', 'motivoEstado' => 'Pase a UE Demo', 'csrf_token' => 'TOK'];
  ob_start(); ctrlMat()->insertNewMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n5') {
  ses(4); $_GET = ['ci' => $ci];
  ob_start(); ctrlMat()->ultimaMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n6') {
  ses(1); // admin rematricula a 2024 (≠ activa) con motivo
  $_POST = ['ci' => $ci, 'gestion' => '2024', 'paralelo' => (string)$par, 'tipo' => 'Regular', 'motivoRectificacion' => 'Prueba REV remat', 'csrf_token' => 'TOK'];
  ob_start(); ctrlMat()->rematricular(); echo ob_get_clean()."\n";
} elseif ($caso === 'n7a') {
  // marca una pensión cobrada y prueba borrar -> has_pagos (sin borrar nada)
  $mid = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci' AND m.gestion=2026")->fetchColumn();
  $pdo->exec("UPDATE pensiones SET estado_pago=1 WHERE id_matricula=$mid LIMIT 1");
  ses(1); $_POST = ['idMatricula' => (string)$mid, 'csrf_token' => 'TOK'];
  ob_start(); ctrlMat()->delMatricula(); echo ob_get_clean()."\n";
} elseif ($caso === 'n7b') {
  // revierte el pago, borra -> cascada a pensiones, verifica
  $mid = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci' AND m.gestion=2026")->fetchColumn();
  $pdo->exec("UPDATE pensiones SET estado_pago=0 WHERE id_matricula=$mid");
  ses(1); $_POST = ['idMatricula' => (string)$mid, 'csrf_token' => 'TOK'];
  ob_start(); ctrlMat()->delMatricula(); echo ob_get_clean()."\n";
  $st = $pdo->query("SELECT m.status ms, SUM(p.status) ps FROM matricula m LEFT JOIN pensiones p ON p.id_matricula=m.id_matricula WHERE m.id_matricula=$mid")->fetch(PDO::FETCH_ASSOC);
  echo 'post='.json_encode($st)."\n";
} elseif ($caso === 'n8') {
  ses(4);
  ctrlMat()->getMatriculaAll(); // salida por stdout (redirigir a archivo); termina en die()
} elseif ($caso === 'clean') {
  $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
  $q = $pdo->quote($ci);
  $pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q");
  $eid = $pdo->query("SELECT e.id_estudiante FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=$q")->fetchColumn();
  if ($eid) { $pdo->exec("DELETE FROM estudiante_inclusion WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM padre WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM estudiante WHERE id_estudiante=".(int)$eid); }
  $pdo->exec("DELETE FROM persona WHERE ci=$q");
  $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
  echo 'left='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-RMAT-%'")->fetchColumn()."\n";
}
