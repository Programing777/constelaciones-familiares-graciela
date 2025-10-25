<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Obtener productos
try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();
} catch(PDOException $e) {
    $productos = [];
    error_log("Error al obtener productos: " . $e->getMessage());
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_producto'])) {
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $categoria = sanitize($_POST['categoria']);
        $tipo = sanitize($_POST['tipo']);
        $duracion = sanitize($_POST['duracion']);
        $estado = sanitize($_POST['estado']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, tipo, duracion, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $tipo, $duracion, $estado]);
            flash('Producto creado exitosamente', 'success');
            header('Location: gestion-productos.php');
            exit;
        } catch(PDOException $e) {
            flash('Error al crear producto: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['editar_producto'])) {
        $id = intval($_POST['id']);
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $categoria = sanitize($_POST['categoria']);
        $tipo = sanitize($_POST['tipo']);
        $duracion = sanitize($_POST['duracion']);
        $estado = sanitize($_POST['estado']);
        
        try {
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, tipo = ?, duracion = ?, estado = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $tipo, $duracion, $estado, $id]);
            flash('Producto actualizado exitosamente', 'success');
            header('Location: gestion-productos.php');
            exit;
        } catch(PDOException $e) {
            flash('Error al actualizar producto: ' . $e->getMessage(), 'error');
        }
    }
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        flash('Producto eliminado exitosamente', 'success');
        header('Location: gestion-productos.php');
        exit;
    } catch(PDOException $e) {
        flash('Error al eliminar producto: ' . $e->getMessage(), 'error');
    }
}

// Obtener producto para editar
$producto_editar = null;
if (isset($_GET['editar'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$_GET['editar']]);
        $producto_editar = $stmt->fetch();
    } catch(PDOException $e) {
        flash('Error al cargar producto: ' . $e->getMessage(), 'error');
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Productos</h1>
                <p>Administra tus productos y servicios digitales</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="admin-content">
                <!-- Formulario de producto -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2><?php echo $producto_editar ? 'Editar Producto' : 'Nuevo Producto'; ?></h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" class="product-form">
                            <?php if ($producto_editar): ?>
                                <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre del Producto</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" 
                                       value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['nombre']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required><?php echo $producto_editar ? htmlspecialchars($producto_editar['descripcion']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="precio" class="form-label">Precio ($)</label>
                                    <input type="number" id="precio" name="precio" step="0.01" class="form-control" 
                                           value="<?php echo $producto_editar ? $producto_editar['precio'] : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="categoria" class="form-label">Categoría</label>
                                    <select id="categoria" name="categoria" class="form-control" required>
                                        <option value="Sesiones Grabadas" <?php echo ($producto_editar && $producto_editar['categoria'] == 'Sesiones Grabadas') ? 'selected' : ''; ?>>Sesiones Grabadas</option>
                                        <option value="Meditaciones" <?php echo ($producto_editar && $producto_editar['categoria'] == 'Meditaciones') ? 'selected' : ''; ?>>Meditaciones</option>
                                        <option value="Talleres Grabados" <?php echo ($producto_editar && $producto_editar['categoria'] == 'Talleres Grabados') ? 'selected' : ''; ?>>Talleres Grabados</option>
                                        <option value="E-books" <?php echo ($producto_editar && $producto_editar['categoria'] == 'E-books') ? 'selected' : ''; ?>>E-books</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select id="tipo" name="tipo" class="form-control" required>
                                        <option value="digital" <?php echo ($producto_editar && $producto_editar['tipo'] == 'digital') ? 'selected' : ''; ?>>Producto Digital</option>
                                        <option value="servicio" <?php echo ($producto_editar && $producto_editar['tipo'] == 'servicio') ? 'selected' : ''; ?>>Servicio</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="duracion" class="form-label">Duración</label>
                                    <input type="text" id="duracion" name="duracion" class="form-control" 
                                           value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['duracion']) : ''; ?>" 
                                           placeholder="Ej: 90 minutos, 2 horas">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado" class="form-label">Estado</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="activo" <?php echo ($producto_editar && $producto_editar['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactivo" <?php echo ($producto_editar && $producto_editar['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <?php if ($producto_editar): ?>
                                    <button type="submit" name="editar_producto" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Actualizar Producto
                                    </button>
                                    <a href="gestion-productos.php" class="btn btn-outline">Cancelar</a>
                                <?php else: ?>
                                    <button type="submit" name="crear_producto" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Crear Producto
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de productos -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Lista de Productos</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($productos)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                                    <p>No hay productos registrados</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($productos as $producto): ?>
                                        <tr>
                                            <td>
                                                <div class="font-medium"><?php echo htmlspecialchars($producto['nombre']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($producto['descripcion']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $producto['tipo'] == 'digital' ? 'status-completado' : 'status-pendiente'; ?>">
                                                    <?php echo ucfirst($producto['tipo']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $producto['estado']; ?>">
                                                    <?php echo ucfirst($producto['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons flex gap-2">
                                                    <a href="?editar=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?eliminar=<?php echo $producto['id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este producto?')">
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
    
    <script src="../js/admin.js"></script>
</body>
</html>