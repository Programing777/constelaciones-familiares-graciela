<?php
include 'includes/config.php';
include 'includes/header.php';

// Simular procesamiento de pago (en producción integrar con Western Union)
$estado_pago = 'completado'; // Por defecto, simular éxito

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = intval($_POST['producto_id']);
    $email = sanitize($_POST['email']);
    $nombre = sanitize($_POST['nombre']);
    $monto = floatval($_POST['monto']);
    $metodo_pago = sanitize($_POST['metodo_pago']);
    
    // En producción, aquí se integraría con Western Union
    if ($metodo_pago === 'westernunion') {
        // Simular procesamiento de Western Union
        $estado_pago = 'completado';
        $id_pago_wu = 'WU_' . time() . '_' . rand(1000, 9999);
    } else {
        // Transferencia bancaria - pendiente de confirmación
        $estado_pago = 'pendiente';
        $id_pago_wu = null;
    }
    
    // Guardar en base de datos
    try {
        // Insertar pago
        $stmt = $pdo->prepare("INSERT INTO pagos (email_cliente, monto, estado, metodo_pago, id_pago_western_union) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$email, $monto, $estado_pago, $metodo_pago, $id_pago_wu]);
        $pago_id = $pdo->lastInsertId();
        
        // Insertar pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (pago_id, producto_id, tipo, precio) VALUES (?, ?, 'producto', ?)");
        $stmt->execute([$pago_id, $producto_id, $monto]);
        
        // Obtener información del producto
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $producto = $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Error al procesar pago: " . $e->getMessage());
        $estado_pago = 'error';
    }
} else {
    // Si se accede directamente sin POST, redirigir
    header('Location: productos.php');
    exit;
}
?>

<section class="page-hero">
    <div class="container">
        <h1>Confirmación de Pago</h1>
        <p>Estado de tu transacción</p>
    </div>
</section>

<section class="payment-confirmation">
    <div class="container">
        <div class="confirmation-card">
            <?php if ($estado_pago === 'completado'): ?>
                <div class="confirmation-success">
                    <div class="success-icon">✅</div>
                    <h2>¡Pago Completado Exitosamente!</h2>
                    <p class="success-message">Tu compra ha sido procesada correctamente. Ya puedes acceder a tu producto.</p>
                    
                    <div class="order-details">
                        <h3>Detalles de tu Pedido</h3>
                        <div class="detail-item">
                            <strong>Número de Pedido:</strong> #<?php echo $pago_id; ?>
                        </div>
                        <div class="detail-item">
                            <strong>Producto:</strong> <?php echo htmlspecialchars($producto['nombre']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Monto:</strong> $<?php echo number_format($monto, 2); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Método de Pago:</strong> <?php echo $metodo_pago === 'westernunion' ? 'Western Union' : 'Transferencia Bancaria'; ?>
                        </div>
                        <div class="detail-item">
                            <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Fecha:</strong> <?php echo date('d/m/Y H:i'); ?>
                        </div>
                    </div>
                    
                    <?php if ($producto['archivo_url']): ?>
                        <div class="download-section">
                            <h3>📥 Descarga tu Producto</h3>
                            <a href="<?php echo $producto['archivo_url']; ?>" class="btn btn-primary btn-download" download>
                                Descargar Ahora
                            </a>
                            <p class="download-note">El enlace de descarga también será enviado a tu email.</p>
                        </div>
                    <?php else: ?>
                        <div class="access-section">
                            <h3>🔐 Acceso a tu Producto</h3>
                            <p>Recibirás un email con las instrucciones de acceso en los próximos minutos.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="confirmation-actions">
                        <a href="productos.php" class="btn btn-outline">Seguir Comprando</a>
                        <a href="index.php" class="btn btn-primary">Ir al Inicio</a>
                    </div>
                </div>
                
            <?php elseif ($estado_pago === 'pendiente'): ?>
                <div class="confirmation-pending">
                    <div class="pending-icon">⏳</div>
                    <h2>Pago Pendiente de Confirmación</h2>
                    <p class="pending-message">Hemos registrado tu pedido. Una vez confirmemos la transferencia, activaremos tu producto.</p>
                    
                    <div class="bank-instructions">
                        <h3>Próximos Pasos:</h3>
                        <ol>
                            <li>Realiza la transferencia a los datos proporcionados</li>
                            <li>Envía el comprobante por WhatsApp al +54 9 11 1234-5678</li>
                            <li>Te contactaremos para activar tu producto (generalmente en menos de 24 horas)</li>
                        </ol>
                    </div>
                    
                    <div class="confirmation-actions">
                        <a href="https://wa.me/5491112345678?text=Hola,%20acabo%20de%20realizar%20una%20transferencia%20para%20el%20producto%20<?php echo urlencode($producto['nombre']); ?>%20(Pedido%20#<?php echo $pago_id; ?>)" 
                           class="btn btn-primary" target="_blank">
                            📱 Enviar Comprobante por WhatsApp
                        </a>
                        <a href="productos.php" class="btn btn-outline">Volver a Productos</a>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="confirmation-error">
                    <div class="error-icon">❌</div>
                    <h2>Error en el Proceso de Pago</h2>
                    <p class="error-message">Ha ocurrido un error al procesar tu pago. Por favor, intenta nuevamente.</p>
                    
                    <div class="error-actions">
                        <a href="proceso-pago.php?producto_id=<?php echo $producto_id; ?>" class="btn btn-primary">Reintentar Pago</a>
                        <a href="productos.php" class="btn btn-outline">Volver a Productos</a>
                    </div>
                    
                    <div class="support-contact">
                        <p>Si el problema persiste, contáctanos:</p>
                        <div class="contact-options">
                            <a href="mailto:info@sientetuesencia.com" class="contact-link">📧 info@sientetuesencia.com</a>
                            <a href="https://wa.me/5491112345678" class="contact-link" target="_blank">📱 +54 9 11 1234-5678</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>