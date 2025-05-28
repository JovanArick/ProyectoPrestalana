<?php
require '../includes/db.php'; // ◀️ Conexión a la BD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => htmlspecialchars($_POST['nombre']),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios 
            (nombre, correo, contrasena, tipo) 
            VALUES (?, ?, ?, 'admin')");
            
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $datos['password']
        ]);

        $_SESSION['exito'] = "Administrador creado exitosamente";
        header("Location: dashboard.php");
        exit;

    } catch(PDOException $e) {
        die("Error creando admin: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-danger">
                <h3 class="text-center">Crear Nuevo Administrador</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Correo Institucional</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required 
                            minlength="6">
                        <small class="form-text text-muted">
                            Mínimo 6 caracteres
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-danger w-100">Crear Administrador</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>