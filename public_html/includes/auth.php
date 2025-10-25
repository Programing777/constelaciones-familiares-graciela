<?php
/**
 * Autenticación para usuarios del frontend (clientes)
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verificar si el usuario está logueado
 */
function usuarioLogueado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Obtener datos del usuario logueado
 */
function obtenerUsuario() {
    if (!usuarioLogueado()) {
        return null;
    }
    
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, fecha_creacion FROM usuarios WHERE id = ? AND estado = 'activo'");
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error al obtener usuario: " . $e->getMessage());
        return null;
    }
}

/**
 * Login de usuario
 */
function loginUsuario($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ? AND estado = 'activo' AND tipo_usuario = 'cliente'");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            // Actualizar último login
            $updateStmt = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
            $updateStmt->execute([$usuario['id']]);
            
            return true;
        }
    } catch (PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Registrar nuevo usuario
 */
function registrarUsuario($nombre, $email, $password) {
    global $pdo;
    
    try {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return 'El email ya está registrado';
        }
        
        // Crear nuevo usuario
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES (?, ?, ?, 'cliente')");
        $stmt->execute([$nombre, $email, $password_hash]);
        
        // Login automático
        $usuario_id = $pdo->lastInsertId();
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_email'] = $email;
        
        return true;
    } catch (PDOException $e) {
        error_log("Error al registrar usuario: " . $e->getMessage());
        return 'Error al registrar usuario';
    }
}

/**
 * Logout
 */
function logoutUsuario() {
    session_destroy();
    session_start();
}

/**
 * Proteger ruta que requiere login
 */
function requerirLogin() {
    if (!usuarioLogueado()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Obtener pedidos del usuario
 */
function obtenerPedidosUsuario($usuario_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, pd.tipo, pd.producto_id, pd.sesion_id, 
                   pr.nombre as producto_nombre, pr.archivo_url,
                   s.nombre as sesion_nombre, s.fecha as sesion_fecha
            FROM pagos p
            LEFT JOIN pedidos pd ON p.id = pd.pago_id
            LEFT JOIN productos pr ON pd.producto_id = pr.id AND pd.tipo = 'producto'
            LEFT JOIN sesiones s ON pd.sesion_id = s.id AND pd.tipo = 'sesion'
            WHERE p.usuario_id = ?
            ORDER BY p.fecha_creacion DESC
        ");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error al obtener pedidos: " . $e->getMessage());
        return [];
    }
}
?>