<?php
// admin/includes/admin-sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-container">
    <nav class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <h3>Panel de Control</h3>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-contenido.php" class="sidebar-link <?php echo $current_page == 'gestion-contenido.php' ? 'active' : ''; ?>">
                    <span class="icon">📝</span>
                    <span>Editar Textos</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-productos.php" class="sidebar-link <?php echo $current_page == 'gestion-productos.php' ? 'active' : ''; ?>">
                    <span class="icon">🛍️</span>
                    <span>Productos</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-sesiones.php" class="sidebar-link <?php echo $current_page == 'gestion-sesiones.php' ? 'active' : ''; ?>">
                    <span class="icon">📅</span>
                    <span>Sesiones</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-blog.php" class="sidebar-link <?php echo $current_page == 'gestion-blog.php' ? 'active' : ''; ?>">
                    <span class="icon">✏️</span>
                    <span>Blog</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-medios.php" class="sidebar-link <?php echo $current_page == 'gestion-medios.php' ? 'active' : ''; ?>">
                    <span class="icon">🖼️</span>
                    <span>Medios</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-contacto.php" class="sidebar-link <?php echo $current_page == 'gestion-contacto.php' ? 'active' : ''; ?>">
                    <span class="icon">📧</span>
                    <span>Contacto</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="gestion-pedidos.php" class="sidebar-link <?php echo $current_page == 'gestion-pedidos.php' ? 'active' : ''; ?>">
                    <span class="icon">📦</span>
                    <span>Pedidos</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="logout.php" class="sidebar-link logout">
                    <span class="icon">🚪</span>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>