<?php 
/**
 * Helpers.php - Refactorizado
 * Funciones Globales del Sistema - Versión Optimizada
 * Sistema de Gestión Escolar Marista SS.CC.
 * 
 * Mejoras implementadas:
 * - Carga lazy de librerías pesadas (PHPMailer, DomPDF)
 * - Separación de funciones por categorías
 * - Mejor manejo de errores
 * - Optimización de performance
 * - Documentación completa
 * - Seguridad mejorada
 */

declare(strict_types=1);

// ============================================================================
// CONSTANTES Y CONFIGURACIÓN
// ============================================================================

if (!defined('BASE_URL')) {
    throw new Exception('BASE_URL no está definida en Config.php');
}

// ============================================================================
// FUNCIONES DE URL Y RUTAS
// ============================================================================

/**
 * Retorna la URL base del proyecto
 */
function base_url(): string
{
    return BASE_URL;
}

/**
 * Retorna la URL de Assets
 */
function media(): string
{
    return BASE_URL . "/Assets";
}

/**
 * Construye una URL completa para una ruta
 */
function route_url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $path ? "{$base}/{$path}" : $base;
}

/**
 * Firma corta para URLs públicas (ej. verificación de recibos por QR).
 */
function firmUrl(int $nro): string
{
    return substr(hash('sha256', $nro . '|' . QR_SECRET), 0, 12);
}

/**
 * URL pública de verificación de un recibo (la que codifica el QR).
 */
function qrVerifyUrl(int $nro): string
{
    return route_url('verificar/recibo/' . $nro . '/' . firmUrl($nro));
}

/**
 * URL pública de verificación de identidad del estudiante (QR del carnet).
 */
function qrVerifyEstUrl(int $idEstudiante): string
{
    $f = substr(hash('sha256', 'est|' . $idEstudiante . '|' . QR_SECRET), 0, 12);
    return route_url('verificar/estudiante/' . $idEstudiante . '/' . $f);
}

// ============================================================================
// FUNCIONES DE TEMPLATES Y VISTAS
// ============================================================================

/**
 * Carga el header del panel administrativo
 */
function headerAdmin(array $data = []): void
{
    $view_header = "Views/Template/header_admin.php";
    require_once $view_header;
}

/**
 * Carga el footer del panel administrativo
 */
function footerAdmin(array $data = []): void
{
    $view_footer = "Views/Template/footer_admin.php";
    require_once $view_footer;
}

/**
 * Carga un modal desde la carpeta de templates
 */
function getModal(string $nameModal, array $data = []): void
{
    $view_modal = "Views/Template/Modals/{$nameModal}.php";
    if (file_exists($view_modal)) {
        require_once $view_modal;
    } else {
        error_log("Modal no encontrado: {$nameModal}");
    }
}

/**
 * Carga un componente reutilizable
 */
function getComponent(string $component, array $data = []): void
{
    $component_path = "Views/Components/{$component}.php";
    if (file_exists($component_path)) {
        extract($data);
        require_once $component_path;
    }
}

// ============================================================================
// FUNCIONES DE DEBUG Y DEPURACIÓN
// ============================================================================

/**
 * Muestra información formateada para debugging (solo en desarrollo)
 */
