<?php

require_once __DIR__ . '/../Auth.php';

class RegisterController
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    // Mostrar formulario de registro
    public function show()
    {
        require __DIR__ . '/../../views/register.php';
    }

    // Procesar registro
     
    public function store()
    {
        if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
            die('Datos incompletos.');
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $result = $this->auth->register($name, $email, $password);

        if ($result['success']) {
            header("Location: " . BASE_URL . "/index.php?action=login");
            exit;
        }

        $error = $result['message'];
        require __DIR__ . '/../../views/register.php';
    }
}
