<?php

// Asegurar inicio de sesión en cualquier contexto
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Database.php';

class Auth
{
    /**
     * Verifica si el usuario está autenticado
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Registrar usuario
     */
    public static function register(string $name, string $email, string $password): array
    {
        $db = Database::getInstance()->getConnection();

        // Verificar email duplicado
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El email ya está registrado.'];
        }

        // Hash contraseña
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (name, email, password) 
            VALUES (:name, :email, :password)
        ");

        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashed
        ]);

        return ['success' => true];
    }

    /**
     * Login
     */
    public static function login(string $email, string $password): array
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("SELECT id, name, email, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Credenciales incorrectas.'];
        }

        // Crear sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        return ['success' => true];
    }

    /**
     * Cerrar sesión
     */
    public static function logout(): void
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600);
    }

    /**
     * Obtener usuario como array
     */
    public static function user(): ?array
    {
        if (!self::isAuthenticated()) return null;

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }
}
