<?php
// models/BaseModel.php
abstract class BaseModel extends Mysql {
    
    protected $table;
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    protected $rules = [];
    
    //##########################################################
    // Obtener todos los registros activos
    //##########################################################
    public function getAllActive() {
        $sql = "SELECT * FROM {$this->table} WHERE status = 1 ORDER BY {$this->primaryKey} DESC";
        return $this->select_all($sql);
    }
    
    //##########################################################
    // Obtener un registro por su ID
    //##########################################################
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? ";
        return $this->select($sql, [$id]);
    }
    
    //##########################################################
    // Eliminación lógica
    //##########################################################
    public function deletebyId($id) {
        $sql = "UPDATE {$this->table} SET status = 0 WHERE {$this->primaryKey} = ?";
        return $this->update($sql, [$id]);
    }
    
    //##########################################################
    // Validación de datos antes de insertar o actualizar
    //##########################################################
    protected function validate(array $data, $isUpdate = false) {
        $errors = [];
        
        foreach ($this->rules as $field => $rule) {
            if ($isUpdate && !isset($data[$field])) {
                continue; // En update, los campos opcionales pueden no estar
            }
            
            $value = $data[$field] ?? null;
            
            // Required
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "El campo {$field} es requerido";
            }
            
            // Email
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "El campo {$field} debe ser un email válido";
            }
            
            // Unique (necesita implementación específica en cada modelo)
            if (strpos($rule, 'unique') !== false && $this->isUnique($field, $value, $isUpdate ? $data[$this->primaryKey] ?? null : null)) {
                $errors[$field] = "El valor de {$field} ya existe";
            }
            
            // Min length
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                if (strlen($value) < intval($matches[1])) {
                    $errors[$field] = "El campo {$field} debe tener al menos {$matches[1]} caracteres";
                }
            }
            
        }

        return $errors;
    }
    
    //##########################################################
    // Verificar si un valor es único en la base de datos (necesita ser implementado en cada modelo)
    //##########################################################
    protected function isUnique($field, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$field} = ?";
        $params = [$value];
        
        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->select($sql, $params);
        return $result['total'] > 0;
    }
    
    //##########################################################
    // Transacciones
    //##########################################################
    public function beginTransaction() {
        return parent::beginTransaction();
    }
    
    /**
     * Confirmar transacción
     */
    public function commit() {
        return parent::commit();
    }
    
    /**
     * Revertir transacción
     */
    public function rollback() {
        return parent::rollback();
    }
}