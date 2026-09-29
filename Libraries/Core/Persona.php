<?php
// models/Persona.php
class Persona extends BaseModel {
    
    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    
    protected $allowedFields = [
        'ci', 'nombre', 'apellido', 'sexo', 'direccion_dom', 
        'cel', 'email', 'usuario', 'password', 'status', 'id_rol'
    ];
    
    protected $rules = [
        'ci' => 'required|min:5',
        'nombre' => 'required|min:2',
        'apellido' => 'required|min:2',
        'sexo' => 'required',
        'direccion_dom' => 'required',
        'cel' => 'required|min:8',
        'email' => 'required|email|unique',
        'usuario' => 'required|min:4|unique',
        'password' => 'required|min:'.PASSWORD_MIN_LENGTH,
        'id_rol' => 'required'
    ];
    
    /**
     * Insertar persona usando el stored procedure
     * @return array ['status' => 'success|exists|error', 'message' => string, 'data' => mixed]
     */
    public function insertPersona(array $data) {
        try {
            // Validar campos requeridos
            $errors = $this->validate($data);
            if (!empty($errors)) {
                return [
                    'status' => RESPONSE_ERROR,
                    'message' => 'Errores de validación',
                    'errors' => $errors
                ];
            }
            
            // Encriptar password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Llamar al stored procedure
            $sql = "CALL sp_insertar_persona(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $arrData = [
                $data['ci'],
                $data['nombre'],
                $data['apellido'],
                $data['sexo'],
                $data['direccion_dom'],
                $data['cel'],
                $data['email'],
                $data['usuario'],
                $data['password'],
                $data['status'] ?? 1,
                $data['id_rol']
            ];
            
            $result = $this->insert($sql, $arrData);
            return [
                'status' => RESPONSE_SUCCESS,
                'message' => 'Persona guardada exitosamente',
                'data' => ['id' => $result]
            ];
            
        } catch (PDOException $e) {
            // Verificar si es error de duplicado
            if ($e->errorInfo[1] == 1644) { // Código para SIGNAL en MySQL
                return [
                    'status' => RESPONSE_EXISTS,
                    'message' => 'La persona ya existe (CI, Email o Usuario duplicado)',
                    'data' => null
                ];
            }
            
            $this->logError($e, $data);
            
            return [
                'status' => RESPONSE_ERROR,
                'message' => ENV_DEVELOPMENT ? $e->getMessage() : 'Error al guardar la persona',
                'data' => null
            ];
        }
    }
    
    /**
     * Actualizar persona usando el stored procedure
     */
    public function updatePersona(int $id, array $data) {
        try {
            // Verificar que la persona existe
            $persona = $this->getById($id);
            if (!$persona) {
                return [
                    'status' => RESPONSE_NOT_FOUND,
                    'message' => 'Persona no encontrada',
                    'data' => null
                ];
            }
            
            // Validar (excluyendo campos que no se actualizan)
            $data[$this->primaryKey] = $id;
            $errors = $this->validate($data, true);
            if (!empty($errors)) {
                return [
                    'status' => RESPONSE_ERROR,
                    'message' => 'Errores de validación',
                    'errors' => $errors
                ];
            }
            // Encriptar password si se proporciona
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
           
            // Preparar los datos finales (usar nuevos o mantener antiguos)
            $finalData = [
                'ci' => array_key_exists('ci', $data) ? $data['ci'] : $persona['ci'],
                'nombre' => array_key_exists('nombre', $data) ? $data['nombre'] : $persona['nombre'],
                'apellido' => array_key_exists('apellido', $data) ? $data['apellido'] : $persona['apellido'],
                'sexo' => array_key_exists('sexo', $data) ? $data['sexo'] : $persona['sexo'],
                'direccion_dom' => array_key_exists('direccion_dom', $data) ? $data['direccion_dom'] : $persona['direccion_dom'],
                'cel' => array_key_exists('cel', $data) ? $data['cel'] : $persona['cel'],
                'email' => array_key_exists('email', $data) ? $data['email'] : $persona['email'],
                'usuario' => array_key_exists('usuario', $data) ? $data['usuario'] : $persona['usuario'],  // ✅ Ahora sí
                'password' => array_key_exists('password', $data) ? $data['password'] : $persona['password'],
                'status' => array_key_exists('status', $data) ? $data['status'] : $persona['status'],
                'id_rol' => array_key_exists('id_rol', $data) ? $data['id_rol'] : $persona['id_rol']
            ];

            $sql = "CALL sp_guardar_o_actualizar_persona(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $arrData = [
                $id,
                $finalData['ci'],
                $finalData['nombre'],
                $finalData['apellido'],
                $finalData['sexo'],
                $finalData['direccion_dom'],
                $finalData['cel'],
                $finalData['email'],
                $finalData['usuario'],
                $finalData['password'],
                $finalData['status'],
                $finalData['id_rol']
            ];
            
            $this->update($sql, $arrData);
            return [
                'status' => RESPONSE_SUCCESS,
                'message' => 'Persona actualizada exitosamente',
                'data' => ['id' => $id]
            ];
            
        } catch (PDOException $e) {
            $this->logError($e, $data);
            return [
                'status' => RESPONSE_ERROR,
                'message' => ENV_DEVELOPMENT ? $e->getMessage() : 'Error al actualizar la persona',
                'data' => null
            ];
        }
    }

    /** 
     * Eliminar persona (eliminación lógica)
     */
    public function deletePersona(int $idPersona) {
    // Puede llamar al método heredado
    return $this->deleteById($idPersona);
    }
    
    /**
     * Seleccionar personas con rol (usa el stored procedure)
     */
    public function selectPersonas(bool $esAdmin = false) {
        try {
            // 1=vista admin (ve todo), 0=vista normal (oculta superadmin id 1).
            // Se pasa entero: las cadenas 'TRUE'/'FALSE' se coercionan a 0 en MySQL.
            $flag = $esAdmin ? 0 : 1;
            $sql = "CALL sp_select_personas_con_rol(?)";
            return $this->select_all($sql, [$flag]);
        } catch (PDOException $e) {
            $this->logError($e);
            return [];
        }
    }
    
    /**
     * Seleccionar una persona específica con su rol
     */
    public function selectPersona(int $id) {
        try {
            $sql = "CALL sp_select_persona_rol(?)";
            return $this->select($sql, [$id]);
        } catch (PDOException $e) {
            $this->logError($e);
            return null;
        }
    }
    
    /**
     * Buscar persona por email o usuario (para login)
     */
    public function findByEmailOrUsuario($emailOrUsuario) {
        $sql = "SELECT p.*, r.nombrerol 
                FROM persona p
                INNER JOIN rol r ON p.id_rol = r.idrol
                WHERE (p.email = ? OR p.usuario = ?) AND p.status = 1";
        return $this->select($sql, [$emailOrUsuario, $emailOrUsuario]);
    }
    
    /**
     * Verificar credenciales para login
     */
    public function verificarCredenciales($emailOrUsuario, $password) {
        $persona = $this->findByEmailOrUsuario($emailOrUsuario);
        
        if ($persona && password_verify($password, $persona['password'])) {
            // Eliminar password del array por seguridad
            unset($persona['password']);
            return $persona;
        }
        
        return null;
    }
    
    /**
     * Override de isUnique para verificar en campos únicos
     */
    protected function isUnique($field, $value, $excludeId = null) {
        // Campos que deben ser únicos en persona
        $uniqueFields = ['ci', 'email', 'usuario', 'cel'];
        
        if (!in_array($field, $uniqueFields)) {
            return false; // No validar unicidad en campos no únicos
        }
        
        return parent::isUnique($field, $value, $excludeId);
    }
    
    /**
     * Log de errores 
     */
    private function logError(PDOException $e, $data = []) {
        $errorMsg = sprintf(
            "[%s] SQL Error: %s\nCódigo: %s\nDatos: %s\nTrace: %s",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getCode(),
            json_encode($data, JSON_PRETTY_PRINT),
            $e->getTraceAsString()
        );
        
        error_log($errorMsg);
    }


}
