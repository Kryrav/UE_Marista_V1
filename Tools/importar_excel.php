<?php
// ============================================================================
// Tools/importar_excel.php — Carga masiva UE Marista V1 (CLI, sin dependencias)
// Lee Tools/plantillas/Plantillas_Marista_V1.xlsx con ZipArchive+XML nativos.
// Uso:
//   php Tools/importar_excel.php --file=Tools/plantillas/Plantillas_Marista_V1.xlsx --todas [--dry-run]
//   php Tools/importar_excel.php --file=... --hoja=05_ESTUDIANTES [--dry-run]
//   php Tools/importar_excel.php --help
// Convención plantilla: fila 2 = cabecera, fila 3 = EJEMPLO (se ignora),
// datos desde fila 4. No dejar filas vacías intermedias.
// ============================================================================

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(2);
}

const HEADER_ROW  = 2;
const EXAMPLE_ROW = 3;
const DATA_START  = 4;
const MAX_ROWS    = 1002;

const ORDER = ['01_GESTION','02_PARALELOS','03_MATERIAS','04_PERSONAL',
    '05_ESTUDIANTES','06_TUTORES','07_MATRICULAS','08_COBROS'];

// ---------------------------------------------------------------- lector XLSX
final class XlsxReader {
    private ZipArchive $zip;
    /** @var string[] */
    private array $shared = [];
    /** @var array<string,string> sheetName => path dentro del zip */
    private array $sheets = [];

    public function __construct(string $file) {
        if (!is_file($file)) throw new RuntimeException("No existe: $file");
        $this->zip = new ZipArchive();
        if ($this->zip->open($file) !== true) throw new RuntimeException("XLSX inválido: $file");
        $this->loadShared();
        $this->loadSheets();
    }

    private function xml(string $path): ?SimpleXMLElement {
        $raw = $this->zip->getFromName($path);
        if ($raw === false) return null;
        $x = simplexml_load_string($raw);
        if ($x === false) return null;
        $x->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $x->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        return $x;
    }

    private function loadShared(): void {
        $x = $this->xml('xl/sharedStrings.xml');
        if (!$x) return;
        foreach ($x->xpath('//m:si') as $si) {
            $t = '';
            foreach ($si->xpath('.//m:t') as $n) $t .= (string)$n;
            $this->shared[] = $t;
        }
    }

    private function loadSheets(): void {
        $wb = $this->xml('xl/workbook.xml');
        $rels = $this->xml('xl/_rels/workbook.xml.rels');
        $id2target = [];
        if ($rels) foreach ($rels->xpath('//*[@Id]') as $r) {
            $a = $r->attributes('http://schemas.openxmlformats.org/package/2006/relationships');
            $id = (string)($a['Id'] ?? '');
            $t = (string)($a['Target'] ?? '');
            if ($id && $t) $id2target[$id] = $t;
        }
        if (!$wb) return;
        foreach ($wb->xpath('//m:sheet') as $s) {
            $name = (string)$s['name'];
            $rr = $s->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $rid = (string)($rr['id'] ?? '');
            $target = $id2target[$rid] ?? ('worksheets/sheet' . (count($this->sheets) + 1) . '.xml');
            if (!str_starts_with($target, 'xl/')) $target = 'xl/' . ltrim($target, '/');
            $this->sheets[$name] = $target;
        }
    }

    /** @return string[] */
    public function sheetNames(): array { return array_keys($this->sheets); }

    /**
     * Devuelve filas como [nroFilaExcel => [colLetra => valorString]].
     * @return array<int,array<string,string>>
     */
    public function readSheet(string $name): array {
        if (!isset($this->sheets[$name])) throw new RuntimeException("Hoja no encontrada: $name");
        $x = $this->xml($this->sheets[$name]);
        if (!$x) return [];
        $rows = [];
        foreach ($x->xpath('//m:sheetData/m:row') as $row) {
            $rn = (int)$row['r'];
            if ($rn < HEADER_ROW || $rn > MAX_ROWS) continue;
            $row->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $cells = $row->xpath('./m:c');
            if (!$cells) continue;
            foreach ($cells as $c) {
                $ref = (string)$c['r'];
                $col = preg_replace('/[^A-Z]/', '', $ref);
                $t = (string)($c['t'] ?? '');
                $v = null;
                if ($t === 's') {
                    $idx = (int)(string)$c->v;
                    $v = $this->shared[$idx] ?? '';
                } elseif ($t === 'inlineStr') {
                    $c->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $n = $c->xpath('./m:is/m:t');
                    $v = $n ? (string)$n[0] : '';
                } elseif ($t === 'str') {
                    $v = (string)$c->v;
                } elseif ($t === 'b') {
                    $v = ((string)$c->v === '1') ? '1' : '0';
                } else {
                    $v = isset($c->v) ? (string)$c->v : '';
                }
                $rows[$rn][$col] = $v;
            }
        }
        return $rows;
    }
}

