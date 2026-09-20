<?php
class Conexion {
    private static $instance = null;
    private $conect;
    
    protected function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function conect() {
        return self::getInstance()->getConnection();
    }
    
    private function connect() {
        
        $connectionString = "mysql:host=" . DB_HOST . 
                           ";dbname=" . DB_NAME . 
                           ";charset=".DB_CHARSET;  
        
        try {
            $this->conect = new PDO($connectionString, DB_USER, DB_PASSWORD);
            
            // Configuraciones de seguridad
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Configuración explícita de charset y collation
            $this->conect->exec("SET NAMES ".DB_CHARSET);
            $this->conect->exec("SET CHARACTER SET ".DB_CHARSET);
            $this->conect->exec("SET collation_connection = ".DB_COLLATION);
            
            // Zona horaria
            $this->conect->exec("SET time_zone = '-04:00'");
            
        } catch (PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] Error de conexión BD: " . $e->getMessage());
            
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                throw new Exception("Error de conexión a BD: " . $e->getMessage());
            } else {
                throw new Exception("Error de conexión. Intente más tarde.");
            }
        }
    }
    
    public function getConnection() {
        try {
            $this->conect->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect();
        }
        return $this->conect;
    }
    
    public function __destruct() {
        $this->conect = null;
    }
}
?>