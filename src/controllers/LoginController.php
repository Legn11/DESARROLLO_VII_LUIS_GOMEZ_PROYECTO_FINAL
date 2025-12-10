<?php
/**
 * LoginController
 *
 * Propósito:
 * Administrar el flujo de autenticación mediante formulario.
 *
 * Funciones:
 *  - show(): mostrar el formulario de login.
 *  - authenticate(): procesar los datos enviados por el formulario.
 *
 * Dependencias:
 *  - Auth.php (gestión de sesiones).
 */

require_once __DIR__ . '/../Auth.php';

class LoginController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    /**
     * Mostrar formulario de login
     */
    public function show()
    {
        require __DIR__ . '/../views/login.php';
    }

    /**
     * Ejecutar login con datos POST
     */
    public function authenticate()
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            die('Datos incompletos.');
        }

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $result = $this->auth->login($email, $password);

        if ($result['success']) {
            header("Location: " . BASE_URL . "/chat");
            exit;
        }

        $error = $result['message'];
        require __DIR__ . '/../../views/login.php';
    }
}
