<?php 
/**
 * Login.php
 * Controlador de Autenticación - Versión Completa
 * Sistema de Gestión Escolar Marista SS.CC.
 * 
 * Incluye:
 * - Validaciones robustas
 * - Rate limiting contra fuerza bruta
 * - Logs de seguridad
 * - Mejor UX en mensajes de error
 */

class Login extends Controllers
{
    // Configuración de seguridad (idealmente de un archivo .env o config.php)
    private $loginConfig = [
        'rate_limit_minutes' => 15,
        'rate_limit_attempts' => 5,
        'session_regenerate_time' => 300 // 5 minutos
    ];
    public function __construct()
    {
        session_start();
        
        // Protección simple contra sesiones fijas
        if (isset($_SESSION['login']) && !isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }

        if (isset($_SESSION['login'])) {
            header('Location: ' . base_url() . '/dashboard');
            exit;
        }
        
        parent::__construct();
    }

    /**
     * Vista principal de login
     */
    public function index()
    {
        $data['page_tag'] = "Login - Sistema Marista";
        $data['page_title'] = "Colegio Marista SS.CC.";
        $data['page_name'] = "login";
        $data['page_functions_js'] = "functions_login.js";
        
        $this->views->getView($this, "login", $data);
    }

    
    /**
     * Obtener la IP real del cliente (considerando proxies)
     * @return string
     */

    private function getClientIP()
    {
        // AUTH: headers de proxy solo si la conexión directa es un proxy confiable
        // (localhost o red privada); si no, REMOTE_ADDR para no evadir el rate-limit.
        $remote = getenv('REMOTE_ADDR') ?: ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN');
        $trusted = ($remote === '127.0.0.1' || $remote === '::1'
            || preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/', (string)$remote) === 1);
        if ($trusted) {
            $fwd = getenv('HTTP_X_FORWARDED_FOR') ?: getenv('HTTP_CLIENT_IP') ?: '';
            if ($fwd !== '') {
                $ip = trim(explode(',', (string)$fwd)[0]);
                if ($ip !== '') {
                    return $ip;
                }
            }
        }
        return $remote ?: 'UNKNOWN';
    }
       /**
     * Helper para responder JSON y terminar la ejecución
     */
    private function responseJson($data, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Procesar login de usuario con rate limiting
     * @return JSON
     */
    public function loginUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseJson(['status' => false, 'msg' => 'Método no permitido'], 405);
        }

        // Validar campos
        if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
            $this->responseJson(['status' => false, 'msg' => 'Complete todos los campos']);
        }

        $strUsuario = strClean($_POST['txtEmail']);
        // AUTH: minúsculas solo para emails; los CI alfanuméricos son case-sensitive
        if (strpos($strUsuario, '@') !== false) {
            $strUsuario = strtolower($strUsuario);
        }
        $strPassword = $_POST['txtPassword']; // Se envía en texto plano, el modelo se encargará de hashearla
        
        $ipUsuario = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // REV-SVC Fase 2: rate-limit + verificación + estado en el servicio
        $svc = new \Services\AuthService(
            $this->model,
            $this->loginConfig['rate_limit_minutes'],
            $this->loginConfig['rate_limit_attempts']
        );
        $r = $svc->attempt($strUsuario, $strPassword, $ipUsuario, $userAgent);
        if (!$r->ok) {
            $this->responseJson(['status' => false, 'msg' => $r->msg]);
        }

        // Login exitoso
        $_SESSION['idUser'] = $r->data['id_persona'];
        $_SESSION['login'] = true;
        session_regenerate_id(true);
        
        // Cargar datos de usuario
        $userData = $this->model->sessionLogin($_SESSION['idUser']);
        $_SESSION['userData'] = $userData;

