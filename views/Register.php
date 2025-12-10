<?php
/**
 * Vista: Registro de Usuario
 *
 * Propósito:
 * Mostrar el formulario para crear nuevos usuarios.
 *
 * Controlador asociado:
 * RegisterController::show()
 * RegisterController::store()
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>

    <h2>Crear Cuenta</h2>

    <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- FORMULARIO ADAPTADO AL ROUTER -->
    <form action="index.php?action=register_store" method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrar</button>
    </form>

    <p>
        ¿Ya tienes cuenta?
        <a href="index.php?action=login">Inicia sesión aquí</a>
    </p>

</body>
</html>
