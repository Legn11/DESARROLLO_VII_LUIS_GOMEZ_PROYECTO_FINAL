<?php

if (!function_exists('loadEnv')) {

    function loadEnv($path)
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Archivo .env no encontrado en: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Validar formato correcto
            if (!str_contains($line, '=')) {
                trigger_error("Formato incorrecto en .env: \"$line\"", E_USER_WARNING);
                continue;
            }

            list($name, $value) = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            // Quitar comillas del valor
            $value = trim($value, "\"'");

            // No sobrescribir variables existentes
            if (getenv($name) !== false) {
                continue;
            }

            putenv("$name=$value");
            $_ENV[$name]    = $value;
            $_SERVER[$name] = $value;
        }
    }
}


loadEnv(__DIR__ . '/.env');


define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'chat.app');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('BASE_URL', 'http://localhost/PROYECTO');