function dep($data, bool $die = false): void
{
    if (!defined('ENVIRONMENT') || ENVIRONMENT !== 'development') {
        return;
    }
    
    echo '<pre style="
        background: #f4f4f4;
        border: 1px solid #ddd;
        padding: 15px;
        margin: 10px 0;
        border-radius: 4px;
        overflow: auto;
        max-height: 500px;
    ">';
    
    if (is_bool($data) || is_null($data)) {
        var_dump($data);
    } else {
        print_r($data);
    }
    
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Log personalizado para diferentes niveles
 */
function log_message(string $message, string $level = 'info', array $context = []): void
{
    $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
    
    if (!in_array($level, $levels, true)) {
        $level = 'info';
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? ' - Context: ' . json_encode($context) : '';
    
    $log_message = "[{$timestamp}] [{$level}] {$message}{$context_str}\n";
    
    if (defined('LOG_PATH')) {
        $log_file = LOG_PATH . '/app-' . date('Y-m-d') . '.log';
        error_log($log_message, 3, $log_file);
    } else {
        error_log($log_message);
    }
}

// ============================================================================
// FUNCIONES DE EMAIL
// ============================================================================

/**
 * Carga las librerías de PHPMailer de forma lazy
 */
function loadPHPMailer(): void
{
    static $loaded = false;
    
    if (!$loaded) {
        $phpmailer_path = __DIR__ . '/Libraries/phpmailer/';
        
        if (!file_exists($phpmailer_path . 'PHPMailer.php')) {
            throw new Exception('PHPMailer no está instalado en Libraries/phpmailer/');
        }
        
        require_once $phpmailer_path . 'Exception.php';
        require_once $phpmailer_path . 'PHPMailer.php';
        require_once $phpmailer_path . 'SMTP.php';
        
        $loaded = true;
    }
}

/**
 * Envío de correos usando PHPMailer (optimizado)
 */
function sendMailLocal(array $data, string $template): bool
{
    try {
        loadPHPMailer();
        
        // Validar datos requeridos
        $required = ['email', 'asunto'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido faltante: {$field}");
            }
        }
        
        // Capturar template
        $template_path = "Views/Template/Email/{$template}.php";
        if (!file_exists($template_path)) {
            throw new Exception("Template de email no encontrado: {$template}");
        }
        
        ob_start();
        require $template_path;
        $mensaje = ob_get_clean();
        
        if (empty($mensaje)) {
            throw new Exception("Template de email vacío");
        }
        
        // Configurar PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración SMTP desde constantes
        $mail->SMTPDebug = defined('SMTP_DEBUG') ? SMTP_DEBUG : 0;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = defined('SMTP_SECURE') ? constant('SMTP_SECURE') : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Configurar timeout
        $mail->Timeout = 30;
        
        // Remitente
        $mail->setFrom(SMTP_USER, NOMBRE_REMITENTE);
        $mail->addReplyTo(SMTP_USER, NOMBRE_REMITENTE);
        
        // Destinatario
        $mail->addAddress($data['email']);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $data['asunto'];
        $mail->Body = $mensaje;
        $mail->AltBody = strip_tags($mensaje);
        
        // Enviar
        if ($mail->send()) {
            log_message("Email enviado exitosamente a: {$data['email']}", 'info', [
                'template' => $template,
                'subject' => $data['asunto']
            ]);
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        log_message("Error al enviar email: {$e->getMessage()}", 'error', [
            'email' => $data['email'] ?? 'unknown',
            'template' => $template
        ]);
        return false;
    }
}

/**
 * Envío de correos usando función mail() nativa (fallback)
 */
function sendEmail(array $data, string $template): bool
{
    try {
        // Validar
        if (empty($data['email']) || empty($data['asunto'])) {
            return false;
        }
        
        $template_path = "Views/Template/Email/{$template}.php";
        if (!file_exists($template_path)) {
            return false;
        }
        
        ob_start();
        require $template_path;
        $mensaje = ob_get_clean();
        
        // Headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . NOMBRE_REMITENTE . ' <' . EMAIL_REMITENTE . '>',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $result = mail(
            $data['email'],
            '=?UTF-8?B?' . base64_encode($data['asunto']) . '?=',
            $mensaje,
            implode("\r\n", $headers)
        );
        
        if ($result) {
            log_message("Email nativo enviado a: {$data['email']}", 'info');
        }
        
        return $result;
        
    } catch (Exception $e) {
        log_message("Error en sendEmail: {$e->getMessage()}", 'error');
        return false;
    }
}

// ============================================================================
// FUNCIONES DE SEGURIDAD
// ============================================================================

/**
 * Limpia cadenas de texto de forma segura
 */
function strClean(string $strCadena): string
{
    if (empty($strCadena)) {
        return '';
    }
    
    // Normalizar espacios
    $string = trim(preg_replace('/\s+/', ' ', $strCadena));
    
    // Prevenir XSS
    $string = htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    
    // Patrones de inyección SQL comunes
    $patterns = [
        // Scripts
        '/<script\b[^>]*>(.*?)<\/script>/is',
        
        // SQL Injection patterns
        '/(union\s+select)/i',
        '/(select\s+\*\s+from)/i',
        '/(insert\s+into)/i',
        '/(delete\s+from)/i',
        '/(drop\s+table)/i',
        '/(update\s+\w+\s+set)/i',
        '/(or\s+\'\d\'=\'\d\')/i',
        '/(or\s+\"\d\"=\"\d\")/i',
        
        // Comments
        '/--/',
        '/\/\*.*\*\//',
        
        // Command injection
        '/(;\s*exec)/i',
        '/(;\s*shutdown)/i',
        '/(;\s*net\s+user)/i',
    ];
    
    $string = preg_replace($patterns, '', $string);
    
    // Remover caracteres peligrosos
    $dangerous = ['^', '[', ']', '==', '`', '\\', "\0", "\n", "\r"];
    $string = str_replace($dangerous, '', $string);
    
    return $string;
}

/**
 * Sanitiza un array de forma recursiva
 */
function cleanArray(array $data): array
{
    $cleaned = [];
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $cleaned[$key] = cleanArray($value);
        } elseif (is_string($value)) {
            $cleaned[$key] = strClean($value);
        } else {
            $cleaned[$key] = $value;
        }
    }
    
    return $cleaned;
}

