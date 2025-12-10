<?php
/**
 * Vista: Login
 *
 * Propósito:
 * Mostrar el formulario de autenticación del usuario.
 *
 * Controlador asociado:
 * LoginController::show()
 * LoginController::authenticate()
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>

    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- FORMULARIO MODIFICADO PARA USAR router.php -->
    <form action="index.php?action=login_auth" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Ingresar</button>
    </form>

    <p>
        ¿No tienes cuenta?
        <a href="index.php?action=register">Regístrate aquí</a>
    </p>

</body>
</html>

