<?php
namespace Services;

/**
 * Reglas de paralelos/cursos. Extrae del controlador la validación de tutor,
 * la normalización y los 3 cálculos inconsistentes de ocupación/cupo.
 */
class CursoService
{
    private $model;
    private $docentes;

    public function __construct($model = null, $docentes = null)
    {
        $this->model = $model ?: new \CursosModel();
        $this->docentes = $docentes;
    }

    private function docentes()
    {
        if ($this->docentes === null) {
            $this->docentes = new \DocentesModel();
        }
        return $this->docentes;
    }

    /** Cupos libres, nunca negativo (unifica las 3 variantes). */
    public static function cupoLibre(int $cupo, int $inscritos): int
    {
        return max(0, $cupo - $inscritos);
    }

    /**
     * Estado de ocupación ÚNICO (antes 3 versiones: cards con clamp a 100,
     * opciones sin clamp, tabla sin clamp).
     * @return array{pct:int, libres:int, label:string, bar:string, class:string}
     */
    public static function estadoOcupacion(int $total, int $cupo): array
    {
        $pct = $cupo > 0 ? min(100, (int)round($total / $cupo * 100)) : 0;
        if ($total >= $cupo && $cupo > 0) {
            return ['pct' => $pct, 'libres' => 0, 'label' => 'Cupo lleno', 'bar' => 'bg-danger', 'class' => 'full'];
        }
        if ($pct >= 85) {
            return ['pct' => $pct, 'libres' => self::cupoLibre($cupo, $total), 'label' => 'Últimos cupos', 'bar' => 'bg-warning', 'class' => 'warn'];
        }
        return ['pct' => $pct, 'libres' => self::cupoLibre($cupo, $total), 'label' => 'Disponible', 'bar' => 'bg-success', 'class' => 'ok'];
    }

    /**
     * Normalización de paralelo (niveles/turnos/cupo/status con defaults).
     * @return array{nivel:string, grado:int, sigla:string, turno:string, cupo:int, status:int}
     */
    public static function normalizar(array $in): array
    {
        $rawGrado = $in['grado'] ?? '';
        if (strtolower((string)$rawGrado) === 'inicial') {
            $nivel = 'Inicial';
            $grado = 0;
        } else {
            $nivel = $in['nivel'] ?? 'Primaria';
            if ($nivel == '' || $nivel == '1') {
                $nivel = 'Primaria';
            }
            $grado = intval($rawGrado);
        }
        $sigla = $in['sigla'] ?? 'A';
        $rawTurno = $in['turno'] ?? 'M';
        $turno = ($rawTurno === 'T' || strtolower((string)$rawTurno) === 'tarde') ? 'Tarde' : 'Mañana';
        $cupo = intval($in['cupo'] ?? 30);
        if ($cupo <= 0) {
            $cupo = 30;
        }
        $status = intval($in['status'] ?? 1);
        if ($status != 0 && $status != 1) {
            $status = 1;
        }
        return ['nivel' => $nivel, 'grado' => $grado, 'sigla' => $sigla, 'turno' => $turno, 'cupo' => $cupo, 'status' => $status];
    }

    /**
     * Resuelve el nombre del tutor validando docente activo.
     * @return ServiceResult ok con ['tutor' => nombre|'Sin asignación'] o fail
     */
    public function resolverTutor(int $idDocente): ServiceResult
    {
        $tutor = $this->docentes()->nombreTutor($idDocente);
        if ($tutor === null) {
            return ServiceResult::fail('El tutor debe ser un docente activo de la unidad educativa.', 'tutor_invalido');
        }
        return ServiceResult::ok('OK', ['tutor' => $tutor]);
    }

    public function opcionesTutores(): array
    {
        return $this->docentes()->optionsActivos() ?: [];
    }
}
