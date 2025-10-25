<?php
include 'includes/config.php';

// Obtener contenido de la página servicios desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'servicios'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_servicios = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Servicios - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Servicios de terapia en Constelaciones Familiares. Sesiones individuales, talleres grupales y más.';
        $meta_keywords = $pagina['keywords'] ?? 'servicios, terapia, sesiones, talleres, constelaciones familiares';
    } else {
        $contenido_servicios = '';
        $page_title = 'Servicios - Graciela Alida Sigalat';
        $meta_descripcion = 'Servicios de terapia en Constelaciones Familiares. Sesiones individuales, talleres grupales y más.';
        $meta_keywords = 'servicios, terapia, sesiones, talleres, constelaciones familiares';
    }
} catch (PDOException $e) {
    $contenido_servicios = '';
    error_log("Error al obtener contenido servicios: " . $e->getMessage());
}

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Servicios</h1>
        <p>Conoce los servicios que ofrezco para tu crecimiento personal</p>
    </div>
</section>

<section class="servicios-content section-padding">
    <div class="container">
        <?php if (!empty($contenido_servicios)): ?>
            <?php echo $contenido_servicios; ?>
        <?php else: ?>
            <!-- Contenido por defecto -->
            <div class="service-detail">
                <div class="service-header" id="individuales">
                    <h2>Sesiones Individuales</h2>
                    <p class="service-price">$5,000 ARS por sesión</p>
                </div>
                <div class="service-description">
                    <p>Sesiones personalizadas de 60 a 90 minutos donde trabajamos en tu situación específica. Utilizamos el método de Constelaciones Familiares para identificar y resolver los patrones que están afectando tu vida.</p>
                    <ul>
                        <li>Enfoque personalizado en tu caso</li>
                        <li>Espacio seguro y confidencial</li>
                        <li>Herramientas prácticas para aplicar en tu día a día</li>
                        <li>Seguimiento del proceso</li>
                    </ul>
                </div>
                <div class="service-actions">
                    <a href="contacto.php?servicio=individual" class="btn btn-primary">Agendar Sesión Individual</a>
                </div>
            </div>

            <div class="service-detail" id="grupales">
                <div class="service-header">
                    <h2>Talleres Grupales</h2>
                    <p class="service-price">$2,500 ARS por taller</p>
                </div>
                <div class="service-description">
                    <p>Espacios de sanación colectiva donde trabajamos en grupo. Los talles grupales permiten compartir experiencias y aprender de los procesos de otros, en un ambiente de respeto y confidencialidad.</p>
                    <ul>
                        <li>Grupos reducidos (máximo 10 personas)</li>
                        <li>Duración de 3 a 4 horas</li>
                        <li>Temáticas específicas cada mes</li>
                        <li>Material de apoyo incluido</li>
                    </ul>
                </div>
                <div class="service-actions">
                    <a href="contacto.php?servicio=grupal" class="btn btn-primary">Información sobre Talleres</a>
                </div>
            </div>

            <div class="service-detail" id="constelaciones">
                <div class="service-header">
                    <h2>Constelaciones Familiares</h2>
                    <p class="service-price">Consultar precios</p>
                </div>
                <div class="service-description">
                    <p>El método de Constelaciones Familiares es una herramienta terapéutica que permite visualizar y resolver conflictos familiares y personales. A través de esta técnica, podemos acceder a patrones inconscientes que se repiten en las familias por generaciones.</p>
                    <ul>
                        <li>Identificación de patrones familiares</li>
                        <li>Sanación de relaciones</li>
                        <li>Liberación de cargas heredadas</li>
                        <li>Reconexión con el flujo de la vida</li>
                    </ul>
                </div>
                <div class="service-actions">
                    <a href="constelaciones.php" class="btn btn-outline">Más sobre Constelaciones</a>
                    <a href="contacto.php?servicio=constelaciones" class="btn btn-primary">Consultar</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>¿Tienes dudas sobre qué servicio elegir?</h2>
            <p>Contacta conmigo para una consulta gratuita de 15 minutos y te ayudo a elegir el mejor camino para ti.</p>
            <div class="cta-actions">
                <a href="contacto.php" class="btn btn-primary">Solicitar Consulta Gratuita</a>
            </div>
        </div>
    </div>
</section>

<style>
.service-detail {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 3rem;
    border-left: 4px solid var(--verde-militar);
}

.service-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gris-medio);
}

.service-header h2 {
    color: var(--verde-militar);
    margin: 0;
    flex: 1;
}

.service-price {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--violeta);
    margin: 0;
}

.service-description p {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    color: var(--texto);
}

.service-description ul {
    margin: 1.5rem 0;
    padding-left: 1.5rem;
}

.service-description li {
    margin-bottom: 0.8rem;
    padding-left: 0.5rem;
    position: relative;
}

.service-description li:before {
    content: "✓";
    color: var(--verde-oliva);
    font-weight: bold;
    position: absolute;
    left: -1.2rem;
}

.service-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .service-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .service-detail {
        padding: 1.5rem;
    }
    
    .service-actions {
        flex-direction: column;
    }
    
    .service-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>