-- -------------------------------------------------------------
-- MIGRACIÓN: Creación de tabla "users"
-- 
-- Propósito:
--  - Registrar a los usuarios de la aplicación.
--  - Almacenar credenciales y estado.
--
-- Campos clave:
--  - id: Identificador único.
--  - username / email: Datos públicos del usuario.
--  - password_hash: Contraseña en formato encriptado.
--  - status: Control del usuario (activo/bloqueado).
--  - last_seen: Última conexión para estados online/offline.
-- -------------------------------------------------------------

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active','blocked') NOT NULL DEFAULT 'active',
    last_seen DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
