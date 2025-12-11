<?php

require_once __DIR__ . '/../Auth.php';
require_once __DIR__ . '/../Database.php';

class ChatController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Página principal del chat
     */
    public function index()
    {
        if (!Auth::isAuthenticated()) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = Auth::user()['id'];

        // Obtener todos los chats del usuario
        $chats = $this->getUserChats($userId);

        // Obtener ID de chat activo si existe
        $activeChatId = $_GET['chat_id'] ?? null;
        $messages = [];

        if ($activeChatId) {

            // Validar acceso al chat
            if (!$this->userBelongsToChat($userId, $activeChatId)) {
                die("Acceso denegado.");
            }

            $messages = $this->getMessages($activeChatId);
        }

        // Mostrar vista
        include __DIR__ . '/../views/Chat.php';
    }

    /**
     * Verifica si el usuario pertenece al chat
     */
    private function userBelongsToChat($userId, $chatId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM chats
            WHERE id = :chat
            AND (user1_id = :uid OR user2_id = :uid)
            LIMIT 1
        ");

        $stmt->execute([
            'chat' => $chatId,
            'uid'  => $userId
        ]);

        return (bool) $stmt->fetch();
    }

    /**
     * Obtiene los chats donde el usuario participa
     */
    private function getUserChats($userId)
    {
        $query = "
            SELECT * FROM chats
            WHERE user1_id = :id OR user2_id = :id
            ORDER BY updated_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Cargar mensajes de un chat específico
     */
    private function getMessages($chatId)
    {
        $query = "
            SELECT m.*, u.name AS user_name
            FROM messages m
            JOIN users u ON u.id = m.user_id
            WHERE m.chat_id = :chat
            ORDER BY m.created_at ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['chat' => $chatId]);

        return $stmt->fetchAll();
    }

    /**
     * Enviar un mensaje
     */
    public function sendMessage()
    {
        if (!Auth::isAuthenticated()) {
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }

        $userId = Auth::user()['id'];
        $chatId = $_POST['chat_id'] ?? null;
        $content = trim($_POST['message'] ?? '');

        if (!$chatId || $content === '') {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        // Validar acceso al chat
        if (!$this->userBelongsToChat($userId, $chatId)) {
            echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO messages (chat_id, user_id, content)
            VALUES (:chat, :user, :content)
        ");

        $stmt->execute([
            'chat' => $chatId,
            'user' => $userId,
            'content' => $content
        ]);

        // Actualizar chat
        $this->db->prepare("
            UPDATE chats SET updated_at = NOW() WHERE id = :id
        ")->execute(['id' => $chatId]);

        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Crear chat entre dos usuarios
     */
    public function newChat()
    {
        if (!Auth::isAuthenticated()) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = Auth::user()['id'];
        $otherId = $_POST['other_id'] ?? null;

        if (!$otherId || !is_numeric($otherId)) {
            die("Usuario no válido.");
        }

        if ($userId == $otherId) {
            die("No puedes abrir un chat contigo mismo.");
        }

        // Verificar si ya existe
        $stmt = $this->db->prepare("
            SELECT id FROM chats
            WHERE (user1_id = :u1 AND user2_id = :u2)
               OR (user1_id = :u2 AND user2_id = :u1)
            LIMIT 1
        ");

        $stmt->execute(['u1' => $userId, 'u2' => $otherId]);

        $chat = $stmt->fetch();

        if ($chat) {
            header("Location: index.php?action=chat&chat_id=" . $chat['id']);
            exit;
        }

        // Crear chat nuevo
        $stmt = $this->db->prepare("
            INSERT INTO chats (user1_id, user2_id)
            VALUES (:u1, :u2)
        ");

        $stmt->execute(['u1' => $userId, 'u2' => $otherId]);
        $chatId = $this->db->lastInsertId();

        header("Location: index.php?action=chat&chat_id=" . $chatId);
        exit;
    }

    /**
     * API para obtener mensajes (AJAX)
     */
    public function getMessagesApi()
    {
        $chatId = $_GET['chat_id'] ?? null;

        if (!$chatId) {
            echo json_encode([]);
            exit;
        }

        echo json_encode($this->getMessages($chatId));
        exit;
    }
}

