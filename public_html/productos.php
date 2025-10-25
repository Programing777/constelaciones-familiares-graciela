<?php
include 'includes/config.php';

// Obtener contenido de la página productos desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT contenido, titulo, descripcion, keywords FROM contenido_paginas WHERE pagina = 'productos'");
    $stmt->execute();
    $pagina = $stmt->fetch();
    
    if ($pagina) {
        $contenido_productos = $pagina['contenido'] ?? '';
        $page_title = $pagina['titulo'] ?? 'Productos - Graciela Alida Sigalat';
        $meta_descripcion = $pagina['descripcion'] ?? 'Productos digitales para tu crecimiento personal. Meditaciones, sesiones grabadas y recursos.';
        $meta_keywords = $pagina['keywords'] ?? 'productos, digital, meditaciones, sesiones grabadas, recursos';
    } else {
        $contenido_productos = '';
        $page_title = 'Productos - Graciela Alida Sigalat';
        $meta_descripcion = 'Productos digitales para tu crecimiento personal. Meditaciones, sesiones grabadas y recursos.';
        $meta_keywords = 'productos, digital, meditaciones, sesiones grabadas, recursos';
    }
} catch (PDOException $e) {
    $contenido_productos = '';
    error_log("Error al obtener contenido productos: " . $e->getMessage());
}

// Obtener productos de la base de datos
$productos = [];
try {
    $stmt = $pdo->query("SELECT * FROM productos WHERE estado = 'activo' ORDER BY id DESC");
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al obtener productos: " . $e->getMessage());
}

include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Productos Digitales</h1>
        <p>Recursos para tu crecimiento personal desde la comodidad de tu hogar</p>
    </div>
</section>

<section class="productos-content section-padding">
    <div class="container">
        <?php if (!empty($contenido_productos)): ?>
            <?php echo $contenido_productos; ?>
        <?php else: ?>
            <!-- Contenido por defecto -->
            <div class="products-intro">
                <h2>Explora Mis Productos Digitales</h2>
                <p>Todos los productos son descargables inmediatamente después del pago. Accede desde cualquier dispositivo y avanza a tu propio ritmo.</p>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <!-- Puedes agregar una imagen por defecto o desde la base de datos -->
                            <img src="images/productos/<?php echo $producto['imagen'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($producto['categoria']); ?></span>
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <div class="product-footer">
                                <span class="product-price">$<?php echo number_format($producto['precio'], 2); ?></span>
                                <a href="proceso-pago.php?producto_id=<?php echo $producto['id']; ?>" class="btn btn-primary">Comprar Ahora</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Productos por defecto si no hay en la base de datos -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="images/productos/meditacion.jpg" alt="Meditación Guiada">
                    </div>
                    <div class="product-info">
                        <span class="product-category">Meditación</span>
                        <h3>Meditación para la Sanación Familiar</h3>
                        <p>Una meditación guiada de 30 minutos para trabajar en la sanación de relaciones familiares y liberar cargas heredadas.</p>
                        <div class="product-footer">
                            <span class="product-price">$1,500.00</span>
                            <a href="proceso-pago.php?producto_id=1" class="btn btn-primary">Comprar Ahora</a>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="images/productos/taller.jpg" alt="Taller Grabado">
                    </div>
                    <div class="product-info">
                        <span class="product-category">Taller</span>
                        <h3>Taller: Introducción a las Constelaciones Familiares</h3>
                        <p>Grabación de un taller de 3 horas donde explico los fundamentos de las Constelaciones Familiares y cómo aplicarlas en tu vida.</p>
                        <div class="product-footer">
                            <span class="product-price">$2,000.00</span>
                            <a href="proceso-pago.php?producto_id=2" class="btn btn-primary">Comprar Ahora</a>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-image">
                        <img src="images/productos/ebook.jpg" alt="E-book">
                    </div>
                    <div class="product-info">
                        <span class="product-category">E-book</span>
                        <h3>Guía Práctica para la Sanación Sistémica</h3>
                        <p>Un e-book de 50 páginas con ejercicios prácticos y reflexiones para iniciar tu proceso de sanación familiar.</p>
                        <div class="product-footer">
                            <span class="product-price">$800.00</span>
                            <a href="proceso-pago.php?producto_id=3" class="btn btn-primary">Comprar Ahora</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="faq-section section-padding bg-light">
    <div class="container">
        <h2 class="section-title">Preguntas Frecuentes</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h3>¿Cómo recibo el producto después de pagar?</h3>
                <p>Inmediatamente después de confirmado el pago, recibirás un email con el enlace de descarga. También podrás acceder desde tu cuenta en el sitio.</p>
            </div>
            <div class="faq-item">
                <h3>¿Los productos tienen garantía?</h3>
                <p>Sí, ofrecemos 30 días de garantía. Si el producto no cumple con tus expectativas, te reembolsamos el 100%.</p>
            </div>
            <div class="faq-item">
                <h3>¿Puedo pagar con transferencia bancaria?</h3>
                <p>Sí, además de Mercado Pago, aceptamos transferencias bancarias. El proceso de activación puede tomar hasta 24 horas.</p>
            </div>
        </div>
    </div>
</section>

<style>
.products-intro {
    text-align: center;
    margin-bottom: 3rem;
}

.products-intro h2 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
}

.products-intro p {
    font-size: 1.1rem;
    color: var(--texto-claro);
    max-width: 600px;
    margin: 0 auto;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.product-image {
    height: 200px;
    background: linear-gradient(135deg, var(--verde-seco), var(--verde-oliva));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    background: var(--violeta-suave);
    color: var(--violeta);
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
    align-self: flex-start;
}

.product-card h3 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
    font-size: 1.3rem;
    line-height: 1.4;
}

.product-card p {
    color: var(--texto-claro);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-price {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--violeta);
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.faq-item {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}

.faq-item h3 {
    color: var(--verde-militar);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.faq-item p {
    color: var(--texto-claro);
    line-height: 1.6;
    margin: 0;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .product-footer .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>