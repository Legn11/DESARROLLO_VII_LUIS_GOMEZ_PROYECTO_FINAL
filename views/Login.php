<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>

    <!-- Estilos del Login -->
    <link rel="stylesheet" href="/PROYECTO/public/assets/css/Auth.css">
</head>
<body>

<div class="login-container">

    <h2>Bienvenido</h2>
    <p class="subtitle">Ingresa tus credenciales para continuar</p>

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=login_auth" method="POST">

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="input-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="********" required>
        </div>

        <button class="btn" type="submit">Iniciar Sesión</button>

        <p class="link">
            ¿No tienes cuenta? <a href="index.php?action=register">Regístrate aquí</a>
        </p>
    </form>

</div>

</body>
</html>


