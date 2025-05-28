<?php
function verificarSesionAdmin() {
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
        header("Location: ../admin/login.php");
        exit;
    }
    
    // Verificación extra para admins
    if ($_SESSION['login_ip'] !== $_SERVER['REMOTE_ADDR'] || 
        $_SESSION['login_ua'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        header("Location: ../admin/login.php?error=sesion");
        exit;
    }
}

function verificarSesionCliente() {
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'cliente') {
        header("Location: ../clientes/login.php");
        exit;
    }
}
?>