<?php
namespace Services;

/**
 * Reglas del recibo/comprobante de pago. Unifica la condición de
 * verificabilidad (antes duplicada en Pensiones::recibos y Verificar::recibo).
 */
class ReciboService
{
    /** Solo un pago vigente con folio es verificable por QR. */
    public static function esVerificable(array $row): bool
    {
        return intval($row['nro_recibo'] ?? 0) > 0 && intval($row['estado_pago'] ?? 0) === 1;
    }

    public static function urlVerificacion(int $nro): string
    {
        return function_exists('qrVerifyUrl') ? qrVerifyUrl($nro) : '';
    }

    public static function formatosValidos(): array
    {
        return ['', 'carta', 'termica'];
    }

    public static function normalizarFormato(string $formato): string
    {
        $f = strtolower(trim($formato));
        return in_array($f, self::formatosValidos(), true) ? $f : '';
    }
}
