<?php
include 'includes/admin-auth.php';
include '../includes/config.php';

// Definir páginas disponibles
$paginas = [
    'index' => [
        'nombre' => 'Página de Inicio',
        'descripcion' => 'Contenido principal de la página de inicio'
    ],
    'about' => [
        'nombre' => 'Sobre Mí',
        'descripcion' => 'Información personal y profesional'
    ],
    'constelaciones' => [
        'nombre' => 'Constelaciones Familiares',
        'descripcion' => 'Explicación del método terapéutico'
    ],
    'servicios' => [
        'nombre' => 'Servicios',
        'descripcion' => 'Lista de servicios ofrecidos'
    ],
    'productos' => [
        'nombre' => 'Productos',
        'descripcion' => 'Catálogo de productos digitales'
    ],
    'contacto' => [
        'nombre' => 'Contacto',
        'descripcion' => 'Información de contacto y formulario'
    ]
];

// Procesar guardado de contenido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagina = $_POST['pagina'];
    $contenido = $_POST['contenido'];
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $keywords = $_POST['keywords'] ?? '';
    
    try {
        // Verificar si ya existe
        $stmt = $pdo->prepare("SELECT id FROM contenido_paginas WHERE pagina = ?");
        $stmt->execute([$pagina]);
        $existe = $stmt->fetch();
        
        if ($existe) {
            // Actualizar
            $stmt = $pdo->prepare("UPDATE contenido_paginas SET contenido = ?, titulo = ?, descripcion = ?, keywords = ?, actualizado = NOW() WHERE pagina = ?");
            $stmt->execute([$contenido, $titulo, $descripcion, $keywords, $pagina]);
        } else {
            // Insertar nuevo
            $stmt = $pdo->prepare("INSERT INTO contenido_paginas (pagina, contenido, titulo, descripcion, keywords) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$pagina, $contenido, $titulo, $descripcion, $keywords]);
        }
        
        flash('Contenido de la página actualizado correctamente', 'success');
    } catch(PDOException $e) {
        error_log("Error al guardar contenido: " . $e->getMessage());
        flash('Error al guardar el contenido: ' . $e->getMessage(), 'error');
    }
}

// Obtener contenido de la página seleccionada
$contenido_actual = '';
$titulo_actual = '';
$descripcion_actual = '';
$keywords_actual = '';
$pagina_seleccionada = $_GET['pagina'] ?? 'index';

if ($pagina_seleccionada && array_key_exists($pagina_seleccionada, $paginas)) {
    try {
        $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = ?");
        $stmt->execute([$pagina_seleccionada]);
        $resultado = $stmt->fetch();
        
        if ($resultado) {
            $contenido_actual = $resultado['contenido'] ?? '';
            $titulo_actual = $resultado['titulo'] ?? '';
            $descripcion_actual = $resultado['descripcion'] ?? '';
            $keywords_actual = $resultado['keywords'] ?? '';
        }
    } catch(PDOException $e) {
        error_log("Error al obtener contenido: " . $e->getMessage());
    }
}

// Función helper para evitar warnings de null
function safe_html($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contenido - Admin Panel</title>
    <link rel="stylesheet" href="/css/admin.css">
    <!-- TinyMCE removido temporalmente -->
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestión de Contenido</h1>
                <p>Edita el contenido de las páginas de tu sitio web</p>
            </div>
            
            <?php showFlash(); ?>
            
            <div class="content-grid" style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem;">
                <!-- Sidebar de navegación -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>Páginas del Sitio</h2>
                    </div>
                    <div class="admin-card-body p-0">
                        <div class="page-list">
                            <?php foreach ($paginas as $key => $info): ?>
                                <a href="?pagina=<?php echo $key; ?>" 
                                   class="page-list-item <?php echo $pagina_seleccionada == $key ? 'active' : ''; ?>">
                                    <div class="page-list-icon">
                                        <?php 
                                        $icons = [
                                            'index' => '🏠',
                                            'about' => '👤',
                                            'constelaciones' => '🔮',
                                            'servicios' => '🎯',
                                            'productos' => '🛍️',
                                            'contacto' => '📧'
                                        ];
                                        echo $icons[$key] ?? '📄';
                                        ?>
                                    </div>
                                    <div class="page-list-info">
                                        <div class="page-list-title"><?php echo safe_html($info['nombre']); ?></div>
                                        <div class="page-list-desc"><?php echo safe_html($info['descripcion']); ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Editor de contenido -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>
                            <?php echo safe_html($paginas[$pagina_seleccionada]['nombre']); ?>
                            <span class="text-sm font-normal text-gray-500">- <?php echo safe_html($paginas[$pagina_seleccionada]['descripcion']); ?></span>
                        </h2>
                    </div>
                    
                    <form method="POST" class="p-6">
                        <input type="hidden" name="pagina" value="<?php echo safe_html($pagina_seleccionada); ?>">
                        
                        <!-- SEO Fields -->
                        <div class="form-row mb-6">
                            <div class="form-group">
                                <label for="titulo" class="form-label">Título SEO</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" 
                                       value="<?php echo safe_html($titulo_actual); ?>" 
                                       placeholder="Título optimizado para SEO">
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion" class="form-label">Descripción Meta</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="2"
                                          placeholder="Descripción para resultados de búsqueda"><?php echo safe_html($descripcion_actual); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group mb-6">
                            <label for="keywords" class="form-label">Palabras Clave</label>
                            <input type="text" id="keywords" name="keywords" class="form-control" 
                                   value="<?php echo safe_html($keywords_actual); ?>" 
                                   placeholder="Palabras clave separadas por comas">
                        </div>
                        
                        <!-- Editor de contenido -->
                        <div class="form-group">
                            <label for="contenido" class="form-label">Contenido de la Página</label>
                            <textarea id="contenido" name="contenido" class="form-control" rows="15" style="width: 100%; font-family: monospace;"><?php echo safe_html($contenido_actual); ?></textarea>
                        </div>
                        
                        <div class="form-actions flex justify-between items-center mt-6">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                                <a href="../<?php echo safe_html($pagina_seleccionada); ?>.php" target="_blank" class="btn btn-outline">
                                    <i class="fas fa-eye"></i>
                                    Ver Página
                                </a>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('contenido').value = ''">
                                    <i class="fas fa-trash"></i>
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <style>
        .content-grid {
            min-height: calc(100vh - 200px);
        }
        
        .page-list {
            display: flex;
            flex-direction: column;
        }
        
        .page-list-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        
        .page-list-item:last-child {
            border-bottom: none;
        }
        
        .page-list-item:hover {
            background: #f3f4f6;
        }
        
        .page-list-item.active {
            background: #4B5320;
            color: white;
        }
        
        .page-list-icon {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }
        
        .page-list-title {
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .page-list-desc {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        
        .page-list-item.active .page-list-desc {
            opacity: 0.9;
        }
        
        .form-actions {
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5rem;
        }
    </style>
</body>
</html>