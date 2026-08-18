<?php
// Configuracion de conexion a la base de datos (XAMPP por defecto)
$DB_HOST = 'localhost';
$DB_NAME = 'dbnomina1';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP por defecto no tiene password para root

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Error de conexion a la base de datos: ' . htmlspecialchars($e->getMessage()) .
        '<br>Verifica que Apache y MySQL esten iniciados en XAMPP y que importaste database/dbnomina1.sql');
}
