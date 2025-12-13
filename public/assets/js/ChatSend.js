document.getElementById("sendMessageForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    const response = await fetch("/PROYECTO/index.php?action=send_message", {
        method: "POST",
        body: formData
    });

    const result = await response.json().catch(err => {
        console.error("Respuesta NO es JSON:", err);
        return null;
    });

    if (result && result.success) {
        this.reset();
    } else {
        console.error("Error al enviar mensaje");
    }
});
