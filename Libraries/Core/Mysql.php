<?php 
class Mysql extends Conexion {
    private $conexion;
    private $strquery;
    private $arrValues;
    private $lastQuery;
    // Profundidad de transacciones explícitas del llamador (p.ej. deleteEstudiante).
    // Un CALL fallido (SIGNAL) deja abierta la transacción iniciada dentro del SP;
    // si nadie la gestiona (depth 0) hay que cerrarla o envenena el resto del request.
    private $txnDepth = 0;
    
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
            $this->rollbackLeaked();
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
            $this->rollbackLeaked();
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
            $this->rollbackLeaked();
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
            $this->rollbackLeaked();
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
            $this->rollbackLeaked();
            $this->logError($e);
            return false;
        }
    }
    
    // Transacciones
    public function beginTransaction() {
        $r = $this->conexion->beginTransaction();
        if ($r) { $this->txnDepth++; }
        return $r;
    }
    
    public function commit() {
        $r = $this->conexion->commit();
        if ($r) { $this->txnDepth = max(0, $this->txnDepth - 1); }
        return $r;
    }
    
    public function rollback() {
        try {
            $r = $this->conexion->rollBack();
        } catch (Exception $x) {
            $r = false;
        }
        $this->txnDepth = max(0, $this->txnDepth - 1);
        return $r;
    }

    // Cierra una transacción huérfana dejada por un SP fallido (START TRANSACTION
    // dentro del SP + SIGNAL sin ROLLBACK). Solo actúa si el llamador no gestiona
    // transacción propia (depth 0); nunca toca transacciones explícitas ajenas.
    // NOTA: PDO::inTransaction() NO sirve aquí porque solo rastrea transacciones
    // iniciadas vía PDO::beginTransaction, no las del servidor; por eso el
    // ROLLBACK es incondicional (sin txn activa es un no-op inofensivo en MySQL).
    private function rollbackLeaked() {
        if ($this->txnDepth !== 0) {
            return;
        }
        try {
            $this->conexion->exec("ROLLBACK");
        } catch (Exception $x) {
            // Sin transacción activa o error al revertir: nada que hacer
        }
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