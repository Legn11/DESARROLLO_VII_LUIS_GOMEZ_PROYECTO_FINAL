function startChat(userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?action=new_chat';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'other_id';
    input.value = userId;

    form.appendChild(input);
    document.body.appendChild(form);

    form.submit();
}
