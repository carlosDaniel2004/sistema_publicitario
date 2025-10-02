-- Base de datos: `sistema_publicitario_db`
CREATE DATABASE IF NOT EXISTS `sistema_publicitario_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_publicitario_db`;

-- Tabla de Usuarios (Administradores)
CREATE TABLE `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `correo` VARCHAR(100) NOT NULL UNIQUE,
  `contrasena` VARCHAR(255) NOT NULL,
  `rol` VARCHAR(50) DEFAULT 'administrador'
) ENGINE=InnoDB;

-- Tabla de Banners
-- sql/schema.sql (versión actualizada para la tabla banners)
CREATE TABLE `banners` (
  `id_banner` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(150) NOT NULL,
  `imagen` VARCHAR(255) NOT NULL,
  `enlace` VARCHAR(255) NULL, -- <-- CAMBIO AQUÍ: Campo para el enlace
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Anuncios
CREATE TABLE `anuncios` (
  `id_anuncio` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `enlace` VARCHAR(255) DEFAULT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Material Publicitario
CREATE TABLE `materiales` (
  `id_material` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(150) NOT NULL,
  `archivo` VARCHAR(255) NOT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Convocatorias de Trabajo
CREATE TABLE `convocatorias` (
  `id_convocatoria` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `enlace_registro` VARCHAR(255) NOT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Estudiantes (para envío de correos)
CREATE TABLE `estudiantes` (
  `id_estudiante` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `correo_institucional` VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Insertar usuario administrador por defecto
-- Contraseña es "admin123" (hasheada)
INSERT INTO `usuarios` (`nombre`, `correo`, `contrasena`) VALUES
('Admin Escuela', 'admin@escuela.com', '$2y$10$Eiz0h8BoM9MOhHclUIHEc.e8uFXd23deFBDsMLfJ2/lmgY5WeJ51q');

-- Insertar estudiantes de ejemplo
INSERT INTO `estudiantes` (`nombre`, `correo_institucional`) VALUES
('Juan Perez', 'juan.perez@email.com'),
('Maria Garcia', 'maria.garcia@email.com'),
('Carlos Rodriguez', 'carlos.rodriguez@email.com');