/**
 * Genera un token seguro
 */
function token(): string
{
    try {
        $random_bytes = random_bytes(32);
        return bin2hex($random_bytes);
    } catch (Exception $e) {
        // Fallback para sistemas sin random_bytes
        return md5(uniqid(mt_rand(), true) . microtime());
    }
}

/**
 * Genera una contraseña segura
 */
function passGenerator(int $length = 12): string
{
    if ($length < 8) {
        $length = 8;
    }
    
    $sets = [
        'abcdefghjkmnpqrstuvwxyz',
        'ABCDEFGHJKMNPQRSTUVWXYZ',
        '23456789',
        '!@#$%&*?'
    ];
    
    $password = '';
    
    // Asegurar al menos un carácter de cada conjunto
    foreach ($sets as $set) {
        $password .= $set[random_int(0, strlen($set) - 1)];
    }
    
    // Completar con caracteres aleatorios
    $all_chars = implode('', $sets);
    for ($i = strlen($password); $i < $length; $i++) {
        $password .= $all_chars[random_int(0, strlen($all_chars) - 1)];
    }
    
    // Mezclar para no tener patrón predecible
    return str_shuffle($password);
}

/**
 * Hash de contraseña (wrapper para futuros cambios)
 */
function hashPassword(string $password): string
{
    return hash('sha256', $password);
}

/**
 * Verifica si una contraseña cumple con los requisitos de seguridad
 */
function isPasswordStrong(string $password): bool
{
    if (strlen($password) < 8) {
        return false;
    }
    
    $checks = [
        '/[A-Z]/',      // Al menos una mayúscula
        '/[a-z]/',      // Al menos una minúscula
        '/[0-9]/',      // Al menos un número
        '/[\W_]/',      // Al menos un carácter especial
    ];
    
    foreach ($checks as $pattern) {
        if (!preg_match($pattern, $password)) {
            return false;
        }
    }
    
    return true;
}

// ============================================================================
// FUNCIONES DE SESIÓN Y AUTENTICACIÓN
// ============================================================================

/**
 * Verifica si el usuario está autenticado
 */
function estaAutenticado(): bool
{
    return isset($_SESSION['login']) && $_SESSION['login'] === true;
}

/**
 * Redirige si el usuario no está autenticado
 */
function requireLogin(): void
{
    if (!estaAutenticado()) {
        header('Location: ' . base_url() . '/login');
        exit;
    }
}

/**
 * Verifica permisos del usuario actual
 */
function getPermisos(int $idmodulo): array
{
    if (!isset($_SESSION['idUser'])) {
        return [];
    }
    
    require_once "Models/PermisosModel.php";
    $objPermisos = new PermisosModel();
    $idrol = $_SESSION['userData']['idrol'] ?? 0;
    
    if ($idrol === 0) {
        return [];
    }
    
    $arrPermisos = $objPermisos->permisosModulo($idrol);
    
    $permisos = $arrPermisos ?? [];
    $permisosMod = $arrPermisos[$idmodulo] ?? [];
    
    $_SESSION['permisos'] = $permisos;
    $_SESSION['permisosMod'] = $permisosMod;
    
    return $permisosMod;
}

/**
 * Verifica si el usuario tiene un permiso específico
 */
