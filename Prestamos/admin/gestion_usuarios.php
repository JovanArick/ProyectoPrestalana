<?php
require_once '../includes/db.php';
require_once 'admin_functions.php';
require_once '../includes/auth.php';
verificarSesionAdmin();

$usuarios = $pdo->query("SELECT * FROM usuarios WHERE tipo = 'cliente'")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    toggleUserStatus($_POST['id']);
    header("Location: gestion_usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">← Volver al Dashboard</a>
        <div>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>
</nav>    

<div class="container mt-4">
    <h2>Clientes Registrados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Estatus</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['nombre'] . ' ' . $u['apellido_paterno'] ?></td>
                <td><?= $u['correo'] ?></td>
                <td><?= $u['telefono'] ?></td>
                <td><?= ucfirst($u['estatus']) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-warning btn-sm">Cambiar Estatus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
</body>
</html>
