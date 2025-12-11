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

    public function index()
    {
        if (!Auth::isAuthenticated()) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = Auth::user()['id'];

        // Lista de chats del usuario
        $chats = $this->getUserChats($userId);

        // Lista de usuarios disponibles para iniciar un chat
        $users = $this->getAllUsersExceptMe($userId);

        $activeChatId = $_GET['chat_id'] ?? null;
        $messages = [];

        if ($activeChatId && $this->userBelongsToChat($userId, $activeChatId)) {
            $messages = $this->getMessages($activeChatId);
        }

        include __DIR__ . '/../../views/Chat.php';
    }

    private function userBelongsToChat($userId, $chatId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM chats
            WHERE id = :chat
            AND (user1_id = :uid OR user2_id = :uid)
            LIMIT 1
        ");

        $stmt->execute(['chat' => $chatId, 'uid' => $userId]);
        return (bool)$stmt->fetch();
    }

    private function getUserChats($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM chats
            WHERE user1_id = :id OR user2_id = :id
            ORDER BY updated_at DESC
        ");

        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    private function getMessages($chatId)
    {
        $stmt = $this->db->prepare("
            SELECT m.*, u.username AS user_name
            FROM messages m
            JOIN users u ON u.id = m.user_id
            WHERE m.chat_id = :chat
            ORDER BY m.created_at ASC
        ");

        $stmt->execute(['chat' => $chatId]);
        return $stmt->fetchAll();
    }

    public function sendMessage()
    {
        if (!Auth::isAuthenticated()) {
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = Auth::user()['id'];
        $chatId = $_POST['chat_id'] ?? null;
        $content = trim($_POST['message'] ?? '');

        if (!$chatId || !$this->userBelongsToChat($userId, $chatId)) {
            echo json_encode(['success' => false]);
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO messages (chat_id, user_id, content)
            VALUES (:chat, :user, :content)
        ");

        $stmt->execute([
            'chat'   => $chatId,
            'user'   => $userId,
            'content'=> $content
        ]);

        $this->db->prepare("
            UPDATE chats SET updated_at = NOW() WHERE id = :id
        ")->execute(['id' => $chatId]);

        echo json_encode(['success' => true]);
        exit;
    }

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
            die("No puedes iniciar un chat contigo mismo.");
        }

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

        $stmt = $this->db->prepare("
            INSERT INTO chats (user1_id, user2_id)
            VALUES (:u1, :u2)
        ");

        $stmt->execute(['u1' => $userId, 'u2' => $otherId]);

        $chatId = $this->db->lastInsertId();

        header("Location: index.php?action=chat&chat_id=" . $chatId);
        exit;
    }

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

    private function getAllUsersExceptMe($userId)
    {
        $stmt = $this->db->prepare("
            SELECT id, username
            FROM users
            WHERE id != :id
        ");

        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }
}
