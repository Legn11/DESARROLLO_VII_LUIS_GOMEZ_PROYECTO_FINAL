<?php
/**
 * Router principal del proyecto
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------
// Iniciar sesión
// ----------------------------------------
session_start();

// ----------------------------------------
// Paths base
// ----------------------------------------
define('BASE_PATH', __DIR__ . '/');

// Configuración general (.env, DB, etc.)
require_once BASE_PATH . 'config.php';

// Librerías comunes
require_once BASE_PATH . 'src/Auth.php';
require_once BASE_PATH . 'src/Database.php';

// Controladores
require_once BASE_PATH . 'src/controllers/LoginController.php';
require_once BASE_PATH . 'src/controllers/RegisterController.php';

// (Opcional, si existe)
if (file_exists(BASE_PATH . 'src/controllers/ChatController.php')) {
    require_once BASE_PATH . 'src/controllers/ChatController.php';
}

// ----------------------------------------
// Obtener acción solicitada
// ----------------------------------------
$action = htmlspecialchars($_GET['action'] ?? 'login');

// ----------------------------------------
// Si ya está logueado, no regresar al login
// ----------------------------------------
if (Auth::isAuthenticated() && $action === 'login') {
    header("Location: ?action=chat");
    exit;
}

// ----------------------------------------
// Rutas
// ----------------------------------------
switch ($action) {

    case 'login':
        (new LoginController())->show();
        break;

    case 'login_auth':
        (new LoginController())->authenticate();
        break;

    case 'register':
        (new RegisterController())->show();
        break;

    case 'register_store':
        (new RegisterController())->store();
        break;

    case 'chat': // nueva ruta
        if (!Auth::isAuthenticated()) {
            header("Location: ?action=login");
            exit;
        }
        (new ChatController())->index();
        break;

    case 'send_message': // nueva ruta
        (new ChatController())->sendMessage();
        break;

    default:
        http_response_code(404);
        echo "<h2>404 - Página no encontrada</h2>";
        break;
}
