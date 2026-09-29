<?php
// TEST M06 — etiqueta de cambio, entrega de docs con quién, historial, ficha y comprobante.
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-M06-01';
$est = new EstudiantesModel();
$mm = new MatriculaModel();
echo 'alta: '.$est->insertEstudiante($ci, '', 'Nuevo', 'Seis', 'Test', 'M', '', '', 'Dir', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null, 1)."\n";
$par = (int)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();
$par2 = (int)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 AND id_paralelo<>$par LIMIT 1")->fetchColumn();
echo 'mat: '.$mm->insertMatricula($ci, 2026, $par, 'Regular', '', 'Confirmado', 1)."\n";
$mid = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci'")->fetchColumn();
// 1) edición con etiqueta cambio_curso + motivo
echo 'upd: '.$mm->updateMatricula($mid, $par2, 'Regular', '', 'Confirmado', '', 1, '', 'cambio_curso', '')."\n";
$c = $mm->cambiosDe($mid);
echo 'cambios='.count($c).' ultimo='.json_encode(['tipo' => $c[0]['tipo'] ?? '?', 'det' => $c[0]['detalle'] ?? '?'], JSON_UNESCAPED_UNICODE)."\n";
// 2) entrega de docs con quién
$mm->setDocumentacion($mid, null, ['ci' => 1, 'cert_nac' => 1, 'rude' => 1, 'solicitud' => 1], 1, 'Completa', 'Confirmado', 1, 'María Pérez (madre)');
$c = $mm->cambiosDe($mid);
echo 'tras-entrega='.count($c).' ultimo='.json_encode(['tipo' => $c[0]['tipo'] ?? '?', 'det' => $c[0]['detalle'] ?? '?'], JSON_UNESCAPED_UNICODE)."\n";
// 3) ficha trae docs + historial trae cambios
$eid = (int)$pdo->query("SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='$ci'")->fetchColumn();
$f = $est->getFicha($eid);
echo 'ficha-docs='.json_encode($f['matriculas'][0]['plazo_documentos_hasta'] ?? null)."\n";
$h = $est->getHistorialMatricula($mid);
echo 'hist-cambios='.count($h['cambios'] ?? [])."\n";
// limpieza
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$pdo->exec("DELETE FROM matricula_cambios WHERE id_matricula=$mid");
$pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula WHERE m.id_matricula=$mid");
$pdo->exec("DELETE FROM matricula WHERE id_matricula=$mid");
$pdo->exec("DELETE FROM estudiante_inclusion WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM padre WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM estudiante WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM persona WHERE ci='$ci'");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
echo 'left='.$pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-M06-%'")->fetchColumn()."\nTEST M06 OK\n";
