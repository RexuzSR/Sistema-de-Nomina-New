<?php

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $DB_HOST = 'localhost';
            $DB_NAME = 'dbnomina1';
            $DB_USER = 'root';
            $DB_PASS = '';

            $configFile = __DIR__ . '/../config/db.php';
            if (file_exists($configFile)) {
                require $configFile;
                // If $pdo was created in config/db.php scope, use it
                if (isset($pdo) && $pdo instanceof PDO) {
                    self::$instance = $pdo;
                    return self::$instance;
                }
            }

            try {
                self::$instance = new PDO(
                    "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
                    $DB_USER,
                    $DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die('Error de conexion a la base de datos: ' . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }
}
