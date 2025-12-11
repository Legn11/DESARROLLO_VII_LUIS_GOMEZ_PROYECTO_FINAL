<?php

require_once __DIR__ . '/../Auth.php';

class LoginController
{
    public function show()
    {
        require __DIR__ . '/../../views/Login.php';
    }

    public function authenticate()
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            die('Datos incompletos.');
        }

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $result = Auth::login($email, $password);

        if ($result['success']) {
            header("Location: index.php?action=chat");
            exit;
        }

        $error = $result['message'];
        require __DIR__ . '/../../views/Login.php';
    }
}
