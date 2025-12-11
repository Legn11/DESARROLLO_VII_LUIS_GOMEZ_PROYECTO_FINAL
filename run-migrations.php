<?php

require_once __DIR__ . '/config.php';

// Cargar la clase MigrationManager
require_once __DIR__ . '/DB/MigrationManager.php';

try {
    // Conexión PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Ruta correcta a la carpeta de migraciones
    $migrationsPath = __DIR__ . '/DB/migrations';

    if (!is_dir($migrationsPath)) {
        throw new RuntimeException("La carpeta de migraciones no existe: $migrationsPath");
    }

    // Ejecutar migraciones
    $migrationManager = new MigrationManager($pdo, $migrationsPath);
    $migrationManager->migrate();

    echo "✔ Migraciones ejecutadas correctamente.\n";

} catch (PDOException $e) {
    die("❌ ERROR DE BASE DE DATOS: " . $e->getMessage());
} catch (Exception $e) {
    die("❌ ERROR EN MIGRACIÓN: " . $e->getMessage());
}
