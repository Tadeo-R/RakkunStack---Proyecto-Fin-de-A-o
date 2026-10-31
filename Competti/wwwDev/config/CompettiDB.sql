-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 31-10-2026 a las 03:00:11
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `CompettiDB`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AUDITORIA`
--

CREATE TABLE `AUDITORIA` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `moduloAfectado` varchar(100) NOT NULL,
  `fechaHora` datetime NOT NULL DEFAULT current_timestamp(),
  `ipOrigen` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `DEPORTE`
--

CREATE TABLE `DEPORTE` (
  `id_deporte` int(11) NOT NULL,
  `nombreDeporte` varchar(100) NOT NULL,
  `organismo` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `DEPORTE`
--

INSERT INTO `DEPORTE` (`id_deporte`, `nombreDeporte`, `organismo`) VALUES
(1, 'Minecraft', NULL),
(2, 'Cartas Pokemon', NULL),
(3, 'Wesa', NULL),
(4, 'Free Fire', NULL),
(5, 'Truco', NULL),
(6, 'Ajedrez', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ENFRENTAMIENTO`
--

CREATE TABLE `ENFRENTAMIENTO` (
  `id_enfrentamiento` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `id_equipo_local` int(11) NOT NULL,
  `id_equipo_visitante` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  `cancha` varchar(100) DEFAULT NULL,
  `estado` varchar(30) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `EQUIPO`
--

CREATE TABLE `EQUIPO` (
  `id_equipo` int(11) NOT NULL,
  `id_organizador_creador` int(11) NOT NULL,
  `nombreEquipo` varchar(100) NOT NULL,
  `fechaFundacion` date DEFAULT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `EQUIPO`
--

INSERT INTO `EQUIPO` (`id_equipo`, `id_organizador_creador`, `nombreEquipo`, `fechaFundacion`, `estado`) VALUES
(17, 1, 'EQ1', NULL, 'activo'),
(18, 1, 'EQ2', NULL, 'activo'),
(20, 2, 'smp2', NULL, 'activo'),
(21, 2, 'smp3', NULL, 'activo'),
(22, 2, 'smp1', NULL, 'activo'),
(23, 2, 'smp4', NULL, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INSCRIBEN`
--

CREATE TABLE `INSCRIBEN` (
  `id_inscripcion` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `fechaInscripcion` date NOT NULL,
  `estadoInscripcion` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INSCRIBEN`
--

INSERT INTO `INSCRIBEN` (`id_inscripcion`, `id_equipo`, `id_torneo`, `fechaInscripcion`, `estadoInscripcion`) VALUES
(22, 17, 11, '2026-10-28', 'aprobado'),
(23, 18, 11, '2026-10-28', 'aprobado'),
(24, 22, 12, '2026-10-28', 'aprobado'),
(25, 23, 12, '2026-10-28', 'aprobado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INTERVIENE`
--

CREATE TABLE `INTERVIENE` (
  `id_intervencion` int(11) NOT NULL,
  `id_administrador_usuario` int(11) NOT NULL,
  `id_usuario_afectado` int(11) NOT NULL,
  `tipoAccion` varchar(100) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fechaHora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ORGANIZADOR`
--

CREATE TABLE `ORGANIZADOR` (
  `id_organizador` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `contacto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ORGANIZADOR`
--

INSERT INTO `ORGANIZADOR` (`id_organizador`, `id_usuario`, `telefono`, `contacto`) VALUES
(1, 3, NULL, NULL),
(2, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PARTICIPANTE`
--

CREATE TABLE `PARTICIPANTE` (
  `id_participante` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `PARTICIPANTE`
--

INSERT INTO `PARTICIPANTE` (`id_participante`, `id_usuario`) VALUES
(3, 3),
(1, 4),
(2, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PERTENECE`
--

CREATE TABLE `PERTENECE` (
  `id_participante` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `esCapitan` tinyint(1) NOT NULL DEFAULT 0,
  `fechaEgreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `PERTENECE`
--

INSERT INTO `PERTENECE` (`id_participante`, `id_equipo`, `fechaIngreso`, `esCapitan`, `fechaEgreso`) VALUES
(2, 18, '2026-10-28', 1, NULL),
(2, 22, '2026-10-28', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `RECUPERACION_PASSWORDS`
--

CREATE TABLE `RECUPERACION_PASSWORDS` (
  `email` varchar(150) NOT NULL,
  `token` char(64) NOT NULL,
  `expira` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `RESULTADO`
--

CREATE TABLE `RESULTADO` (
  `id_enfrentamiento` int(11) NOT NULL,
  `puntosLocal` int(11) NOT NULL,
  `puntosVisitante` int(11) NOT NULL,
  `id_equipo_ganador` int(11) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ROL`
--

CREATE TABLE `ROL` (
  `id_rol` int(11) NOT NULL,
  `nombreRol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ROL`
--

INSERT INTO `ROL` (`id_rol`, `nombreRol`, `descripcion`) VALUES
(1, 'Participante', 'Usuario registrado en la plataforma');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TABLA_POSICIONES`
--

CREATE TABLE `TABLA_POSICIONES` (
  `id_tabla` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `partidosJugados` int(11) NOT NULL DEFAULT 0,
  `partidosGanados` int(11) NOT NULL DEFAULT 0,
  `partidosEmpatados` int(11) NOT NULL DEFAULT 0,
  `partidosPerdidos` int(11) NOT NULL DEFAULT 0,
  `puntos` int(11) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TORNEO`
--

CREATE TABLE `TORNEO` (
  `id_torneo` int(11) NOT NULL,
  `id_organizador` int(11) NOT NULL,
  `id_deporte` int(11) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `juego` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `formato` varchar(50) NOT NULL,
  `pts_victoria` int(11) DEFAULT 3,
  `pts_empate` int(11) DEFAULT 1,
  `pts_derrota` int(11) DEFAULT 0,
  `tercer_puesto` tinyint(4) DEFAULT 0,
  `nombreTorneo` varchar(150) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `cupos` int(11) DEFAULT NULL,
  `fechaInicio` date NOT NULL,
  `streaming_url` varchar(255) DEFAULT NULL,
  `premio1` varchar(100) DEFAULT NULL,
  `premio2` varchar(100) DEFAULT NULL,
  `estado` varchar(20) NOT NULL,
  `max_integrantes_equipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `TORNEO`
--

INSERT INTO `TORNEO` (`id_torneo`, `id_organizador`, `id_deporte`, `categoria`, `juego`, `descripcion`, `formato`, `pts_victoria`, `pts_empate`, `pts_derrota`, `tercer_puesto`, `nombreTorneo`, `modalidad`, `cupos`, `fechaInicio`, `streaming_url`, `premio1`, `premio2`, `estado`, `max_integrantes_equipo`) VALUES
(11, 1, 6, 'mentales', 'Ajedrez', 'wasa', 'suizo', 3, 1, 0, 1, 'Torneo Prueba', 'equipos', 2, '2026-10-30', NULL, NULL, NULL, 'inscripciones', NULL),
(12, 2, 1, 'mentales', 'Minecraft', 'aaa', 'liga', 3, 1, 0, 1, 'Prueba2', 'equipos', 4, '2026-10-29', NULL, NULL, NULL, 'inscripciones', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIO`
--

CREATE TABLE `USUARIO` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL,
  `bio` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `USUARIO`
--

INSERT INTO `USUARIO` (`id_usuario`, `id_rol`, `nombre`, `ubicacion`, `email`, `contrasena`, `avatar`, `fechaRegistro`, `estado`, `bio`) VALUES
(3, 1, 'BenZyK_', 'La Teja, Uruguay', 'benjamingabrielzyk@gmail.com', '$2y$10$M3H8l1nBw963qZABpBT5d.v6K1bpIGWdT6lYl5QQcmLPC54S2Exbm', '/uploads/avatar_3_1793095148.jpg', '2026-10-26 18:56:48', 'activo', 'weeeesa'),
(4, 1, 'Luciernaguittaaa', 'Con mi esposo', 'lucianaaberasteguy008@gmail.com', '$2y$10$3cq6kSi2J7SPxqCvcexh8ua/PT3xM4Qk/6lNuWIDqHwEFyU1UOa/m', NULL, '2026-10-28 11:36:20', 'activo', 'De mi hombre 👻'),
(5, 1, 'Benjamin Coppetti', '', 'benjamincoppetti9@gmail.com', '$2y$10$hOIJveJz6uV2K.Ds1s1NO.rzqc85Q14y9BgSLk9GnWOC6yJ2ac2e.', NULL, '2026-10-28 12:22:57', 'activo', ''),
(6, 1, 'Rakkun Stack', '', 'rakkunstack@gmail.com', '$2y$10$mxCYO0CjZhU2DeUJTWIpe.mjkEhNseru5HW1.Ogv0e5uNIsd.E3Ha', 'https://lh3.googleusercontent.com/a/ACg8ocLtusW6ncQDeBLoqbQAJhfGNxMHaQOejiBimIegyYGVNaWY1Y0=s96-c', '2026-10-30 08:48:32', 'activo', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `AUDITORIA`
--
ALTER TABLE `AUDITORIA`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `DEPORTE`
--
ALTER TABLE `DEPORTE`
  ADD PRIMARY KEY (`id_deporte`),
  ADD UNIQUE KEY `nombre` (`nombreDeporte`);

--
-- Indices de la tabla `ENFRENTAMIENTO`
--
ALTER TABLE `ENFRENTAMIENTO`
  ADD PRIMARY KEY (`id_enfrentamiento`),
  ADD KEY `id_torneo` (`id_torneo`),
  ADD KEY `id_equipo_local` (`id_equipo_local`),
  ADD KEY `id_equipo_visitante` (`id_equipo_visitante`);

--
-- Indices de la tabla `EQUIPO`
--
ALTER TABLE `EQUIPO`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `id_organizador_creador` (`id_organizador_creador`);

--
-- Indices de la tabla `INSCRIBEN`
--
ALTER TABLE `INSCRIBEN`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `id_equipo` (`id_equipo`,`id_torneo`),
  ADD KEY `id_torneo` (`id_torneo`);

--
-- Indices de la tabla `INTERVIENE`
--
ALTER TABLE `INTERVIENE`
  ADD PRIMARY KEY (`id_intervencion`),
  ADD KEY `id_administrador_usuario` (`id_administrador_usuario`),
  ADD KEY `id_usuario_afectado` (`id_usuario_afectado`);

--
-- Indices de la tabla `ORGANIZADOR`
--
ALTER TABLE `ORGANIZADOR`
  ADD PRIMARY KEY (`id_organizador`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  ADD PRIMARY KEY (`id_participante`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `PERTENECE`
--
ALTER TABLE `PERTENECE`
  ADD PRIMARY KEY (`id_participante`,`id_equipo`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `RECUPERACION_PASSWORDS`
--
ALTER TABLE `RECUPERACION_PASSWORDS`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indices de la tabla `RESULTADO`
--
ALTER TABLE `RESULTADO`
  ADD PRIMARY KEY (`id_enfrentamiento`),
  ADD KEY `id_equipo_ganador` (`id_equipo_ganador`);

--
-- Indices de la tabla `ROL`
--
ALTER TABLE `ROL`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombreRol` (`nombreRol`);

--
-- Indices de la tabla `TABLA_POSICIONES`
--
ALTER TABLE `TABLA_POSICIONES`
  ADD PRIMARY KEY (`id_tabla`),
  ADD UNIQUE KEY `id_torneo` (`id_torneo`,`id_equipo`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `TORNEO`
--
ALTER TABLE `TORNEO`
  ADD PRIMARY KEY (`id_torneo`),
  ADD KEY `id_organizador` (`id_organizador`),
  ADD KEY `id_deporte` (`id_deporte`);

--
-- Indices de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `AUDITORIA`
--
ALTER TABLE `AUDITORIA`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `DEPORTE`
--
ALTER TABLE `DEPORTE`
  MODIFY `id_deporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ENFRENTAMIENTO`
--
ALTER TABLE `ENFRENTAMIENTO`
  MODIFY `id_enfrentamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `EQUIPO`
--
ALTER TABLE `EQUIPO`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `INSCRIBEN`
--
ALTER TABLE `INSCRIBEN`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `INTERVIENE`
--
ALTER TABLE `INTERVIENE`
  MODIFY `id_intervencion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ORGANIZADOR`
--
ALTER TABLE `ORGANIZADOR`
  MODIFY `id_organizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ROL`
--
ALTER TABLE `ROL`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `TABLA_POSICIONES`
--
ALTER TABLE `TABLA_POSICIONES`
  MODIFY `id_tabla` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TORNEO`
--
ALTER TABLE `TORNEO`
  MODIFY `id_torneo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `AUDITORIA`
--
ALTER TABLE `AUDITORIA`
  ADD CONSTRAINT `AUDITORIA_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `USUARIO` (`id_usuario`);

--
-- Filtros para la tabla `ENFRENTAMIENTO`
--
ALTER TABLE `ENFRENTAMIENTO`
  ADD CONSTRAINT `ENFRENTAMIENTO_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `TORNEO` (`id_torneo`),
  ADD CONSTRAINT `ENFRENTAMIENTO_ibfk_2` FOREIGN KEY (`id_equipo_local`) REFERENCES `EQUIPO` (`id_equipo`),
  ADD CONSTRAINT `ENFRENTAMIENTO_ibfk_3` FOREIGN KEY (`id_equipo_visitante`) REFERENCES `EQUIPO` (`id_equipo`);

--
-- Filtros para la tabla `EQUIPO`
--
ALTER TABLE `EQUIPO`
  ADD CONSTRAINT `EQUIPO_ibfk_1` FOREIGN KEY (`id_organizador_creador`) REFERENCES `ORGANIZADOR` (`id_organizador`);

--
-- Filtros para la tabla `INSCRIBEN`
--
ALTER TABLE `INSCRIBEN`
  ADD CONSTRAINT `INSCRIBEN_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `EQUIPO` (`id_equipo`),
  ADD CONSTRAINT `INSCRIBEN_ibfk_2` FOREIGN KEY (`id_torneo`) REFERENCES `TORNEO` (`id_torneo`);

--
-- Filtros para la tabla `INTERVIENE`
--
ALTER TABLE `INTERVIENE`
  ADD CONSTRAINT `INTERVIENE_ibfk_1` FOREIGN KEY (`id_administrador_usuario`) REFERENCES `USUARIO` (`id_usuario`),
  ADD CONSTRAINT `INTERVIENE_ibfk_2` FOREIGN KEY (`id_usuario_afectado`) REFERENCES `USUARIO` (`id_usuario`);

--
-- Filtros para la tabla `ORGANIZADOR`
--
ALTER TABLE `ORGANIZADOR`
  ADD CONSTRAINT `ORGANIZADOR_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `USUARIO` (`id_usuario`);

--
-- Filtros para la tabla `PARTICIPANTE`
--
ALTER TABLE `PARTICIPANTE`
  ADD CONSTRAINT `PARTICIPANTE_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `USUARIO` (`id_usuario`);

--
-- Filtros para la tabla `PERTENECE`
--
ALTER TABLE `PERTENECE`
  ADD CONSTRAINT `PERTENECE_ibfk_1` FOREIGN KEY (`id_participante`) REFERENCES `PARTICIPANTE` (`id_participante`),
  ADD CONSTRAINT `PERTENECE_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `EQUIPO` (`id_equipo`);

--
-- Filtros para la tabla `RECUPERACION_PASSWORDS`
--
ALTER TABLE `RECUPERACION_PASSWORDS`
  ADD CONSTRAINT `RECUPERACION_PASSWORDS_ibfk_1` FOREIGN KEY (`email`) REFERENCES `USUARIO` (`email`);

--
-- Filtros para la tabla `RESULTADO`
--
ALTER TABLE `RESULTADO`
  ADD CONSTRAINT `RESULTADO_ibfk_1` FOREIGN KEY (`id_enfrentamiento`) REFERENCES `ENFRENTAMIENTO` (`id_enfrentamiento`),
  ADD CONSTRAINT `RESULTADO_ibfk_2` FOREIGN KEY (`id_equipo_ganador`) REFERENCES `EQUIPO` (`id_equipo`);

--
-- Filtros para la tabla `TABLA_POSICIONES`
--
ALTER TABLE `TABLA_POSICIONES`
  ADD CONSTRAINT `TABLA_POSICIONES_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `TORNEO` (`id_torneo`),
  ADD CONSTRAINT `TABLA_POSICIONES_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `EQUIPO` (`id_equipo`);

--
-- Filtros para la tabla `TORNEO`
--
ALTER TABLE `TORNEO`
  ADD CONSTRAINT `TORNEO_ibfk_1` FOREIGN KEY (`id_organizador`) REFERENCES `ORGANIZADOR` (`id_organizador`),
  ADD CONSTRAINT `TORNEO_ibfk_2` FOREIGN KEY (`id_deporte`) REFERENCES `DEPORTE` (`id_deporte`);

--
-- Filtros para la tabla `USUARIO`
--
ALTER TABLE `USUARIO`
  ADD CONSTRAINT `USUARIO_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `ROL` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
