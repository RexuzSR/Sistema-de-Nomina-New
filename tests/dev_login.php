<?php
/**
 * Helper de autenticación local exclusiva para pruebas de interfaz desde 127.0.0.1
 */
if (($_SERVER['REMOTE_ADDR'] ?? '') !== '127.0.0.1' && ($_SERVER['REMOTE_ADDR'] ?? '') !== '::1') {
    die('Acceso denegado. Exclusivo para 127.0.0.1');
}

require_once __DIR__ . '/../core/Security.php';
require_once __DIR__ . '/../core/Database.php';

Security::initSecureSession();

$db = Database::getInstance();
$stmt = $db->query("SELECT u.*, r.nombre_rol FROM tb_usuario u LEFT JOIN tb_rol r ON r.id_rol = u.id_rol WHERE u.nombre_usuario = 'samu'");
$user = $stmt->fetch();

if ($user) {
    $_SESSION['usuario'] = $user['nombre_usuario'];
    $_SESSION['user_id'] = $user['id_usuario'];
    $_SESSION['user_nombre'] = $user['nombre_usuario'];
    $_SESSION['user_rol'] = $user['nombre_rol'] ?? 'Administrador';
    $_SESSION['user_correo'] = $user['correo'] ?? '';
    $_SESSION['user_avatar'] = $user['foto_perfil'] ?? '';
    $_SESSION['user_preferencias'] = $user['preferencias'] ?? '';
    $_SESSION['last_activity'] = time();
    $_SESSION['last_regeneration'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: ../index.php?r=perfil');
    exit;
} else {
    echo "Usuario no encontrado.";
}
