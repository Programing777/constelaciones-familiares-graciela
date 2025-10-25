<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Obtener mensajes de contacto
try {
    $stmt = $pdo->query("SELECT * FROM contactos ORDER BY fecha_creacion DESC");
    $mensajes = $stmt->fetchAll();
} catch (PDOException $e) {
    $mensajes = [];
    error_log("Error al obtener mensajes: " . $e->getMessage());
}

// Marcar como leído
if (isset($_GET['leer'])) {
    $id = intval($_GET['leer']);
    try {
        $stmt = $pdo->prepare("UPDATE contactos SET leido = 1 WHERE id = ?");
        $stmt->execute([$id]);
        flash('Mensaje marcado como leído', 'success');
        header('Location: gestion-contacto.php');
        exit;
    } catch (PDOException $e) {
        flash('Error al marcar mensaje: ' . $e->getMessage(), 'error');
    }
}

// Eliminar mensaje
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = ?");
        $stmt->execute([$id]);
        flash('Mensaje eliminado exitosamente', 'success');
        header('Location: gestion-contacto.php');
        exit;
    } catch (PDOException $e) {
        flash('Error al eliminar mensaje: ' . $e->getMessage(), 'error');
    }
}

// Obtener estadísticas
$total_mensajes = count($mensajes);
$no_leidos = 0;
$leidos = 0;

foreach ($mensajes as $mensaje) {
    if ($mensaje['leido']) {
        $leidos++;
    } else {
        $no_leidos++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contacto - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Contacto</h1>
                <p>Administra los mensajes recibidos a través del formulario de contacto</p>
            </div>
            
            <?php showFlash(); ?>
            
            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_mensajes; ?></h3>
                        <p>Total Mensajes</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $no_leidos; ?></h3>
                        <p>No Leídos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $leidos; ?></h3>
                        <p>Leídos</p>
                    </div>
                </div>
            </div>
            
            <div class="admin-content">
                <!-- Lista de mensajes -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Mensajes de Contacto</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Remitente</th>
                                        <th>Email</th>
                                        <th>Asunto</th>
                                        <th>Mensaje</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mensajes)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>No hay mensajes de contacto</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($mensajes as $mensaje): ?>
                                        <tr class="<?php echo !$mensaje['leido'] ? 'bg-blue-50' : ''; ?>">
                                            <td>
                                                <div class="font-medium"><?php echo htmlspecialchars($mensaje['nombre']); ?></div>
                                                <?php if ($mensaje['telefono']): ?>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($mensaje['telefono']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($mensaje['email']); ?></td>
                                            <td><?php echo htmlspecialchars($mensaje['servicio']); ?></td>
                                            <td>
                                                <div class="max-w-xs truncate"><?php echo htmlspecialchars($mensaje['mensaje']); ?></div>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($mensaje['fecha_creacion'])); ?></td>
                                            <td>
                                                <?php if ($mensaje['leido']): ?>
                                                    <span class="status-badge status-completado">
                                                        <i class="fas fa-check"></i>
                                                        Leído
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge status-pendiente">
                                                        <i class="fas fa-clock"></i>
                                                        Nuevo
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline" 
                                                            onclick="mostrarMensaje(<?php echo htmlspecialchars(json_encode($mensaje)); ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if (!$mensaje['leido']): ?>
                                                        <a href="?leer=<?php echo $mensaje['id']; ?>" class="btn btn-sm btn-outline">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="?eliminar=<?php echo $mensaje['id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este mensaje?')">
                                                        <i class="fas fa-trash"></i>
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
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para ver mensaje completo -->
    <div id="mensajeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Mensaje de Contacto</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="mensajeContenido"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="cerrarModal()">Cerrar</button>
                <a href="#" id="btnResponder" class="btn btn-primary">
                    <i class="fas fa-reply"></i>
                    Responder
                </a>
            </div>
        </div>
    </div>

    <script>
        function mostrarMensaje(mensaje) {
            const modal = document.getElementById('mensajeModal');
            const contenido = document.getElementById('mensajeContenido');
            const btnResponder = document.getElementById('btnResponder');
            
            const html = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nombre</label>
                            <p class="font-medium">${mensaje.nombre}</p>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <p>${mensaje.email}</p>
                        </div>
                    </div>
                    
                    ${mensaje.telefono ? `
                    <div>
                        <label class="form-label">Teléfono</label>
                        <p>${mensaje.telefono}</p>
                    </div>
                    ` : ''}
                    
                    <div>
                        <label class="form-label">Servicio de interés</label>
                        <p>${mensaje.servicio}</p>
                    </div>
                    
                    <div>
                        <label class="form-label">Mensaje</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="whitespace-pre-wrap">${mensaje.mensaje}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label">Fecha de envío</label>
                        <p>${new Date(mensaje.fecha_creacion).toLocaleString('es-ES')}</p>
                    </div>
                </div>
            `;
            
            contenido.innerHTML = html;
            btnResponder.href = `mailto:${mensaje.email}?subject=Re: ${mensaje.servicio}&body=Hola ${mensaje.nombre},%0D%0A%0D%0A`;
            
            modal.classList.add('show');
        }
        
        function cerrarModal() {
            const modal = document.getElementById('mensajeModal');
            modal.classList.remove('show');
        }
        
        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('mensajeModal');
            if (e.target === modal) {
                cerrarModal();
            }
        });
        
        // Cerrar modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
    
    <script src="../js/admin.js"></script>
</body>
</html>