<?php
include 'includes/admin-auth.php';

$upload_dir = '../uploads/';

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Subir archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $nombre_archivo = time() . '_' . basename($archivo['name']);
    $ruta_destino = $upload_dir . $nombre_archivo;
    
    // Validar tipo de archivo
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'mp3', 'zip'];
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensiones_permitidas)) {
        flash("Tipo de archivo no permitido", 'error');
    } elseif ($archivo['size'] > 50 * 1024 * 1024) {
        flash("El archivo es demasiado grande (máximo 50MB)", 'error');
    } elseif (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        flash("Archivo subido correctamente", 'success');
    } else {
        flash("Error al subir el archivo", 'error');
    }
}

// Eliminar archivo
if (isset($_GET['eliminar'])) {
    $archivo = $_GET['eliminar'];
    $ruta_completa = $upload_dir . $archivo;
    
    if (file_exists($ruta_completa) && unlink($ruta_completa)) {
        flash("Archivo eliminado correctamente", 'success');
    } else {
        flash("Error al eliminar el archivo", 'error');
    }
    
    header("Location: gestion-medios.php");
    exit;
}

// Obtener lista de archivos
$archivos = glob($upload_dir . '*');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Medios - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Medios</h1>
                <p>Administra imágenes, videos y archivos de tu sitio web</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="admin-content">
                <!-- Subida de archivos -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Subir Nuevo Archivo</h2>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" enctype="multipart/form-data" class="upload-form">
                            <div class="form-group">
                                <label for="archivo" class="form-label">Seleccionar Archivo</label>
                                <input type="file" id="archivo" name="archivo" class="form-control" 
                                       accept="image/*,video/*,audio/*,.pdf,.zip" required>
                                <small class="text-gray-500">
                                    Formatos permitidos: JPG, PNG, GIF, PDF, MP4, MP3, ZIP. Máximo 50MB.
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i>
                                Subir Archivo
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Biblioteca de medios -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Biblioteca de Medios</h2>
                    </div>
                    <div class="admin-card-body">
                        <div class="media-grid">
                            <?php if (empty($archivos)): ?>
                                <div class="text-center p-6 text-gray-500">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>No hay archivos subidos</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($archivos as $archivo): 
                                    $nombre = basename($archivo);
                                    $ruta_publica = str_replace('../', '', $archivo);
                                    $es_imagen = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $nombre);
                                    $es_video = preg_match('/\.(mp4|webm|ogg)$/i', $nombre);
                                    $es_audio = preg_match('/\.(mp3|wav|ogg)$/i', $nombre);
                                    $es_pdf = preg_match('/\.pdf$/i', $nombre);
                                    $tamano = filesize($archivo);
                                    $fecha_modificacion = filemtime($archivo);
                                ?>
                                <div class="media-item">
                                    <div class="media-preview">
                                        <?php if ($es_imagen): ?>
                                            <img src="<?php echo $ruta_publica; ?>" alt="<?php echo $nombre; ?>" loading="lazy">
                                        <?php elseif ($es_video): ?>
                                            <div class="media-icon">
                                                <i class="fas fa-video"></i>
                                            </div>
                                        <?php elseif ($es_audio): ?>
                                            <div class="media-icon">
                                                <i class="fas fa-music"></i>
                                            </div>
                                        <?php elseif ($es_pdf): ?>
                                            <div class="media-icon">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="media-icon">
                                                <i class="fas fa-file"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="media-info">
                                        <div class="media-name"><?php echo $nombre; ?></div>
                                        <div class="media-meta">
                                            <span><?php echo $this->formatFileSize($tamano); ?></span>
                                            <span><?php echo date('d/m/Y H:i', $fecha_modificacion); ?></span>
                                        </div>
                                        
                                        <div class="media-actions">
                                            <input type="text" value="<?php echo $ruta_publica; ?>" readonly 
                                                   class="form-control form-control-sm" onclick="this.select()">
                                            <div class="action-buttons flex gap-2 mt-2">
                                                <a href="<?php echo $ruta_publica; ?>" target="_blank" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?eliminar=<?php echo $nombre; ?>" class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('¿Eliminar este archivo?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .media-item {
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: white;
            transition: var(--transition);
        }
        
        .media-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .media-preview {
            height: 150px;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .media-icon {
            font-size: 2rem;
            color: var(--gray-400);
        }
        
        .media-info {
            padding: 1rem;
        }
        
        .media-name {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            word-break: break-all;
        }
        
        .media-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-bottom: 1rem;
        }
        
        .media-actions input {
            font-size: 0.75rem;
            padding: 0.5rem;
        }
    </style>

    <script>
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
    
    <script src="../js/admin.js"></script>
</body>
</html>