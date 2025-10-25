<?php
include 'includes/config.php';

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

        // Enviar email de notificación (opcional)
        // $asunto = "Nuevo mensaje de contacto de $nombre";
        // $cuerpo = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\nServicio: $servicio\nMensaje: $mensaje";
        // mail('info@sientetuesencia.com', $asunto, $cuerpo);

        // Redirigir con mensaje de éxito
        $_SESSION['mensaje_exito'] = "Tu mensaje ha sido enviado correctamente. Te contactaré pronto.";
        header('Location: contacto.php');
        exit;

    } catch (PDOException $e) {
        error_log("Error al guardar contacto: " . $e->getMessage());
        $_SESSION['mensaje_error'] = "Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente.";
        header('Location: contacto.php');
        exit;
    }
} else {
    // Si se accede directamente sin POST, redirigir al contacto
    header('Location: contacto.php');
    exit;
}