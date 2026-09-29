<?php
namespace Services;

/**
 * Reglas del catálogo de materias (unicidad nombre+grado vive en el modelo/SP).
 */
class MateriaService
{
    private $model;

    public function __construct($model = null)
    {
        $this->model = $model ?: new \MateriasModel();
    }

    public static function normalizar(array $in): array
    {
        return [
            'area' => $in['area'] ?? '',
            'nombre' => $in['nombre'] ?? '',
            'descripcion' => $in['descripcion'] ?? '',
            'grado' => intval($in['grado'] ?? 0),
            'nivel' => $in['nivel'] ?? '',
            'horas' => intval($in['horas'] ?? 0),
            'status' => intval($in['status'] ?? 1),
        ];
    }

    public function guardar(int $id, array $n): ServiceResult
    {
        // REV-SVC: el SP aborta con SIGNAL ante duplicado; se traduce a exists
        // en vez de dejar el fatal (comportamiento anterior en development).
        try {
            if ($id > 0) {
                $res = $this->model->updateMateria($id, $n['area'], $n['nombre'], $n['descripcion'], $n['grado'], $n['nivel'], $n['horas'], $n['status']);
                $msg = 'Materia actualizada satisfactoriamente.';
            } else {
                $res = $this->model->insertMateria($n['area'], $n['nombre'], $n['descripcion'], $n['grado'], $n['nivel'], $n['horas'], $n['status']);
                $msg = 'Materia registrada satisfactoriamente.';
            }
        } catch (\Exception $e) {
            $res = $e->getMessage();
        }
        if ($res == "dato_guardado") {
            return ServiceResult::ok($msg);
        }
        if ($res == "exist" || stripos((string)$res, 'ya existe') !== false) {
            return ServiceResult::exists('La materia ya existe en este grado.');
        }
        return ServiceResult::fail('No es posible guardar datos: ' . $res);
    }
}
