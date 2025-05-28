<?php
require_once '../includes/db.php';
require_once 'admin_functions.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

$morosos = generateDelinquencyReport();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Prestapache</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Dashboard</a>
        <div>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>Reporte de Clientes Morosos</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Cliente</th>
                <th>ID Préstamo</th>
                <th>Monto Aprobado</th>
                <th>Plazo</th>
                <th>Fecha Solicitud</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($morosos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= $p['id'] ?></td>
                <td>$<?= number_format($p['monto_aprobado'], 2) ?></td>
                <td><?= $p['plazo'] ?> meses</td>
                <td><?= date('d/m/Y', strtotime($p['fecha_solicitud'])) ?></td>
                <td><span class="badge bg-danger">Moroso</span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($morosos)): ?>
            <tr>
                <td colspan="6" class="text-center">No hay clientes morosos actualmente.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
