<?php
// test_login_fixed.php
require_once('Config/Config.php');
require_once('Helpers/Helpers.php');

require_once('Libraries/Core/Conexion.php');
require_once('Libraries/Core/Mysql.php');
require_once('Models/LoginModel.php');

// Simular login con contraseña en texto plano
$usuario = 'admin';
$password = '123'; // Texto plano

echo "=== TEST DE LOGIN CORREGIDO ===\n\n";

$model = new LoginModel();
$resultado = $model->loginUser($usuario, $password);

echo "Usuario: $usuario\n";
echo "Contraseña (texto plano): $password\n";
echo "Resultado: " . ($resultado ? '✅ Login exitoso' : '❌ Login fallido') . "\n";

if ($resultado) {
    echo "ID Usuario: " . $resultado['id_persona'] . "\n";
    echo "Status: " . $resultado['status'] . "\n";
}

echo "\n=== FIN DEL TEST ===\n";

class RepararBD {
    private $db;
    
    public function __construct() {
        $this->db = Conexion::conect();
    }
    
    public function repararTodo() {
        echo "🔧 REPARACIÓN COMPLETA DE BASE DE DATOS\n";
        echo "========================================\n\n";
        
        // 1. Verificar conexión actual
        $stmt = $this->db->query("SELECT 
            @@character_set_client as client_charset,
            @@character_set_connection as conn_charset,
            @@character_set_results as results_charset,
            @@collation_connection as collation");
        $charsets = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "1. CONFIGURACIÓN ACTUAL:\n";
        print_r($charsets);
        
        // // 2. Cambiar BD a utf8mb4
        // echo "\n2. CAMBIANDO BASE DE DATOS A utf8mb4...\n";
        // $this->db->exec("ALTER DATABASE db_mr_sistm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        // echo "   ✅ Base de datos actualizada\n";
        
        // // 3. Obtener todas las tablas
        // $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        // // 4. Cambiar cada tabla
        // echo "\n3. CAMBIANDO TABLAS A utf8mb4:\n";
        // foreach ($tables as $table) {
        //     $this->db->exec("ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        //     echo "   ✅ Tabla: $table\n";
        // }
        
        // // 5. Cambiar columnas específicas a utf8mb4_bin
        // echo "\n4. OPTIMIZANDO COLUMNAS DE SEGURIDAD:\n";
        
        // $columnas = [
        //     'persona' => ['password', 'token'],
        //     'estudiante' => ['rude'],
        //     'matricula' => ['folio']
        // ];
        
        // foreach ($columnas as $tabla => $columnas_tabla) {
        //     foreach ($columnas_tabla as $columna) {
        //         try {
        //             $this->db->exec("ALTER TABLE $tabla MODIFY $columna TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
        //             echo "   ✅ $tabla.$columna → utf8mb4_bin\n";
        //         } catch (Exception $e) {
        //             echo "   ⚠️ $tabla.$columna: " . $e->getMessage() . "\n";
        //         }
        //     }
        // }
        
        // 6. Actualizar password del admin
        echo "\n5. ACTUALIZANDO PASSWORD DEL ADMIN:\n";
        $nuevo_hash = password_hash('123', PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE persona SET password = ? WHERE usuario = 'admin'");
        $stmt->execute([$nuevo_hash]);
        echo "   ✅ Hash actualizado\n";
        
        // 7. Verificar login
        $stmt = $this->db->prepare("SELECT password FROM persona WHERE usuario = 'admin'");
        $stmt->execute();
        $hash_bd = $stmt->fetchColumn();
        
        if (password_verify('123', $hash_bd)) {
            echo "\n✅ VERIFICACIÓN EXITOSA - Todo funciona correctamente\n";
        } else {
            echo "\n❌ VERIFICACIÓN FALLÓ - Revisa manualmente\n";
        }
    }
}

$reparar = new RepararBD();
$reparar->repararTodo();
?>