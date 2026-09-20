<?php 
/**
 * LoginModel.php
 * Modelo de Autenticación - Versión Completa con Rate Limiting
 * Sistema de Gestión Escolar Marista SS.CC.
 * 
 * Incluye:
 * - Prepared statements en todas las consultas
 * - Sistema de logs de intentos
 * - Rate limiting contra fuerza bruta
 * - Auditoría de seguridad
 */

class LoginModel extends Mysql
{
    private $intIdUsuario;
    private $strUsuario;
    private $strPassword;
    private $strToken;

    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * Autenticación de usuario
     * @param string $usuario - Email o nombre de usuario
     * @param string $password - Contraseña hasheada (SHA256)
     * @return array|false - Datos del usuario o false si no existe
     */

// En LoginModel.php
public function loginUser(string $usuario, string $password)
{
    error_log("=== LoginModel ===");
    error_log("Usuario recibido: " . $usuario);
    error_log("Password recibido (texto plano): " . $password);
    error_log("Longitud password: " . strlen($password));
    
    $sql = "SELECT id_persona, password, status FROM persona WHERE usuario = ?";
    $request = $this->select($sql, [$usuario]);
    
    if ($request) {
        error_log("Usuario encontrado en BD");
        error_log("Hash almacenado: " . $request['password']);
        error_log("Tipo hash: " . (strlen($request['password']) == 60 ? 'bcrypt' : 'desconocido'));
        // Verifica la longitud real del hash
        $hash = $request['password'];
        error_log("Longitud real del hash: " . strlen($hash));
        error_log("Hash en hex: " . bin2hex($hash));
        
        $verify = password_verify($password, $request['password']);
        error_log("Resultado password_verify: " . ($verify ? 'true' : 'false'));
        
        if ($verify) {
            return ['id_persona' => $request['id_persona'], 'status' => $request['status']];
        }
    } else {
        error_log("Usuario NO encontrado en BD");
    }
    
    return false;
}
    
    /**
     * Verifica si un hash de contraseña necesita ser actualizado a bcrypt.
     * @param string $hash
     * @return bool
     */
    private function needsRehash(string $hash): bool {
        // Si el hash no es de bcrypt (ej. tiene 64 caracteres hex, típico de SHA256)
        return strlen($hash) === 64 && ctype_xdigit($hash);
    }

    /**
     * Carga información completa del usuario para la sesión
     * @param int $iduser - ID del usuario
     * @return array|false - Datos completos del usuario con rol
     */
    public function sessionLogin(int $iduser)
    {
        $this->intIdUsuario = $iduser;
        
        $sql = "SELECT 
                p.id_persona,
                p.ci,
                p.nombre,
                p.apellido,
                p.cel,
                p.email,
                p.direccion_dom,
                p.sexo,
                p.usuario,
                DATE_FORMAT(p.fecha_reg, '%d-%m-%Y') AS fecha_reg,
                r.idrol,
                r.nombrerol,
                p.status 
            FROM persona p
            INNER JOIN rol r ON p.id_rol = r.idrol
            WHERE p.id_persona = ?";
        
        $arrData = [$this->intIdUsuario];
        $request = $this->select($sql, $arrData);
        
        if ($request) {
            $_SESSION['userData'] = $request;
        }
        
        return $request;
    }

    /**
     * Buscar usuario por email
     * @param string $strEmail - Email del usuario
     * @return array|false
     */
    public function getUserEmail(string $strEmail)
    {
        $sql = "SELECT id_persona, nombre, apellido, status 
                FROM persona WHERE email = ? AND status = 1";
        return $this->select($sql, [$strEmail]);
    }

    /**
     * Asignar token de recuperación
     * @param int $idpersona - ID del usuario
     * @param string $token - Token único
     * @return bool
     */
    public function setTokenUser(int $idpersona, string $token)
    {
        $sql = "UPDATE persona SET token = ? WHERE id_persona = ?";
        return $this->update($sql, [$token, $idpersona]);
    }

    /**
     * Validar usuario y token
     * @param string $email - Email del usuario
     * @param string $token - Token de recuperación
     * @return array|false
     */
    public function getUsuario(string $email, string $token)
    {
        $sql = "SELECT id_persona FROM persona WHERE email = ? AND token = ? AND status = 1";
        return $this->select($sql, [$email, $token]);
    }

    /**
     * Actualizar contraseña
     * @param int $idPersona - ID del usuario
     * @param string $password - Nueva contraseña hasheada
     * @return bool
     */
    public function insertPassword(int $idPersona, string $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE persona SET password = ?, token = ? WHERE id_persona = ?";
        $arrData = [$hashedPassword, "", $idPersona];
        return $this->update($sql, $arrData);
    }

