<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Obtener pedidos
try {
    $stmt = $pdo->query("
        SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email 
        FROM pagos p 
        LEFT JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.fecha_creacion DESC
    ");
    $pedidos = $stmt->fetchAll();
} catch (PDOException $e) {
    $pedidos = [];
    error_log("Error al obtener pedidos: " . $e->getMessage());
}

// Cambiar estado de pedido
if (isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['id']);
    $estado = sanitize($_GET['cambiar_estado']);
    
    try {
        $stmt = $pdo->prepare("UPDATE pagos SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);
        flash('Estado del pedido actualizado exitosamente', 'success');
        header('Location: gestion-pedidos.php');
        exit;
    } catch (PDOException $e) {
        flash('Error al actualizar estado: ' . $e->getMessage(), 'error');
    }
}

// Obtener estadísticas
$total_pedidos = count($pedidos);
$completados = 0;
$pendientes = 0;
$fallidos = 0;
$total_ingresos = 0;

foreach ($pedidos as $pedido) {
    switch ($pedido['estado']) {
        case 'completado':
            $completados++;
            $total_ingresos += $pedido['monto'];
            break;
        case 'pendiente':
            $pendientes++;
            break;
        case 'fallido':
            $fallidos++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Pedidos</h1>
                <p>Administra los pedidos y pagos de tus clientes</p>
            </div>
            
            <?php showFlash(); ?>
            
            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_pedidos; ?></h3>
                        <p>Total Pedidos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $completados; ?></h3>
                        <p>Completados</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pendientes; ?></h3>
                        <p>Pendientes</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $fallidos; ?></h3>
                        <p>Fallidos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>$<?php echo number_format($total_ingresos, 2); ?></h3>
                        <p>Ingresos Totales</p>
                    </div>
                </div>
            </div>
            
            <div class="admin-content">
                <!-- Lista de pedidos -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Historial de Pedidos</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pedidos)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-receipt fa-2x mb-2"></i>
                                                    <p>No hay pedidos registrados</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pedidos as $pedido): ?>
                                        <tr>
                                            <td>#<?php echo $pedido['id']; ?></td>
                                            <td>
                                                <div class="font-medium"><?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente'); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($pedido['cliente_email'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td>$<?php echo number_format($pedido['monto'], 2); ?></td>
                                            <td>
                                                <?php 
                                                $metodos = [
                                                    'westernunion' => 'Western Union',
                                                    'transferencia' => 'Transferencia'
                                                ];
                                                echo $metodos[$pedido['metodo_pago']] ?? ucfirst($pedido['metodo_pago']);
                                                ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $pedido['estado']; ?>">
                                                    <?php echo ucfirst($pedido['estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])); ?></td>
                                            <td>
                                                <div class="action-buttons flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline" 
                                                            onclick="mostrarDetalles(<?php echo htmlspecialchars(json_encode($pedido)); ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <div class="dropdown relative">
                                                        <button class="btn btn-sm btn-outline dropdown-toggle">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="?id=<?php echo $pedido['id']; ?>&cambiar_estado=completado" 
                                                               class="dropdown-item">Completado</a>
                                                            <a href="?id=<?php echo $pedido['id']; ?>&cambiar_estado=pendiente" 
                                                               class="dropdown-item">Pendiente</a>
                                                            <a href="?id=<?php echo $pedido['id']; ?>&cambiar_estado=fallido" 
                                                               class="dropdown-item">Fallido</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para detalles del pedido -->
    <div id="detallesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles del Pedido</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="detallesContenido"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="cerrarDetalles()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        function mostrarDetalles(pedido) {
            const modal = document.getElementById('detallesModal');
            const contenido = document.getElementById('detallesContenido');
            
            const html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">ID del Pedido</label>
                            <p class="font-medium">#${pedido.id}</p>
                        </div>
                        <div>
                            <label class="form-label">Fecha</label>
                            <p>${new Date(pedido.fecha_creacion).toLocaleString('es-ES')}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Cliente</label>
                        <p>${pedido.cliente_nombre || 'Cliente'} (${pedido.cliente_email || 'N/A'})</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Monto</label>
                            <p class="font-medium text-lg">$${parseFloat(pedido.monto).toFixed(2)}</p>
                        </div>
                        <div>
                            <label class="form-label">Método de Pago</label>
                            <p>${pedido.metodo_pago === 'westernunion' ? 'Western Union' : 'Transferencia'}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Estado</label>
                        <span class="status-badge status-${pedido.estado}">
                            ${pedido.estado.charAt(0).toUpperCase() + pedido.estado.slice(1)}
                        </span>
                    </div>
                    
                    ${pedido.id_pago_western_union ? `
                    <div>
                        <label class="form-label">ID Western Union</label>
                        <p>${pedido.id_pago_western_union}</p>
                    </div>
                    ` : ''}
                    
                    ${pedido.notas ? `
                    <div>
                        <label class="form-label">Notas</label>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p>${pedido.notas}</p>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
            
            contenido.innerHTML = html;
            modal.classList.add('show');
        }
        
        function cerrarDetalles() {
            const modal = document.getElementById('detallesModal');
            modal.classList.remove('show');
        }
        
        // Dropdown functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('dropdown-toggle')) {
                const dropdown = e.target.closest('.dropdown');
                dropdown.querySelector('.dropdown-menu').classList.toggle('show');
            } else {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
    
    <script src="../js/admin.js"></script>
</body>
</html>