<?php
include 'includes/config.php';

// Obtener contenido de la página de inicio desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'index'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_index = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Siente Tu Esencia - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Terapeuta especializada en Constelaciones Familiares. Sesiones individuales, grupales y productos digitales para tu crecimiento personal.';
        $meta_keywords = $pagina['keywords'] ?? 'constelaciones familiares, terapia, crecimiento personal, sesiones';
    } else {
        $contenido_index = '';
        $page_title = 'Siente Tu Esencia - Graciela Alida Sigalat';
        $meta_descripcion = 'Terapeuta especializada en Constelaciones Familiares. Sesiones individuales, grupales y productos digitales para tu crecimiento personal.';
        $meta_keywords = 'constelaciones familiares, terapia, crecimiento personal, sesiones';
    }
} catch (PDOException $e) {
    $contenido_index = '';
    error_log("Error al obtener contenido index: " . $e->getMessage());
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Bienvenida a Siente Tu Esencia</h1>
            <p class="hero-subtitle">Terapeuta especializada en Constelaciones Familiares</p>
            <div class="hero-actions">
                <a href="servicios.php" class="btn btn-primary">Ver Servicios</a>
                <a href="contacto.php" class="btn btn-outline">Agendar Sesión</a>
            </div>
        </div>
    </div>
</section>

<!-- Contenido dinámico desde la base de datos -->
<?php if (!empty($contenido_index)): ?>
<section class="dynamic-content section-padding">
    <div class="container">
        <?php echo $contenido_index; ?>
    </div>
</section>
<?php else: ?>
<!-- Contenido por defecto -->
<section class="features section-padding">
    <div class="container">
        <h2 class="section-title">¿Por Qué Elegir Constelaciones Familiares?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3>Sanación Profunda</h3>
                <p>Trabajamos en la raíz de los problemas para una transformación duradera.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Enfoque Sistémico</h3>
                <p>Comprendemos los patrones familiares que influyen en tu vida actual.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💫</div>
                <h3>Transformación Personal</h3>
                <p>Libera cargas heredadas y encuentra tu propio camino.</p>
            </div>
        </div>
    </div>
</section>

<section class="services-preview section-padding bg-light">
    <div class="container">
        <h2 class="section-title">Mis Servicios</h2>
        <div class="services-grid">
            <div class="service-preview-card">
                <h3>Sesiones Individuales</h3>
                <p>Sesiones personalizadas de 60-90 minutos</p>
                <ul>
                    <li>Enfoque en tu situación específica</li>
                    <li>Espacio seguro y confidencial</li>
                    <li>Herramientas prácticas</li>
                </ul>
                <a href="servicios.php#individuales" class="btn btn-outline">Más Información</a>
            </div>
            <div class="service-preview-card">
                <h3>Talleres Grupales</h3>
                <p>Sesiones grupales para sanación colectiva</p>
                <ul>
                    <li>Compartir experiencias</li>
                    <li>Aprendizaje mutuo</li>
                    <li>Costos accesibles</li>
                </ul>
                <a href="servicios.php#grupales" class="btn btn-outline">Próximos Talleres</a>
            </div>
            <div class="service-preview-card">
                <h3>Constelaciones Familiares</h3>
                <p>Método terapéutico para sanar relaciones</p>
                <ul>
                    <li>Visualización de conflictos</li>
                    <li>Resolución de patrones</li>
                    <li>Armonización familiar</li>
                </ul>
                <a href="constelaciones.php" class="btn btn-outline">Conoce Más</a>
            </div>
        </div>
    </div>
</section>

<section class="testimonials section-padding">
    <div class="container">
        <h2 class="section-title">Testimonios</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "Las sesiones con Graciela transformaron mi relación con mi familia. Encontré paz y entendimiento."
                </div>
                <div class="testimonial-author">
                    <strong>María G.</strong>
                    <span>Buenos Aires</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "Un espacio seguro para sanar. Graciela tiene una capacidad increíble para guiar el proceso."
                </div>
                <div class="testimonial-author">
                    <strong>Carlos R.</strong>
                    <span>Córdoba</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "Los talleres grupales fueron una experiencia reveladora. Recomiendo totalmente su trabajo."
                </div>
                <div class="testimonial-author">
                    <strong>Ana L.</strong>
                    <span>Mendoza</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>¿Lista para Empezar tu Viaje de Sanación?</h2>
            <p>Agenda una sesión y comienza a transformar tu vida hoy mismo.</p>
            <div class="cta-actions">
                <a href="contacto.php" class="btn btn-primary">Agendar Sesión</a>
                <a href="servicios.php" class="btn btn-outline">Conocer Servicios</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.section-padding {
    padding: 80px 0;
}

.bg-light {
    background-color: var(--gris-claro);
}

.dynamic-content {
    line-height: 1.8;
}

.dynamic-content h1,
.dynamic-content h2,
.dynamic-content h3 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
}

.dynamic-content p {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.dynamic-content ul,
.dynamic-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.dynamic-content li {
    margin-bottom: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>