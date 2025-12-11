<?php
$user = Auth::user();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <style>
        body { font-family: Arial; }
        .chat-box {
            width: 60%;
            margin: auto;
            border: 1px solid #ccc;
            padding: 15px;
            height: 400px;
            overflow-y: scroll;
        }
        .msg {
            padding: 6px 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .me {
            background: #d0ebff;
            text-align: right;
        }
        .other {
            background: #f1f1f1;
        }
        .form-box {
            width: 60%;
            margin: auto;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <h2>Chat</h2>

    <p>Usuario: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
    <p><a href="index.php?action=logout">Cerrar sesión</a></p>

    <div class="chat-box">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="msg <?= ($msg['user_id'] == $user['id']) ? 'me' : 'other'; ?>">
                    <strong><?= htmlspecialchars($msg['user_name']) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($msg['content'])) ?><br>
                    <small><?= $msg['created_at'] ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay mensajes aún.</p>
        <?php endif; ?>
    </div>

    <div class="form-box">
        <form action="index.php?action=send_message" method="POST">
            <input type="hidden" name="chat_id" value="<?= htmlspecialchars($_GET['chat_id'] ?? '') ?>">
            <textarea name="message" rows="3" style="width:100%;" required></textarea><br><br>
            <button type="submit">Enviar</button>
        </form>
    </div>

</body>
</html>
