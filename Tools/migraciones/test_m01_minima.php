<?php
require 'Config/Config.php';
require 'Helpers/Helpers.php';
require 'Libraries/Core/Autoload.php';

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$ci = 'TEST-M01-'.date('His');
echo "CI prueba: $ci\n";

// 1) Alta mínima sin RUDE/email/cel
$est = new EstudiantesModel();
$res = $est->insertEstudiante($ci, '', 'Nuevo', 'Prueba', 'M01', 'M', '', '', 'Dir Test', '2015-05-01', 'Bolivia', '', '', '', '', '7', password_hash($ci, PASSWORD_DEFAULT), 1, null, null, null, null, null);
echo "insertEstudiante: $res\n";
if ($res !== 'dato_guardado') { echo "FALLO alta\n"; exit(1); }

$row = $pdo->query("SELECT ci, cel, email, usuario FROM persona WHERE ci=".$pdo->quote($ci))->fetch(PDO::FETCH_ASSOC);
echo "persona: ".json_encode($row)."\n";

// 2) Matrícula pendiente
$mm = new MatriculaModel();
$gestion = (int)date('Y');
$par = $pdo->query("SELECT id_paralelo FROM paralelo WHERE status=1 LIMIT 1")->fetchColumn();
echo "paralelo: $par gestion: $gestion\n";
$res2 = $mm->insertMatricula($ci, $gestion, (int)$par, 'Regular', '', 'Pendiente_Documentos');
echo "insertMatricula: $res2\n";
$plazo = plazo30Habiles();
$ok = $mm->setDocumentacionByCiGestion($ci, $gestion, $plazo, ['ci'=>1,'cert_nac'=>0,'rude'=>0,'solicitud'=>1], 1, 'Prueba M01', 'Pendiente_Documentos');
echo "setDocs: ".var_export($ok, true)." plazo=$plazo\n";
$mat = $pdo->query("SELECT estado_inscripcion, plazo_documentos_hasta, docs_checklist, compromiso_firmado FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=".$pdo->quote($ci)." AND m.gestion=$gestion")->fetch(PDO::FETCH_ASSOC);
echo "matricula: ".json_encode($mat)."\n";
$np = $pdo->query("SELECT COUNT(*) c FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=".$pdo->quote($ci))->fetchColumn();
echo "pensiones generadas: $np\n";

// 3) Limpieza
$pdo->exec("DELETE p FROM pensiones p INNER JOIN matricula m ON p.id_matricula=m.id_matricula INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=".$pdo->quote($ci));
$pdo->exec("DELETE m FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=".$pdo->quote($ci));
$pdo->exec("DELETE pa FROM padre pa INNER JOIN estudiante e ON pa.id_estudiante=e.id_estudiante INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=".$pdo->quote($ci));
$pdo->exec("DELETE e FROM estudiante e INNER JOIN persona pp ON e.id_persona=pp.id_persona WHERE pp.ci=".$pdo->quote($ci));
$pdo->exec("DELETE FROM persona WHERE ci=".$pdo->quote($ci));
echo "Limpieza OK\n";
echo "TEST M01 OK\n";
