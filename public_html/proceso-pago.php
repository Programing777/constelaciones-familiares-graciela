<?php
include 'includes/config.php';

// Verificar que se haya proporcionado un ID de producto
if (!isset($_GET['producto_id']) || empty($_GET['producto_id'])) {
    header('Location: productos.php');
    exit;
}

$producto_id = intval($_GET['producto_id']);

// Obtener información del producto
$producto = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND estado = 'activo'");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error al obtener producto: " . $e->getMessage());
}

// Si no se encuentra el producto, redirigir
if (!$producto) {
    header('Location: productos.php');
    exit;
}

$page_title = 'Proceso de Pago - ' . $producto['nombre'];
$meta_descripcion = 'Completa tu compra de ' . $producto['nombre'];
$meta_keywords = 'pago, compra, producto, ' . $producto['nombre'];

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Proceso de Pago</h1>
        <p>Completa tu compra de <?php echo htmlspecialchars($producto['nombre']); ?></p>
    </div>
</section>

<section class="checkout-content section-padding">
    <div class="container">
        <div class="checkout-grid">
            <div class="checkout-form">
                <h2>Información de Pago</h2>
                <form method="POST" action="confirmacion-pago.php">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="monto" value="<?php echo $producto['precio']; ?>">
                    
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Método de Pago</h3>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="westernunion" name="metodo_pago" value="westernunion" checked>
                                <label for="westernunion">
                                    <span class="payment-icon">🏦</span>
                                    <span class="payment-text">Western Union</span>
                                    <span class="payment-desc">Transferencia internacional rápida y segura</span>
                                </label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="transferencia" name="metodo_pago" value="transferencia">
                                <label for="transferencia">
                                    <span class="payment-icon">💳</span>
                                    <span class="payment-text">Transferencia Bancaria</span>
                                    <span class="payment-desc">Transferencia desde tu banco (hasta 24hs para activación)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-full">Proceder al Pago</button>
                        <a href="productos.php" class="btn btn-outline btn-full">Cancelar</a>
                    </div>
                </form>
            </div>
            
            <div class="order-summary">
                <div class="summary-card">
                    <h3>Resumen de Compra</h3>
                    <div class="summary-item">
                        <div class="item-image">
                            <img src="images/productos/<?php echo $producto['imagen'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        </div>
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        </div>
                        <div class="item-price">
                            $<?php echo number_format($producto['precio'], 2); ?>
                        </div>
                    </div>
                    
                    <div class="summary-totals">
                        <div class="total-line">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($producto['precio'], 2); ?></span>
                        </div>
                        <div class="total-line">
                            <span>Impuestos:</span>
                            <span>$0.00</span>
                        </div>
                        <div class="total-line total">
                            <span>Total:</span>
                            <span>$<?php echo number_format($producto['precio'], 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="product-features">
                        <h4>¿Qué incluye?</h4>
                        <ul>
                            <li>Acceso inmediato después del pago</li>
                            <li>Descarga ilimitada</li>
                            <li>Soporte por email</li>
                            <li>Garantía de 30 días</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>