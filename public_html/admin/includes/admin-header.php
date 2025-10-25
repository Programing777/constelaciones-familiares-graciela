<?php
// admin/includes/admin-header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel - Graciela Alida Sigalat'; ?></title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-header-left">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <a href="dashboard.php" class="admin-logo">
                    <img src="../images/logo.png" alt="Graciela Alida Sigalat" height="40">
                    <span>Admin Panel</span>
                </a>
            </div>
            
            <div class="admin-header-right">
                <div class="admin-user-menu">
                    <div class="user-greeting">
                        <i class="fas fa-user-circle"></i>
                        <span>Hola, <?php echo $_SESSION['admin_user_name'] ?? 'Administrador'; ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="user-dropdown">
                        <a href="../index.php" target="_blank" class="dropdown-item">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Ver Sitio Web</span>
                        </a>
                        <a href="profile.php" class="dropdown-item">
                            <i class="fas fa-user-cog"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>