<?php
include '../includes/config.php';

// Solo accesible con credenciales especiales
$auth_user = 'soporte';
$auth_pass = 'soporte2024';

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] != $auth_user || 
    $_SERVER['PHP_AUTH_PASS'] != $auth_pass) {
    header('WWW-Authenticate: Basic realm="Soporte Técnico"');
    header('HTTP/1.0 401 Unauthorized');
    die('Acceso requerido para soporte técnico');
}

echo "<h1>Panel de Seguridad - Solo Soporte Técnico</h1>";

// Funciones de seguridad
if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'ver_usuarios':
            mostrarUsuarios();
            break;
        case 'reiniciar_password':
            reiniciarPassword();
            break;
        case 'bloquear_usuario':
            bloquearUsuario();
            break;
        default:
            echo "<p>Acción no válida.</p>";
    }
} else {
    mostrarMenu();
}

function mostrarMenu() {
    echo "
    <div style='display: grid; gap: 10px; max-width: 400px;'>
        <a href='?accion=ver_usuarios' style='padding: 15px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px;'>
            👥 Ver Usuarios Administradores
        </a>
        <a href='?accion=reiniciar_password' style='padding: 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>
            🔑 Reiniciar Contraseña
        </a>
        <a href='?accion=bloquear_usuario' style='padding: 15px; background: #f44336; color: white; text-decoration: none; border-radius: 5px;'>
            🚫 Bloquear Usuario
        </a>
    </div>
    ";
}

function mostrarUsuarios() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT id, nombre, email, tipo_usuario, estado, fecha_creacion, ultimo_login FROM usuarios WHERE tipo_usuario = 'admin'");
        $usuarios = $stmt->fetchAll();
        
        echo "<h2>Usuarios Administradores</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f5f5f5;'><th>ID</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Último Login</th></tr>";
        
        foreach ($usuarios as $usuario) {
            $estado_color = $usuario['estado'] == 'activo' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>{$usuario['id']}</td>";
            echo "<td>{$usuario['nombre']}</td>";
            echo "<td>{$usuario['email']}</td>";
            echo "<td style='color: {$estado_color};'>{$usuario['estado']}</td>";
            echo "<td>{$usuario['ultimo_login']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style='margin-top: 20px;'><a href='seguridad.php'>← Volver al menú</a></p>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

function reiniciarPassword() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $pdo;
        
        $usuario_id = intval($_POST['usuario_id']);
        $nueva_password = $_POST['nueva_password'];
        
        if (empty($nueva_password) || strlen($nueva_password) < 8) {
            echo "<p style='color: red;'>La contraseña debe tener al menos 8 caracteres.</p>";
        } else {
            try {
                $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ? AND tipo_usuario = 'admin'");
                $stmt->execute([$password_hash, $usuario_id]);
                
                if ($stmt->rowCount() > 0) {
                    echo "<p style='color: green;'>✅ Contraseña reiniciada exitosamente.</p>";
                    echo "<p><strong>Nueva contraseña:</strong> {$nueva_password}</p>";
                    echo "<p><strong>⚠️ Informa inmediatamente al usuario sobre esta contraseña temporal.</strong></p>";
                } else {
                    echo "<p style='color: red;'>No se pudo actualizar la contraseña.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // Formulario para reiniciar contraseña
    echo "
    <h2>Reiniciar Contraseña de Administrador</h2>
    <form method='POST' style='max-width: 400px;'>
        <div style='margin-bottom: 15px;'>
            <label>ID del Usuario:</label>
            <input type='number' name='usuario_id' required style='width: 100%; padding: 8px;'>
        </div>
        <div style='margin-bottom: 15px;'>
            <label>Nueva Contraseña Temporal:</label>
            <input type='text' name='nueva_password' required style='width: 100%; padding: 8px;' 
                   placeholder='Mínimo 8 caracteres'>
        </div>
        <button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px;'>
            Reiniciar Contraseña
        </button>
    </form>
    <p style='margin-top: 20px;'><a href='seguridad.php'>← Volver al menú</a></p>
    ";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
.info-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
.warning { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; }
</style>

<div class="info-box">
    <strong>🔒 Panel de Seguridad Técnica</strong><br>
    Este panel solo debe ser usado por el personal de soporte técnico autorizado.
</div>

<div class="warning">
    <strong>⚠️ ADVERTENCIA</strong><br>
    Todas las acciones quedan registradas. Usa este panel responsablemente.
</div>