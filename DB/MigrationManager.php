<?php

class MigrationManager
{
    private PDO $pdo;
    private string $migrationsPath;

    /**
     * @param PDO $pdo
     * @param string $migrationsPath
     */
    public function __construct(PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = rtrim($migrationsPath, '/');
    }

    /**
     * Ejecuta todas las migraciones pendientes
     */
    public function migrate(): void
    {
        $this->createMigrationsTableIfNotExists();

        $applied  = $this->getAppliedMigrations();
        $files    = $this->getMigrationFiles();
        $pending  = array_diff($files, $applied);

        if (empty($pending)) {
            echo "No new migrations to apply.\n";
            return;
        }

        foreach ($pending as $migration) {
            $this->applyMigration($migration);
        }
    }

    /**
     * Crea la tabla migrations si no existe
     */
    private function createMigrationsTableIfNotExists(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Obtiene migraciones ya aplicadas
     */
    private function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Retorna lista de archivos .sql ordenados
     */
    private function getMigrationFiles(): array
    {
        $files = scandir($this->migrationsPath);

        $migrations = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
        });

        sort($migrations);
        return array_values($migrations);
    }

    /**
     * Aplica una migración individual
     */
    private function applyMigration(string $migration): void
{
    $path = $this->migrationsPath . '/' . $migration;

    if (!file_exists($path)) {
        throw new RuntimeException("Migration file not found: $path");
    }

    $sql = trim(file_get_contents($path));

    // Evitar ejecutar migraciones vacías
    if ($sql === '') {
        echo "⚠ Migración vacía o sin contenido: $migration<br>";
        return;
    }

    try {
        $this->pdo->beginTransaction();

        // Ejecutar múltiples sentencias separadas por punto y coma
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if ($statement !== '') {
                $this->pdo->exec($statement);
            }
        }

        // Registrar migración como aplicada
        $stmt = $this->pdo->prepare("
            INSERT INTO migrations (migration) VALUES (:migration)
        ");
        $stmt->execute(['migration' => $migration]);

        $this->pdo->commit();

        echo "✔ Migración aplicada: $migration<br>";

    } catch (Exception $e) {

        // Solo hacer rollback si existe una transacción activa
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        echo "❌ Error applying migration $migration: " . $e->getMessage() . "<br>";
    }
}
}