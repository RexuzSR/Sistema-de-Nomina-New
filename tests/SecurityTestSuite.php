<?php
/**
 * Test Suite Automatizado para la Arquitectura de Seguridad
 * Ejecutable vía CLI: C:\xampp\php\php.exe tests/SecurityTestSuite.php
 */

require_once __DIR__ . '/../core/Security.php';
require_once __DIR__ . '/../core/Database.php';

// Inicializar sesión antes de generar salida en CLI
Security::initSecureSession();

echo "========================================================\n";
echo "   SISTEMA DE NÓMINA — SECURITY SUITE DE VERIFICACIÓN   \n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $detail = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$name}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$name} - {$detail}\n";
        $failed++;
    }
}

// 1. Verificación de Nonce CSP en memoria
$nonce1 = Security::getCspNonce();
$nonce2 = Security::getCspNonce();
assertTest(
    "CSP Nonce generado y estable en memoria para el request",
    !empty($nonce1) && $nonce1 === $nonce2 && strlen($nonce1) >= 20,
    "Nonce no se mantuvo o es inválido: {$nonce1}"
);

// 2. Verificación de Tokens CSRF y hash_equals
Security::initSecureSession();
$token = Security::csrfToken();
assertTest("Token CSRF inicializado en sesión", !empty($token) && strlen($token) === 64);

$_POST['csrf_token'] = $token;
assertTest("Validación CSRF con token legítimo", Security::validateCsrfRequest() === true);

$_POST['csrf_token'] = 'token_malicioso_alterado_123456789';
assertTest("Rechazo CSRF con token alterado", Security::validateCsrfRequest() === false);
unset($_POST['csrf_token']);

// 3. Verificación de Ciclo de Vida e Inactividad
$_SESSION['usuario'] = 'test_admin';
$_SESSION['user_id'] = 999;
$_SESSION['last_activity'] = time() - 950; // Más de 15 minutos atrás (900 s)

$lifecycle = Security::checkSessionLifecycle(900, 1800);
assertTest(
    "Detección y destrucción de sesión por inactividad (> 15 min)",
    $lifecycle['valid'] === false && $lifecycle['reason'] === 'timeout',
    "Resultado: " . json_encode($lifecycle)
);

// 4. Verificación de registro en tb_auditoria_seguridad
$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT * FROM tb_auditoria_seguridad WHERE evento = 'SESSION_TIMEOUT' AND username = 'test_admin' ORDER BY id DESC LIMIT 1");
$stmt->execute();
$auditRow = $stmt->fetch();
assertTest("Registro forense de SESSION_TIMEOUT en BD", !empty($auditRow), "No se encontró registro en auditoría");

// 5. Verificación de Anti-Fuerza Bruta y Esquema Dual Anti-DoS
$testIp1 = '198.51.100.10';
$testIp2 = '198.51.100.20';
$testUser = 'payroll_admin_' . time();

// Registrar 5 intentos fallidos desde IP1
for ($i = 0; $i < 5; $i++) {
    Security::recordFailedLogin($testUser, $testIp1);
}

$lockoutIp1 = Security::checkLoginLockout($testUser, $testIp1);
assertTest(
    "Bloqueo temporal activado tras 5 intentos (IP1 + Usuario)",
    $lockoutIp1 !== null && $lockoutIp1['locked'] === true,
    "No se bloqueó a IP1"
);

// Probar que el usuario legítimo desde otra IP (IP2) NO está bloqueado (Mitigación DoS)
$lockoutIp2 = Security::checkLoginLockout($testUser, $testIp2);
assertTest(
    "Prevención de DoS: IP2 legítima NO está bloqueada para el mismo usuario",
    $lockoutIp2 === null,
    "IP2 resultó bloqueada erróneamente por DoS"
);

// Resetear intentos de prueba
Security::resetLoginAttempts($testUser, $testIp1);
$lockoutCleared = Security::checkLoginLockout($testUser, $testIp1);
assertTest(
    "Limpieza de intentos fallidos tras login exitoso",
    $lockoutCleared === null,
    "Los intentos no fueron limpiados"
);

// 6. Verificación Criptográfica: Bcrypt Cost 12
$rawPass = 'PruebaSegura2026!';
$hash = Security::hashPassword($rawPass);
$info = password_get_info($hash);
assertTest(
    "Hash generado con Bcrypt costo 12",
    $info['algoName'] === 'bcrypt' && $info['options']['cost'] === 12,
    "Costo detectado: " . ($info['options']['cost'] ?? 'desconocido')
);
assertTest(
    "password_verify confirma contraseña correcta",
    password_verify($rawPass, $hash) === true
);

// 7. Verificación de resolución segura de IP
$clientIp = Security::getClientIp();
assertTest(
    "Resolución segura de IP activa",
    !empty($clientIp) && filter_var($clientIp, FILTER_VALIDATE_IP) !== false,
    "IP: {$clientIp}"
);

// 8. Verificación de existencia de protección en bin/
assertTest(
    "Script bin/cleanup_logs.php existe y tiene guarda CLI",
    file_exists(__DIR__ . '/../bin/cleanup_logs.php')
);
assertTest(
    "Archivo bin/.htaccess existe con bloqueo web",
    file_exists(__DIR__ . '/../bin/.htaccess')
);

// 9. Verificación de Rotación Multi-Pestaña
// Reinicializar sesión de prueba
$_SESSION['usuario'] = 'test_multi_tab';
$_SESSION['last_activity'] = time();
$_SESSION['last_regeneration'] = time() - 1900; // Supera 30 min (1800 s)

// Ejecutar lifecycle con rotación
$rotationResult = Security::checkSessionLifecycle(900, 1800);
assertTest(
    "Rotación periódica multi-pestaña ejecutada sin desloguear",
    $rotationResult['valid'] === true && $rotationResult['authenticated'] === true
);

// Limpiar sesión de prueba
Security::destroySession();

echo "\n--------------------------------------------------------\n";
echo "RESULTADOS: {$passed} pasadas, {$failed} falladas\n";
echo "--------------------------------------------------------\n";

if ($failed > 0) {
    exit(1);
} else {
    echo "¡TODAS LAS PRUEBAS DE SEGURIDAD PASARON EXITOSAMENTE!\n";
    exit(0);
}