// ---------------------------------------------------------------- utilidades
function clean(?string $v): string {
    $v = $v ?? '';
    $v = trim(strip_tags($v));
    return preg_replace('/\s+/', ' ', $v);
}
function digits(string $v): string { return preg_replace('/\D/', '', $v); }
function isEmptyRow(array $row): bool {
    foreach ($row as $v) if (clean((string)$v) !== '') return false;
    return true;
}
/** Convierte serial Excel o texto a AAAA-MM-DD o null si inválido. */
function toDate(?string $v): ?string {
    $v = clean($v);
    if ($v === '') return null;
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
        [$y,$m,$d] = array_map('intval', explode('-', $v));
        return checkdate($m, $d, $y) ? $v : null;
    }
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $v, $mm)) {
        if (checkdate((int)$mm[2], (int)$mm[1], (int)$mm[3]))
            return sprintf('%04d-%02d-%02d', (int)$mm[3], (int)$mm[2], (int)$mm[1]);
        return null;
    }
    if (is_numeric($v)) { // serial Excel
        $serial = (float)$v;
        if ($serial > 20000 && $serial < 80000) {
            $unix = (int)(($serial - 25569) * 86400);
            return gmdate('Y-m-d', $unix);
        }
    }
    return null;
}
function validEmail(string $e): bool { return (bool)filter_var($e, FILTER_VALIDATE_EMAIL); }

// ---------------------------------------------------------------- validación
/** @return array{cols:string[],rows:array<int,array<string,string>>} */
function sheetMatrix(XlsxReader $r, string $sheet): array {
    $raw = $r->readSheet($sheet);
    if (!isset($raw[HEADER_ROW])) throw new RuntimeException("Sin cabecera en $sheet");
    $cols = []; // letra => header
    foreach ($raw[HEADER_ROW] as $letter => $h) {
        $h = clean((string)$h);
        if ($h !== '') $cols[$letter] = $h;
    }
    $rows = [];
    for ($i = DATA_START; $i <= MAX_ROWS; $i++) {
        if (!isset($raw[$i]) || isEmptyRow($raw[$i])) continue;
        $assoc = [];
        foreach ($cols as $letter => $h) $assoc[$h] = clean((string)($raw[$i][$letter] ?? ''));
        $rows[$i] = $assoc;
    }
    return ['cols' => array_values($cols), 'rows' => $rows];
}

function err(array &$errors, string $sheet, int $row, string $msg): void {
    $errors[] = "$sheet fila $row: $msg";
}

function vGestion(array $r, array &$e, string $s, int $n, array $seen): ?array {
    $g = $r['gestion*'] ?? '';
    if (!ctype_digit($g) || (int)$g < 2000 || (int)$g > 2100) { err($e,$s,$n,"gestion año 2000-2100."); return null; }
    $ini = toDate($r['inicio*'] ?? ''); $fin = toDate($r['fin*'] ?? '');
    if (!$ini) { err($e,$s,$n,"inicio AAAA-MM-DD inválido."); return null; }
    if (!$fin) { err($e,$s,$n,"fin AAAA-MM-DD inválido."); return null; }
    if ($fin < $ini) { err($e,$s,$n,"fin anterior a inicio."); return null; }
    $gl = $r['gestion_l'] !== '' ? $r['gestion_l'] : $g;
    $mp = $r['monto_pension*'] ?? '';
    if (!is_numeric(str_replace(',','.',$mp)) || (float)str_replace(',','.',$mp) <= 0) { err($e,$s,$n,"monto_pension > 0."); return null; }
    $st = strtolower($r['status*'] ?? '');
    if (!in_array($st, ['activa','cerrada'], true)) { err($e,$s,$n,"status Activa/Cerrada."); return null; }
    if (($r['descripcion*'] ?? '') === '') { err($e,$s,$n,"descripcion obligatoria."); return null; }
    if (isset($seen['g'][$g])) { err($e,$s,$n,"gestion $g duplicada en archivo."); return null; }
    return ['gestion'=>(int)$g,'inicio'=>$ini,'fin'=>$fin,'gestion_l'=>$gl,
        'monto'=>(float)str_replace(',','.',$mp),'descripcion'=>$r['descripcion*'],
        'status'=> $st==='activa'?1:2];
}

function vParalelo(array $r, array &$e, string $s, int $n, array $seen): ?array {
    $niv = $r['nivel*'] ?? '';
    if (!in_array($niv, ['Inicial','Primaria'], true)) { err($e,$s,$n,"nivel Inicial/Primaria."); return null; }
    $gr = $r['grado*'] ?? '';
    if (!ctype_digit((string)$gr)) { err($e,$s,$n,"grado numérico."); return null; }
    $gr = (int)$gr;
    if ($niv === 'Inicial' && $gr !== 0) { err($e,$s,$n,"Inicial exige grado 0."); return null; }
    if ($niv === 'Primaria' && ($gr < 1 || $gr > 6)) { err($e,$s,$n,"Primaria grado 1-6."); return null; }
    $sig = strtoupper($r['sigla'] !== '' ? $r['sigla'] : 'A');
    if (!in_array($sig, ['A','B','C','D','E','F'], true)) { err($e,$s,$n,"sigla A-F."); return null; }
    $cupo = $r['cupo'] !== '' ? $r['cupo'] : '30';
    if (!ctype_digit((string)$cupo) || (int)$cupo <= 0) { err($e,$s,$n,"cupo > 0."); return null; }
    $tur = $r['turno'] !== '' ? $r['turno'] : 'Mañana';
    if (!in_array($tur, ['Mañana','Tarde'], true)) { err($e,$s,$n,"turno Mañana/Tarde."); return null; }
    $st = $r['status'] !== '' ? $r['status'] : 'Activo';
    if (!in_array($st, ['Activo','Inactivo'], true)) { err($e,$s,$n,"status Activo/Inactivo."); return null; }
    $key = "$niv|$gr|$sig";
    if (isset($seen['p'][$key])) { err($e,$s,$n,"paralelo $key duplicado en archivo."); return null; }
    return ['nivel'=>$niv,'grado'=>$gr,'sigla'=>$sig,'cupo'=>(int)$cupo,
        'tutor'=> $r['tutor'] !== '' ? $r['tutor'] : 'Sin asignación','turno'=>$tur,
        'status'=> $st==='Activo'?1:0];
}

