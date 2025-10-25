<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Obtener estadísticas
try {
    // Productos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'");
    $total_productos = $stmt->fetch()['total'];
    
    // Sesiones activas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sesiones WHERE estado = 'activa' AND fecha >= NOW()");
    $total_sesiones = $stmt->fetch()['total'];
    
    // Artículos del blog
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM blog WHERE estado = 'publicado'");
    $total_blog = $stmt->fetch()['total'];
    
    // Mensajes de contacto no leídos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contactos WHERE leido = 0");
    $total_contactos = $stmt->fetch()['total'];
    
    // Pedidos recientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pagos WHERE DATE(fecha_creacion) = CURDATE()");
    $pedidos_hoy = $stmt->fetch()['total'];
    
    // Ingresos del mes
    $stmt = $pdo->query("SELECT COALESCE(SUM(monto), 0) as total FROM pagos WHERE estado = 'completado' AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())");
    $ingresos_mes = $stmt->fetch()['total'];
    
    // Últimos pedidos
    $stmt = $pdo->query("
        SELECT p.*, u.nombre as cliente_nombre, u.email 
        FROM pagos p 
        LEFT JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_creacion DESC 
        LIMIT 8
    ");
    $ultimos_pedidos = $stmt->fetchAll();
    
    // Próximas sesiones
    $stmt = $pdo->query("
        SELECT * FROM sesiones 
        WHERE fecha >= NOW() AND estado = 'activa'
        ORDER BY fecha ASC 
        LIMIT 5
    ");
    $proximas_sesiones = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Error al obtener estadísticas: " . $e->getMessage());
    $total_productos = $total_sesiones = $total_blog = $total_contactos = $pedidos_hoy = $ingresos_mes = 0;
    $ultimos_pedidos = $proximas_sesiones = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <p>Bienvenido al panel de administración, <?php echo $_SESSION['admin_user_name']; ?></p>
            </div>
            
            <?php showFlash(); ?>
            
            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_productos; ?></h3>
                        <p>Productos Activos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_sesiones; ?></h3>
                        <p>Sesiones Programadas</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_blog; ?></h3>
                        <p>Artículos Publicados</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_contactos; ?></h3>
                        <p>Mensajes Nuevos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pedidos_hoy; ?></h3>
                        <p>Pedidos Hoy</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>$<?php echo number_format($ingresos_mes, 2); ?></h3>
                        <p>Ingresos del Mes</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Últimos Pedidos -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Últimos Pedidos</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ultimos_pedidos)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>No hay pedidos recientes</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimos_pedidos as $pedido): ?>
                                        <tr>
                                            <td>#<?php echo $pedido['id']; ?></td>
                                            <td>
                                                <div>
                                                    <div class="font-medium"><?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente'); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($pedido['email'] ?? 'N/A'); ?></div>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($pedido['monto'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $pedido['estado']; ?>">
                                                    <?php echo ucfirst($pedido['estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])); ?></td>
                                            <td>
                                                <div class="flex gap-2">
                                                    <a href="gestion-pedidos.php?ver=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="admin-card-footer">
                        <a href="gestion-pedidos.php" class="btn btn-outline">Ver Todos los Pedidos</a>
                    </div>
                </div>
                
                <!-- Próximas Sesiones -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Próximas Sesiones</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="sessions-list">
                            <?php if (empty($proximas_sesiones)): ?>
                                <div class="text-center p-6 text-gray-500">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                    <p>No hay sesiones programadas</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($proximas_sesiones as $sesion): ?>
                                <div class="session-item">
                                    <div class="session-info">
                                        <h4><?php echo htmlspecialchars($sesion['nombre']); ?></h4>
                                        <p><?php echo date('d/m/Y H:i', strtotime($sesion['fecha'])); ?></p>
                                        <span class="session-type"><?php echo ucfirst($sesion['tipo']); ?></span>
                                    </div>
                                    <div class="session-actions">
                                        <a href="gestion-sesiones.php?editar=<?php echo $sesion['id']; ?>" class="btn btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="admin-card-footer">
                        <a href="gestion-sesiones.php" class="btn btn-outline">Gestionar Sesiones</a>
                    </div>
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="admin-card mt-6">
                <div class="admin-card-header">
                    <h2>Acciones Rápidas</h2>
                </div>
                <div class="admin-card-body">
                    <div class="quick-actions-grid">
                        <a href="gestion-contenido.php" class="quick-action-btn">
                            <i class="fas fa-edit"></i>
                            <span>Editar Contenido</span>
                        </a>
                        <a href="gestion-productos.php" class="quick-action-btn">
                            <i class="fas fa-box"></i>
                            <span>Gestionar Productos</span>
                        </a>
                        <a href="gestion-sesiones.php" class="quick-action-btn">
                            <i class="fas fa-calendar"></i>
                            <span>Gestionar Sesiones</span>
                        </a>
                        <a href="gestion-blog.php" class="quick-action-btn">
                            <i class="fas fa-blog"></i>
                            <span>Gestionar Blog</span>
                        </a>
                        <a href="gestion-medios.php" class="quick-action-btn">
                            <i class="fas fa-images"></i>
                            <span>Gestionar Medios</span>
                        </a>
                        <a href="gestion-contacto.php" class="quick-action-btn">
                            <i class="fas fa-envelope"></i>
                            <span>Gestionar Contacto</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../js/admin.js"></script>
</body>
</html>