function tienePermiso(string $permiso): bool
{
    if (!isset($_SESSION['permisosMod']) || empty($_SESSION['permisosMod'])) {
        return false;
    }
    
    return isset($_SESSION['permisosMod'][$permiso]) && $_SESSION['permisosMod'][$permiso] == 1;
}

/**
 * Verifica múltiples permisos
 */
function tieneTodosPermisos(array $permisos): bool
{
    foreach ($permisos as $permiso) {
        if (!tienePermiso($permiso)) {
            return false;
        }
    }
    return true;
}

/**
 * Verifica al menos uno de los permisos
 */
function tieneAlgunPermiso(array $permisos): bool
{
    foreach ($permisos as $permiso) {
        if (tienePermiso($permiso)) {
            return true;
        }
    }
    return false;
}

// ============================================================================
// FUNCIONES DE FORMATO
// ============================================================================

/**
 * Formatea valores monetarios
 */
function formatMoney(float $cantidad): string
{
    if (!defined('SPD') || !defined('SPM')) {
        return number_format($cantidad, 2, '.', ',');
    }
    
    return number_format($cantidad, 2, SPD, SPM);
}

/**
 * Formatea fecha
 */
function formatDate(string $date, string $format = 'd-m-Y'): string
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    return date($format, $timestamp);
}

/**
 * Formatea fecha y hora
 */
function formatDateTime(string $datetime, string $format = 'd-m-Y H:i'): string
{
    return formatDate($datetime, $format);
}

/**
 * Convierte a título (primera letra mayúscula de cada palabra)
 */
function toTitleCase(string $string): string
{
    return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
}

// ============================================================================
// FUNCIONES DE ARCHIVOS Y REPORTES
// ============================================================================

/**
 * Carga DomPDF de forma lazy
 */
function loadDompdf(): void
{
    static $loaded = false;
    
    if (!$loaded) {
        $dompdf_path = __DIR__ . '/Libraries/dompdf/autoload.inc.php';
        
        if (!file_exists($dompdf_path)) {
            throw new Exception('Dompdf no está instalado en Libraries/dompdf/');
        }
        
        require_once $dompdf_path;
        $loaded = true;
    }
}

/**
 * Genera un reporte en PDF
 */
function getNewReport(
    string $html, 
    string $filename = "reporte.pdf", 
    string $orientation = "portrait", 
    string $paperSize = "A4",
    bool $download = true
): void {
    try {
        loadDompdf();
        
        $dompdf = new Dompdf\Dompdf();
        
        // Configuración
        $options = $dompdf->getOptions();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf->setOptions($options);
        
        // Cargar HTML
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paperSize, $orientation);
        
        // Renderizar
        $dompdf->render();
        
        // Output
        if ($download) {
            $dompdf->stream($filename, [
                "Attachment" => true,
                "compress" => true
            ]);
        } else {
            echo $dompdf->output();
        }
        
    } catch (Exception $e) {
        log_message("Error al generar PDF: {$e->getMessage()}", 'error');
        
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            throw $e;
        }
        
        echo "Error al generar el reporte. Por favor intente más tarde.";
    }
}

/**
 * Valida y sanitiza una imagen
 */
function processImageUpload(array $file, array $allowed_types = ['jpg', 'jpeg', 'png', 'gif']): array
{
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Error al subir el archivo';
        return $result;
    }
    
    // Validar tamaño (max 5MB)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        $result['message'] = 'El archivo es demasiado grande (máx 5MB)';
        return $result;
    }
    
    // Validar tipo
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);
    
    if (!in_array($extension, $allowed_types, true)) {
        $result['message'] = 'Tipo de archivo no permitido';
        return $result;
    }
    
    // Validar contenido real del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    if (!in_array($mime_type, $allowed_mimes, true)) {
        $result['message'] = 'El archivo no es una imagen válida';
        return $result;
    }
    
    // Generar nombre único
    $filename = uniqid('img_', true) . '.' . $extension;
    
    $result['success'] = true;
    $result['filename'] = $filename;
    $result['message'] = 'Imagen válida';
    
    return $result;
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Valida un email
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida un RUC/CI (ejemplo para Bolivia)
 */
