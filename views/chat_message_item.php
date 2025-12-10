<div class="msg <?php echo ($msg['user_id'] == $_SESSION['user']['id']) ? 'me' : 'other'; ?>">
    <strong><?php echo htmlspecialchars($msg['user_name']); ?>:</strong><br>
    <?php echo nl2br(htmlspecialchars($msg['content'])); ?><br>
    <small><?php echo $msg['created_at']; ?></small>
</div>