function vMateria(array $r, array &$e, string $s, int $n, array $seen): ?array {
    foreach (['area_mat*','nombre_mat*','grado*','nivel*','horas_mat*'] as $c)
        if (($r[$c] ?? '') === '') { err($e,$s,$n,"$c obligatorio."); return null; }
    if (!ctype_digit($r['grado*']) || (int)$r['grado*'] < 1 || (int)$r['grado*'] > 6) { err($e,$s,$n,"grado 1-6."); return null; }
    if (!in_array($r['nivel*'], ['Primaria','Secundaria'], true)) { err($e,$s,$n,"nivel Primaria/Secundaria."); return null; }
    if (!ctype_digit($r['horas_mat*']) || (int)$r['horas_mat*'] < 1 || (int)$r['horas_mat*'] > 7) { err($e,$s,$n,"horas 1-7."); return null; }
    $st = $r['status'] !== '' ? $r['status'] : 'Activa';
    if (!in_array($st, ['Activa','Inactiva'], true)) { err($e,$s,$n,"status Activa/Inactiva."); return null; }
    $key = mb_strtolower($r['nombre_mat*']) . '|' . (int)$r['grado*'];
    if (isset($seen['m'][$key])) { err($e,$s,$n,"materia nombre+grado duplicada en archivo."); return null; }
    return ['area'=>$r['area_mat*'],'nombre'=>$r['nombre_mat*'],'desc'=>$r['descripcion'] ?? '',
        'grado'=>(int)$r['grado*'],'nivel'=>$r['nivel*'],'horas'=>(int)$r['horas_mat*'],
        'status'=> $st==='Activa'?1:2];
}

function vPersonaBase(array $r, string $pfx, array &$e, string $s, int $n): ?array {
    // $pfx '' o 'ci_tutor' style: usamos mapa de columnas
    $ci = $r[$pfx.'ci*'] ?? $r['ci*'] ?? '';
    $no = $r[$pfx.'nombre*'] ?? $r['nombre*'] ?? '';
    $ap = $r[$pfx.'apellido*'] ?? $r['apellido*'] ?? '';
    $ce = $r[$pfx.'cel*'] ?? $r['cel*'] ?? '';
    $em = strtolower($r[$pfx.'email*'] ?? $r['email*'] ?? '');
    if ($ci===''||$no===''||$ap===''||$ce===''||$em==='') { err($e,$s,$n,"ci/nombre/apellido/cel/email obligatorios."); return null; }
    if (!validEmail($em)) { err($e,$s,$n,"email '$em' inválido."); return null; }
    $d = digits($ce);
    if (strlen($d) < 7 || strlen($d) > 9) { err($e,$s,$n,"cel 7-9 dígitos."); return null; }
    $sx = $r[$pfx.'sexo'] ?? $r['sexo'] ?? '';
    if ($sx !== '' && !in_array($sx, ['M','F'], true)) { err($e,$s,$n,"sexo M/F."); return null; }
    return ['ci'=>$ci,'nombre'=>mb_convert_case($no, MB_CASE_TITLE, 'UTF-8'),
        'apellido'=>mb_convert_case($ap, MB_CASE_TITLE, 'UTF-8'),'sexo'=>$sx!==''?$sx:'M',
        'dir'=>$r[$pfx.'direccion'] ?? $r['direccion'] ?? '','cel'=>$ce,'email'=>$em];
}