function isValidCI(string $ci): bool
{
    $ci = preg_replace('/[^0-9]/', '', $ci);
    
    if (strlen($ci) < 5 || strlen($ci) > 10) {
        return false;
    }
    
    // Aquí podrías implementar el algoritmo de validación específico
    // Por ahora solo validamos que sean números
    return ctype_digit($ci);
}

/**
 * Valida un teléfono
 */
function isValidPhone(string $phone): bool
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) < 7 || strlen($phone) > 12) {
        return false;
    }
    
    return ctype_digit($phone);
}

/**
 * Sanitiza un número
 */
function cleanNumber($number): float
{
    if (!is_numeric($number)) {
        $number = preg_replace('/[^0-9.-]/', '', (string)$number);
    }
    
    return (float) $number;
}

// ============================================================================
// FUNCIONES DE ARRAYS Y OBJETOS
// ============================================================================

/**
 * Busca en un array multidimensional
 */
function array_search_multidimensional(array $array, string $key, $value)
{
    foreach ($array as $subarray) {
        if (isset($subarray[$key]) && $subarray[$key] === $value) {
            return $subarray;
        }
    }
    return null;
}

/**
 * Ordena array multidimensional por una clave
 */
function array_sort_multidimensional(array $array, string $key, string $direction = 'asc'): array
{
    usort($array, function ($a, $b) use ($key, $direction) {
        if (!isset($a[$key]) || !isset($b[$key])) {
            return 0;
        }
        
        if ($direction === 'asc') {
            return $a[$key] <=> $b[$key];
        } else {
            return $b[$key] <=> $a[$key];
        }
    });
    
    return $array;
}

// ============================================================================
// FUNCIONES DE REDIRECCIÓN Y RESPUESTA
// ============================================================================

/**
 * Redirige a una URL
 */
function redirect(string $url, int $status_code = 302): void
{
    if (!headers_sent()) {
        header("Location: {$url}", true, $status_code);
        exit;
    }
    
    // Fallback si headers ya fueron enviados
    echo "<script>window.location.href='{$url}';</script>";
    echo "<noscript><meta http-equiv='refresh' content='0;url={$url}'></noscript>";
    exit;
}

/**
 * Retorna una respuesta JSON
 */
function jsonResponse(array $data, int $status_code = 200): void
{
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Retorna una respuesta de error JSON
 */
function jsonError(string $message, array $details = [], int $status_code = 400): void
{
    jsonResponse([
        'success' => false,
        'message' => $message,
        'details' => $details,
        'timestamp' => time()
    ], $status_code);
}

/**
 * Retorna una respuesta de éxito JSON
 */
function jsonSuccess(string $message, array $data = [], int $status_code = 200): void
{
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ], $status_code);
}

// ============================================================================
// ITERACIÓN 1 — Documentación diferida 30 días hábiles (Bolivia)
// ============================================================================

/**
 * Calcula fecha de vencimiento sumando N días hábiles (lun-vie).
 * No rompe nada existente: función nueva, pura.
 */
function plazo30Habiles(?string $desde = null, int $dias = 30): string
{
    $d = $desde ? new DateTime($desde) : new DateTime('now');
    $sum = 0;
    while ($sum < $dias) {
        $d->modify('+1 day');
        $w = (int)$d->format('N'); // 1-5 hábil
        if ($w <= 5) { $sum++; }
    }
    return $d->format('Y-m-d');
}

/**
 * Días hábiles restantes hasta el plazo (negativo = vencido).
 */
function diasHabilesRestantes(string $plazoYmd): int
{
    $hoy = new DateTime('today');
    $fin = new DateTime($plazoYmd);
    if ($fin < $hoy) {
        // cuenta vencidos como negativos
        $c = 0; $d = clone $fin;
        while ($d < $hoy) { $d->modify('+1 day'); if ((int)$d->format('N') <= 5) { $c++; } }
        return -$c;
    }
    $c = 0; $d = clone $hoy;
    while ($d < $fin) { $d->modify('+1 day'); if ((int)$d->format('N') <= 5) { $c++; } }
    return $c;
}

/**
 * Catálogo oficial de documentos mínimos exigibles.
 */
function docsCatalogo(): array
{
    return ['ci', 'cert_nac', 'rude', 'solicitud'];
}

/**
 * Normaliza checklist docs_checklist (JSON/TEXT) a array seguro.
 */
