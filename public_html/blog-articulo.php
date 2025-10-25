<?php
include 'includes/config.php';

// Verificar que se haya proporcionado un ID de artículo
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: blog.php');
    exit;
}

$articulo_id = intval($_GET['id']);

// Obtener el artículo
$articulo = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ? AND estado = 'publicado'");
    $stmt->execute([$articulo_id]);
    $articulo = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error al obtener artículo: " . $e->getMessage());
}

// Si no se encuentra el artículo, redirigir al blog
if (!$articulo) {
    header('Location: blog.php');
    exit;
}

$page_title = $articulo['titulo'] . ' - Graciela Alida Sigalat';
$meta_descripcion = $articulo['resumen'];
$meta_keywords = $articulo['tags'] ?? 'blog, artículo, constelaciones familiares';

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($articulo['titulo']); ?></h1>
        <p><?php echo htmlspecialchars($articulo['resumen']); ?></p>
    </div>
</section>

<section class="blog-article-content section-padding">
    <div class="container">
        <article class="article-main">
            <div class="article-meta">
                <span class="article-category"><?php echo htmlspecialchars($articulo['categoria']); ?></span>
                <span class="article-date">Publicado el <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?></span>
            </div>
            
            <?php if (!empty($articulo['imagen'])): ?>
            <div class="article-image">
                <img src="images/blog/<?php echo $articulo['imagen']; ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
            </div>
            <?php endif; ?>
            
            <div class="article-content">
                <?php echo $articulo['contenido']; ?>
            </div>
            
            <?php if (!empty($articulo['tags'])): ?>
            <div class="article-tags">
                <strong>Etiquetas:</strong>
                <?php
                $tags = explode(',', $articulo['tags']);
                foreach ($tags as $tag):
                    $tag = trim($tag);
                    if (!empty($tag)):
                ?>
                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
            <?php endif; ?>
        </article>
        
        <aside class="article-sidebar">
            <div class="sidebar-widget">
                <h4>Sobre la Autora</h4>
                <div class="author-info">
                    <div class="author-avatar">
                        <!-- Imagen de la autora -->
                        <img src="images/graciela-avatar.jpg" alt="Graciela Alida Sigalat">
                    </div>
                    <div class="author-details">
                        <h5>Graciela Alida Sigalat</h5>
                        <p>Terapeuta especializada en Constelaciones Familiares con más de 10 años de experiencia.</p>
                        <a href="about.php" class="btn btn-outline btn-sm">Conoce más</a>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-widget">
                <h4>Artículos Relacionados</h4>
                <?php
                // Obtener artículos relacionados (misma categoría)
                try {
                    $stmt = $pdo->prepare("SELECT id, titulo, resumen FROM blog WHERE categoria = ? AND id != ? AND estado = 'publicado' ORDER BY fecha_publicacion DESC LIMIT 3");
                    $stmt->execute([$articulo['categoria'], $articulo_id]);
                    $relacionados = $stmt->fetchAll();
                } catch (PDOException $e) {
                    $relacionados = [];
                    error_log("Error al obtener artículos relacionados: " . $e->getMessage());
                }
                
                if (!empty($relacionados)):
                ?>
                    <div class="related-articles">
                        <?php foreach ($relacionados as $relacionado): ?>
                        <div class="related-article">
                            <h5><a href="blog-articulo.php?id=<?php echo $relacionado['id']; ?>"><?php echo htmlspecialchars($relacionado['titulo']); ?></a></h5>
                            <p><?php echo htmlspecialchars($relacionado['resumen']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay artículos relacionados por el momento.</p>
                <?php endif; ?>
            </div>
            
            <div class="sidebar-widget">
                <h4>¿Te gustó el artículo?</h4>
                <p>Comparte tus thoughts o agenda una sesión para profundizar.</p>
                <a href="contacto.php" class="btn btn-primary">Contactar</a>
            </div>
        </aside>
    </div>
</section>

<style>
.blog-article-content .container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: start;
}

.article-meta {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gris-medio);
}

.article-category {
    background: var(--violeta-suave);
    color: var(--violeta);
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.article-date {
    color: var(--texto-claro);
    font-size: 0.9rem;
}

.article-image {
    margin-bottom: 2rem;
    border-radius: 12px;
    overflow: hidden;
}

.article-image img {
    width: 100%;
    height: auto;
    display: block;
}

.article-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4 {
    color: var(--verde-militar);
    margin: 2rem 0 1rem 0;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-tags {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gris-medio);
}

.tag {
    display: inline-block;
    background: var(--gris-claro);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.9rem;
    margin: 0.3rem;
    color: var(--texto-claro);
}

.sidebar-widget {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.sidebar-widget h4 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.author-info {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.author-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.author-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.author-details h5 {
    margin: 0 0 0.5rem 0;
    color: var(--verde-militar);
}

.author-details p {
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.related-articles {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.related-article h5 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
}

.related-article h5 a {
    color: var(--verde-militar);
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-article h5 a:hover {
    color: var(--violeta);
}

.related-article p {
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.5;
    color: var(--texto-claro);
}

@media (max-width: 768px) {
    .blog-article-content .container {
        grid-template-columns: 1fr;
    }
    
    .article-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>