<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
verificarSesionCliente();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Usuario no autenticado.");
        }

        $id_cliente = $_SESSION['user_id'];
        $id_plan = $_POST['id_plan'] ?? null;
        $monto = $_POST['monto'] ?? null;
        $plazo = $_POST['plazo'] ?? null;

        if (!$id_plan || !$monto || !$plazo) {
            throw new Exception("Faltan datos obligatorios.");
        }

        $stmtPlan = $pdo->prepare("SELECT tasa_interes FROM planes_interes WHERE id = ?");
        $stmtPlan->execute([$id_plan]);
        $tasa = $stmtPlan->fetchColumn();

        if (!$tasa) {
            throw new Exception("No se encontró el plan seleccionado.");
        }

        $interes_decimal = $tasa / 100;
        $cuota = ($monto * (1 + $interes_decimal)) / $plazo;

        $stmt = $pdo->prepare("INSERT INTO prestamos 
            (id_cliente, id_plan, monto_solicitado, tasa_final, plazo, cuota_mensual, estado)
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");

        $stmt->execute([$id_cliente, $id_plan, $monto, $tasa, $plazo, $cuota]);

        header("Location: dashboard.php?solicitud=ok");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$planes = $pdo->query("SELECT * FROM planes_interes WHERE estatus = 'activo'")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-outline-secondary">← Volver al Dashboard</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-warning">
            <h3 class="text-center">Solicitar Nuevo Préstamo</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">❌ Error: <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Selecciona un plan</label>
                    <select name="id_plan" class="form-select" required>
                        <option value="">-- Elige una opción --</option>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?= $plan['id'] ?>">
                                <?= $plan['nombre_plan'] ?> - <?= $plan['tasa_interes'] ?>% interés
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Monto solicitado</label>
                    <input type="number" name="monto" class="form-control" min="1000" required>
                </div>

                <div class="mb-3">
                    <label>Plazo (meses)</label>
                    <input type="number" name="plazo" class="form-control" min="3" required>
                </div>

                <button type="submit" class="btn btn-warning w-100">Enviar Solicitud</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
