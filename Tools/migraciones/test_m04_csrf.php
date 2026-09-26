<?php
// TEST I4 — CSRF: sin token se bloquea, con token pasa el filtro (un caso por proceso por die()).
// Uso: php test_m04_csrf.php unit|notoken-est|notoken-mat|notoken-tut|notoken-del
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$caso = $argv[1] ?? '';
if ($caso === 'unit') {
  $t = csrf_token();
  echo 'token-len='.strlen($t)."\n";
  echo 'ok='.var_export(csrf_check($t), true)."\n";
  echo 'bad='.var_export(csrf_check('x'), true)."\n";
  echo 'empty='.var_export(csrf_check(''), true)."\n";
  echo 'stable='.var_export(csrf_token() === $t, true)."\n";
  exit(0);
}

$_SESSION['login'] = true;
$_SESSION['permisosMod'] = ['r' => 1, 'w' => 1, 'u' => 1, 'd' => 1];
$_SESSION['userData'] = ['idrol' => 1];
$_SESSION['csrf_token'] = 'TOKEN-PRUEBA-123';

function runMethod(string $ctrl, string $method, array $post) {
  $_POST = $post;
  require_once "Controllers/$ctrl.php";
  $rc = new ReflectionClass($ctrl);
  $c = $rc->newInstanceWithoutConstructor();
  ob_start();
  $c->$method();
  return ob_get_clean();
}

if ($caso === 'notoken-est') {
  // setEstudiante sin token: debe frenar en CSRF (antes de validar campos)
  echo runMethod('Estudiantes', 'setEstudiante', ['newStudent' => '1'])."\n";
} elseif ($caso === 'notoken-mat') {
  echo runMethod('Matricula', 'insertNewMatricula', ['newG' => '1'])."\n";
} elseif ($caso === 'notoken-tut') {
  echo runMethod('Tutores', 'saveTutor', ['listEstudiante' => '1'])."\n";
} elseif ($caso === 'notoken-del') {
  echo runMethod('Matricula', 'delMatricula', ['idMatricula' => '1'])."\n";
}