function vEstudiante(array $r, array &$e, string $s, int $n, array $seen): ?array {
    $p = vPersonaBase($r, '', $e, $s, $n);
    if (!$p) return null;
    $fn = toDate($r['fnacimiento*'] ?? '');
    if (!$fn) { err($e,$s,$n,"fnacimiento AAAA-MM-DD."); return null; }
    if ($fn > date('Y-m-d')) { err($e,$s,$n,"fnacimiento futura."); return null; }
    if ($fn < date('Y-m-d', strtotime('-30 years'))) { err($e,$s,$n,"fnacimiento hace >30 años."); return null; }
    if (($r['rude*'] ?? '') === '') { err($e,$s,$n,"rude obligatorio."); return null; }
    $er = $r['estado_reg'] !== '' ? $r['estado_reg'] : 'Nuevo';
    if (!in_array($er, ['Nuevo','Antiguo','Retirado'], true)) { err($e,$s,$n,"estado_reg Nuevo/Antiguo/Retirado."); return null; }
    $ff = $r['folio_fisico'] ?? '';
    if ($ff !== '' && (!ctype_digit($ff) || (int)$ff <= 0)) { err($e,$s,$n,"folio_fisico >0 o vacío."); return null; }
    $el = $r['estado_legajo'] ?? '';
    if ($el !== '' && !in_array($el, ['archivado','prestado','digitalizado','observado'], true)) { err($e,$s,$n,"estado_legajo inválido."); return null; }
    foreach ([['ci',$p['ci'],'c'],['email',$p['email'],'e'],['cel',$p['cel'],'t'],['rude',$r['rude*'],'r']] as [$k,$v,$t]) {
        if (isset($seen[$t][$v])) { err($e,$s,$n,"$k duplicado en archivo."); return null; }
    }
    if ($ff !== '' && isset($seen['f'][$ff])) { err($e,$s,$n,"folio_fisico $ff duplicado."); return null; }
    return array_merge($p, ['fn'=>$fn,'rude'=>$r['rude*'],'estado_reg'=>$er,
        'col'=>$r['colegio_proc'] ?? '','prov'=>$r['provincia'] ?? '','ciu'=>$r['ciudad'] ?? '',
        'pais'=>$r['pais'] !== '' ? $r['pais'] : 'Bolivia','emer'=>$r['emergencia'] ?? '',
        'folio'=>$ff,'est'=>isset($r['estante'])?mb_strtoupper($r['estante']):'',
        'gav'=>$r['gaveta'] ?? '','leg'=>$el !== '' ? $el : null]);
}

function vTutor(array $r, array &$e, string $s, int $n, array $seen): ?array {
    // columnas con nombres propios: ci_tutor*, ci_estudiante*
    $tmp = ['ci*'=>$r['ci_tutor*'] ?? '','nombre*'=>$r['nombre*'] ?? '','apellido*'=>$r['apellido*'] ?? '',
        'sexo'=>$r['sexo'] ?? '','direccion'=>$r['direccion'] ?? '','cel*'=>$r['cel*'] ?? '','email*'=>$r['email*'] ?? ''];
    $p = vPersonaBase($tmp, '', $e, $s, $n);
    if (!$p) return null;
    if (($r['ci_estudiante*'] ?? '') === '') { err($e,$s,$n,"ci_estudiante obligatorio."); return null; }
    $par = $r['parentesco'] !== '' ? $r['parentesco'] : 'Padre';
    if (!in_array($par, ['Padre','Madre','Tutor','Tío','Tía','Hermano','Apoderado'], true)) { err($e,$s,$n,"parentesco inválido."); return null; }
    $ec = $r['estado_civil'] ?? '';
    if ($ec !== '' && !in_array($ec, ['Soltero','Casado','Divorciado','Viudo'], true)) { err($e,$s,$n,"estado_civil inválido."); return null; }
    $key = $p['ci'] . '|' . $r['ci_estudiante*'];
    if (isset($seen['v'][$key])) { err($e,$s,$n,"vínculo tutor-estudiante duplicado en archivo."); return null; }
    return array_merge($p, ['nacionalidad'=>$r['nacionalidad'] !== '' ? $r['nacionalidad'] : 'Boliviana',
        'ec'=>$ec,'prof'=>$r['profesion'] ?? '','emp'=>$r['empresa'] ?? '',
        'ci_est'=>$r['ci_estudiante*'],'parentesco'=>$par]);
}

function vMatricula(array $r, array &$e, string $s, int $n, array $seen): ?array {
    if (($r['ci_estudiante*'] ?? '') === '') { err($e,$s,$n,"ci_estudiante obligatorio."); return null; }
    $g = $r['gestion*'] ?? '';
    if (!ctype_digit((string)$g)) { err($e,$s,$n,"gestion año."); return null; }
    if (($r['paralelo*'] ?? '') === '') { err($e,$s,$n,"paralelo Nivel-Grado-Sigla."); return null; }
    $ti = $r['tipo'] !== '' ? $r['tipo'] : 'Regular';
    if (!in_array($ti, ['Regular','Becado'], true)) { err($e,$s,$n,"tipo Regular/Becado."); return null; }
    $es = $r['estado*'] ?? '';
    if (!in_array($es, ['Confirmado','Inscrito'], true)) { err($e,$s,$n,"estado Confirmado/Inscrito."); return null; }
    $key = $r['ci_estudiante*'] . '|' . $g;
    if (isset($seen['mt'][$key])) { err($e,$s,$n,"matrícula estudiante+gestion duplicada en archivo."); return null; }
    return ['ci'=>$r['ci_estudiante*'],'gestion'=>(int)$g,'par'=>$r['paralelo*'],'tipo'=>$ti,
        'folio'=>$r['folio'] ?? '','estado'=>$es];
}

function vCobro(array $r, array &$e, string $s, int $n): ?array {
    if (($r['nombre*'] ?? '') === '') { err($e,$s,$n,"nombre obligatorio."); return null; }
    $ti = $r['tipo*'] ?? '';
    if (!in_array($ti, ['Unico','Mensual'], true)) { err($e,$s,$n,"tipo Unico/Mensual."); return null; }
    $nc = $r['ncuota*'] ?? '';
    if ($ti === 'Unico') $nc = '1';
    if (!ctype_digit((string)$nc) || (int)$nc <= 0) { err($e,$s,$n,"ncuota > 0."); return null; }
    $va = $r['valor*'] ?? '';
    if (!is_numeric(str_replace(',','.',$va)) || (float)str_replace(',','.',$va) <= 0) { err($e,$s,$n,"valor > 0."); return null; }
    $st = $r['status'] !== '' ? $r['status'] : 'Activo';
    if (!in_array($st, ['Activo','Inactivo'], true)) { err($e,$s,$n,"status Activo/Inactivo."); return null; }
    return ['nombre'=>$r['nombre*'],'desc'=>$r['descripcion'] ?? '','tipo'=>$ti,'ncuota'=>(int)$nc,
        'valor'=>(float)str_replace(',','.',$va),'status'=> $st==='Activo'?1:0];
}

