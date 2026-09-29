# AUTH — Correcciones al modelo de autenticación (M05)

**Fecha:** 2026-09-28 · **Migración:** `Tools/migraciones/m05_token_expiry.sql` (`persona.token_expiry`, tolerante si falta)

## Defectos corregidos

1. **Secretos en logs:** `LoginModel::loginUser` registraba clave en plano + hash. Eliminado; solo retorna.
2. **Reset sin vencimiento ni atomicidad:** `setTokenUser(id, token, minutes)` guarda `token_expiry`; `getUsuario` exige vigencia (NULL/expirado = inválido); `insertPassword` invalida token+vigencia. `resetPass` solo persiste el token si el correo salió; si falla, lo invalida. Vigencia: `RESET_TOKEN_MINUTES` (60).
3. **Rate-limit:** umbrales desde `$loginConfig` (ya no hardcodeados); `verificarBloqueo` fail-closed ante error (sigue permisivo solo si falta la tabla, con aviso).
4. **Sesión en una capa:** `sessionLogin` ya no escribe `$_SESSION` (lo hace el controlador).
5. **Política única:** `PASSWORD_MIN_LENGTH` en `Config.php`, usada en `Login` y `Persona::$rules`.
6. **Menores:** `getClientIP` solo honra proxy tras localhost/red privada; `strtolower` solo a emails; `LOGIN_MAX_ATTEMPTS/BLOCK_MINUTES` centralizados.

## Verificación

`TEST M05 OK`: login ok/mal, token vigente/expirado/invalidado, cambio de clave, sin efecto en sesión, `left=0`. Endpoint `loginUser` responde correctamente.