        $this->responseJson(['status' => true, 'msg' => 'ok']);
    }


    /**
     * Solicitar recuperación de contraseña
     * @return JSON
     */
    public function resetPass()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die(json_encode(['status' => false, 'msg' => 'Método no permitido']));
        }

        if (empty($_POST['txtEmailReset'])) {
            $arrResponse = [
                'status' => false, 
                'msg' => 'Por favor ingrese un email válido'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $strEmail = strtolower(strClean($_POST['txtEmailReset']));

        // REV-SVC Fase 2: solicitud de recuperación en el servicio (atómica)
        $svc = new \Services\PasswordResetService($this->model, RESET_TOKEN_MINUTES);
        $r = $svc->request($strEmail, function (array $d) {
            $d['asunto'] = 'Recuperar cuenta - ' . NOMBRE_REMITENTE;
            $d['url_recovery'] = base_url() . '/login/confirmUser/' . $d['email'] . ',' . $d['token'];
            $sent = sendMailLocal($d, 'email_cambioPassword');
            if (!$sent) {
                $sent = sendEmail($d, 'email_cambioPassword');
            }
            return $sent;
        });

        if ($r->ok) {
            if ($r->msg === 'Se ha enviado un correo con instrucciones para recuperar tu contraseña') {
                error_log("Email de recuperación enviado a: {$strEmail}");
            }
            $arrResponse = ['status' => true, 'msg' => $r->msg];
        } else {
            if ($r->code === 'mail') {
                error_log("Error al enviar email de recuperación a: {$strEmail}");
            }
            $arrResponse = ['status' => false, 'msg' => $r->msg];
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Confirmar usuario y mostrar formulario de cambio de contraseña
     * @param string $params - email,token
     */
    public function confirmUser(string $params = '')
    {
        if (empty($params)) {
            header('Location: ' . base_url());
            exit;
        }

        $arrParams = explode(',', $params);

        if (count($arrParams) < 2) {
            header('Location: ' . base_url());
            exit;
        }

        $strEmail = strClean($arrParams[0]);
        $strToken = strClean($arrParams[1]);

        if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . base_url());
            exit;
        }

        $arrResponse = (new \Services\PasswordResetService($this->model))->confirm($strEmail, $strToken);

        if (empty($arrResponse) || !$arrResponse->ok) {
            header("Location: " . base_url());
            exit;
        }

        $data['page_tag'] = "Cambiar contraseña - Sistema Marista";
        $data['page_name'] = "cambiar_contrasenia";
        $data['page_title'] = "Cambiar Contraseña";
        $data['email'] = $strEmail;
        $data['token'] = $strToken;
        $data['idpersona'] = $arrResponse->data['id_persona'];
        $data['page_functions_js'] = "functions_login.js";

        $this->views->getView($this, "cambiar_password", $data);
        die();
    }

    /**
     * Establecer nueva contraseña
     * @return JSON
     */
    public function setPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die(json_encode(['status' => false, 'msg' => 'Método no permitido']));
        }

        if (empty($_POST['idUsuario']) || 
            empty($_POST['txtEmail']) || 
            empty($_POST['txtToken']) || 
            empty($_POST['txtPassword']) || 
            empty($_POST['txtPasswordConfirm'])) {
            
            $arrResponse = [
                'status' => false, 
                'msg' => 'Por favor complete todos los campos'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $intIdpersona = intval($_POST['idUsuario']);
        $strPassword = $_POST['txtPassword'];
        $strPasswordConfirm = $_POST['txtPasswordConfirm'];
        $strEmail = strClean($_POST['txtEmail']);
        $strToken = strClean($_POST['txtToken']);

        // REV-SVC Fase 2: validación y cambio en el servicio
        $svc = new \Services\PasswordResetService($this->model, RESET_TOKEN_MINUTES);
        $r = $svc->reset($intIdpersona, $strEmail, $strToken, $strPassword, $strPasswordConfirm);

        if ($r->ok) {
            error_log("Contraseña actualizada para usuario ID: {$intIdpersona}");

            $arrResponse = [
                'status' => true, 
                'msg' => 'Contraseña actualizada correctamente'
            ];
        } else {
            if ($r->code !== 'mismatch' && $r->code !== 'corta' && $r->code !== 'token') {
                error_log("Error al actualizar contraseña para usuario ID: {$intIdpersona}");
            }

            $arrResponse = [
                'status' => false, 
                'msg' => $r->msg
            ];
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

}
?>