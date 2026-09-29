<?php
namespace Services;

/**
 * Caso de uso Iniciar sesión. Centraliza rate-limit + verificación + estado
 * (antes esparcido en Login::loginUser con umbrales hardcodeados).
 * No toca $_SESSION (lo hace el controlador); retorna datos para la sesión.
 */
class AuthService
{
    private $model;
    private int $minutes;
    private int $attempts;

    public function __construct($loginModel, int $minutes = 15, int $attempts = 5)
    {
        $this->model = $loginModel;
        $this->minutes = $minutes > 0 ? $minutes : 15;
        $this->attempts = $attempts > 0 ? $attempts : 5;
    }

    /**
     * @return ServiceResult ok con ['id_persona','status'] / fail con mensaje final
     */
    public function attempt(string $usuario, string $password, string $ip, string $agent = ''): ServiceResult
    {
        if ($this->model->verificarBloqueo($usuario, $this->minutes, $this->attempts)) {
            $this->model->registrarIntentoLogin($usuario, false, $ip, $agent);
            return ServiceResult::fail('Demasiados intentos. Espere ' . $this->minutes . ' minutos.', 'bloqueado');
        }
        $u = $this->model->loginUser($usuario, $password);
        if (!$u) {
            $this->model->registrarIntentoLogin($usuario, false, $ip, $agent);
            return ServiceResult::fail('Usuario o contraseña incorrectos', 'credenciales');
        }
        if (intval($u['status'] ?? 0) != 1) {
            $this->model->registrarIntentoLogin($usuario, false, $ip, $agent);
            return ServiceResult::fail('Usuario inactivo', 'inactivo');
        }
        $this->model->registrarIntentoLogin($usuario, true, $ip, $agent);
        return ServiceResult::ok('ok', ['id_persona' => $u['id_persona'], 'status' => $u['status']]);
    }
}