// ---------------------------------------------------------------- BD
function db(): PDO {
    require __DIR__ . '/../Config/Config.php';
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec('SET NAMES ' . DB_CHARSET);
    return $pdo;
}
function tableCols(PDO $pdo, string $t): array {
    try {
        $r = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$t'");
        return array_column($r->fetchAll(), 'COLUMN_NAME');
    } catch (Throwable $x) { return []; }
}
function rolId(PDO $pdo, string $nombre, bool $crearTutor): ?int {
    $st = $pdo->prepare('SELECT idrol FROM rol WHERE LOWER(nombrerol)=LOWER(?) LIMIT 1');
    $st->execute([$nombre]);
    $id = $st->fetchColumn();
    if ($id) return (int)$id;
    if ($crearTutor && mb_strtolower($nombre) === 'tutor') {
        $pdo->prepare('INSERT INTO rol (nombrerol, descripcion, status) VALUES (?,?,1)')
            ->execute(['Tutor','Apoderados y tutores']);
        return (int)$pdo->lastInsertId();
    }
    return null;
}
function paraleloId(PDO $pdo, string $txt): ?int {
    $p = explode('-', $txt);
    if (count($p) < 3) return null;
    $sig = array_pop($p); $gra = array_pop($p); $niv = implode('-', $p);
    if (!ctype_digit((string)$gra)) return null;
    $st = $pdo->prepare('SELECT id_paralelo FROM paralelo WHERE nivel=? AND grado=? AND sigla=? AND status!=0 ORDER BY id_paralelo LIMIT 1');
    $st->execute([$niv, (int)$gra, $sig]);
    $id = $st->fetchColumn();
    return $id ? (int)$id : null;
}

// ---------------------------------------------------------------- main
function help(): void {
    echo <<<TXT
Uso:
  php Tools/importar_excel.php --file=RUTA --todas [--dry-run]
  php Tools/importar_excel.php --file=RUTA --hoja=05_ESTUDIANTES [--dry-run]

Opciones:
  --file=RUTA   Libro xlsx de plantillas (requerido).
  --todas       Importa en orden 01..08 (recomendado en BD limpia).
  --hoja=NOMBRE Importa una sola hoja (ej 07_MATRICULAS).
  --dry-run     Solo valida, no escribe en la BD.
  --help        Esta ayuda.

Ejemplos:
  php Tools/importar_excel.php --file=Tools/plantillas/Plantillas_Marista_V1.xlsx --todas --dry-run
  php Tools/importar_excel.php --file=Tools/plantillas/Plantillas_Marista_V1.xlsx --todas

TXT;
}

