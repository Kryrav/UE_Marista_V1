<?php
namespace Services;

/**
 * Presentación compartida (badges/estados). Cadenas HTML idénticas a las
 * actuales: solo cambia el lugar donde se generan, no lo que se ve.
 */
class Presenter
{
    public static function estado(int $status): string
    {
        return $status === 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>';
    }

    public static function estadoEstudiante(int $status): string
    {
        if ($status === 1) {
            return '<span class="badge badge-success">Activo</span>';
        }
        if ($status === 2) {
            return '<span class="badge badge-warning">Inactivo</span>';
        }
        return '<span class="badge badge-danger">Eliminado</span>';
    }

    public static function estadoInscripcion(string $estado, string $motivo = '', string $plazo = ''): string
    {
        switch ($estado) {
            case 'Pendiente_Documentos':
                $alerta = '';
                if ($plazo !== '' && function_exists('diasHabilesRestantes')) {
                    try {
                        $r = diasHabilesRestantes($plazo);
                        $alerta = $r < 0 ? ' (vencido ' . abs($r) . 'd)' : " ($r d)";
                    } catch (\Exception $x) {
                    }
                }
                return '<span class="badge badge-warning" title="Plazo: ' . htmlspecialchars($plazo) . '">Pendiente docs' . $alerta . '</span>';
            case 'Confirmado':
                return '<span class="badge badge-success">Confirmado</span>';
            case 'Inscrito':
                return '<span class="badge badge-info">Inscrito</span>';
            case 'Retirado':
                return '<span class="badge badge-secondary"' . ($motivo !== '' ? ' title="' . htmlspecialchars($motivo) . '"' : '') . '>Retirado</span>';
            case 'Trasladado':
                return '<span class="badge badge-dark"' . ($motivo !== '' ? ' title="' . htmlspecialchars($motivo) . '"' : '') . '>Trasladado</span>';
            case 'Egresado':
                return '<span class="badge badge-primary"' . ($motivo !== '' ? ' title="' . htmlspecialchars($motivo) . '"' : '') . '>Egresado</span>';
            default:
                return htmlspecialchars($estado);
        }
    }

    public static function pago(array $row): string
    {
        return FinanzasService::estadoPension($row)['badge'];
    }

    public static function sinAsignar(string $tutor): string
    {
        $tutor = trim($tutor);
        return $tutor === '' ? 'Sin asignación' : $tutor;
    }
}
