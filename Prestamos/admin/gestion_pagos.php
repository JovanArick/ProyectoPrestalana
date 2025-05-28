<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

// Lista de clientes para el filtro
$clientes = $pdo->query("SELECT id, nombre FROM usuarios WHERE id IN (SELECT id_cliente FROM prestamos)")->fetchAll(PDO::FETCH_ASSOC);

// Filtro por cliente
$filtro_cliente = $_GET['cliente'] ?? '';
$where_clause = "WHERE ep.estatus IN ('pendiente', 'atrasado')";
$params = [];

if (!empty($filtro_cliente)) {
    $where_clause .= " AND u.id = ?";
    $params[] = $filtro_cliente;
}

$sql = "SELECT 
            ep.id AS id_cuota,
            p.id AS id_prestamo,
            u.id AS id_cliente,
            u.nombre,
            ep.numero_cuota,
            ep.capital,
            ep.interes,
            ep.fecha_vencimiento,
            ep.estatus,
            ep.saldo_restante
        FROM esquema_pagos ep
        JOIN prestamos p ON ep.id_prestamo = p.id
        JOIN usuarios u ON p.id_cliente = u.id
        $where_clause
        ORDER BY ep.fecha_vencimiento ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cuota'])) {
    $id_cuota = $_POST['id_cuota'];
    $metodo = $_POST['metodo'];
    $referencia = $_POST['referencia'] ?? null;
    $monto_pagado = $_POST['monto_pagado'];

    $stmtInfo = $pdo->prepare("SELECT ep.*, p.id_cliente, p.id as id_prestamo FROM esquema_pagos ep JOIN prestamos p ON ep.id_prestamo = p.id WHERE ep.id = ?");
    $stmtInfo->execute([$id_cuota]);
    $cuota = $stmtInfo->fetch(PDO::FETCH_ASSOC);

    $id_prestamo = $cuota['id_prestamo'];

    try {
        $pdo->beginTransaction();

        $stmtPago = $pdo->prepare("INSERT INTO pagos (id_esquema_pago, monto_pagado, metodo, referencia) VALUES (?, ?, ?, ?)");
        $stmtPago->execute([$id_cuota, $monto_pagado, $metodo, $referencia]);

        $pdo->prepare("UPDATE esquema_pagos SET estatus = 'pagado' WHERE id = ?")
            ->execute([$id_cuota]);

        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM esquema_pagos WHERE id_prestamo = ? AND estatus != 'pagado'");
        $stmtCheck->execute([$id_prestamo]);
        $cuotas_restantes = $stmtCheck->fetchColumn();

        if ($cuotas_restantes == 0) {
            $pdo->prepare("UPDATE prestamos SET estado = 'liquidado', fecha_liquidacion = NOW() WHERE id = ?")
                ->execute([$id_prestamo]);
        }

        $pdo->commit();
        header("Location: gestion_pagos.php?pagado=ok&cliente=$filtro_cliente");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error al registrar el pago: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-4">
    <h2>Cuotas Pendientes</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($_GET['pagado']) && $_GET['pagado'] === 'ok'): ?>
        <div class="alert alert-success">Pago registrado correctamente.</div>
    <?php endif; ?>

    <form class="row g-3 mb-3" method="GET">
        <div class="col-md-4">
            <select name="cliente" class="form-select">
                <option value="">Todos los clientes</option>
                <?php foreach ($clientes as $cli): ?>
                    <option value="<?= $cli['id'] ?>" <?= ($filtro_cliente == $cli['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cli['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-secondary">
            <tr>
                <th>Cliente</th>
                <th>Préstamo</th>
                <th>Cuota</th>
                <th>Capital</th>
                <th>Interés</th>
                <th>Vencimiento</th>
                <th>Saldo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cuotas as $cuota): ?>
            <tr>
                <td><?= htmlspecialchars($cuota['nombre']) ?></td>
                <td>#<?= $cuota['id_prestamo'] ?></td>
                <td><?= $cuota['numero_cuota'] ?></td>
                <td>$<?= number_format($cuota['capital'], 2) ?></td>
                <td>$<?= number_format($cuota['interes'], 2) ?></td>
                <td><?= date('d/m/Y', strtotime($cuota['fecha_vencimiento'])) ?></td>
                <td>$<?= number_format($cuota['saldo_restante'], 2) ?></td>
                <td>
                    <form method="POST" class="d-flex flex-column gap-2">
                        <input type="hidden" name="id_cuota" value="<?= $cuota['id_cuota'] ?>">
                        <input type="number" step="0.01" name="monto_pagado" class="form-control form-control-sm" placeholder="Monto" required>
                        <select name="metodo" class="form-select form-select-sm" required>
                            <option value="">Método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="otros">Otros</option>
                        </select>
                        <input type="text" name="referencia" class="form-control form-control-sm" placeholder="Referencia opcional">
                        <button type="submit" class="btn btn-success btn-sm">Registrar Pago</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
</body>
</html>