function docsChecklistDecode($raw): array
{
    if (empty($raw)) { return []; }
    if (is_array($raw)) { return $raw; }
    $a = json_decode((string)$raw, true);
    return is_array($a) ? $a : [];
}

function docsChecklistEncode(array $a): string
{
    $base = array_fill_keys(docsCatalogo(), 0);
    foreach ($a as $k => $v) {
        if (isset($base[$k])) { $base[$k] = $v ? 1 : 0; }
    }
    return json_encode($base, JSON_UNESCAPED_UNICODE);
}

/**
 * ¿La matrícula está pendiente de documentos?
 */
function esPendienteDocs(array $mat): bool
{
    $e = strtolower(trim($mat['estado_inscripcion'] ?? ''));
    if ($e === 'pendiente_documentos' || $e === 'pendiente docs') { return true; }
    // Compat: si hay plazo futuro y no Confirmado, también se considera pendiente
    return !empty($mat['plazo_documentos_hasta']);
}

// ============================================================================
// ITERACIÓN 2 — Catálogo de estados de inscripción + terminales
// ============================================================================

/**
 * Catálogo oficial de estado_inscripcion (M01 + M02).
 * Terminales preservan historial: no se borra la fila, solo cambia el estado.
 */
function estadosInscripcion(): array
{
    return ['Confirmado','Inscrito','Pendiente_Documentos','Retirado','Trasladado','Egresado'];
}

function esEstadoTerminal(string $estado): bool
{
    return in_array($estado, ['Retirado','Trasladado','Egresado'], true);
}

function esEstadoInscripcionValido(string $estado): bool
{
    return in_array($estado, estadosInscripcion(), true);
}

// ============================================================================
// ITERACIÓN 3 — Rezago escolar (Bolivia): Inicial 4-5a, Primaria 1° = 6a
// ============================================================================

/**
 * Calcula rezago: edad vs grado esperado. Alerta con 2+ años (Comisión Técnica).
 * @return array{edad:int, esperada:int, rezago:int, alerta:bool}
 */
function calculaRezago(string $fnacYmd, string $nivel, $grado): array
{
    try { $fn = new DateTime($fnacYmd); } catch (Exception $e) { return ['edad' => 0, 'esperada' => 0, 'rezago' => 0, 'alerta' => false]; }
    $edad = (new DateTime('today'))->diff($fn)->y;
    $esperada = (strcasecmp(trim($nivel), 'Inicial') === 0) ? 4 : ((int)$grado + 5);
    $rez = $edad - $esperada;
    return ['edad' => $edad, 'esperada' => $esperada, 'rezago' => $rez, 'alerta' => ($rez >= 2)];
}

// ============================================================================
// ITERACIÓN 4 — CSRF (tokens por sesión para formularios mutantes)
// ============================================================================

/**
 * Token CSRF de la sesión (se genera una vez, no rota por request para no
 * romper modales con varias pestañas abiertas).
 */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return '';
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Campo oculto listo para pegar dentro de cada <form>.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8').'">';
}

/**
 * Valida el token recibido por POST. Comparación en tiempo constante.
 */
function csrf_check(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    $sess = $_SESSION['csrf_token'] ?? '';
    if ($sess === '' || $token === null || $token === '') {
        return false;
    }
    return hash_equals($sess, $token);
}

// ============================================================================
// INICIALIZACIÓN (si es necesario)
// ============================================================================

/**
 * Inicializa funciones esenciales
 */
function initHelpers(): void
{
    // Configurar zona horaria si no está configurada
    if (!ini_get('date.timezone')) {
        date_default_timezone_set('America/La_Paz');
    }
    
    // Configurar encoding
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding('UTF-8');
    }
    
    // Iniciar sesión si no está iniciada
    // if (session_status() === PHP_SESSION_NONE) {
    //     session_start([
    //         'cookie_secure' => isset($_SERVER['HTTPS']),
    //         'cookie_httponly' => true,
    //         'use_strict_mode' => true
    //     ]);
    // }
}
function sessionUser(int $idpersona)
{
    require_once("Models/LoginModel.php");
    $objLogin = new LoginModel();
    $request = $objLogin->sessionLogin($idpersona);
    return $request;
}
// Inicializar al cargar el archivo
initHelpers();
?>