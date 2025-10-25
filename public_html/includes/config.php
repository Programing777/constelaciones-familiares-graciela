<?php
// includes/config.php - VERSIÓN CORREGIDA Y OPTIMIZADA

if (defined('CONFIG_LOADED')) {
    return;
}
define('CONFIG_LOADED', true);

// Configuración de la base de datos CORREGIDA
define('DB_HOST', 'localhost');
define('DB_USER', 'u968357910_brian1993marti');
define('DB_PASS', '=5rq=NqQ');
define('DB_NAME', 'u968357910_graciela_terap');

// Configuración del sitio
define('SITE_URL', 'https://sientetuesencia.com');
define('SITE_NAME', 'Siente Tu Esencia - Graciela Alida Sigalat');

// Configuración Western Union (reemplazando Mercado Pago)
define('WU_ACCESS_TOKEN', 'WU_ACCESS_TOKEN_HERE');
define('WU_EMAIL', 'payments@sientetuesencia.com');

// Configuración de email
define('EMAIL_FROM', 'info@sientetuesencia.com');
define('EMAIL_FROM_NAME', 'Graciela Alida Sigalat');

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos CORREGIDA
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("ERROR: No se pudo conectar a la base de datos. " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor, intente más tarde.");
}

// Función para sanitizar datos
if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Función para redireccionar
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

// Función para mostrar mensajes flash
if (!function_exists('flash')) {
    function flash($message, $type = 'info') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
}

// Función para mostrar mensajes flash
if (!function_exists('showFlash')) {
    function showFlash() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info';
            $alert_class = $type === 'error' ? 'alert-error' : 'alert-success';
            echo "<div class='alert $alert_class'>$message</div>";
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        }
    }
}
?>