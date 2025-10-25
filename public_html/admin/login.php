<?php
// admin/login.php - VERSIÓN CORREGIDA

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usar require_once para evitar inclusiones múltiples
require_once '../includes/config.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Procesar login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Verificar credenciales hardcodeadas temporalmente
    $admin_email = 'admin@sientetuesencia.com';
    $admin_password = 'admin123'; // En producción, usar hash
    
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = 1;
        $_SESSION['admin_user_name'] = 'Administrador';
        $_SESSION['admin_user_email'] = $email;
        
        flash('¡Bienvenido al panel de administración!', 'success');
        header('Location: dashboard.php');
        exit;
    } else {
        // Intentar con base de datos
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND tipo_usuario = 'admin' AND estado = 'activo'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_user_name'] = $user['nombre'];
                $_SESSION['admin_user_email'] = $user['email'];
                
                // Actualizar último login
                $updateStmt = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                flash('¡Bienvenido de nuevo, ' . $user['nombre'] . '!', 'success');
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Credenciales incorrectas. Por favor, intente nuevamente.";
            }
        } catch(PDOException $e) {
            $error = "Error al procesar el login. Por favor, intente más tarde.";
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Graciela Alida Sigalat</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4B5320 0%, #6B8E23 100%);
            padding: 20px;
        }
        
        .login-box {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo img {
            max-height: 60px;
        }
        
        .login-title {
            text-align: center;
            color: #4B5320;
            margin-bottom: 2rem;
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4B5320;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #8A2BE2;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #8A2BE2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-login:hover {
            background: #9370DB;
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #c33;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .demo-credentials {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .demo-credentials h4 {
            margin: 0 0 0.5rem 0;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <img src="../images/logo.png" alt="Graciela Alida Sigalat">
            </div>
            
            <h1 class="login-title">Panel de Administración</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
            
            <div class="demo-credentials">
                <h4>Credenciales de Demo:</h4>
                <p><strong>Email:</strong> admin@sientetuesencia.com</p>
                <p><strong>Contraseña:</strong> admin123</p>
            </div>
            
            <div class="login-footer">
                &copy; <?php echo date('Y'); ?> Graciela Alida Sigalat
            </div>
        </div>
    </div>
</body>
</html>