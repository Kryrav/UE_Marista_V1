<?php
// TEST M03 — inclusión, rezago, búsqueda, comprobante, auditoría (se limpia al final)
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
$ci = 'TEST-M03-'.date('His');
echo "CI: $ci\n";

// 1) rezago unitario: nacido 2014 en Primaria 1 (esperada 6) -> rezago>=2 alerta
$r = calculaRezago('2014-01-01', 'Primaria', 1);
echo 'rezago='.json_encode($r)."\n";
if (!$r['alerta']) { echo "FALLO rezago\n"; exit(1); }
$r2 = calculaRezago(date('Y-m-d', strtotime('-6 years')), 'Primaria', 1);
echo 'no-rezago='.json_encode($r2)."\n";
if ($r2['alerta']) { echo "FALLO no-rezago\n"; exit(1); }

// 2) alta con auditoría (uid 1) + inclusión
$est = new EstudiantesModel();
$res = $est->insertEstudiante($ci, '', 'Nuevo', 'Tres', 'Test', 'M', '', '', 'Dir', '2014-01-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null, 1);
echo "alta: $res\n";
$eid = (int)$pdo->query("SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchColumn();
echo "created_by=".$pdo->query("SELECT created_by FROM estudiante WHERE id_estudiante=$eid")->fetchColumn()."\n";
$ok = $est->saveInclusion($eid, ['tiene_discapacidad' => 1, 'tipo_discapacidad' => 'Auditiva', 'adaptaciones' => 'Primera fila', 'centro_especial' => '', 'matricula_paralela' => 0, 'requiere_comision' => 1], 1);
echo 'inclusion: '.var_export($ok, true).' '.json_encode($est->getInclusion($eid), JSON_UNESCAPED_UNICODE)."\n";

// 3) matrícula con auditoría (uid 1) -> created_by + id_user reales
$mm = new MatriculaModel();
$par = (int)$pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();
echo 'mat: '.$mm->insertMatricula($ci, 2026, $par, 'Regular', '', 'Inscrito', 1)."\n";
echo 'audmat='.json_encode($pdo->query("SELECT m.created_by, m.id_user FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci)." AND m.gestion=2026")->fetch(PDO::FETCH_ASSOC))."\n";

// 4) búsqueda server
$found = $est->buscarEstudiantes(substr($ci, 0, 12));
echo 'buscar: '.count($found)." (ci=".($found[0]['ci'] ?? '?').")\n";

// 5) candidatos rezago incluye al nuevo (2014 en el paralelo que toque)
$cand = $est->rezagoCandidates() ?: [];
$mine = array_values(array_filter($cand, fn($c) => ($c['ci'] ?? '') === $ci));
echo 'candidatos='.count($cand).' mio='.count($mine)."\n";

// 6) comprobante: getHistorialMatricula trae motivo/plazo
$mid = (int)$pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchColumn();
$hist = $est->getHistorialMatricula($mid);
echo 'comprobante: cab='.(!empty($hist['cabecera']) ? 'ok' : 'FALLO').' cuotas='.($hist['totales']['cuotas'] ?? '?')."\n";

// limpieza por PKs
$ids = $pdo->query("SELECT m.id_matricula FROM matricula m JOIN estudiante e ON m.id_estudiante=e.id_estudiante JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci))->fetchAll(PDO::FETCH_COLUMN);
if ($ids) { $pdo->exec("DELETE FROM pensiones WHERE id_matricula IN (".implode(',', array_map('intval', $ids)).")"); $pdo->exec("DELETE FROM matricula WHERE id_matricula IN (".implode(',', array_map('intval', $ids)).")"); }
$pdo->exec("DELETE FROM estudiante_inclusion WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM padre WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM estudiante WHERE id_estudiante=$eid");
$pdo->exec("DELETE FROM persona WHERE ci=".$pdo->quote($ci));
$left = $pdo->query("SELECT COUNT(*) FROM persona WHERE ci LIKE 'TEST-M03-%'")->fetchColumn();
echo "left=$left\nTEST M03 OK\n";
