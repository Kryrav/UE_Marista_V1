<?php
namespace Services;

/**
 * Resultado estándar de la capa de servicios.
 * Sustituye los códigos mágicos de los modelos ('exist:', 'matricula_guardada'...)
 * en el borde servicio→controlador. Los modelos y SPs no cambian.
 */
class ServiceResult
{
    public bool $ok;
    public string $code;
    public string $msg;
    public array $data;

    private function __construct(bool $ok, string $code, string $msg, array $data = [])
    {
        $this->ok = $ok;
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    public static function ok(string $msg = 'OK', array $data = []): self
    {
        return new self(true, 'ok', $msg, $data);
    }

    public static function fail(string $msg, string $code = 'error', array $data = []): self
    {
        return new self(false, $code, $msg, $data);
    }

    public static function exists(string $msg): self
    {
        return new self(false, 'exist', $msg);
    }

    /** Respuesta JSON con el formato que ya espera el frontend ({status,msg,...}). */
    public function toArray(): array
    {
        return array_merge(['status' => $this->ok, 'msg' => $this->msg], $this->data);
    }
}
