<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', __DIR__ . '/');

// Configuración general
require_once BASE_PATH . 'config.php';

// Base de datos (ruta correcta)
require_once BASE_PATH . 'src/Database.php';

// Auth (usa Database, por eso debe venir después)
require_once BASE_PATH . 'src/Auth.php';

// Controladores
require_once BASE_PATH . 'src/controllers/LoginController.php';
require_once BASE_PATH . 'src/controllers/RegisterController.php';

if (file_exists(BASE_PATH . 'src/controllers/ChatController.php')) {
    require_once BASE_PATH . 'src/controllers/ChatController.php';
}

$action = htmlspecialchars($_GET['action'] ?? 'login');

// Redirección si ya está logueado
if (Auth::isAuthenticated() && $action === 'login') {
    header("Location: ?action=chat");
    exit;
}

// Rutas
switch ($action) {

    case 'login':
        (new LoginController())->show();
        break;

    case 'login_auth':
        (new LoginController())->authenticate();
        break;

    case 'logout':
        Auth::logout();
        header("Location: ?action=login");
        exit;


    case 'register':
        (new RegisterController())->show();
        break;

    case 'register_store':
        (new RegisterController())->store();
        break;

    case 'chat':
        if (!Auth::isAuthenticated()) {
            header("Location: ?action=login");
            exit;
        }
        (new ChatController())->index();
        break;
    
    case 'send_message':
        (new ChatController())->sendMessage();
        exit;

    case 'get_messages_api':
        (new ChatController())->getMessagesApi();
        break;

    case 'new_chat':
        (new ChatController())->newChat();
        break;

    default:
        http_response_code(404);
        echo "<h2>404 - Página no encontrada</h2>";
        break;
}

