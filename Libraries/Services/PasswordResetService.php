<?php
namespace Services;

/**
 * Caso de uso Recuperar contraseña. Centraliza token + vigencia + envío
 * (antes en Login::resetPass/setPassword con el token persistido antes de enviar).
 * El token solo queda válido si el correo salió; si falla, se invalida.
 * No envía directamente: recibe un callable $mailer(array $data): bool.
 */
class PasswordResetService
{
    private $model;
    private int $minutes;

    public function __construct($loginModel, int $minutes = 60)
    {
        $this->model = $loginModel;
        $this->minutes = $minutes > 0 ? $minutes : 60;
    }

    /**
     * @return ServiceResult ok (mensajes finales idénticos a los actuales)
     */
    public function request(string $email, callable $mailer): ServiceResult
    {
        $email = strtolower(trim($email));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return ServiceResult::fail('El formato del email no es válido', 'email');
        }
        $user = $this->model->getUserEmail($email);
        if (empty($user)) {
            // No revelar si existe (mismo texto de antes)
            return ServiceResult::ok('Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña');
        }
        $token = function_exists('token') ? token() : bin2hex(random_bytes(32));
        $id = (int)$user['id_persona'];
        $sent = $mailer([
            'nombreUsuario' => ($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''),
            'email' => $email,
            'token' => $token,
        ]);
        if ($sent) {
            $this->model->setTokenUser($id, $token, $this->minutes);
            return ServiceResult::ok('Se ha enviado un correo con instrucciones para recuperar tu contraseña');
        }
        $this->model->setTokenUser($id, '');
        return ServiceResult::fail('Error al enviar el correo. Por favor intente más tarde', 'mail');
    }

    public function confirm(string $email, string $token): ServiceResult
    {
        $row = $this->model->getUsuario($email, $token);
        if (empty($row)) {
            return ServiceResult::fail('Token inválido o expirado', 'token');
        }
        return ServiceResult::ok('OK', ['id_persona' => $row['id_persona']]);
    }

    public function reset(int $idPersona, string $email, string $token, string $pass, string $confirm): ServiceResult
    {
        if ($pass !== $confirm) {
            return ServiceResult::fail('Las contraseñas no coinciden', 'mismatch');
        }
        if (!PasswordPolicy::meetsLength($pass)) {
            return ServiceResult::fail('La contraseña debe tener al menos ' . PasswordPolicy::minLength() . ' caracteres', 'corta');
        }
        $row = $this->model->getUsuario($email, $token);
        if (empty($row) || intval($row['id_persona']) !== $idPersona) {
            return ServiceResult::fail('Token inválido o expirado', 'token');
        }
        if ($this->model->insertPassword($idPersona, $pass)) {
            return ServiceResult::ok('Contraseña actualizada correctamente');
        }
        return ServiceResult::fail('Error al actualizar la contraseña. Por favor intente más tarde', 'error');
    }
}
