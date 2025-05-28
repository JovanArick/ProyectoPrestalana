<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

// Consulta para obtener clientes con al menos una cuota pendiente o atrasada
$sql = "
    SELECT DISTINCT u.id, u.nombre, u.apellido_paterno, u.correo, u.telefono
    FROM usuarios u
    JOIN prestamos p ON u.id = p.id_cliente
    JOIN esquema_pagos ep ON p.id = ep.id_prestamo
    WHERE ep.estatus IN ('pendiente', 'atrasado')
";

$morosos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clientes Morosos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</nav>

<div class="container">
    <h2>Clientes Morosos</h2>

    <?php if (count($morosos) > 0): ?>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($morosos as $cliente): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?></td>
                    <td><?= htmlspecialchars($cliente['correo']) ?></td>
                    <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-success">No hay clientes morosos actualmente.</div>
    <?php endif; ?>
</div>
</body>
</html>