function main(array $argv): int {
    $opt = ['file'=>null,'todas'=>false,'hoja'=>null,'dry'=>false];
    foreach ($argv as $a) {
        if ($a === '--help' || $a === '-h') { help(); return 0; }
        if ($a === '--todas') $opt['todas'] = true;
        if ($a === '--dry-run') $opt['dry'] = true;
        if (str_starts_with($a, '--file=')) $opt['file'] = substr($a, 7);
        if (str_starts_with($a, '--hoja=')) $opt['hoja'] = substr($a, 7);
    }
    if (!$opt['file']) { fwrite(STDERR, "Falta --file (ver --help).\n"); return 2; }
    if (!$opt['todas'] && !$opt['hoja']) { fwrite(STDERR, "Indique --todas o --hoja.\n"); return 2; }

    try {
        $reader = new XlsxReader($opt['file']);
    } catch (Throwable $x) { fwrite(STDERR, 'Error XLSX: ' . $x->getMessage() . "\n"); return 2; }

    $hojas = $opt['todas'] ? ORDER : [$opt['hoja']];
    foreach ($hojas as $h) {
        if (!in_array($h, $reader->sheetNames(), true)) { fwrite(STDERR, "Hoja inexistente: $h\n"); return 2; }
    }

    // Fase 1: lectura + validación de archivo (sin BD)
    $data = []; $errors = []; $seen = [];
    foreach ($hojas as $h) {
        try {
            $m = sheetMatrix($reader, $h);
        } catch (Throwable $x) { $errors[] = "$h: " . $x->getMessage(); continue; }
        $valid = [];
        foreach ($m['rows'] as $n => $row) {
            $v = match ($h) {
                '01_GESTION' => vGestion($row, $errors, $h, $n, $seen),
                '02_PARALELOS' => vParalelo($row, $errors, $h, $n, $seen),
                '03_MATERIAS' => vMateria($row, $errors, $h, $n, $seen),
                '04_PERSONAL' => vPersonaBase(
                    ['ci*'=>$row['ci*']??'','nombre*'=>$row['nombre*']??'','apellido*'=>$row['apellido*']??'',
                     'sexo'=>$row['sexo']??'','direccion'=>$row['direccion']??'','cel*'=>$row['cel*']??'','email*'=>$row['email*']??''],
                    '', $errors, $h, $n),
                '05_ESTUDIANTES' => vEstudiante($row, $errors, $h, $n, $seen),
                '06_TUTORES' => vTutor($row, $errors, $h, $n, $seen),
                '07_MATRICULAS' => vMatricula($row, $errors, $h, $n, $seen),
                '08_COBROS' => vCobro($row, $errors, $h, $n),
            };
            if ($h === '04_PERSONAL') {
                $rol = $row['rol*'] ?? '';
                if (!in_array($rol, ['Administrador','Director','Coordinador','Secretario','Docente','Contador'], true))
                    { err($errors,$h,$n,"rol inválido."); $v = null; }
                elseif ($v) $v['rol'] = $rol;
            }
            if ($v !== null) {
                $valid[$n] = array_merge($v, ['_row' => $row]);
                // marcar vistos intra-archivo
                if ($h === '01_GESTION') $seen['g'][$v['gestion']] = true;
                if ($h === '02_PARALELOS') $seen['p'][$v['nivel'].'|'.$v['grado'].'|'.$v['sigla']] = true;
                if ($h === '03_MATERIAS') $seen['m'][mb_strtolower($v['nombre']).'|'.$v['grado']] = true;
                if (in_array($h, ['04_PERSONAL','05_ESTUDIANTES','06_TUTORES'], true)) {
                    $seen['c'][$v['ci']] = true; $seen['e'][mb_strtolower($v['email'])] = true; $seen['t'][$v['cel']] = true;
                }
                if ($h === '05_ESTUDIANTES') { $seen['r'][$v['rude']] = true; if ($v['folio'] !== '') $seen['f'][$v['folio']] = true; }
                if ($h === '06_TUTORES') $seen['v'][$v['ci'].'|'.$v['ci_est']] = true;
                if ($h === '07_MATRICULAS') $seen['mt'][$v['ci'].'|'.$v['gestion']] = true;
            }
        }
        $data[$h] = $valid;
        printf("%s: %d filas con datos, %d válidas (archivo).\n", $h, count($m['rows']), count($valid));
    }

    if ($opt['dry'] && empty($data)) { echo "Sin datos.\n"; return 0; }

    // Fase 2: BD (en dry-run también se chequea duplicados si hay conexión)
    try { $pdo = db(); }
    catch (Throwable $x) {
        echo "AVISO sin BD (" . $x->getMessage() . "): solo validación de archivo.\n";
        foreach ($errors as $e) echo "  [ERROR] $e\n";
        return $errors ? 1 : 0;
    }

    $estCols = tableCols($pdo, 'estudiante');
    $hasLegajo = in_array('folio_fisico', $estCols, true);
    $folioNext = null;
    if ($hasLegajo) {
        $folioNext = (int)($pdo->query('SELECT COALESCE(MAX(folio_fisico),0)+1 FROM estudiante')->fetchColumn() ?: 1);
    }

    $totIns = 0;
    foreach ($hojas as $h) {
        $rows = $data[$h] ?? [];
        if (!$rows) continue;
        $ins = 0;
        try {
            if ($h !== '07_MATRICULAS') $pdo->beginTransaction();
            foreach ($rows as $n => $v) {
                $ins += match ($h) {
                    '01_GESTION' => insGestion($pdo, $v, $errors, $h, $n),
                    '02_PARALELOS' => insParalelo($pdo, $v, $errors, $h, $n),
                    '03_MATERIAS' => insMateria($pdo, $v, $errors, $h, $n),
                    '04_PERSONAL' => insPersonal($pdo, $v, $errors, $h, $n),
                    '05_ESTUDIANTES' => insEstudiante($pdo, $v, $errors, $h, $n, $estCols, $folioNext),
                    '06_TUTORES' => insTutor($pdo, $v, $errors, $h, $n),
                    '07_MATRICULAS' => insMatricula($pdo, $v, $errors, $h, $n),
                    '08_COBROS' => insCobro($pdo, $v, $errors, $h, $n),
                };
            }
            if ($opt['dry']) {
                if ($h !== '07_MATRICULAS' && $pdo->inTransaction()) $pdo->rollBack();
                printf("[DRY-RUN] %s: %d/%d insertables.\n", $h, $ins, count($rows));
            } else {
                if ($h !== '07_MATRICULAS' && $pdo->inTransaction()) $pdo->commit();
                printf("%s: %d/%d insertados.\n", $h, $ins, count($rows));
                $totIns += $ins;
            }
        } catch (Throwable $x) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = "$h: " . $x->getMessage();
        }
    }

    if ($errors) {
        echo "\nERRORES (" . count($errors) . "):\n";
        foreach (array_slice($errors, 0, 80) as $e) echo "  [ERROR] $e\n";
        if (count($errors) > 80) echo "  ... y " . (count($errors) - 80) . " más.\n";
        return 1;
    }
    echo $opt['dry'] ? "\nDRY-RUN OK: archivo válido.\n" : "\nOK: $totIns registros insertados.\n";
    return 0;
}

