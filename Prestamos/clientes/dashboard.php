<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionCliente();

$cliente_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Bienvenido</span>
        <a href="load_logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($_GET['solicitud']) && $_GET['solicitud'] === 'ok'): ?>
        <div class="alert alert-success">Tu solicitud de préstamo fue enviada correctamente.</div>
    <?php endif; ?>

    <div class="row mb-3 g-2">
        <div class="col-md-3">
            <button class="btn btn-outline-primary w-100" onclick="loadSection('load_prestamos.php')">Mis Préstamos</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-success w-100" onclick="loadSection('load_pagos.php')">Pagos</button>
        </div>
        <div class="col-md-3">
            <a href="solicitar_prestamo.php" class="btn btn-outline-warning w-100">Solicitar Préstamo</a>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" onclick="loadSection('load_perfil.php')">Mi Perfil</button>
        </div>
    </div>

    <!-- Sección dinámica -->
    <div id="contenido" class="card shadow p-3">
        <p class="text-center">Selecciona una opción para ver la información.</p>
    </div>
</div>

<script>
function loadSection(url) {
    const contenido = document.getElementById('contenido');
    contenido.innerHTML = '<div class="text-center my-3">Cargando...</div>';

    fetch(url)
        .then(res => res.text())
        .then(html => contenido.innerHTML = html)
        .catch(err => contenido.innerHTML = '<div class="text-danger">Error al cargar.</div>');
}
</script>
</body>
</html>
