<?php 
class Mysql extends Conexion {
    private $conexion;
    private $strquery;
    private $arrValues;
    private $lastQuery;
    
    function __construct() {
        // Obtener la instancia singleton de Conexion
        $conexionInstance = parent::getInstance();
        $this->conexion = $conexionInstance->getConnection();
    }

    //Insertar un registro
    public function insert(string $query, array $arrValues = []) {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $this->lastQuery = $query;
        
        try {
            $insert = $this->conexion->prepare($this->strquery);
            $resInsert = $insert->execute($this->arrValues);
            
            if($resInsert) {
                $lastInsert = $this->conexion->lastInsertId();
                return $lastInsert ?: true;
            }
            return false;
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    //Busca un registro (CON PARÁMETROS)
    public function select(string $query, array $arrValues = []) {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $this->lastQuery = $query;
        
        try {
            $result = $this->conexion->prepare($this->strquery);
            $result->execute($this->arrValues);
            return $result->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    //Devuelve todos los registros (CON PARÁMETROS)
    public function select_all(string $query, array $arrValues = []) {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $this->lastQuery = $query;
        
        try {
            $result = $this->conexion->prepare($this->strquery);
            $result->execute($this->arrValues);
            return $result->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->logError($e);
            return [];
        }
    }

    //Actualiza registros
    public function update(string $query, array $arrValues = []) {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $this->lastQuery = $query;
        
        try {
            $update = $this->conexion->prepare($this->strquery);
            return $update->execute($this->arrValues);
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    //Eliminar un registro (CON PARÁMETROS)
    public function delete(string $query, array $arrValues = []) {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $this->lastQuery = $query;
        
        try {
            $result = $this->conexion->prepare($this->strquery);
            return $result->execute($this->arrValues);
            
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }
    
    // Transacciones
    public function beginTransaction() {
        return $this->conexion->beginTransaction();
    }
    
    public function commit() {
        return $this->conexion->commit();
    }
    
    public function rollback() {
        return $this->conexion->rollback();
    }
    
    // Helper para debugging
    public function getLastQuery() {
        return $this->lastQuery;
    }
    
    // Log de errores
    private function logError(PDOException $e) {
        $errorMsg = sprintf(
            "SQL Error: %s\nQuery: %s\nParams: %s",
            $e->getMessage(),
            $this->lastQuery,
            json_encode($this->arrValues)
        );
        
        error_log($errorMsg);
        
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            throw new Exception("Error en consulta SQL: " . $e->getMessage());
        }
    }
}
?>