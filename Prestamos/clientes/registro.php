<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => htmlspecialchars($_POST['nombre']),
        'apellido' => htmlspecialchars($_POST['apellido']),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'telefono' => preg_replace('/[^0-9]/', '', $_POST['telefono']),
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ];

    try {
        // Verificar si el correo ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$datos['email']]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "El correo ya está registrado";
            header("Location: registro.php");
            exit;
        }

        // Insertar nuevo cliente
        $stmt = $pdo->prepare("INSERT INTO usuarios 
            (nombre, apellido_paterno, correo, telefono, contrasena, tipo) 
            VALUES (?, ?, ?, ?, ?, 'cliente')");
            
        $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['telefono'],
            $datos['password']
        ]);

        $_SESSION['exito'] = "Registro exitoso. Por favor inicia sesión";
        header("Location: login.php");
        exit;

    } catch(PDOException $e) {
        $_SESSION['error'] = "Error en el registro: " . $e->getMessage();
        header("Location: registro.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registro de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">Registro de Clientes</h3>
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                    <?php unset($_SESSION['error']) ?>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required 
                                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo letras">
                        </div>
                        
                        <div class="col-md-6">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="form-control" required 
                                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo letras">
                        </div>
                        
                        <div class="col-md-6">
                            <label>Correo electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" required 
                                pattern="[0-9]{10}" title="10 dígitos">
                        </div>
                        
                        <div class="col-md-6">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required 
                                minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                            <small class="form-text text-muted">
                                Mínimo 8 caracteres, 1 mayúscula y 1 número
                            </small>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-4">Registrarse</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="login.php">¿Ya tienes cuenta? Inicia Sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>