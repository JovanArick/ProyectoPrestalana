<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

// Obtener lista de clientes
$clientes = $pdo->query("SELECT id, nombre, correo FROM usuarios WHERE tipo = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);

// Si se seleccionó un cliente
$historial = [];
if (isset($_GET['cliente_id']) && is_numeric($_GET['cliente_id'])) {
    $id_cliente = $_GET['cliente_id'];

    $stmt = $pdo->prepare("
        SELECT 
            u.nombre AS cliente,
            u.correo,
            p.id AS id_prestamo,
            p.monto_solicitado,
            p.tasa_final,
            p.plazo,
            p.estado,
            p.fecha_aprobacion,
            ep.numero_cuota,
            ep.capital,
            ep.interes,
            ep.fecha_vencimiento,
            ep.estatus AS estatus_cuota,
            pg.monto_pagado,
            pg.metodo,
            pg.referencia,
            pg.fecha_pago
        FROM usuarios u
        JOIN prestamos p ON u.id = p.id_cliente
        LEFT JOIN esquema_pagos ep ON ep.id_prestamo = p.id
        LEFT JOIN pagos pg ON pg.id_esquema_pago = ep.id
        WHERE u.id = ?
        ORDER BY p.id DESC, ep.numero_cuota ASC
    ");
    $stmt->execute([$id_cliente]);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Historial del Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4">
    <a href="dashboard.php" class="navbar-brand">← Volver al Dashboard</a>
    <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
</nav>

<div class="container mt-4">
    <h2>Historial de Clientes</h2>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <select name="cliente_id" class="form-select" required>
                <option value="">Selecciona un cliente</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= isset($id_cliente) && $id_cliente == $c['id'] ? 'selected' : '' ?>>
                        <?= $c['nombre'] ?> (<?= $c['correo'] ?>)
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Ver Historial</button>
        </div>
    </form>

    <?php if (!empty($historial)): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Préstamo</th>
                    <th>Cuota</th>
                    <th>Capital</th>
                    <th>Interés</th>
                    <th>Vencimiento</th>
                    <th>Estado Cuota</th>
                    <th>Monto Pagado</th>
                    <th>Método</th>
                    <th>Referencia</th>
                    <th>Fecha Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $row): ?>
                <tr>
                    <td>#<?= $row['id_prestamo'] ?> (<?= $row['estado'] ?>)</td>
                    <td><?= $row['numero_cuota'] ?></td>
                    <td>$<?= number_format($row['capital'], 2) ?></td>
                    <td>$<?= number_format($row['interes'], 2) ?></td>
                    <td><?= $row['fecha_vencimiento'] ? date('d/m/Y', strtotime($row['fecha_vencimiento'])) : '-' ?></td>
                    <td><?= ucfirst($row['estatus_cuota']) ?></td>
                    <td><?= $row['monto_pagado'] ? '$' . number_format($row['monto_pagado'], 2) : '-' ?></td>
                    <td><?= $row['metodo'] ?? '-' ?></td>
                    <td><?= $row['referencia'] ?? '-' ?></td>
                    <td><?= $row['fecha_pago'] ? date('d/m/Y H:i', strtotime($row['fecha_pago'])) : '-' ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php elseif (isset($id_cliente)): ?>
        <div class="alert alert-warning">Este cliente no tiene historial registrado.</div>
    <?php endif; ?>
</div>
</body>
</html>
