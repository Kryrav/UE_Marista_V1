<?php
// TEST M05 — auth: login, token con vigencia, password, sesión sin efectos (se limpia al final)
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-AUTH-01'; $em = 'testauth01@demo.bo';
$pdo->exec("DELETE FROM persona WHERE ci=".$pdo->quote($ci));
$pdo->exec("INSERT INTO persona (ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,id_rol,status) VALUES (".$pdo->quote($ci).",'Auth','Test','M','Dir','70000001',".$pdo->quote($em).",".$pdo->quote($em).",'".password_hash('clave123', PASSWORD_DEFAULT)."',7,1)");
$id = (int)$pdo->query("SELECT id_persona FROM persona WHERE ci=".$pdo->quote($ci))->fetchColumn();

$lm = new LoginModel();
// 1) login ok / mal
$r = $lm->loginUser($em, 'clave123');
echo 'login-ok='.var_export($r && $r['id_persona'] == $id, true)."\n";
echo 'login-mal='.var_export($lm->loginUser($em, 'otra'), true)."\n";
// 2) token vigente / expirado / invalidado
$lm->setTokenUser($id, 'TOK-1', 60);
echo 'tok-vigente='.var_export(!empty($lm->getUsuario($em, 'TOK-1')), true)."\n";
$lm->setTokenUser($id, 'TOK-2', -60);
echo 'tok-expirado='.var_export(empty($lm->getUsuario($em, 'TOK-2')), true)."\n";
$lm->setTokenUser($id, '');
echo 'tok-clear='.var_export($pdo->query("SELECT token FROM persona WHERE id_persona=$id")->fetchColumn() === null, true)."\n";
// 3) cambio de clave invalida token y verifica
$lm->setTokenUser($id, 'TOK-3', 60);
$lm->insertPassword($id, 'nueva123');
$v = $lm->loginUser($em, 'nueva123');
echo 'pass-new='.var_export($v && $v['id_persona'] == $id, true)."\n";
echo 'pass-old='.var_export($lm->loginUser($em, 'clave123'), true)."\n";
echo 'tok-postpass='.var_export(empty($lm->getUsuario($em, 'TOK-3')), true)."\n";
// 4) sessionLogin no escribe $_SESSION
unset($_SESSION['userData']);
$lm->sessionLogin($id);
echo 'no-session-side-effect='.var_export(!isset($_SESSION['userData']), true)."\n";
// 5) bloqueo: admin real no bloqueado (ruta normal)
echo 'bloqueo-bool='.var_export(is_bool($lm->verificarBloqueo('nadie@demo.bo')), true)."\n";
// limpieza
$pdo->exec("DELETE FROM persona WHERE ci=".$pdo->quote($ci));
echo 'left='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-AUTH-%'")->fetchColumn()."\nTEST M05 OK\n";
