<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Obtener datos del usuario actual
$usuario_id = $_SESSION['admin_user_id'];
$usuario = null;

try {
    $stmt = $pdo->prepare("SELECT id, nombre, email, fecha_creacion, ultimo_login FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error al obtener usuario: " . $e->getMessage());
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_perfil'])) {
        $nombre = sanitize($_POST['nombre']);
        $email = sanitize($_POST['email']);
        
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $usuario_id]);
            flash('Perfil actualizado correctamente', 'success');
            // Actualizar sesión
            $_SESSION['admin_user_name'] = $nombre;
            $_SESSION['admin_user_email'] = $email;
            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al actualizar perfil: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['cambiar_password'])) {
        $password_actual = $_POST['password_actual'];
        $nueva_password = $_POST['nueva_password'];
        $confirmar_password = $_POST['confirmar_password'];
        
        if ($nueva_password !== $confirmar_password) {
            flash('Las contraseñas no coinciden', 'error');
        } else {
            try {
                // Verificar contraseña actual
                $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
                $stmt->execute([$usuario_id]);
                $usuario_db = $stmt->fetch();
                
                if ($usuario_db && password_verify($password_actual, $usuario_db['password'])) {
                    $nueva_password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                    $stmt->execute([$nueva_password_hash, $usuario_id]);
                    flash('Contraseña actualizada correctamente', 'success');
                    header('Location: profile.php');
                    exit;
                } else {
                    flash('La contraseña actual es incorrecta', 'error');
                }
            } catch (PDOException $e) {
                flash('Error al cambiar contraseña: ' . $e->getMessage(), 'error');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Mi Perfil</h1>
                <p>Gestiona tu información personal y contraseña</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="admin-content">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Información Personal</h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Fecha de Registro</label>
                                    <p class="form-control-static"><?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></p>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Último Acceso</label>
                                    <p class="form-control-static">
                                        <?php echo $usuario['ultimo_login'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_login'])) : 'Nunca'; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="actualizar_perfil" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Actualizar Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Cambiar Contraseña</h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="password_actual" class="form-label">Contraseña Actual</label>
                                <input type="password" id="password_actual" name="password_actual" class="form-control" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" id="nueva_password" name="nueva_password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="cambiar_password" class="btn btn-primary">
                                    <i class="fas fa-key"></i>
                                    Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>