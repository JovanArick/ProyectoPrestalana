<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? AND tipo = 'cliente'");
        $stmt->execute([$correo]);
        $cliente = $stmt->fetch();

        if ($cliente && password_verify($password, $cliente['contrasena'])) {
            $_SESSION['user_id'] = $cliente['id'];
            $_SESSION['user_tipo'] = 'cliente';
            
            header("Location: dashboard.php");
            exit;
        }
        
        header("Location: login.php?error=credenciales");
    } catch(PDOException $e) {
        header("Location: login.php?error=servidor");
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-header bg-success">
                <h3 class="text-center">Acceso Clientes</h3>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= match($_GET['error']) {
                        'credenciales' => 'Credenciales incorrectas',
                        'servidor' => 'Error del servidor',
                        default => 'Error desconocido'
                    } ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Acceder</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="registro.php">¿No tienes cuenta? Regístrate</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>