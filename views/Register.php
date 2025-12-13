<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta</title>

    <link rel="stylesheet" href="/PROYECTO/public/assets/css/Auth.css">
</head>
<body>

<div class="login-container">

    <h2>Crea tu Cuenta</h2>
    <p class="subtitle">Regístrate para comenzar a chatear</p>

    <?php if (!empty($error)) : ?>
        <div class="error-box">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=register_store" method="POST">

        <div class="input-group">
            <label>Nombre de usuario</label>
            <input type="text" name="name" placeholder="Tu nombre" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="input-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="********" required>
        </div>

        <button class="btn" type="submit">Registrarme</button>

        <p class="link">
            ¿Ya tienes cuenta?  
            <a href="index.php?action=login">Inicia sesión aquí</a>
        </p>

    </form>

</div>

</body>
</html>
