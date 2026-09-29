<?php
namespace Services;

/**
 * Reglas financieras compartidas. Unifica la inferencia de estado
 * (antes duplicada y divergida entre Pensiones y Usuarios) y el
 * formato monetario (antes 'Bs. '+number_format en 3 lugares).
 */
class FinanzasService
{
    /**
     * Estado de una mensualidad. Devuelve [clave, badge, detalle].
     * Claves: pagado | vencido | pendiente.
     */
    public static function estadoPension(array $row): array
    {
        if (intval($row['estado_pago'] ?? 0) === 1) {
            return [
                'clave' => 'pagado',
                'badge' => '<span class="badge badge-success m-1 px-3">Pagado</span>',
                'detalle' => '',
            ];
        }
        $fv = trim((string)($row['fecha_vencimiento'] ?? ''));
        if (!empty($row['vencida'])) {
            return [
                'clave' => 'vencido',
                'badge' => '<span class="badge badge-danger m-1 px-3">Vencido</span><br><small class="text-muted">venció ' . $fv . '</small>',
                'detalle' => $fv,
            ];
        }
        return [
            'clave' => 'pendiente',
            'badge' => '<span class="badge badge-warning m-1 px-3">Pendiente</span><br><small class="text-muted">vence ' . $fv . '</small>',
            'detalle' => $fv,
        ];
    }

    public static function puedePagar(array $row): bool
    {
        return intval($row['estado_pago'] ?? 0) !== 1;
    }

    public static function puedeAnular(array $row): bool
    {
        return intval($row['estado_pago'] ?? 0) === 1;
    }

    public static function bolivianos($monto): string
    {
        return 'Bs. ' . number_format((float)$monto, 2);
    }

    public static function folio(int $nro): string
    {
        return '<b>' . str_pad((string)$nro, 6, '0', STR_PAD_LEFT) . '</b>';
    }

    // ---- Fragmentos SQL compartidos (Fase 4: una sola definición del agregado;
    // los modelos solo agregan el alias). Cadenas idénticas a las históricas. ----
    public const SQL_COB = "COALESCE(SUM(CASE WHEN p.estado_pago=1 THEN p.monto ELSE 0 END),0)";
    public const SQL_ADE = "COALESCE(SUM(CASE WHEN p.estado_pago=0 THEN p.monto ELSE 0 END),0)";
    public const SQL_VEN = "COALESCE(SUM(CASE WHEN p.estado_pago=0 AND p.fecha_vencimiento < CURDATE() THEN p.monto ELSE 0 END),0)";

    public static function pctCobro($cobrado, $adeudado): float
    {
        $cobrado = (float)$cobrado;
        $adeudado = (float)$adeudado;
        return ($cobrado + $adeudado) > 0 ? round(100 * $cobrado / ($cobrado + $adeudado), 1) : 0;
    }
}
