<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Database.php';

class Auth
{
    private $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = Database::getInstance()->getConnection();

    }

    // Registrar un nuevo usuario
    public function register($name, $email, $password)
    {
        $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El email ya está registrado.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insert = "INSERT INTO users (name, email, password) 
                   VALUES (:name, :email, :password)";
        $stmt = $this->pdo->prepare($insert);
        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashed
        ]);

        return ['success' => true];
    }

    // Iniciar sesión
    public function login($email, $password)
    {
        $query = "SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Credenciales incorrectas.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Credenciales incorrectas.'];
        }

        // Crear sesión
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email']
        ];

        return ['success' => true];
    }

     // Crear sesión
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
    }

    // Verificar si hay usuario autenticado
    public function check()
    {
        return isset($_SESSION['user']);
    }

    // Obtener información del usuario autenticado
    public function user()
    {
        return $_SESSION['user'] ?? null;
    }
}
