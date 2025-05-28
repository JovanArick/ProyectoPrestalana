<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

// Buscar préstamos aprobados sin cuotas generadas
$sql = "SELECT p.* FROM prestamos p
        WHERE p.estado = 'aprobado'
        AND NOT EXISTS (
            SELECT 1 FROM esquema_pagos ep WHERE ep.id_prestamo = p.id
        )";

$prestamos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$totalGenerados = 0;
foreach ($prestamos as $prestamo) {
    $id_prestamo = $prestamo['id'];
    $monto = $prestamo['monto_aprobado'];
    $interes = $prestamo['tasa_final'] / 100;
    $plazo = $prestamo['plazo'];

    $cuota_base = $monto / $plazo;
    $interes_mensual = ($monto * $interes) / $plazo;
    $saldo = $monto;
    $hoy = new DateTime();

    $stmt = $pdo->prepare("INSERT INTO esquema_pagos 
        (id_prestamo, numero_cuota, fecha_vencimiento, capital, interes, saldo_restante) 
        VALUES (?, ?, ?, ?, ?, ?)");

    for ($i = 1; $i <= $plazo; $i++) {
        $hoy->modify('+1 month');
        $fecha = $hoy->format('Y-m-d');
        $saldo -= $cuota_base;
        $stmt->execute([
            $id_prestamo,
            $i,
            $fecha,
            $cuota_base,
            $interes_mensual,
            max($saldo, 0)
        ]);
        $totalGenerados++;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Esquemas de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3>✅ Proceso completado</h3>
            <p>Se generaron <strong><?= $totalGenerados ?></strong> cuotas de pagos para préstamos aprobados.</p>
            <a href="gestion_pagos.php" class="btn btn-primary">Ir a Gestión de Pagos</a>
        </div>
    </div>
</div>
</body>
</html>