    // ========================================================================
    // FUNCIONES DE SEGURIDAD - RATE LIMITING
    // ========================================================================

    /**
     * Registrar intento de login
     * @param string $usuario - Usuario que intentó login
     * @param bool $exitoso - Si el login fue exitoso
     * @param string $ip - IP del usuario
     * @param string $userAgent - User agent del navegador
     * @return bool
     */
    public function registrarIntentoLogin(
        string $usuario, 
        bool $exitoso, 
        string $ip, 
        string $userAgent = ''
    ) {
        try {
            $sql = "CALL sp_registrar_intento_login(?, ?, ?, ?)";
            $arrData = [
                $usuario,
                $exitoso ? 1 : 0,
                $ip,
                $userAgent
            ];
            
            $this->insert($sql, $arrData);
            return true;
        } catch (Exception $e) {
            error_log("Error al registrar intento de login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un usuario está bloqueado por intentos fallidos
     * @param string $usuario - Usuario a verificar
     * @param int $minutos - Ventana de tiempo (default: 15 minutos)
     * @param int $maxIntentos - Máximo de intentos (default: 5)
     * @return bool - True si está bloqueado
     */
    public function verificarBloqueo(
        string $usuario, 
        int $minutos = 15, 
        int $maxIntentos = 5
    ) {
        try {
            // Verificar si existe la tabla de logs
            $checkTable = "SHOW TABLES LIKE 'login_intentos'";
            $tableExists = $this->select($checkTable);
            
            if (!$tableExists) {
                // Si no existe la tabla, no bloquear
                return false;
            }

            // Usar procedimiento almacenado
            $sql = "CALL sp_verificar_bloqueo_login(?, ?, ?, @bloqueado)";
            $arrData = [$usuario, $minutos, $maxIntentos];
            $this->select($sql, $arrData);
            
            // Obtener el resultado
            $result = $this->select("SELECT @bloqueado as bloqueado");
            
            return isset($result['bloqueado']) && $result['bloqueado'] == 1;
        } catch (Exception $e) {
            error_log("Error al verificar bloqueo: " . $e->getMessage());
            // En caso de error, no bloquear (fail-open)
            return false;
        }
    }

    /**
     * Obtener estadísticas de intentos de login
     * @param int $horas - Últimas N horas
     * @return array
     */
    public function getEstadisticasLogin(int $horas = 24)
    {
        try {
            $sql = "SELECT 
                    usuario,
                    COUNT(*) as total_intentos,
                    SUM(CASE WHEN exitoso = 1 THEN 1 ELSE 0 END) as exitosos,
                    SUM(CASE WHEN exitoso = 0 THEN 1 ELSE 0 END) as fallidos,
                    MAX(fecha_intento) as ultimo_intento
                FROM login_intentos
                WHERE fecha_intento > DATE_SUB(NOW(), INTERVAL ? HOUR)
                GROUP BY usuario
                ORDER BY fallidos DESC
                LIMIT 20";
            
            $arrData = [$horas];
            $request = $this->select_all($sql, $arrData);
            
            return $request;
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener IPs con actividad sospechosa
     * @param int $intentos - Mínimo de intentos fallidos
     * @param int $minutos - En los últimos N minutos
     * @return array
     */
    public function getIPsSospechosas(int $intentos = 10, int $minutos = 60)
    {
        try {
            $sql = "SELECT 
                    ip_address,
                    COUNT(*) as total_intentos,
                    COUNT(DISTINCT usuario) as usuarios_diferentes,
                    MAX(fecha_intento) as ultimo_intento
                FROM login_intentos
                WHERE exitoso = 0 
                AND fecha_intento > DATE_SUB(NOW(), INTERVAL ? MINUTE)
                GROUP BY ip_address
                HAVING total_intentos >= ?
                ORDER BY total_intentos DESC";
            
            $arrData = [$minutos, $intentos];
            $request = $this->select_all($sql, $arrData);
            
            return $request;
        } catch (Exception $e) {
            error_log("Error al obtener IPs sospechosas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Limpiar intentos exitosos antiguos de un usuario
     * (Para no acumular registros innecesarios)
     * @param string $usuario - Usuario a limpiar
     * @return bool
     */
    public function limpiarIntentosExitosos(string $usuario)
    {
        try {
            $sql = "DELETE FROM login_intentos 
                    WHERE usuario = ? 
                    AND exitoso = 1 
                    AND fecha_intento < DATE_SUB(NOW(), INTERVAL 7 DAY)";
            
            $arrData = [$usuario];
            $this->delete($sql, $arrData);
            
            return true;
        } catch (Exception $e) {
            error_log("Error al limpiar intentos: " . $e->getMessage());
            return false;
        }
    }
}
?>