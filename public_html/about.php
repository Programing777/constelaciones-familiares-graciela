<?php
include 'includes/config.php';

// Obtener contenido de la página about desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'about'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_about = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Sobre Mí - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Conoce a Graciela Alida Sigalat, terapeuta especializada en Constelaciones Familiares.';
        $meta_keywords = $pagina['keywords'] ?? 'sobre mí, experiencia, formación, terapeuta';
    } else {
        $contenido_about = '';
        $page_title = 'Sobre Mí - Graciela Alida Sigalat';
        $meta_descripcion = 'Conoce a Graciela Alida Sigalat, terapeuta especializada en Constelaciones Familiares.';
        $meta_keywords = 'sobre mí, experiencia, formación, terapeuta';
    }
} catch (PDOException $e) {
    $contenido_about = '';
    error_log("Error al obtener contenido about: " . $e->getMessage());
}

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Sobre Mí</h1>
        <p>Conoce mi journey y experiencia</p>
    </div>
</section>

<section class="about-content section-padding">
    <div class="container">
        <?php if (!empty($contenido_about)): ?>
            <?php echo $contenido_about; ?>
        <?php else: ?>
            <!-- Contenido por defecto -->
            <div class="about-grid">
                <div class="about-text">
                    <h2>Graciela Alida Sigalat</h2>
                    <p>Terapeuta especializada en Constelaciones Familiares con más de 10 años de experiencia acompañando procesos de sanación y crecimiento personal.</p>
                    
                    <h3>Mi Misión</h3>
                    <p>Mi propósito es guiarte en el descubrimiento de tu esencia, ayudándote a liberar cargas familiares y emocionales que te impiden vivir en plenitud.</p>
                    
                    <h3>Mi Enfoque</h3>
                    <p>Trabajo desde una perspectiva sistémica y humanista, comprendiendo que cada persona es parte de un sistema familiar y social que influye en su vida.</p>
                    
                    <div class="experience-stats">
                        <div class="stat">
                            <span class="number">10+</span>
                            <span class="label">Años de Experiencia</span>
                        </div>
                        <div class="stat">
                            <span class="number">500+</span>
                            <span class="label">Personas Acompañadas</span>
                        </div>
                        <div class="stat">
                            <span class="number">100+</span>
                            <span class="label">Talleres Impartidos</span>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <!-- Aquí iría una imagen de Graciela -->
                    <div class="image-placeholder">
                        <span>Imagen de Graciela Alida Sigalat</span>
                    </div>
                </div>
            </div>
            
            <div class="certifications">
                <h3>Formación y Certificaciones</h3>
                <ul>
                    <li>Formación en Constelaciones Familiares - Instituto Bert Hellinger</li>
                    <li>Especialización en Terapia Sistémica - Universidad de Buenos Aires</li>
                    <li>Certificación en Mindfulness y Meditación</li>
                    <li>Diplomado en Psicología Transpersonal</li>
                    <li>Formación continua en Terapias Holísticas</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
<link rel="stylesheet" href="css/styles.css">
<?php include 'includes/footer.php'; ?>