<?php
namespace Services;

/**
 * Caso de uso Inscribir/Rematricular. Dueño único de la regla que antes vivía
 * duplicada en Estudiantes::matricularNuevo y Matricula::insert/rematricular.
 * Los controladores solo traducen HTTP↔parámetros y permisos; los mensajes
 * se conservan idénticos para no romper UX ni tests.
 */
class InscripcionService
{
    private $mat;
    private $gestionSvc;

    public function __construct($matModel = null, $gestionSvc = null)
    {
        $this->mat = $matModel ?: new \MatriculaModel();
        $this->gestionSvc = $gestionSvc ?: new GestionService();
    }

    /** ¿Faltan diferibles? → se sugiere pendiente aunque no marquen el check. */
    public static function requierePendiente(string $rude, string $email, string $cel, bool $marcado): bool
    {
        return $marcado || trim($rude) === '' || trim($email) === '' || trim($cel) === '';
    }

    /**
     * Plan documental. Respeta un plazo explícito si viene (formulario Matrícula);
     * si no, lo calcula (30 días hábiles) cuando es pendiente.
     * @return array{estado:string, plazo:?string, checklist:array, compromiso:int, obs:string}
     */
    public static function planDocs(string $estado, ?string $plazoOverride, array $checks, bool $compromiso, string $obs): array
    {
        $pendiente = ($estado === 'Pendiente_Documentos');
        $plazo = trim((string)$plazoOverride);
        if ($pendiente && $plazo === '') {
            $plazo = function_exists('plazo30Habiles') ? plazo30Habiles() : date('Y-m-d', strtotime('+30 days'));
        }
        if ($plazo === '') {
            $plazo = null;
        }
        return [
            'estado' => $estado,
            'plazo' => $plazo,
            'checklist' => [
                'ci' => 1,
                'cert_nac' => !empty($checks['cert_nac']) ? 1 : 0,
                'rude' => !empty($checks['rude']) ? 1 : 0,
                'solicitud' => !empty($checks['solicitud']) ? 1 : 0,
            ],
            'compromiso' => ($compromiso || $pendiente) ? 1 : 0,
            'obs' => $obs,
        ];
    }

    /**
     * Inscribe (SP genera las 10 pensiones) + docs + motivo de rectificación.
     * @return ServiceResult ok con ['idMatricula'] / exists / fail
     */
    public function inscribir(string $ci, int $gestion, int $paralelo, string $tipo, string $estado, ?int $userId, ?array $docsPlan = null, ?string $rectMotivo = null): ServiceResult
    {
        $res = $this->mat->insertMatricula($ci, $gestion, $paralelo, $tipo, '', $estado, $userId);
        if ($res == 'matricula_guardada') {
            if ($docsPlan !== null) {
                $this->mat->setDocumentacionByCiGestion($ci, $gestion, $docsPlan['plazo'], $docsPlan['checklist'], $docsPlan['compromiso'], $docsPlan['obs'], $estado);
            }
            if ($rectMotivo !== null && trim($rectMotivo) !== '') {
                $this->mat->setMotivoByCiGestion($ci, $gestion, 'Rectificación histórica: ' . trim($rectMotivo));
            }
            $row = $this->mat->select(
                "SELECT m.id_matricula FROM matricula m INNER JOIN estudiante e ON m.id_estudiante=e.id_estudiante
                 INNER JOIN persona p ON e.id_persona=p.id_persona
                 WHERE p.ci=? AND m.gestion=? ORDER BY m.id_matricula DESC LIMIT 1", [$ci, $gestion]);
            return ServiceResult::ok('matricula_guardada', ['idMatricula' => intval($row['id_matricula'] ?? 0)]);
        }
        if ($res == 'matricula_existente') {
            return ServiceResult::exists('matricula_existente');
        }
        return ServiceResult::fail((string)$res);
    }

    public function tutoresCount(string $ci): int
    {
        try {
            $nt = $this->mat->select(
                "SELECT COUNT(*) AS c FROM padre pa INNER JOIN estudiante e ON pa.id_estudiante=e.id_estudiante
                 INNER JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=? AND pa.status != 0", [$ci]);
            return intval($nt['c'] ?? 0);
        } catch (\Exception $x) {
            return 0;
        }
    }

    public function ultimaPorCi(string $ci): ?array
    {
        $last = $this->mat->ultimaMatriculaByCi($ci);
        return $last ?: null;
    }

    /**
     * Rematriculación (también primera matrícula de existente sin historial).
     * La regla de gestión activa vive aquí; el permiso de rectificar lo decide
     * el controlador (conoce el rol) y llega como $puedeRectificar.
     */
    public function rematricular(string $ci, int $gestion, int $paralelo, string $tipo, ?int $userId, int $gestionActiva, bool $puedeRectificar, string $motivoRect = ''): ServiceResult
    {
        if ($gestionActiva > 0 && $gestion !== $gestionActiva) {
            if (!$puedeRectificar) {
                return ServiceResult::fail('Solo se rematricula en la gestión activa (' . $gestionActiva . ').', 'gestion_no_activa');
            }
            if (trim($motivoRect) === '') {
                return ServiceResult::fail('Rectificación fuera de gestión activa: indique el motivo.', 'falta_motivo');
            }
        }
        if ($tipo === '') {
            $tipo = 'Regular';
        }
        $last = $this->ultimaPorCi($ci);
        $esPrimera = empty($last);
        if ($esPrimera) {
            $ex = $this->mat->select(
                "SELECT e.id_estudiante FROM estudiante e INNER JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci=? AND e.status != 0", [$ci]);
            if (empty($ex)) {
                return ServiceResult::fail('El CI no existe. Registre al estudiante primero.', 'sin_estudiante');
            }
        }
        $r = $this->inscribir($ci, $gestion, $paralelo, $tipo, $esPrimera ? 'Confirmado' : 'Inscrito', $userId,
            null, ($gestionActiva > 0 && $gestion !== $gestionActiva) ? $motivoRect : null);
        if (!$r->ok) {
            if ($r->code === 'exist') {
                return ServiceResult::fail('Ya está matriculado en esa gestión.', 'duplicado');
            }
            return ServiceResult::fail('No se pudo rematricular: ' . $r->msg);
        }
        $idEst = intval($last['id_estudiante'] ?? ($ex['id_estudiante'] ?? 0));
        $msg = $esPrimera
            ? "Matriculado en gestión $gestion (10 pensiones generadas)."
            : "Rematriculado en gestión $gestion (10 pensiones generadas). Curso anterior: " . ($last['curso'] ?? '—') . ".";
        return ServiceResult::ok($msg, ['idMatricula' => $r->data['idMatricula'], 'idEstudiante' => $idEst]);
    }
}
