<?php
require_once '../includes/db.php';
require_once 'admin_functions.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

// Estadísticas clave
$stats = [
    'total_clientes' => getTotalUsers('cliente'),
    'prestamos_pendientes' => getLoansByStatus('pendiente'),
    'monto_total_aprobado' => getTotalApprovedAmount(),
    'morosidad' => getDelinquencyRate()
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Admin - Prestalana</span>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-4">
    <!-- Botones principales -->
    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="aprobacion_prestamos.php" class="btn btn-primary">Aprobar Préstamos</a>
        <a href="gestion_usuarios.php" class="btn btn-secondary">Gestión de Usuarios</a>
        <a href="historial_clientes.php" class="btn btn-warning">Historial de Clientes</a>
        <a href="gestion_pagos.php" class="btn btn-success">Ver Pagos</a>
        <a href="clientes_morosos.php" class="btn btn-info">Ver Clientes Morosos</a>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <?php foreach($stats as $key => $value): ?>
        <div class="col">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title text-muted"><?= ucfirst(str_replace('_', ' ', $key)) ?></h5>
                    <p class="display-6">
                        <?= is_numeric($value) ? number_format($value) : $value ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Gráfico de Préstamos -->
    <div class="card shadow">
        <div class="card-body">
            <canvas id="loansChart"></canvas>
        </div>
    </div>
</div>

<script>
// Gráfico con Chart.js
const ctx = document.getElementById('loansChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Aprobados', 'Pendientes', 'Rechazados', 'Morosos'],
        datasets: [{
            label: 'Distribución de Préstamos',
            data: [<?= implode(',', getLoanDistribution()) ?>],
            backgroundColor: ['#4CAF50', '#FFC107', '#F44336', '#9E9E9E']
        }]
    }
});
</script>
</body>
</html>
