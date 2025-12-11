<?php

require_once __DIR__ . '/../Auth.php';

class LoginController
{
    // Mostrar formulario de login
    public function show()
    {
        require __DIR__ . '/../views/Login.php';
    }

    // Ejecutar login con datos POST
    public function authenticate()
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            die('Datos incompletos.');
        }

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Llamar método estático correctamente
        $result = Auth::login($email, $password);

        if ($result['success']) {
            header("Location: index.php?action=chat");
            exit;
        }

        // Mostrar error en la vista
        $error = $result['message'];
        require __DIR__ . '/../views/Login.php';
    }
}

