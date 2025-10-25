<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_articulo'])) {
        $titulo = sanitize($_POST['titulo']);
        $contenido = $_POST['contenido'];
        $resumen = sanitize($_POST['resumen']);
        $categoria = sanitize($_POST['categoria']);
        $tags = sanitize($_POST['tags']);
        $estado = sanitize($_POST['estado']);
        $fecha_publicacion = sanitize($_POST['fecha_publicacion']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO blog (titulo, contenido, resumen, categoria, tags, estado, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $contenido, $resumen, $categoria, $tags, $estado, $fecha_publicacion]);
            flash('Artículo creado exitosamente', 'success');
            header('Location: gestion-blog.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al crear artículo: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['editar_articulo'])) {
        $id = intval($_POST['id']);
        $titulo = sanitize($_POST['titulo']);
        $contenido = $_POST['contenido'];
        $resumen = sanitize($_POST['resumen']);
        $categoria = sanitize($_POST['categoria']);
        $tags = sanitize($_POST['tags']);
        $estado = sanitize($_POST['estado']);
        $fecha_publicacion = sanitize($_POST['fecha_publicacion']);
        
        try {
            $stmt = $pdo->prepare("UPDATE blog SET titulo = ?, contenido = ?, resumen = ?, categoria = ?, tags = ?, estado = ?, fecha_publicacion = ? WHERE id = ?");
            $stmt->execute([$titulo, $contenido, $resumen, $categoria, $tags, $estado, $fecha_publicacion, $id]);
            flash('Artículo actualizado exitosamente', 'success');
            header('Location: gestion-blog.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al actualizar artículo: ' . $e->getMessage(), 'error');
        }
    }
    
    if (isset($_POST['eliminar_articulo'])) {
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM blog WHERE id = ?");
            $stmt->execute([$id]);
            flash('Artículo eliminado exitosamente', 'success');
            header('Location: gestion-blog.php');
            exit;
        } catch (PDOException $e) {
            flash('Error al eliminar artículo: ' . $e->getMessage(), 'error');
        }
    }
}

// Obtener artículos
try {
    $stmt = $pdo->query("SELECT * FROM blog ORDER BY fecha_creacion DESC");
    $articulos = $stmt->fetchAll();
} catch (PDOException $e) {
    $articulos = [];
    error_log("Error al obtener artículos: " . $e->getMessage());
}

// Artículo para editar
$articulo_editar = null;
if (isset($_GET['editar'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ?");
        $stmt->execute([$_GET['editar']]);
        $articulo_editar = $stmt->fetch();
    } catch (PDOException $e) {
        flash('Error al cargar artículo: ' . $e->getMessage(), 'error');
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión del Blog - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión del Blog</h1>
                <p>Administra los artículos y publicaciones del blog</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="admin-content">
                <!-- Formulario de artículo -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2><?php echo $articulo_editar ? 'Editar Artículo' : 'Nuevo Artículo'; ?></h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" class="blog-form">
                            <?php if ($articulo_editar): ?>
                                <input type="hidden" name="id" value="<?php echo $articulo_editar['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="titulo" class="form-label">Título del Artículo</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" 
                                       value="<?php echo $articulo_editar ? htmlspecialchars($articulo_editar['titulo']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="resumen" class="form-label">Resumen</label>
                                <textarea id="resumen" name="resumen" class="form-control" rows="3" required><?php echo $articulo_editar ? htmlspecialchars($articulo_editar['resumen']) : ''; ?></textarea>
                                <small class="text-gray-500">Breve descripción que aparecerá en la lista de artículos</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="contenido" class="form-label">Contenido</label>
                                <textarea id="contenido" name="contenido" class="form-control rich-editor" rows="15"><?php echo $articulo_editar ? htmlspecialchars($articulo_editar['contenido']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="categoria" class="form-label">Categoría</label>
                                    <input type="text" id="categoria" name="categoria" class="form-control" 
                                           value="<?php echo $articulo_editar ? htmlspecialchars($articulo_editar['categoria']) : 'Constelaciones Familiares'; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tags" class="form-label">Etiquetas</label>
                                    <input type="text" id="tags" name="tags" class="form-control" 
                                           value="<?php echo $articulo_editar ? htmlspecialchars($articulo_editar['tags']) : ''; ?>" 
                                           placeholder="Separadas por comas">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select id="estado" name="estado" class="form-control" required>
                                        <option value="borrador" <?php echo $articulo_editar && $articulo_editar['estado'] == 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                        <option value="publicado" <?php echo $articulo_editar && $articulo_editar['estado'] == 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="datetime-local" id="fecha_publicacion" name="fecha_publicacion" class="form-control" 
                                           value="<?php echo $articulo_editar ? date('Y-m-d\TH:i', strtotime($articulo_editar['fecha_publicacion'])) : date('Y-m-d\TH:i'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <?php if ($articulo_editar): ?>
                                    <button type="submit" name="editar_articulo" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Actualizar Artículo
                                    </button>
                                    <a href="gestion-blog.php" class="btn btn-outline">Cancelar</a>
                                <?php else: ?>
                                    <button type="submit" name="crear_articulo" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Crear Artículo
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de artículos -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Artículos del Blog</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Fecha Publicación</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($articulos)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center p-6">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-newspaper fa-2x mb-2"></i>
                                                    <p>No hay artículos</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($articulos as $articulo): ?>
                                        <tr>
                                            <td>
                                                <div class="font-medium"><?php echo htmlspecialchars($articulo['titulo']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($articulo['resumen']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($articulo['categoria']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $articulo['estado']; ?>">
                                                    <?php echo ucfirst($articulo['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($articulo['fecha_publicacion']): ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($articulo['fecha_publicacion'])); ?>
                                                <?php else: ?>
                                                    <em class="text-gray-500">No publicada</em>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($articulo['fecha_creacion'])); ?></td>
                                            <td>
                                                <div class="action-buttons flex gap-2">
                                                    <a href="?editar=<?php echo $articulo['id']; ?>" class="btn btn-sm btn-outline">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../blog-articulo.php?id=<?php echo $articulo['id']; ?>" target="_blank" class="btn btn-sm btn-outline">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo $articulo['id']; ?>">
                                                        <button type="submit" name="eliminar_articulo" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('¿Estás seguro de eliminar este artículo?')">
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

    <script>
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#contenido',
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code',
            height: 400,
            menubar: false,
            branding: false,
            promotion: false,
            language: 'es',
            content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; }'
        });
    </script>
    
    <script src="../js/admin.js"></script>
</body>
</html>