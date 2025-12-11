document.addEventListener("DOMContentLoaded", () => {

    let activeChat = window.activeChatId ?? null;
    let currentUserId = window.currentUserId ?? null;

    function loadMessages() {
        if (!activeChat) return;

        fetch(`index.php?action=get_messages_api&chat_id=${activeChat}`)
            .then(response => response.json())
            .then(data => {
                const box = document.querySelector(".messages-box");
                if (!box) return;

                box.innerHTML = "";

                if (data.length === 0) {
                    box.innerHTML = "<p>No hay mensajes aún.</p>";
                    return;
                }

                data.forEach(msg => {
                    let div = document.createElement("div");
                    div.className = "msg " + (msg.user_id == currentUserId ? "me" : "other");

                    div.innerHTML = `
                        <strong>${msg.user_name}</strong><br>
                        ${msg.content.replace(/\n/g, "<br>")}<br>
                        <small>${msg.created_at}</small>
                    `;

                    box.appendChild(div);
                });

                box.scrollTop = box.scrollHeight;
            })
            .catch(err => console.error("Error cargando mensajes:", err));
    }

    // Recargar mensajes cada 2 segundos
    setInterval(loadMessages, 2000);

    // Cargar al entrar
    loadMessages();
});
