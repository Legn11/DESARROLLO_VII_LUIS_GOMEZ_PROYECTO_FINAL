<?php
// Datos que llegan desde el ChatController:
// $users → usuarios disponibles para iniciar chat
// $chats → chats existentes
// $messages → mensajes del chat activo
// $activeChatId → id del chat activo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>

    <style>
        body {
            font-family: Arial;
            display: flex;
            background: #f4f4f4;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            padding: 15px;
            border-right: 1px solid #ccc;
            height: 100vh;
            overflow-y: auto;
        }

        .chat-area {
            flex: 1;
            padding: 20px;
        }

        .user-item, .chat-item {
            padding: 8px;
            margin-bottom: 5px;
            background: #eee;
            border-radius: 5px;
            cursor: pointer;
        }

        .user-item:hover, .chat-item:hover {
            background: #dcdcdc;
        }

        .messages-box {
            border: 1px solid #ccc;
            height: 400px;
            padding: 10px;
            background: #fff;
            overflow-y: scroll;
            margin-bottom: 10px;
        }

        .msg {
            padding: 5px;
            margin: 8px 0;
            border-radius: 5px;
        }

        .me {
            background: #d0ebff;
            text-align: right;
        }

        .other {
            background: #ececec;
        }

        .send-box {
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
        }

        textarea {
            width: 100%;
            height: 60px;
        }

        .top-bar {
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- =====================  PANEL LATERAL  ===================== -->
<div class="sidebar">

    <h3>Usuario: <strong><?php echo $_SESSION['user_name']; ?></strong></h3>
    <a href="index.php?action=logout">Cerrar sesión</a>

    <hr>

    <h3>Usuarios disponibles</h3>
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $usr): ?>
            <div class="user-item" onclick="startChat(<?php echo $usr['id']; ?>)">
                👤 <?php echo htmlspecialchars($usr['username']); ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay otros usuarios registrados.</p>
    <?php endif; ?>

    <hr>

    <h3>Mis Chats</h3>
    <?php if (!empty($chats)): ?>
        <?php foreach ($chats as $chat): ?>
            <div class="chat-item" onclick="location.href='index.php?action=chat&chat_id=<?php echo $chat['id']; ?>'">
                💬 <?php echo htmlspecialchars($chat['other_username']); ?>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p>No tienes chats aún.</p>
    <?php endif; ?>

</div>

<!-- =====================  AREA DE CHAT  ===================== -->
<div class="chat-area">

    <div class="top-bar">
        <h2>Chat</h2>
    </div>

    <div class="messages-box">
        <?php if (!empty($messages)): ?>

            <?php foreach ($messages as $msg): ?>
                <div class="msg <?php echo ($msg['user_id'] == $_SESSION['user_id']) ? 'me' : 'other'; ?>">
                    <strong><?php echo htmlspecialchars($msg['user_name']); ?></strong><br>
                    <?php echo nl2br(htmlspecialchars($msg['content'])); ?><br>
                    <small><?php echo $msg['created_at']; ?></small>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p>No hay mensajes aún.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($activeChatId)): ?>
        <div class="send-box">
             <form id="sendMessageForm">
                <input type="hidden" name="chat_id" value="<?php echo $activeChatId; ?>">
                <textarea name="message" required></textarea>
                <button type="submit">Enviar</button>
            </form>

        </div>
    <?php else: ?>
        <p>Selecciona un chat o inicia uno nuevo.</p>
    <?php endif; ?>

</div>

<script>
    window.activeChatId = <?php echo $activeChatId ? $activeChatId : 'null'; ?>;
    window.currentUserId = <?php echo $_SESSION['user_id']; ?>;
</script>

<script src="/PROYECTO/public/assets/js/Chat.js"></script>
<script src="/PROYECTO/public/assets/js/ChatRefresh.js"></script>
<script src="/PROYECTO/public/assets/js/ChatSend.js"></script>
<script src="/PROYECTO/public/assets/js/StartChat.js"></script>



</body>
</html>
