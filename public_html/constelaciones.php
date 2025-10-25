<?php
include 'includes/config.php';

// Obtener contenido de la página constelaciones desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'constelaciones'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_constelaciones = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Constelaciones Familiares - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Descubre el método de Constelaciones Familiares para sanar relaciones y patrones familiares.';
        $meta_keywords = $pagina['keywords'] ?? 'constelaciones familiares, método terapéutico, sanación, relaciones familiares';
    } else {
        $contenido_constelaciones = '';
        $page_title = 'Constelaciones Familiares - Graciela Alida Sigalat';
        $meta_descripcion = 'Descubre el método de Constelaciones Familiares para sanar relaciones y patrones familiares.';
        $meta_keywords = 'constelaciones familiares, método terapéutico, sanación, relaciones familiares';
    }
} catch (PDOException $e) {
    $contenido_constelaciones = '';
    error_log("Error al obtener contenido constelaciones: " . $e->getMessage());
}

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Constelaciones Familiares</h1>
        <p>Descubre el poder de las Constelaciones Familiares</p>
    </div>
</section>

<section class="constelaciones-content section-padding">
    <div class="container">
        <?php if (!empty($contenido_constelaciones)): ?>
            <?php echo $contenido_constelaciones; ?>
        <?php else: ?>
            <!-- Contenido por defecto -->
            <div class="content-grid">
                <div class="main-content">
                    <h2>¿Qué son las Constelaciones Familiares?</h2>
                    <p>Las Constelaciones Familiares son un método terapéutico desarrollado por Bert Hellinger que permite visualizar y resolver conflictos familiares y personales que se repiten a lo largo de generaciones.</p>
                    
                    <p>Esta técnica nos ayuda a descubrir dinámicas ocultas en nuestro sistema familiar que influyen en nuestra vida actual, permitiéndonos liberar cargas heredadas y encontrar un nuevo orden que trae paz y armonía.</p>
                    
                    <h3>¿Cómo funcionan?</h3>
                    <p>Mediante la representación del sistema familiar, podemos acceder a información del inconsciente familiar y observar patrones que se repiten. Al hacerlo consciente, se produce una reorganización que permite la sanación.</p>
                    
                    <div class="benefits">
                        <h3>Beneficios de las Constelaciones Familiares</h3>
                        <div class="benefits-grid">
                            <div class="benefit-card">
                                <div class="benefit-icon">🔄</div>
                                <h4>Sanación de Patrones</h4>
                                <p>Libera patrones destructivos que se repiten en tu familia.</p>
                            </div>
                            <div class="benefit-card">
                                <div class="benefit-icon">❤️</div>
                                <h4>Mejora Relaciones</h4>
                                <p>Transforma tus relaciones familiares y de pareja.</p>
                            </div>
                            <div class="benefit-card">
                                <div class="benefit-icon">🎯</div>
                                <h4>Claridad Personal</h4>
                                <p>Encuentra tu lugar en el sistema familiar.</p>
                            </div>
                            <div class="benefit-card">
                                <div class="benefit-icon">🌱</div>
                                <h4>Crecimiento Espiritual</h4>
                                <p>Desarrolla una comprensión más profunda de la vida.</p>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Proceso de una Constelación</h3>
                    <ol>
                        <li><strong>Definición del tema:</strong> Identificamos el conflicto o situación a trabajar.</li>
                        <li><strong>Representación:</strong> Se eligen representantes para los miembros del sistema familiar.</li>
                        <li><strong>Observación:</strong> Se observan las dinámicas que emergen.</li>
                        <li><strong>Intervención:</strong> Se introducen frases sanadoras y movimientos.</li>
                        <li><strong>Integración:</strong> Se asimilan los insights y cambios.</li>
                    </ol>
                </div>
                
                <div class="sidebar">
                    <div class="sidebar-card">
                        <h4>¿Para quién es recomendable?</h4>
                        <ul>
                            <li>Personas con conflictos familiares recurrentes</li>
                            <li>Quienes repiten patrones de relación</li>
                            <li>Personas con dificultades en la pareja</li>
                            <li>Quienes buscan entender su lugar en la familia</li>
                            <li>Personas en procesos de duelo</li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-card">
                        <h4>Duración y Frecuencia</h4>
                        <p><strong>Duración:</strong> 60-90 minutos por sesión</p>
                        <p><strong>Frecuencia:</strong> Variable según el caso</p>
                        <p><strong>Modalidades:</strong> Individual y grupal</p>
                    </div>
                    
                    <div class="sidebar-card">
                        <h4>¿Lista para comenzar?</h4>
                        <p>Agenda una sesión y comienza tu proceso de sanación.</p>
                        <a href="contacto.php?servicio=constelaciones" class="btn btn-primary">Agendar Sesión</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}

.main-content h2 {
    color: var(--verde-militar);
    margin-bottom: 1.5rem;
    font-size: 2rem;
}

.main-content h3 {
    color: var(--verde-militar);
    margin: 2rem 0 1rem 0;
    font-size: 1.5rem;
}

.main-content p {
    line-height: 1.7;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.main-content ol {
    margin: 1.5rem 0;
    padding-left: 1.5rem;
}

.main-content li {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.benefits {
    margin: 2rem 0;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.benefit-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    text-align: center;
}

.benefit-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.benefit-card h4 {
    color: var(--verde-militar);
    margin-bottom: 0.5rem;
}

.benefit-card p {
    font-size: 0.95rem;
    margin: 0;
}

.sidebar-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.sidebar-card h4 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.sidebar-card ul {
    list-style: none;
    padding: 0;
}

.sidebar-card li {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gris-medio);
    position: relative;
    padding-left: 1.5rem;
}

.sidebar-card li:before {
    content: "•";
    color: var(--verde-oliva);
    font-weight: bold;
    position: absolute;
    left: 0;
}

.sidebar-card li:last-child {
    border-bottom: none;
}

.sidebar-card p {
    margin: 0.5rem 0;
    line-height: 1.5;
}

.sidebar-card .btn {
    width: 100%;
    text-align: center;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>