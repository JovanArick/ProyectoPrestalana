<?php
function enviarNotificacionPrestamo($pdo, $id_usuario, $titulo, $mensaje) {
    $stmt = $pdo->prepare("INSERT INTO notificaciones (id_usuario, titulo, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$id_usuario, $titulo, $mensaje]);
}
?>
