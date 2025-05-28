<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionCliente();

$id_cliente = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT p.*, pl.nombre_plan, pl.tasa_interes
    FROM prestamos p
    JOIN planes_interes pl ON p.id_plan = pl.id
    WHERE p.id_cliente = ?
    ORDER BY p.fecha_solicitud DESC");
$stmt->execute([$id_cliente]);
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h4 class="mb-3">Mis Préstamos</h4>
<?php if ($prestamos): ?>
<table class="table table-striped">
    <thead class="table-primary">
        <tr>
            <th>Plan</th>
            <th>Monto</th>
            <th>Interés</th>
            <th>Estado</th>
            <th>Cuota</th>
            <th>Plazo</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($prestamos as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nombre_plan']) ?></td>

            <td>$<?= number_format($p['estado'] === 'aprobado' ? $p['monto_aprobado'] : $p['monto_solicitado'],2) ?></td>
            <td><?= $p['tasa_final'] ?: $p['tasa_interes'] ?>%</td>
            <td><span class="badge bg-<?= match($p['estado']) {
                'aprobado' => 'success',
                'pendiente' => 'warning',
                'rechazado' => 'danger',
                'moroso' => 'dark',
                'liquidado' => 'primary',
                default => 'secondary'
            } ?>"><?= ucfirst($p['estado']) ?></span></td>
            <td>$<?= number_format($p['cuota_mensual'], 2) ?></td>
            <td><?= $p['plazo'] ?> meses</td>
            <td><?= date('d/m/Y', strtotime($p['fecha_solicitud'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-muted">No tienes préstamos registrados aún.</p>
<?php endif; ?>
