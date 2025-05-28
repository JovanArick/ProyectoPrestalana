<?php
function enviarNotificacionPrestamo($pdo, $id_usuario, $titulo, $mensaje) {
    $stmt = $pdo->prepare("INSERT INTO notificaciones (id_usuario, titulo, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$id_usuario, $titulo, $mensaje]);
}

// Este código se colocaría dentro de aprobacion_prestamos.php donde ya se actualiza el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $id = $_POST['id'];
    $accion = $_POST['accion'];

    $prestamo = $pdo->prepare("SELECT id_cliente FROM prestamos WHERE id = ?");
    $prestamo->execute([$id]);
    $id_cliente = $prestamo->fetchColumn();

    if ($accion === 'aprobar') {
        $pdo->prepare("UPDATE prestamos SET estado = 'aprobado', fecha_aprobacion = NOW() WHERE id = ?")
            ->execute([$id]);

        enviarNotificacionPrestamo($pdo, $id_cliente, 'Préstamo aprobado', 'Tu solicitud de préstamo ha sido aprobada.');

    } elseif ($accion === 'rechazar') {
        $motivo = $_POST['motivo'] ?? 'Sin motivo especificado';
        $pdo->prepare("UPDATE prestamos SET estado = 'rechazado', motivo_rechazo = ? WHERE id = ?")
            ->execute([$motivo, $id]);

        enviarNotificacionPrestamo($pdo, $id_cliente, 'Préstamo rechazado', "Tu solicitud ha sido rechazada. Motivo: $motivo");
    }

    header("Location: aprobacion_prestamos.php");
    exit;
}
?>