// ---------------------------------------------------------------- inserts
function dupPersona(PDO $pdo, string $ci, string $em, string $ce, ?int $exclude = null): ?string {
    $q = 'SELECT ci, email, cel FROM persona WHERE ci=? OR email=? OR cel=?' . ($exclude ? ' AND id_persona!=?' : '') . ' LIMIT 1';
    $st = $pdo->prepare($q);
    $st->execute($exclude ? [$ci, $em, $ce, $exclude] : [$ci, $em, $ce]);
    $r = $st->fetch();
    if (!$r) return null;
    if ($r['ci'] === $ci) return "CI $ci ya existe en BD.";
    if (mb_strtolower($r['email']) === mb_strtolower($em)) return "email $em ya existe en BD.";
    return "celular $ce ya existe en BD.";
}

function insGestion(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    if ($pdo->query('SELECT 1 FROM gestion WHERE gestion=' . (int)$v['gestion'])->fetchColumn()) { err($e,$s,$n,"gestion {$v['gestion']} ya existe."); return 0; }
    if ($v['status'] === 1 && $pdo->query('SELECT 1 FROM gestion WHERE status=1')->fetchColumn()) { err($e,$s,$n,"ya hay gestión Activa."); return 0; }
    $pdo->prepare('INSERT INTO gestion (gestion,inicio,fin,gestion_l,monto_pension,descripcion,status) VALUES (?,?,?,?,?,?,?)')
        ->execute([$v['gestion'],$v['inicio'],$v['fin'],$v['gestion_l'],$v['monto'],$v['descripcion'],$v['status']]);
    return 1;
}

function insParalelo(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    $st = $pdo->prepare('SELECT 1 FROM paralelo WHERE nivel=? AND grado=? AND sigla=? LIMIT 1');
    $st->execute([$v['nivel'],$v['grado'],$v['sigla']]);
    if ($st->fetchColumn()) { err($e,$s,$n,"paralelo {$v['nivel']}-{$v['grado']}-{$v['sigla']} ya existe."); return 0; }
    $tutor = $v['tutor'];
    if ($tutor !== 'Sin asignación') { // debe ser docente activo
        $st = $pdo->prepare("SELECT CONCAT(nombre,' ',apellido) FROM persona WHERE CONCAT(nombre,' ',apellido)=? AND id_rol=5 AND status=1 LIMIT 1");
        $st->execute([$tutor]);
        if (!$st->fetchColumn()) { err($e,$s,$n,"tutor '$tutor' no es docente activo."); return 0; }
    }
    $pdo->prepare('INSERT INTO paralelo (tutor,nivel,grado,sigla,cupo,turno,status) VALUES (?,?,?,?,?,?,?)')
        ->execute([$tutor,$v['nivel'],$v['grado'],$v['sigla'],$v['cupo'],$v['turno'],$v['status']]);
    return 1;
}

function insMateria(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    $st = $pdo->prepare('SELECT 1 FROM materia WHERE nombre_mat=? AND grado=? LIMIT 1');
    $st->execute([$v['nombre'],$v['grado']]);
    if ($st->fetchColumn()) { err($e,$s,$n,"materia {$v['nombre']} grado {$v['grado']} ya existe."); return 0; }
    $pdo->prepare('INSERT INTO materia (area_mat,nombre_mat,descripcion_mat,grado,nivel,horas_mat,status) VALUES (?,?,?,?,?,?,?)')
        ->execute([$v['area'],$v['nombre'],$v['desc'] ?: null,$v['grado'],$v['nivel'],$v['horas'],$v['status']]);
    return 1;
}

function insPersonal(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    if ($m = dupPersona($pdo, $v['ci'], $v['email'], $v['cel'])) { err($e,$s,$n,$m); return 0; }
    $idRol = rolId($pdo, $v['rol'], false);
    if (!$idRol) { err($e,$s,$n,"rol '{$v['rol']}' no existe."); return 0; }
    $pdo->prepare('INSERT INTO persona (ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,status,id_rol) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$v['ci'],$v['nombre'],$v['apellido'],$v['sexo'],$v['dir'],$v['cel'],$v['email'],$v['email'],
            password_hash($v['ci'], PASSWORD_DEFAULT), 1, $idRol]);
    // Nota: tablas hijas docente/administrativo no las usa el MVC (ver revisión); se deja solo persona.
    return 1;
}

