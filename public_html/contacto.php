<?php
include 'includes/config.php';

// Obtener contenido de la página contacto desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'contacto'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_contacto = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Contacto - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Contacta con Graciela Alida Sigalat para agendar sesiones, talleres o resolver tus dudas.';
        $meta_keywords = $pagina['keywords'] ?? 'contacto, agendar, información, consulta';
    } else {
        $contenido_contacto = '';
        $page_title = 'Contacto - Graciela Alida Sigalat';
        $meta_descripcion = 'Contacta con Graciela Alida Sigalat para agendar sesiones, talleres o resolver tus dudas.';
        $meta_keywords = 'contacto, agendar, información, consulta';
    }
} catch (PDOException $e) {
    $contenido_contacto = '';
    error_log("Error al obtener contenido contacto: " . $e->getMessage());
}

// Procesar el formulario de contacto si se envió
$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre']);
    $email = sanitize($_POST['email']);
    $telefono = sanitize($_POST['telefono'] ?? '');
    $servicio = sanitize($_POST['servicio'] ?? '');
    $mensaje = sanitize($_POST['mensaje']);

    try {
        // Guardar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO contactos (nombre, email, telefono, servicio, mensaje) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $telefono, $servicio, $mensaje]);

        // Enviar email (opcional, descomenta si tienes configurado el envío de emails)
        // if (enviarEmailContacto($nombre, $email, $mensaje, $telefono, $servicio)) {
        //     $mensaje_exito = "Tu mensaje ha sido enviado correctamente. Te contactaré pronto.";
        // } else {
        //     $mensaje_error = "Hubo un error al enviar el email, pero tu mensaje se ha guardado. Te contactaré pronto.";
        // }

        $mensaje_exito = "Tu mensaje ha sido enviado correctamente. Te contactaré pronto.";

    } catch (PDOException $e) {
        error_log("Error al guardar contacto: " . $e->getMessage());
        $mensaje_error = "Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente.";
    }
}

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Contacto</h1>
        <p>Estoy aquí para acompañarte en tu proceso</p>
    </div>
</section>

<section class="contacto-content section-padding">
    <div class="container">
        <?php if (!empty($contenido_contacto)): ?>
            <?php echo $contenido_contacto; ?>
        <?php else: ?>
            <!-- Contenido por defecto -->
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>Información de Contacto</h2>
                    <div class="contact-item">
                        <div class="contact-icon">📧</div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p>info@sientetuesencia.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">📱</div>
                        <div class="contact-details">
                            <h3>Teléfono / WhatsApp</h3>
                            <p>+54 9 11 1234-5678</p>
                            <a href="https://wa.me/5491112345678" class="whatsapp-link" target="_blank">Enviar mensaje por WhatsApp</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">🕒</div>
                        <div class="contact-details">
                            <h3>Horarios de Atención</h3>
                            <p>Lunes a Viernes: 9:00 - 18:00</p>
                            <p>Sábados: 9:00 - 13:00</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <h2>Envíame un Mensaje</h2>
                    <?php if ($mensaje_exito): ?>
                        <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
                    <?php endif; ?>
                    <?php if ($mensaje_error): ?>
                        <div class="alert alert-error"><?php echo $mensaje_error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="contacto.php">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono (opcional)</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="servicio">Servicio de interés</label>
                            <select id="servicio" name="servicio" class="form-control">
                                <option value="">Selecciona una opción</option>
                                <option value="individual">Sesión Individual</option>
                                <option value="grupal">Taller Grupal</option>
                                <option value="constelaciones">Constelaciones Familiares</option>
                                <option value="productos">Productos Digitales</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mensaje">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="map-section section-padding bg-light">
    <div class="container">
        <h2 class="section-title">Ubicación</h2>
        <p>Atiendo tanto presencialmente en Buenos Aires como online para todo el mundo.</p>
        <!-- Aquí puedes integrar un mapa de Google Maps si lo deseas -->
        <div class="map-placeholder">
            <div class="map-content">
                <div class="map-icon">📍</div>
                <h3>Consultorio Presencial</h3>
                <p>Buenos Aires, Argentina</p>
                <p class="map-note">* Dirección exacta proporcionada al confirmar la cita</p>
            </div>
            <div class="map-content">
                <div class="map-icon">💻</div>
                <h3>Sesiones Online</h3>
                <p>Disponible para todo el mundo</p>
                <p class="map-note">* Plataformas: Zoom, Google Meet, WhatsApp</p>
            </div>
        </div>
    </div>
</section>

<style>
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}

.contact-info {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: fit-content;
}

.contact-info h2 {
    color: var(--verde-militar);
    margin-bottom: 2rem;
    text-align: center;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gris-medio);
}

.contact-item:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

.contact-icon {
    font-size: 1.5rem;
    background: var(--verde-suave);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-details h3 {
    color: var(--verde-militar);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.contact-details p {
    margin: 0.25rem 0;
    color: var(--texto);
}

.whatsapp-link {
    color: var(--violeta);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-block;
    margin-top: 0.5rem;
}

.whatsapp-link:hover {
    text-decoration: underline;
}

.contact-form-container {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.contact-form-container h2 {
    color: var(--verde-militar);
    margin-bottom: 2rem;
    text-align: center;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--verde-militar);
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--gris-medio);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--violeta);
    box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.1);
}

.btn-full {
    width: 100%;
}

.map-placeholder {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.map-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.map-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.map-content h3 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
}

.map-content p {
    color: var(--texto);
    margin-bottom: 0.5rem;
}

.map-note {
    font-size: 0.9rem;
    color: var(--texto-claro);
    font-style: italic;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left-color: #28a745;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left-color: #dc3545;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .contact-info,
    .contact-form-container {
        padding: 1.5rem;
    }
    
    .map-placeholder {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>