<?php
/**
 * Funciones auxiliares para el sitio
 */

/**
 * Subir archivo de manera segura
 */
function subirArchivo($archivo, $carpeta) {
    // Directorio de subida
    $directorio = "uploads/$carpeta/";
    
    // Crear directorio si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }
    
    // Validar tipo de archivo
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'mp3', 'zip'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensiones_permitidas)) {
        return false;
    }
    
    // Validar tamaño (máximo 50MB)
    if ($archivo['size'] > 50 * 1024 * 1024) {
        return false;
    }
    
    // Generar nombre único
    $nombre_archivo = time() . '_' . uniqid() . '.' . $extension;
    $ruta_completa = $directorio . $nombre_archivo;
    
    // Mover archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
        return $ruta_completa;
    }
    
    return false;
}

/**
 * Obtener URL amigable
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

/**
 * Enviar email de contacto
 */
function enviarEmailContacto($nombre, $email, $mensaje, $telefono = '', $servicio = '') {
    $to = 'info@sientetuesencia.com';
    $subject = "Nuevo mensaje de contacto de $nombre";
    
    $body = "
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> $nombre</p>
    <p><strong>Email:</strong> $email</p>
    " . ($telefono ? "<p><strong>Teléfono:</strong> $telefono</p>" : "") . "
    " . ($servicio ? "<p><strong>Servicio de interés:</strong> $servicio</p>" : "") . "
    <p><strong>Mensaje:</strong></p>
    <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $nombre <$email>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Formatear fecha en español
 */
function fechaEspanol($fecha) {
    $meses = [
        'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
        'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
        'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
        'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
    ];
    
    $dias_semana = [
        'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
        'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado',
        'Sunday' => 'domingo'
    ];
    
    $timestamp = strtotime($fecha);
    $mes = $meses[date('F', $timestamp)];
    $dia_semana = $dias_semana[date('l', $timestamp)];
    
    return $dia_semana . ', ' . date('d', $timestamp) . ' de ' . $mes . ' de ' . date('Y', $timestamp);
}

/**
 * Limitar texto
 */
function limitarTexto($texto, $limite = 100) {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    
    $texto = substr($texto, 0, $limite);
    $texto = substr($texto, 0, strrpos($texto, ' '));
    
    return $texto . '...';
}

/**
 * Generar token CSRF
 */
function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token CSRF
 */
function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Función segura para htmlspecialchars que maneja null
 */
function safe_html($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Generar sitemap.xml
 */
function generarSitemap() {
    global $pdo;
    
    $base_url = 'https://sientetuesencia.com';
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Páginas estáticas
    $paginas_estaticas = ['index', 'about', 'constelaciones', 'servicios', 'productos', 'contacto', 'blog'];
    
    foreach ($paginas_estaticas as $pagina) {
        $url = $pagina === 'index' ? $base_url . '/' : $base_url . '/' . $pagina . '.php';
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $sitemap .= "    <changefreq>weekly</changefreq>\n";
        $sitemap .= "    <priority>" . ($pagina === 'index' ? '1.0' : '0.8') . "</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    // Artículos del blog
    try {
        $stmt = $pdo->query("SELECT id, fecha_publicacion FROM blog WHERE estado = 'publicado'");
        $articulos = $stmt->fetchAll();
        
        foreach ($articulos as $articulo) {
            $sitemap .= "  <url>\n";
            $sitemap .= "    <loc>" . $base_url . "/blog-articulo.php?id=" . $articulo['id'] . "</loc>\n";
            $sitemap .= "    <lastmod>" . date('Y-m-d', strtotime($articulo['fecha_publicacion'])) . "</lastmod>\n";
            $sitemap .= "    <changefreq>monthly</changefreq>\n";
            $sitemap .= "    <priority>0.6</priority>\n";
            $sitemap .= "  </url>\n";
        }
    } catch(PDOException $e) {
        error_log("Error al generar sitemap para blog: " . $e->getMessage());
    }
    
    $sitemap .= '</urlset>';
    
    file_put_contents('../sitemap.xml', $sitemap);
}
?>