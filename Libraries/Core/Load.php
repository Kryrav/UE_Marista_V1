<?php 
// ============================================================================
// Load.php - Router Seguro Refactorizado
// ============================================================================

class Router {
    
    /**
     * Despacha la petición al controlador y método correspondientes
     */
    public static function dispatch($controller, $method, $params = []) {
        
        // 1. Validar nombres (solo letras permitidas)
        if (!self::isValidName($controller) || !self::isValidName($method)) {
            self::showError(400, "Nombre de controlador o método inválido");
            return;
        }
        
        // 2. Formatear nombres
        $controller = ucfirst($controller);
        $controllerFile = "Controllers/" . $controller . ".php";
        
        // 3. Prevenir directory traversal attacks
        if (!self::isValidPath($controllerFile)) {
            self::showError(404, "Controlador no encontrado");
            return;
        }
        
        // 4. Verificar existencia del archivo
        if (!file_exists($controllerFile)) {
            self::showError(404, "Controlador no encontrado: " . $controller);
            return;
        }
        
        // 5. Cargar controlador
        require_once $controllerFile;
        
        // 6. Verificar que la clase exista
        if (!class_exists($controller)) {
            self::showError(500, "La clase del controlador no existe: " . $controller);
            return;
        }
        
        // 7. Instanciar controlador
        $controllerInstance = new $controller();
        
        // 8. Lista de métodos prohibidos por seguridad
        $forbiddenMethods = [
            '__construct', '__destruct', '__call', '__callStatic', 
            '__get', '__set', '__isset', '__unset', '__sleep', 
            '__wakeup', '__toString', '__invoke', '__set_state', 
            '__clone', '__debugInfo', '__serialize', '__unserialize'
        ];
        
        // 9. Verificar método
        if (in_array(strtolower($method), $forbiddenMethods)) {
            self::showError(403, "Método no permitido por seguridad");
            return;
        }
        
        if (!method_exists($controllerInstance, $method)) {
            self::showError(404, "Método no encontrado: " . $method);
            return;
        }
        
        // 10. Ejecutar método con parámetros
        try {
            call_user_func_array([$controllerInstance, $method], $params);
        } catch (Exception $e) {
            self::showError(500, "Error en la ejecución: " . $e->getMessage());
        }
    }
    
    /**
     * Valida que un nombre solo contenga letras
     */
    private static function isValidName($name) {
        return preg_match('/^[a-zA-Z]+$/', $name);
    }
    
    /**
     * Valida que la ruta sea segura (previene directory traversal)
     */
    private static function isValidPath($path) {
        // Normalizar la ruta
        $normalizedPath = realpath($path) ?: $path;
        
        // Verificar que no intente salir del directorio raíz
        $basePath = realpath("Controllers/");
        if ($basePath === false) {
            return false;
        }
        
        // Asegurar que la ruta comience con el directorio base
        return strpos($normalizedPath, $basePath) === 0;
    }
    
    /**
     * Muestra página de error
     */
    private static function showError($code, $message) {
        http_response_code($code);
        
        // Loggear error
        error_log("Error $code: $message - URL: " . $_SERVER['REQUEST_URI']);
        
        // Intentar cargar controlador de errores personalizado
        $errorController = "Controllers/Error.php";
        if (file_exists($errorController)) {
            require_once $errorController;
            if (class_exists('Error')) {
                $errorInstance = new Error();
                if (method_exists($errorInstance, 'index')) {
                    $errorInstance->index($message);
                    return;
                }
            }
        }
        
        // Página de error por defecto
        self::defaultErrorPage($code, $message);
    }
    
    /**
     * Página de error por defecto
     */
    private static function defaultErrorPage($code, $message) {
        $titles = [
            400 => 'Solicitud incorrecta',
            403 => 'Acceso prohibido',
            404 => 'Página no encontrada',
            500 => 'Error del servidor'
        ];
        
        $title = isset($titles[$code]) ? $titles[$code] : 'Error';
        
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $code . ' - ' . $title . '</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #333; font-size: 50px; }
                h2 { color: #666; }
                .error-code { color: #dc3545; }
                .error-message { color: #6c757d; max-width: 600px; margin: 20px auto; }
                .home-link { display: inline-block; margin-top: 20px; padding: 10px 20px; 
                            background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <h1><span class="error-code">' . $code . '</span> - ' . $title . '</h1>
            <h2>' . htmlspecialchars($message) . '</h2>
            <a href="' . BASE_URL . '" class="home-link">Volver al inicio</a>
        </body>
        </html>';
    }
}

// Para compatibilidad con el código existente en index.php
// index.php debería llamar: Router::dispatch($controller, $method, $params);
?>