<?php 
// ============================================================================
// index.php - Front Controller Refactorizado
// ============================================================================
// 1. Cargar configuración básica
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");

// 2. Configuración por defecto segura
$defaultController = 'Home';
$defaultMethod = 'index';

// 3. Obtener URL de forma segura
$url = '';
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url = trim($_GET['url'], '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
} else {
    $url = strtolower($defaultController) . '/' . $defaultMethod;
}

// 4. Dividir URL en partes
$arrUrl = explode("/", $url);

// 5. Determinar Controlador (con sanitización)
if (isset($arrUrl[0]) && $arrUrl[0] != '') {
    $controller = strtolower($arrUrl[0]);
    $controller = preg_replace('/[^a-z]/', '', $controller); // Solo letras minúsculas
    $controller = empty($controller) ? $defaultController : ucfirst($controller);
} else {
    $controller = $defaultController;
}

// 6. Determinar Método (con sanitización)
if (isset($arrUrl[1]) && $arrUrl[1] != '') {
    $method = strtolower($arrUrl[1]);
    $method = preg_replace('/[^a-z]/', '', $method); // Solo letras minúsculas
    $method = empty($method) ? $defaultMethod : $method;
} else {
    $method = $defaultMethod;
}

// 7. Parámetros (sanitizados)
$params = [];
if (count($arrUrl) > 2) {
    for ($i = 2; $i < count($arrUrl); $i++) {
        if ($arrUrl[$i] != '') {
            // Sanitizar cada parámetro
            $param = htmlspecialchars($arrUrl[$i], ENT_QUOTES, 'UTF-8');
            $param = strip_tags($param);
            $params[] = $param;
        }
    }
}

// 8. Debug mode (opcional)
if (defined('DEBUG') && DEBUG === true) {
    error_log("Controller: $controller, Method: $method, Params: " . json_encode($params));
}

// 9. Cargar autocargador y enrutador
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
Router::dispatch($controller, $method, $params);

// 10. Llamar al router con los parámetros sanitizados
// Nota: Esto asume que Load.php ahora usa una clase Router
?>