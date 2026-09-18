<?php
/**
 * Script de mantenimiento y depuración de tablas de seguridad.
 * Ejecutable exclusivamente vía CLI o Tarea Programada de Windows (Task Scheduler).
 * 
 * Uso:
 *   php bin/cleanup_logs.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Acceso Denegado: Ejecución exclusiva vía interfaz de línea de comandos (CLI).\n");
}

require_once __DIR__ . '/../core/Database.php';

echo "[" . date('Y-m-d H:i:s') . "] Iniciando depuración de registros de seguridad...\n";

try {
    $pdo = Database::getInstance();

    // 1. Purgar intentos de login con más de 7 días de antigüedad
    $stmt1 = $pdo->prepare("DELETE FROM tb_login_intentos WHERE ultimo_intento < (NOW() - INTERVAL 7 DAY)");
    $stmt1->execute();
    $purgedAttempts = $stmt1->rowCount();
    echo " -> Registros purgados en tb_login_intentos (> 7 días): {$purgedAttempts}\n";

    // 2. Purgar registros de auditoría que excedan la política de retención (365 días)
    $stmt2 = $pdo->prepare("DELETE FROM tb_auditoria_seguridad WHERE fecha_hora < (NOW() - INTERVAL 365 DAY)");
    $stmt2->execute();
    $purgedAudit = $stmt2->rowCount();
    echo " -> Registros purgados en tb_auditoria_seguridad (> 365 días): {$purgedAudit}\n";

    echo "[" . date('Y-m-d H:i:s') . "] Depuración completada con éxito.\n";
    exit(0);
} catch (Exception $e) {
    echo "ERROR en depuración: " . $e->getMessage() . "\n";
    exit(1);
}
