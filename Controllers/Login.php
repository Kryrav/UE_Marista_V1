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
        $ipaddress = '';
        
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        
        return $ipaddress;
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

        $strUsuario = strtolower(strClean($_POST['txtEmail']));
        $strPassword = $_POST['txtPassword']; // Se envía en texto plano, el modelo se encargará de hashearla
        
        $ipUsuario = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // Rate limiting
        $estaBloqueado = $this->model->verificarBloqueo($strUsuario, 15, 5);
        if ($estaBloqueado) {
            $this->model->registrarIntentoLogin($strUsuario, false, $ipUsuario, $userAgent);
            $this->responseJson(['status' => false, 'msg' => 'Demasiados intentos. Espere 15 minutos.']);
        }

        // 🔴 LLAMADA CORRECTA: Se envía la contraseña en texto plano
        $requestUser = $this->model->loginUser($strUsuario, $strPassword);

        if (!$requestUser) {
            $this->model->registrarIntentoLogin($strUsuario, false, $ipUsuario, $userAgent);
            $this->responseJson(['status' => false, 'msg' => 'Usuario o contraseña incorrectos']);
        }

        if ($requestUser['status'] != 1) {
            $this->model->registrarIntentoLogin($strUsuario, false, $ipUsuario, $userAgent);
            $this->responseJson(['status' => false, 'msg' => 'Usuario inactivo']);
        }

        // Login exitoso
        $this->model->registrarIntentoLogin($strUsuario, true, $ipUsuario, $userAgent);
        
        $_SESSION['idUser'] = $requestUser['id_persona'];
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

        if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            $arrResponse = [
                'status' => false, 
                'msg' => 'El formato del email no es válido'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // Buscar usuario
        $arrData = $this->model->getUserEmail($strEmail);

        // Por seguridad, siempre responder lo mismo (no revelar si existe)
        if (empty($arrData)) {
            $arrResponse = [
                'status' => true, 
                'msg' => 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña'
            ];
        } else {
            $token = token();
            $idpersona = $arrData['id_persona'];
            $nombreUsuario = $arrData['nombre'] . ' ' . $arrData['apellido'];

            $url_recovery = base_url() . '/login/confirmUser/' . $strEmail . ',' . $token;

            $requestUpdate = $this->model->setTokenUser($idpersona, $token);

            if ($requestUpdate) {
                $dataUsuario = [
                    'nombreUsuario' => $nombreUsuario,
                    'email' => $strEmail,
                    'asunto' => 'Recuperar cuenta - ' . NOMBRE_REMITENTE,
                    'url_recovery' => $url_recovery
                ];

                // Intentar enviar por PHPMailer primero
                $sendEmail = sendMailLocal($dataUsuario, 'email_cambioPassword');
                
                // Si falla, intentar con mail() nativo
                if (!$sendEmail) {
                    $sendEmail = sendEmail($dataUsuario, 'email_cambioPassword');
                }

                if ($sendEmail) {
                    error_log("Email de recuperación enviado a: {$strEmail}");
                    
                    $arrResponse = [
                        'status' => true, 
                        'msg' => 'Se ha enviado un correo con instrucciones para recuperar tu contraseña'
                    ];
                } else {
                    error_log("Error al enviar email de recuperación a: {$strEmail}");
                    
                    $arrResponse = [
                        'status' => false, 
                        'msg' => 'Error al enviar el correo. Por favor intente más tarde'
                    ];
                }
            } else {
                $arrResponse = [
                    'status' => false, 
                    'msg' => 'Error al procesar la solicitud. Por favor intente más tarde'
                ];
            }
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

        $arrResponse = $this->model->getUsuario($strEmail, $strToken);

        if (empty($arrResponse)) {
            header("Location: " . base_url());
            exit;
        }

        $data['page_tag'] = "Cambiar contraseña - Sistema Marista";
        $data['page_name'] = "cambiar_contrasenia";
        $data['page_title'] = "Cambiar Contraseña";
        $data['email'] = $strEmail;
        $data['token'] = $strToken;
        $data['idpersona'] = $arrResponse['id_persona'];
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

        if ($strPassword !== $strPasswordConfirm) {
            $arrResponse = [
                'status' => false, 
                'msg' => 'Las contraseñas no coinciden'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        if (strlen($strPassword) < 6) {
            $arrResponse = [
                'status' => false, 
                'msg' => 'La contraseña debe tener al menos 6 caracteres'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $arrResponseUser = $this->model->getUsuario($strEmail, $strToken);

        if (empty($arrResponseUser)) {
            $arrResponse = [
                'status' => false, 
                'msg' => 'Token inválido o expirado'
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $strPasswordHash = $_POST['txtPassword'];
        $requestPass = $this->model->insertPassword($intIdpersona, $strPasswordHash);

        if ($requestPass) {
            error_log("Contraseña actualizada para usuario ID: {$intIdpersona}");
            
            $arrResponse = [
                'status' => true, 
                'msg' => 'Contraseña actualizada correctamente'
            ];
        } else {
            error_log("Error al actualizar contraseña para usuario ID: {$intIdpersona}");
            
            $arrResponse = [
                'status' => false, 
                'msg' => 'Error al actualizar la contraseña. Por favor intente más tarde'
            ];
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

}
?>