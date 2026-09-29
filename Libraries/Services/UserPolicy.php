<?php
namespace Services;

/**
 * Reglas de autorización y normalización de usuarios. Centraliza los
 * `if(permisosMod)` de dominio, la protección del admin y el usuario por defecto.
 */
class UserPolicy
{
    /** El superadmin nunca se toca. */
    public static function isProtected(int $idPersona): bool
    {
        return $idPersona === 1;
    }

    public static function isSelf(array $session, int $targetId): bool
    {
        return intval($session['idUser'] ?? 0) === $targetId;
    }

    /** ¿Puede administrar usuarios? (módulo Usuarios exige rol admin o ser id 1). */
    public static function canAdminister(array $session): bool
    {
        if (intval($session['idUser'] ?? 0) === 1) {
            return true;
        }
        return intval($session['userData']['idrol'] ?? 0) === 1;
    }

    /**
     * Regla exacta de los botones editar/eliminar (antes duplicada en ambos):
     * superadmin total, o admin sobre no-admines.
     */
    public static function puedeEditar(array $session, array $target): bool
    {
        $idUser = intval($session['idUser'] ?? 0);
        $miRol = intval($session['userData']['idrol'] ?? 0);
        $rolObj = intval($target['idrol'] ?? 0);
        return ($idUser == 1 && $miRol == 1) || ($miRol == 1 && $rolObj != 1);
    }

    public static function puedeEliminar(array $session, array $target): bool
    {
        if (!self::puedeEditar($session, $target)) {
            return false;
        }
        return intval($session['userData']['id_persona'] ?? 0) != intval($target['id_persona'] ?? 0);
    }

    public static function can(array $session, string $perm, string $modulo = ''): bool
    {
        if ($modulo !== '') {
            return !empty($session['permisos'][$modulo][$perm]);
        }
        return !empty($session['permisosMod'][$perm]);
    }

    /** Usuario de acceso: el digitado o el email (nunca vacío). */
    public static function loginFor(string $typed, string $email): string
    {
        $typed = strtolower(trim($typed));
        if ($typed !== '') {
            return $typed;
        }
        return PasswordPolicy::loginFor($email, '');
    }
}
