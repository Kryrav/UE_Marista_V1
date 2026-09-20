<?php 
// ============================================================================
// Autoload.php - Autocargador 
// ============================================================================

spl_autoload_register(function($className) {
    
    // 1. Normalizar nombre de clase (manejar namespaces si existen)
    $className = str_replace('\\', '/', $className);
    $originalClassName = $className;
    
    // 2. Definir directorios de búsqueda en orden de prioridad
    $directories = [
        'Libraries/Core/',
        'Models/',
        'Controllers/',
        'Helpers/',
        'Libraries/'
    ];
    
    // 3. Buscar en cada directorio
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            
            // Verificar que la clase fue cargada
            if (class_exists($originalClassName) || interface_exists($originalClassName) || trait_exists($originalClassName)) {
                return true;
            }
        }
        
        // También intentar con la primera letra en mayúscula (para compatibilidad)
        $file = $directory . ucfirst($className) . '.php';
        if (file_exists($file)) {
            require_once $file;
            
            if (class_exists($originalClassName) || interface_exists($originalClassName) || trait_exists($originalClassName)) {
                return true;
            }
        }
    }
    
    // 4. Loggear error si no se encuentra (solo en desarrollo)
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        error_log("Autoload: Clase no encontrada - " . $originalClassName);
    }
    
    return false;
});

// 5. Cargar clases esenciales que siempre se necesitan
if (!class_exists('Conexion') && file_exists('Libraries/Core/Conexion.php')) {
    require_once 'Libraries/Core/Conexion.php';
}

if (!class_exists('Mysql') && file_exists('Libraries/Core/Mysql.php')) {
    require_once 'Libraries/Core/Mysql.php';
}
?>