-- Base de datos: `fulbito_db`
CREATE DATABASE IF NOT EXISTS `fulbito_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fulbito_db`;

-- Tabla de Usuarios (Administradores)
CREATE TABLE `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `correo` VARCHAR(100) NOT NULL UNIQUE,
  `contrasena` VARCHAR(255) NOT NULL,
  `rol` VARCHAR(50) DEFAULT 'administrador'
) ENGINE=InnoDB;

-- Tabla de Equipos
CREATE TABLE `equipos` (
  `id_equipo` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_equipo` VARCHAR(100) NOT NULL,
  `logo_equipo` VARCHAR(255) NULL,
  `descripcion` TEXT NULL
) ENGINE=InnoDB;

-- Tabla de Jugadores
CREATE TABLE `jugadores` (
  `id_jugador` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_jugador` VARCHAR(100) NOT NULL,
  `posicion` VARCHAR(50) NULL,
  `foto_jugador` VARCHAR(255) NULL,
  `id_equipo` INT,
  `descripcion` TEXT NULL,
  FOREIGN KEY (`id_equipo`) REFERENCES `equipos`(`id_equipo`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabla de Campeonatos
CREATE TABLE `campeonatos` (
  `id_campeonato` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_campeonato` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NULL,
  `fecha_inicio` DATE,
  `fecha_fin` DATE
) ENGINE=InnoDB;

-- Tabla de Partidos
CREATE TABLE `partidos` (
  `id_partido` INT AUTO_INCREMENT PRIMARY KEY,
  `id_campeonato` INT,
  `id_equipo_local` INT,
  `id_equipo_visitante` INT,
  `fecha_partido` DATETIME,
  `lugar` VARCHAR(150),
  `resultado_local` INT NULL,
  `resultado_visitante` INT NULL,
  `estado` VARCHAR(50) DEFAULT 'pendiente', -- pendiente, en_juego, finalizado
  FOREIGN KEY (`id_campeonato`) REFERENCES `campeonatos`(`id_campeonato`) ON DELETE CASCADE,
  FOREIGN KEY (`id_equipo_local`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_equipo_visitante`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertar usuario administrador por defecto
-- Contraseña es "admin123" (hasheada)
INSERT INTO `usuarios` (`nombre`, `correo`, `contrasena`) VALUES
('Admin Campeonatos', 'admin@fulbito.com', '$2y$10$Eiz0h8BoM9MOhHclUIHEc.e8uFXd23deFBDsMLfJ2/lmgY5WeJ51q');
