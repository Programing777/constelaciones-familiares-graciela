<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_sesion'])) {
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $tipo = sanitize($_POST['tipo']);
        $fecha = sanitize($_POST['fecha']);
        $duracion = intval($_POST['duracion']);
        $precio = floatval($_POST['precio']);
        $max_participantes = intval($_POST['max_participantes']);
        $estado = sanitize($_POST['estado']);
        $enlace_zoom = sanitize($_POST['enlace_zoom']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO sesiones (nombre, descripcion, tipo, fecha, duracion, precio, max_participantes, estado, enlace_zoom) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $tipo, $fecha, $duracion, $precio, $max_participantes, $estado, $enlace_zoom]);
            flash('Sesión creada exitosamente', 'success');
            header('Location: gestion-sesiones.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al crear sesión: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['editar_sesion'])) {
        $id = intval($_POST['id']);
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $tipo = sanitize($_POST['tipo']);
        $fecha = sanitize($_POST['fecha']);
        $duracion = intval($_POST['duracion']);
        $precio = floatval($_POST['precio']);
        $max_participantes = intval($_POST['max_participantes']);
        $estado = sanitize($_POST['estado']);
        $enlace_zoom = sanitize($_POST['enlace_zoom']);
        
        try {
            $stmt = $pdo->prepare("UPDATE sesiones SET nombre = ?, descripcion = ?, tipo = ?, fecha = ?, duracion = ?, precio = ?, max_participantes = ?, estado = ?, enlace_zoom = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion, $tipo, $fecha, $duracion, $precio, $max_participantes, $estado, $enlace_zoom, $id]);
            flash('Sesión actualizada exitosamente', 'success');
            header('Location: gestion-sesiones.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al actualizar sesión: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['eliminar_sesion'])) {
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM sesiones WHERE id = ?");
            $stmt->execute([$id]);
            flash('Sesión eliminada exitosamente', 'success');
            header('Location: gestion-sesiones.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al eliminar sesión: ' . $e->getMessage(), 'error');
        }
    }
}

// Obtener sesiones
try {
    $stmt = $pdo->query("SELECT * FROM sesiones ORDER BY fecha DESC");
    $sesiones = $stmt->fetchAll();
} catch (PDOException $e) {
    $sesiones = [];
    error_log("Error al obtener sesiones: " . $e->getMessage());
}

// Sesión para editar
$sesion_editar = null;
if (isset($_GET['editar'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM sesiones WHERE id = ?");
        $stmt->execute([$_GET['editar']]);
        $sesion_editar = $stmt->fetch();
    } catch (PDOException $e) {
        flash('Error al cargar sesión: ' . $e->getMessage(), 'error');
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sesiones - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Sesiones</h1>
                <p>Administra sesiones individuales, grupales y talleres</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="admin-content">
                <!-- Formulario de sesión -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2><?php echo $sesion_editar ? 'Editar Sesión' : 'Nueva Sesión'; ?></h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" class="session-form">
                            <?php if ($sesion_editar): ?>
                                <input type="hidden" name="id" value="<?php echo $sesion_editar['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre de la Sesión</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" 
                                       value="<?php echo $sesion_editar ? htmlspecialchars($sesion_editar['nombre']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?php echo $sesion_editar ? htmlspecialchars($sesion_editar['descripcion']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="tipo" class="form-label">Tipo de Sesión</label>
                                    <select id="tipo" name="tipo" class="form-control" required>
                                        <option value="individual" <?php echo $sesion_editar && $sesion_editar['tipo'] == 'individual' ? 'selected' : ''; ?>>Individual</option>
                                        <option value="grupal" <?php echo $sesion_editar && $sesion_editar['tipo'] == 'grupal' ? 'selected' : ''; ?>>Grupal</option>
                                        <option value="taller" <?php echo $sesion_editar && $sesion_editar['tipo'] == 'taller' ? 'selected' : ''; ?>>Taller</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select id="estado" name="estado" class="form-control" required>
                                        <option value="activa" <?php echo $sesion_editar && $sesion_editar['estado'] == 'activa' ? 'selected' : ''; ?>>Activa</option>
                                        <option value="cancelada" <?php echo $sesion_editar && $sesion_editar['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                        <option value="completada" <?php echo $sesion_editar && $sesion_editar['estado'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fecha" class="form-label">Fecha y Hora</label>
                                    <input type="datetime-local" id="fecha" name="fecha" class="form-control" 
                                           value="<?php echo $sesion_editar ? date('Y-m-d\TH:i', strtotime($sesion_editar['fecha'])) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="duracion" class="form-label">Duración (minutos)</label>
                                    <input type="number" id="duracion" name="duracion" class="form-control" 
                                           value="<?php echo $sesion_editar ? $sesion_editar['duracion'] : '60'; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" id="precio" name="precio" step="0.01" class="form-control" 
                                           value="<?php echo $sesion_editar ? $sesion_editar['precio'] : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="max_participantes" class="form-label">Máx. Participantes</label>
                                    <input type="number" id="max_participantes" name="max_participantes" class="form-control" 
                                           value="<?php echo $sesion_editar ? $sesion_editar['max_participantes'] : '1'; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="enlace_zoom" class="form-label">Enlace Zoom/Meet</label>
                                <input type="url" id="enlace_zoom" name="enlace_zoom" class="form-control" 
                                       value="<?php echo $sesion_editar ? htmlspecialchars($sesion_editar['enlace_zoom']) : ''; ?>" 
                                       placeholder="https://zoom.us/j/...">
                            </div>
                            
                            <div class="form-actions">
                                <?php if ($sesion_editar): ?>
                                    <button type="submit" name="editar_sesion" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Actualizar Sesión
                                    </button>
                                    <a href="gestion-sesiones.php" class="btn btn-outline">Cancelar</a>
                                <?php else: ?>
                                    <button type="submit" name="crear_sesion" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Crear Sesión
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de sesiones -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Sesiones Programadas</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Duración</th>
                                        <th>Precio</th>
                                        <th>Participantes</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sesiones)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                                    <p>No hay sesiones programadas</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($sesiones as $sesion): ?>
                                        <tr>
                                            <td>
                                                <div class="font-medium"><?php echo htmlspecialchars($sesion['nombre']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($sesion['descripcion']); ?></div>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $sesion['tipo'] == 'individual' ? 'status-completado' : ($sesion['tipo'] == 'grupal' ? 'status-pendiente' : 'status-activo'); ?>">
                                                    <?php echo ucfirst($sesion['tipo']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($sesion['fecha'])); ?></td>
                                            <td><?php echo $sesion['duracion']; ?> min</td>
                                            <td>$<?php echo number_format($sesion['precio'], 2); ?></td>
                                            <td><?php echo $sesion['max_participantes']; ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $sesion['estado']; ?>">
                                                    <?php echo ucfirst($sesion['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons flex gap-2">
                                                    <a href="?editar=<?php echo $sesion['id']; ?>" class="btn btn-sm btn-outline">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo $sesion['id']; ?>">
                                                        <button type="submit" name="eliminar_sesion" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('¿Estás seguro de eliminar esta sesión?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
    
    <script src="../js/admin.js"></script>
</body>
</html>