<?php
$page_title = 'Página No Encontrada - 404';
include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>Error 404</h1>
        <p>Página no encontrada</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="text-center">
            <h2>Lo sentimos, la página que buscas no existe.</h2>
            <p>Puede que haya sido movida, eliminada o que hayas escrito mal la dirección.</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
                <a href="contacto.php" class="btn btn-outline">Contactar Soporte</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>