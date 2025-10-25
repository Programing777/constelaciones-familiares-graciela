<?php
include 'includes/config.php';

// Obtener artículos del blog
$articulos = [];
try {
    $stmt = $pdo->query("SELECT * FROM blog WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC");
    $articulos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al obtener artículos del blog: " . $e->getMessage());
}

$page_title = 'Blog - Graciela Alida Sigalat';
$meta_descripcion = 'Artículos sobre Constelaciones Familiares, crecimiento personal y sanación.';
$meta_keywords = 'blog, artículos, constelaciones familiares, crecimiento personal, sanación';

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Blog</h1>
        <p>Artículos sobre Constelaciones Familiares y Crecimiento Personal</p>
    </div>
</section>

<section class="blog-content section-padding">
    <div class="container">
        <?php if (!empty($articulos)): ?>
            <div class="blog-grid">
                <?php foreach ($articulos as $articulo): ?>
                    <article class="blog-card">
                        <div class="blog-image">
                            <!-- Imagen del artículo, si existe -->
                            <img src="images/blog/<?php echo $articulo['imagen'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category"><?php echo htmlspecialchars($articulo['categoria']); ?></span>
                            <h3><a href="blog-articulo.php?id=<?php echo $articulo['id']; ?>"><?php echo htmlspecialchars($articulo['titulo']); ?></a></h3>
                            <p><?php echo htmlspecialchars($articulo['resumen']); ?></p>
                            <div class="blog-meta">
                                <span class="blog-date"><?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?></span>
                                <a href="blog-articulo.php?id=<?php echo $articulo['id']; ?>" class="read-more">Leer más</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Artículos por defecto si no hay en la base de datos -->
            <div class="blog-grid">
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog/constelaciones-familiares.jpg" alt="Constelaciones Familiares">
                    </div>
                    <div class="blog-content">
                        <span class="blog-category">Constelaciones Familiares</span>
                        <h3><a href="blog-articulo.php?id=1">¿Qué son las Constelaciones Familiares?</a></h3>
                        <p>Descubre en qué consiste este método terapéutico y cómo puede ayudarte a sanar relaciones familiares.</p>
                        <div class="blog-meta">
                            <span class="blog-date">15/03/2024</span>
                            <a href="blog-articulo.php?id=1" class="read-more">Leer más</a>
                        </div>
                    </div>
                </article>

                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog/sanacion-emocional.jpg" alt="Sanación Emocional">
                    </div>
                    <div class="blog-content">
                        <span class="blog-category">Sanación Emocional</span>
                        <h3><a href="blog-articulo.php?id=2">El poder de sanar nuestras heridas emocionales</a></h3>
                        <p>Aprende cómo las constelaciones familiares pueden ayudarte a liberar cargas emocionales del pasado.</p>
                        <div class="blog-meta">
                            <span class="blog-date">10/03/2024</span>
                            <a href="blog-articulo.php?id=2" class="read-more">Leer más</a>
                        </div>
                    </div>
                </article>

                <article class="blog-card">
                    <div class="blog-image">
                        <img src="images/blog/relaciones-familiares.jpg" alt="Relaciones Familiares">
                    </div>
                    <div class="blog-content">
                        <span class="blog-category">Relaciones Familiares</span>
                        <h3><a href="blog-articulo.php?id=3">Cómo mejorar las relaciones con tus padres</a></h3>
                        <p>Estrategias prácticas para sanar y transformar la relación con tus figuras parentales.</p>
                        <div class="blog-meta">
                            <span class="blog-date">05/03/2024</span>
                            <a href="blog-articulo.php?id=3" class="read-more">Leer más</a>
                        </div>
                    </div>
                </article>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.blog-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.blog-image {
    height: 200px;
    background: var(--gris-medio);
    overflow: hidden;
}

.blog-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.blog-card:hover .blog-image img {
    transform: scale(1.05);
}

.blog-content {
    padding: 1.5rem;
}

.blog-category {
    background: var(--violeta-suave);
    color: var(--violeta);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.blog-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.3rem;
    line-height: 1.4;
}

.blog-card h3 a {
    color: var(--verde-militar);
    text-decoration: none;
    transition: color 0.3s ease;
}

.blog-card h3 a:hover {
    color: var(--violeta);
}

.blog-card p {
    color: var(--texto-claro);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.blog-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--gris-medio);
}

.blog-date {
    color: var(--texto-claro);
    font-size: 0.9rem;
}

.read-more {
    color: var(--violeta);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--violeta-claro);
}

@media (max-width: 768px) {
    .blog-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>