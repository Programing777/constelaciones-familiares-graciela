<?php
// admin/includes/admin-auth.php

// Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado como admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirigir al login
    header('Location: ../login.php');
    exit;
}

// Para desarrollo: permitir acceso sin verificación de base de datos
// En producción, descomentar la verificación de base de datos:

/*
include '../../includes/config.php';

try {
    $stmt = $pdo->prepare("SELECT id, nombre, estado FROM usuarios WHERE id = ? AND tipo_usuario = 'admin' AND estado = 'activo'");
    $stmt->execute([$_SESSION['admin_user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Usuario no encontrado o no es admin
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
    
    // Actualizar información de sesión si es necesario
    $_SESSION['admin_user_name'] = $user['nombre'];
    
} catch(PDOException $e) {
    error_log("Error al verificar usuario admin: " . $e->getMessage());
    // En caso de error, permitir continuar pero loguear el error
}
*/

// Función para mostrar mensajes flash
function showFlash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        echo "<div class='alert alert-$type'>$message</div>";
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}

// Función para establecer mensajes flash
function flash($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}
?>