<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionCliente();

$id_cliente = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nombre, apellido_paterno, apellido_materno, correo, telefono, fecha_registro FROM usuarios WHERE id = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<h4 class="mb-3">Mi Perfil</h4>
<?php if ($cliente): ?>
<div class="row">
    <div class="col-md-6">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre']) ?></p>
        <p><strong>Apellido Paterno:</strong> <?= htmlspecialchars($cliente['apellido_paterno']) ?></p>
        <p><strong>Apellido Materno:</strong> <?= htmlspecialchars($cliente['apellido_materno']) ?></p>
    </div>
    <div class="col-md-6">
        <p><strong>Correo:</strong> <?= htmlspecialchars($cliente['correo']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono']) ?></p>
        <p><strong>Miembro desde:</strong> <?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></p>
    </div>
</div>
<?php else: ?>
<p class="text-danger">No se pudo cargar la información del perfil.</p>
<?php endif; ?>
