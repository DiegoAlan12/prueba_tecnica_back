-- 1) Crear la BD: Expediente_de_Datos (usar utf8mb4).
CREATE DATABASE IF NOT EXISTS Expediente_de_Datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Expediente_de_Datos;

-- 2) Crear tabla actividad
CREATE TABLE IF NOT EXISTS actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion LONGTEXT NOT NULL,
    estatus ENUM('ACTIVO', 'DESACTIVADO') NOT NULL DEFAULT 'ACTIVO',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estatus (estatus),
    FULLTEXT ft_titulo_descripcion (titulo, descripcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
