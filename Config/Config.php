<?php 
	
	//define("BASE_URL", "http://localhost/URL_PROYECTO/");
	const BASE_URL = "http://localhost/_02/marista";

	//Zona horaria
	date_default_timezone_set('America/La_Paz');

	//Datos de conexión a Base de Datos
	const DB_HOST = "localhost";
	const DB_NAME = "db_marista";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB_CHARSET = "utf8mb4";
	const DB_COLLATION = "utf8mb4_unicode_ci";  // Collation por defecto	

	//Deliminadores decimal y millar Ej. 24,1989.00
	const SPD = ".";
	const SPM = ",";

	//Simbolo de moneda
	const SMONEY = "Q";

	//Datos envio de correo
	const NOMBRE_REMITENTE = "Marista Sagrados Corazones";
	const EMAIL_REMITENTE = "no-reply@abelosh.com";
	const NOMBRE_EMPESA = "Marista-Robore";
	const WEB_EMPRESA = "www.naraservice.com";
	

	// Ambiente (development/production)
	define('ENVIRONMENT', 'development');

	// Secreto para firmar URLs públicas de verificación de recibos (QR).
	// Cambiar en producción por una cadena aleatoria larga.
	const QR_SECRET = "mrista-qr-2026-c4mb14r-cl4v3";

	// Configuración para el Home público
	define('SITE_NAME', 'Colegio Marista SS.CC.');
	define('SITE_DESCRIPTION', 'Institución educativa de excelencia en Roboré, Bolivia');
	define('DEFAULT_EMAIL', 'info@maristarobore.bo');
	define('DEFAULT_PHONE', '9742039');

	// ========================================
	// CONFIGURACIÓN DE CORREO ELECTRÓNICO
	// ========================================

	// Configuración SMTP para PHPMailer
	const SMTP_HOST = 'smtp.gmail.com';
	const SMTP_PORT = 465; // 465 para SSL, 587 para TLS
	const SMTP_USER = 'tu_email@gmail.com'; // ⚠️ CAMBIAR por tu email
	const SMTP_PASS = 'tu_app_password_aqui'; // ⚠️ CAMBIAR por contraseña de aplicación

	// ========================================
	// CONFIGURACIÓN DE CÓDIGOS DE RESPUESTA
	// ========================================

	// Códigos de respuesta
	define('RESPONSE_SUCCESS', 'success');
	define('RESPONSE_EXISTS', 'exists');
	define('RESPONSE_ERROR', 'error');
	define('RESPONSE_NOT_FOUND', 'not_found');

		// ========================================
	// NOTAS IMPORTANTES:
	// ========================================
	// 1. Para Gmail, NO uses tu contraseña normal
	// 2. Debes generar una "Contraseña de Aplicación":
	//    - Ve a: https://myaccount.google.com/security
	//    - Habilita la verificación en 2 pasos
	//    - Ve a "Contraseñas de aplicaciones"
	//    - Genera una para "Correo"
	//    - Usa esa contraseña en SMTP_PASS
	//
	// 3. Si usas otro proveedor (Outlook, SendGrid, etc):
	//    - Cambia SMTP_HOST y SMTP_PORT según corresponda
	//
	// 4. Para producción, considera usar variables de entorno
	//    en lugar de constantes hardcodeadas

 ?>