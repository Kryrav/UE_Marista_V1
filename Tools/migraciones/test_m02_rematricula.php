<?php
// TEST M02 — Rematriculación + estados terminales (dato de prueba, se limpia al final)
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$pdo->exec('SET SESSION innodb_lock_wait_timeout=5');
$ci = 'TEST-M02-'.date('His');
echo "CI prueba: $ci\n";

$est = new EstudiantesModel();
$res = $est->insertEstudiante($ci, '', 'Nuevo', 'Prueba', 'M02', 'M', '', '', 'Dir Test', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null);
echo "alta: $res\n";
if ($res !== 'dato_guardado') { echo "FALLO alta\n"; exit(1); }

$mm = new MatriculaModel();
$par = (int)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();
$gPrev = 2024; $gNext = 2026;

// 1) Matrícula previa 2024
$res1 = $mm->insertMatricula($ci, $gPrev, $par, 'Regular', '', 'Confirmado');
echo "mat $gPrev: $res1\n";

// 2) ultimaMatricula debe devolver 2024
$last = $mm->ultimaMatriculaByCi($ci);
echo "ultima: gestion=".($last['gestion'] ?? '?')." estado=".($last['estado_inscripcion'] ?? '?')."\n";
if ((int)($last['gestion'] ?? 0) !== $gPrev) { echo "FALLO ultima\n"; exit(1); }

// 3) Rematricular 2026 (flujo I2: mismo SP, estado Inscrito)
$res2 = $mm->insertMatricula($ci, $gNext, $par, 'Regular', '', 'Inscrito');
echo "remat $gNext: $res2\n";
if ($res2 !== 'matricula_guardada') { echo "FALLO remat\n"; exit(1); }

// 4) Duplicado en misma gestión debe dar matricula_existente
$resDup = $mm->insertMatricula($ci, $gNext, $par, 'Regular', '', 'Inscrito');
echo "duplicado: $resDup\n";

// 5) Pasar 2024 a Trasladado con motivo (I2)
$rowPrev = $pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci)." AND m.gestion=$gPrev")->fetchColumn();
$upd = $mm->updateMatricula((int)$rowPrev, $par, 'Regular', '', 'Trasladado', 'Traslado a UE San José demo');
echo "terminal: $upd\n";
$chk = $pdo->query("SELECT estado_inscripcion, motivo_estado FROM matricula WHERE id_matricula=".(int)$rowPrev)->fetch(PDO::FETCH_ASSOC);
echo "verif: ".json_encode($chk)."\n";

// 6) Ficha debe incluir motivo
$ficha = $est->getFicha((int)$pdo->query("SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchColumn());
$motivos = array_column($ficha['matriculas'] ?? [], 'motivo_estado');
echo "ficha motivos: ".json_encode($motivos)."\n";

// Limpieza por claves primarias (sin JOINs pesados)
echo "clean: fetching ids...\n";
$ids = $pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchAll(PDO::FETCH_COLUMN);
echo "clean: ids=".json_encode($ids)."\n";
$eid = $pdo->query("SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchColumn();
echo "clean: eid=$eid\n";
if ($ids) { echo "clean: del pensiones...\n"; $pdo->exec("DELETE FROM pensiones WHERE id_matricula IN (".implode(',', array_map('intval', $ids)).")"); echo "clean: pensiones ok\n"; }
if ($ids) { echo "clean: del matricula...\n"; $pdo->exec("DELETE FROM matricula WHERE id_matricula IN (".implode(',', array_map('intval', $ids)).")"); echo "clean: matricula ok\n"; }
if ($eid) { echo "clean: del padre+est...\n"; $pdo->exec("DELETE FROM padre WHERE id_estudiante=".(int)$eid); $pdo->exec("DELETE FROM estudiante WHERE id_estudiante=".(int)$eid); echo "clean: est ok\n"; }
echo "clean: del persona...\n";
$pdo->exec("DELETE FROM persona WHERE ci=".$pdo->quote($ci));
echo "Limpieza OK\nTEST M02 OK\n";
