<?php
namespace Services;

/**
 * Reglas del catálogo de cobros. Saca del controlador la normalización
 * tipo/ncuota/status (el modelo es CRUD puro y no valida).
 */
class CobroService
{
    private $model;

    public function __construct($model = null)
    {
        $this->model = $model ?: new \CobrosModel();
    }

    public static function normalizarTipo(string $raw): string
    {
        return ($raw == '2' || strtolower($raw) == 'mensual') ? 'Mensual' : 'Unico';
    }

    /**
     * Normaliza + valida un cobro desde el POST ya saneado.
     * @return ServiceResult ok con ['nombre','descripcion','tipo','ncuota','valor','status']
     */
    public static function normalizar(array $in): ServiceResult
    {
        $nombre = $in['nombre'] ?? '';
        $valor = floatval($in['valor'] ?? 0);
        if ($nombre == '' || $valor <= 0) {
            return ServiceResult::fail('Título y monto válido son obligatorios.', 'invalido');
        }
        $tipo = self::normalizarTipo((string)($in['tipo'] ?? 'Unico'));
        $ncuota = intval($in['ncuota'] ?? 1);
        if ($ncuota <= 0) {
            $ncuota = 1;
        }
        if ($tipo == 'Unico') {
            $ncuota = 1;
        }
        $status = intval($in['status'] ?? 1);
        if ($status != 0 && $status != 1) {
            $status = 1;
        }
        return ServiceResult::ok('OK', [
            'nombre' => $nombre,
            'descripcion' => $in['descripcion'] ?? '',
            'tipo' => $tipo,
            'ncuota' => $ncuota,
            'valor' => $valor,
            'status' => $status,
        ]);
    }

    public function guardar(int $id, array $norm): ServiceResult
    {
        if ($id > 0) {
            $res = $this->model->updateCobro($id, $norm['nombre'], $norm['descripcion'], $norm['tipo'], $norm['ncuota'], $norm['valor'], $norm['status']);
            $msg = 'Cobro actualizado.';
        } else {
            $res = $this->model->insertCobro($norm['nombre'], $norm['descripcion'], $norm['tipo'], $norm['ncuota'], $norm['valor'], $norm['status']);
            $msg = 'Cobro registrado.';
        }
        if ($res == 'dato_guardado') {
            return ServiceResult::ok($msg);
        }
        return ServiceResult::fail('No se pudo guardar: ' . $res);
    }
}
