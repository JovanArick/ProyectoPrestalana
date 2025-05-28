<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionCliente();

$id_cliente = $_SESSION['user_id'];

// Obtener pagos del cliente
$sql = "SELECT 
            p.id AS id_prestamo,
            pl.nombre_plan,
            e.numero_cuota,
            pa.monto_pagado,
            pa.metodo,
            pa.referencia,
            pa.fecha_pago
        FROM pagos pa
        JOIN esquema_pagos e ON pa.id_esquema_pago = e.id
        JOIN prestamos p ON e.id_prestamo = p.id
        JOIN planes_interes pl ON p.id_plan = pl.id
        WHERE p.id_cliente = ?
        ORDER BY pa.fecha_pago DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_cliente]);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h4 class="mb-3">Historial de Pagos</h4>
<?php if ($pagos): ?>
<table class="table table-hover">
    <thead class="table-light">
        <tr>
            <th>Préstamo</th>
            <th>Plan</th>
            <th>Cuota</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Referencia</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pagos as $p): ?>
        <tr>
            <td>#<?= $p['id_prestamo'] ?></td>
            <td><?= htmlspecialchars($p['nombre_plan']) ?></td>
            <td><?= $p['numero_cuota'] ?></td>
            <td>$<?= number_format($p['monto_pagado'], 2) ?></td>
            <td><?= ucfirst($p['metodo']) ?></td>
            <td><?= $p['referencia'] ?: '-' ?></td>
            <td><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-muted">No tienes pagos registrados todavía.</p>
<?php endif; ?>
