<?php
namespace Services;

/**
 * Reglas de gestión escolar anual. Unifica las 3 variantes de "gestión activa"
 * (CALL get_gestion_activa, SELECT status=1, subquery inline) y los fallbacks
 * de año esparcidos en Cursos/Matrícula.
 */
class GestionService
{
    private $model;

    public function __construct($model = null)
    {
        $this->model = $model ?: new \GestionModel();
    }

    /** Gestión activa o 0 si no hay. */
    public function activa(): int
    {
        $act = $this->model->selectGestionAct();
        return intval(is_array($act) ? ($act['gestion'] ?? 0) : 0);
    }

    /** Normaliza un año: fuera de rango 2000-2100 → activa, si no año actual. */
    public function resolver($gestion): int
    {
        $g = intval($gestion);
        if ($g >= 2000 && $g <= 2100) {
            return $g;
        }
        $a = $this->activa();
        return $a > 0 ? $a : (int)date('Y');
    }

    /** Monto de pensión de una gestión (0 si no existe). */
    public function monto(int $gestion): float
    {
        $row = $this->model->selectGestion($gestion);
        if (!is_array($row)) {
            return 0.0;
        }
        return floatval($row['monto_pension'] ?? 0);
    }

    /** Campos mínimos del formulario de gestión (mismo mensaje de antes). */
    public static function validar(array $p): ?string
    {
        if (empty($p['anio']) || empty($p['fechaInicio']) || empty($p['fechaFin']) || empty($p['intPension']) || empty($p['txtDescripcion'])) {
            return 'Datos incorrectos.';
        }
        return null;
    }

    /**
     * Apertura (status 1) o actualización según $esNueva.
     * Devuelve el código crudo del modelo para que el controlador
     * conserve su mapeo histórico de mensajes.
     */
    public function guardar(bool $esNueva, int $gestion, string $inicio, string $fin, string $gest, int $pension, string $descripcion): ServiceResult
    {
        if ($esNueva) {
            $res = $this->model->insertNewGestion($gestion, $inicio, $fin, $gest, $pension, $descripcion, 1);
        } else {
            $res = $this->model->updateGestion($gestion, $inicio, $fin, $gest, $pension, $descripcion);
        }
        if ($res == "dato_guardado") {
            return ServiceResult::ok('Datos guardados correctamente.');
        }
        return ServiceResult::fail((string)$res, 'modelo');
    }

    public function cerrar(): ServiceResult
    {
        if ($this->model->deleteGestion()) {
            return ServiceResult::ok('Se ha cerrado la gestión');
        }
        return ServiceResult::fail('Error al cerrar la gestión.');
    }
}
