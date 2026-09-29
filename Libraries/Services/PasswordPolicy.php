<?php
namespace Services;

/**
 * Política única de credenciales. Elimina la regla `password=CI` duplicada
 * en 5 controladores (Usuarios, Tutores, Estudiantes, Docentes, Administrativos).
 */
class PasswordPolicy
{
    /** Clave inicial por defecto: el CI (los cambios van en Usuarios). */
    public static function defaultPassword(string $ci): string
    {
        return password_hash(trim($ci), PASSWORD_DEFAULT);
    }

    public static function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public static function minLength(): int
    {
        return defined('PASSWORD_MIN_LENGTH') ? (int)PASSWORD_MIN_LENGTH : 6;
    }

    public static function meetsLength(string $plain): bool
    {
        return strlen($plain) >= self::minLength();
    }

    /** Usuario de acceso: email si hay, si no el CI (nunca vacío). */
    public static function loginFor(?string $email, string $ci): string
    {
        $email = trim(strtolower((string)$email));
        if ($email !== '') {
            return $email;
        }
        return trim($ci);
    }
}