function insEstudiante(PDO $pdo, array $v, array &$e, string $s, int $n, array $estCols, ?int &$folioNext): int {
    if ($m = dupPersona($pdo, $v['ci'], $v['email'], $v['cel'])) { err($e,$s,$n,$m); return 0; }
    $st = $pdo->prepare('SELECT 1 FROM estudiante WHERE rude=? LIMIT 1');
    $st->execute([$v['rude']]);
    if ($st->fetchColumn()) { err($e,$s,$n,"rude {$v['rude']} ya existe."); return 0; }
    $folio = $v['folio'];
    if ($folio === '' && in_array('folio_fisico', $estCols, true)) { $folio = (string)$folioNext; $folioNext++; }
    if ($folio !== '' && in_array('folio_fisico', $estCols, true)) {
        $st = $pdo->prepare('SELECT 1 FROM estudiante WHERE folio_fisico=? LIMIT 1');
        $st->execute([$folio]);
        if ($st->fetchColumn()) { err($e,$s,$n,"folio_fisico $folio ya existe."); return 0; }
    }
    $idRol = rolId($pdo, 'Estudiante', false) ?? 7;
    $pdo->prepare('INSERT INTO persona (ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,status,id_rol) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$v['ci'],$v['nombre'],$v['apellido'],$v['sexo'],$v['dir'],$v['cel'],$v['email'],$v['email'],
            password_hash($v['ci'], PASSWORD_DEFAULT), 1, $idRol]);
    $idP = (int)$pdo->lastInsertId();
    $cols = ['id_persona','colegio_proc','rude','provincia','ciudad','pais','fnacimiento','emergencia','estado_reg','status'];
    $vals = [$idP,$v['col'],$v['rude'],$v['prov'],$v['ciu'],$v['pais'],$v['fn'],$v['emer'],$v['estado_reg'],1];
    if (in_array('folio_fisico', $estCols, true)) { $cols[] = 'folio_fisico'; $vals[] = $folio !== '' ? (int)$folio : null; }
    foreach (['estante'=>$v['est'],'gaveta'=>$v['gav'],'estado_legajo'=>$v['leg']] as $c => $val) {
        if (in_array($c, $estCols, true)) { $cols[] = $c; $vals[] = ($val !== '' ? $val : null); }
    }
    $pdo->prepare('INSERT INTO estudiante (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')')
        ->execute($vals);
    return 1;
}

function insTutor(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    $st = $pdo->prepare('SELECT e.id_estudiante FROM estudiante e JOIN persona p ON p.id_persona=e.id_persona WHERE p.ci=? AND e.status!=0 LIMIT 1');
    $st->execute([$v['ci_est']]);
    $idEst = $st->fetchColumn();
    if (!$idEst) { err($e,$s,$n,"estudiante CI {$v['ci_est']} no existe."); return 0; }
    $st = $pdo->prepare('SELECT id_persona, id_rol FROM persona WHERE ci=? LIMIT 1');
    $st->execute([$v['ci']]);
    $per = $st->fetch();
    $idRolTutor = rolId($pdo, 'Tutor', true);
    if ($per && (int)$per['id_rol'] !== $idRolTutor) { err($e,$s,$n,"CI {$v['ci']} pertenece a otro rol."); return 0; }
    if (!$per) {
        if ($m = dupPersona($pdo, $v['ci'], $v['email'], $v['cel'])) { err($e,$s,$n,$m); return 0; }
        $pdo->prepare('INSERT INTO persona (ci,nombre,apellido,sexo,direccion_dom,cel,email,usuario,password,status,id_rol) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$v['ci'],$v['nombre'],$v['apellido'],$v['sexo'],$v['dir'],$v['cel'],$v['email'],$v['email'],
                password_hash($v['ci'], PASSWORD_DEFAULT), 1, $idRolTutor]);
        $idPer = (int)$pdo->lastInsertId();
    } else {
        $idPer = (int)$per['id_persona'];
        $st = $pdo->prepare('SELECT 1 FROM padre WHERE id_persona=? AND id_estudiante=? AND status!=0 LIMIT 1');
        $st->execute([$idPer, $idEst]);
        if ($st->fetchColumn()) { err($e,$s,$n,"vínculo ya existe."); return 0; }
        $pdo->prepare('UPDATE persona SET nombre=?,apellido=?,sexo=?,direccion_dom=?,cel=?,email=?,usuario=? WHERE id_persona=?')
            ->execute([$v['nombre'],$v['apellido'],$v['sexo'],$v['dir'],$v['cel'],$v['email'],$v['email'],$idPer]);
    }
    $pdo->prepare('INSERT INTO padre (id_persona,id_estudiante,tipo_parentesco,nacionalidad,estado_civil,profesion,empresa_trabajo,status) VALUES (?,?,?,?,?,?,?,1)')
        ->execute([$idPer,$idEst,$v['parentesco'],$v['nacionalidad'],$v['ec'] !== '' ? $v['ec'] : null,
            $v['prof'] !== '' ? $v['prof'] : null,$v['emp'] !== '' ? $v['emp'] : null]);
    return 1;
}

function insMatricula(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    $idPar = paraleloId($pdo, $v['par']);
    if (!$idPar) { err($e,$s,$n,"paralelo '{$v['par']}' no existe (use Nivel-Grado-Sigla)."); return 0; }
    try {
        $st = $pdo->prepare('CALL matricular_estudiante_y_generar_pensiones(?,?,?,?,?,?)');
        $st->execute([$v['ci'],$v['gestion'],$idPar,$v['tipo'],$v['folio'],$v['estado']]);
        $st->closeCursor();
    } catch (Throwable $x) {
        $m = $x->getMessage();
        $m = preg_replace('/^SQLSTATE\[\d+\]:\s*/', '', $m);
        $m = preg_replace('/^<<Unknown error>>:\s*\d+\s*/', '', $m);
        err($e,$s,$n,$m);
        return 0;
    }
    return 1;
}

function insCobro(PDO $pdo, array $v, array &$e, string $s, int $n): int {
    $pdo->prepare('INSERT INTO cobro (nombre,descripcion,tipo,ncuota,valor,status) VALUES (?,?,?,?,?,?)')
        ->execute([$v['nombre'],$v['desc'] !== '' ? $v['desc'] : null,$v['tipo'],$v['ncuota'],$v['valor'],$v['status']]);
    return 1;
}

exit(main($argv));
