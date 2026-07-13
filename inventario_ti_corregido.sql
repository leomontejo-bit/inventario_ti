-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para inventario_ti
CREATE DATABASE IF NOT EXISTS `inventario_ti` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `inventario_ti`;

-- Volcando estructura para tabla inventario_ti.departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Departamentos de las propiedades';

-- Volcando datos para la tabla inventario_ti.departamentos: ~9 rows (aproximadamente)
INSERT INTO `departamentos` (`id`, `nombre`, `activo`, `created_at`) VALUES
	(1, 'IT / Soporte Técnico', 1, '2026-05-26 02:30:27'),
	(2, 'Recepción / Front Desk', 1, '2026-05-26 02:30:27'),
	(3, 'Mantenimiento', 1, '2026-05-26 02:30:27'),
	(4, 'Recursos Humanos', 1, '2026-05-26 02:30:27'),
	(5, 'Alimentos y Bebidas', 1, '2026-05-26 02:30:27'),
	(6, 'Ama de Llaves', 1, '2026-05-26 02:30:27'),
	(7, 'Gerencia General', 1, '2026-05-26 02:30:27'),
	(8, 'Contabilidad / Finanzas', 1, '2026-05-26 02:30:27'),
	(9, 'Seguridad / CCTV', 1, '2026-05-26 02:30:27');

-- Volcando estructura para tabla inventario_ti.hoteles
CREATE TABLE IF NOT EXISTS `hoteles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ej: TUL, AKU, TEQ, SIK',
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_hoteles_codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Propiedades del grupo Bahia Principe';

-- Volcando datos para la tabla inventario_ti.hoteles: ~4 rows (aproximadamente)
INSERT INTO `hoteles` (`id`, `nombre`, `codigo`, `direccion`, `activo`, `created_at`) VALUES
	(1, 'Bahia Principe Grand Tulum', 'TUL', NULL, 1, '2026-05-26 02:30:27'),
	(2, 'Bahia Principe Grand Akumal', 'AKU', NULL, 1, '2026-05-26 02:30:27'),
	(3, 'Bahia Principe Luxury Tequila', 'TEQ', NULL, 1, '2026-05-26 02:30:27'),
	(4, 'Bahia Principe Sian Ka\'an', 'SIK', NULL, 1, '2026-05-26 02:30:27');

-- Volcando estructura para tabla inventario_ti.tipos_activo
CREATE TABLE IF NOT EXISTS `tipos_activo` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PC, Monitor, Teclado, Mouse, No-break, etc.',
  `categoria` enum('equipo_computo','periferico','red','licencia','contrato','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'otro',
  `prefijo_codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Prefijo para generar código interno, ej: PC, MON, NB',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de tipos de activo TI';

-- Volcando datos para la tabla inventario_ti.tipos_activo: ~13 rows (aproximadamente)
INSERT INTO `tipos_activo` (`id`, `nombre`, `categoria`, `prefijo_codigo`, `activo`) VALUES
	(1, 'Equipo de cómputo (PC/Laptop)', 'equipo_computo', 'PC', 1),
	(2, 'Monitor', 'periferico', 'MON', 1),
	(3, 'Teclado', 'periferico', 'TEC', 1),
	(4, 'Mouse', 'periferico', 'MOU', 1),
	(5, 'Cargador / Adaptador', 'periferico', 'CAR', 1),
	(6, 'No break / UPS', 'equipo_computo', 'NB', 1),
	(7, 'Impresora térmica', 'periferico', 'IMP', 1),
	(8, 'Lector de código de barras', 'periferico', 'LEC', 1),
	(9, 'Escáner de pasaporte', 'periferico', 'ESC', 1),
	(10, 'Switch / Router', 'red', 'NET', 1),
	(11, 'Licencia de software', 'licencia', 'LIC', 1),
	(12, 'Contrato / Leasing', 'contrato', 'CNT', 1),
	(13, 'Otro periférico', 'periferico', 'OTR', 1);

-- Volcando estructura para tabla inventario_ti.usuarios_sistema
CREATE TABLE IF NOT EXISTS `usuarios_sistema` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol` enum('admin','soporte','auditor','consulta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'consulta',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema de inventario TI';

-- Volcando datos para la tabla inventario_ti.usuarios_sistema: ~2 rows (aproximadamente)
INSERT INTO `usuarios_sistema` (`id`, `nombre`, `email`, `password_hash`, `remember_token`, `rol`, `activo`, `ultimo_acceso`, `created_at`) VALUES
	(3, 'Administrador TI', 'admin.ti@bahiaprincipe.com', '$2y$12$It.Sr5TXD2VAPYAMZIfP6eZ/U3PYJI3DJRL.QeJRMeuuSwqbZBMWm', 'BvvKs8d6DYWJjZxgOtI2McWfEKmkcwO0MbFQ6HQWXezPUhVls0dFdsDSLwE7', 'admin', 1, '2026-06-28 05:21:25', '2026-05-27 02:40:48'),
	(4, 'Administrador TI', 'admin@bahiaprincipe.com', '$2y$12$mK3Z30RrIrkiXcGnyDe9uO8oQEDuNQ5umqtNEKTRC9wSep7PauZCG', 'IucQbL7aem71gdZ633iiUtTouDPurnKrCjb4dFsC7TevYTaRdelgJ4h5eBzh', 'admin', 1, '2026-06-28 04:38:47', '2026-06-03 02:46:29');

-- Volcando estructura para tabla inventario_ti.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.users: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.cache: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.cache_locks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.job_batches: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.migrations: ~0 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1);

-- Volcando estructura para tabla inventario_ti.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla inventario_ti.sessions: ~3 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('4QdOeDhzoN7PNQy8xClSGQAhvLMMMvlfhMq0jiUs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJRZWVzQmZ3TEp0M3haZW5jeWNKN2lHSVdEZk03SVEyQld2OWU3UUoxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvc2VpLmNlbnRlcmRhdGF0ZWNoLmNvbSJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvc2VpLmNlbnRlcmRhdGF0ZWNoLmNvbVwvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782621498),
	('aORpPwcQFXoC0sFBPe6E0V9wQnIIE2cAsKuVwnwc', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI5d0tSTW1uOE1hVks2WFBDenRCdzJQckd2NmxFcVNNY3UxQm1pZmdFIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3VzdWFyaW9zIiwicm91dGUiOiJ1c3Vhcmlvcy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6M30=', 1782626462),
	('k7eEHEcX935A0Sd3Ag7ZmNuhQVTDqSu3eLnO0oKg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.125.1 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'eyJfdG9rZW4iOiJ2ODJibW1hVmRCRTNRdUdlTnJIZUZJZDBFRE1DQWRhaUVOb2pQTFJwIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782621507);

-- Volcando estructura para tabla inventario_ti.colaboradores
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` int unsigned NOT NULL,
  `departamento_id` int unsigned NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_empleado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_corporativo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_ad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Usuario en Active Directory',
  `puesto` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','baja','vacaciones','licencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_colaboradores_num_emp` (`num_empleado`),
  KEY `idx_colaboradores_hotel` (`hotel_id`),
  KEY `idx_colaboradores_dpto` (`departamento_id`),
  CONSTRAINT `fk_colab_dpto` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_colab_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hoteles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Empleados a los que se asignan activos TI';

-- Volcando datos para la tabla inventario_ti.colaboradores: ~0 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.activos
CREATE TABLE IF NOT EXISTS `activos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo_activo_id` int unsigned NOT NULL,
  `hotel_id` int unsigned NOT NULL,
  `departamento_id` int unsigned NOT NULL,
  `colaborador_id` int unsigned DEFAULT NULL COMMENT 'Asignación actual (NULL = sin asignar)',
  `num_inventario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de inventario corporativo',
  `codigo_interno_ti` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código interno del área TI',
  `codigo_barras` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código generado para etiqueta física',
  `num_serie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_equipo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hostname o nombre descriptivo',
  `marca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procesador` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram_gb` decimal(6,2) DEFAULT NULL,
  `almacenamiento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sistema_operativo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IPv4 o IPv6',
  `direccion_mac` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','baja','mantenimiento','extraviado','stock','prestamo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stock',
  `fecha_adquisicion` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `motivo_baja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_adquisicion` decimal(10,2) DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_activos_num_inv` (`num_inventario`),
  UNIQUE KEY `uq_activos_codigo_barras` (`codigo_barras`),
  KEY `idx_activos_tipo` (`tipo_activo_id`),
  KEY `idx_activos_hotel` (`hotel_id`),
  KEY `idx_activos_dpto` (`departamento_id`),
  KEY `idx_activos_colaborador` (`colaborador_id`),
  KEY `idx_activos_serie` (`num_serie`),
  KEY `idx_activos_ip` (`direccion_ip`),
  KEY `idx_activos_estado` (`estado`),
  CONSTRAINT `fk_activos_colab` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_activos_dpto` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_activos_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hoteles` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_activos_tipo` FOREIGN KEY (`tipo_activo_id`) REFERENCES `tipos_activo` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Activos TI individuales con trazabilidad completa';

-- Volcando datos para la tabla inventario_ti.activos: ~1 rows (aproximadamente)
INSERT INTO `activos` (`id`, `tipo_activo_id`, `hotel_id`, `departamento_id`, `colaborador_id`, `num_inventario`, `codigo_interno_ti`, `codigo_barras`, `num_serie`, `nombre_equipo`, `marca`, `modelo`, `procesador`, `ram_gb`, `almacenamiento`, `sistema_operativo`, `direccion_ip`, `direccion_mac`, `estado`, `fecha_adquisicion`, `fecha_baja`, `motivo_baja`, `valor_adquisicion`, `observaciones`, `created_at`, `updated_at`) VALUES
	(17, 11, 2, 9, NULL, '123', '456', '6789', '098766', 'cam', 'hike', '34', 'amd', 12.00, '256', 'winndows', '12345678', 'rr4r', 'activo', '2026-06-01', NULL, NULL, 12.00, 'rggg5g5g', '2026-06-02 09:17:20', '2026-06-02 09:17:20');

-- Volcando estructura para tabla inventario_ti.asignaciones
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` int unsigned NOT NULL,
  `colaborador_id` int unsigned NOT NULL,
  `usuario_sistema_id` int unsigned NOT NULL COMMENT 'Quién registró la asignación',
  `fecha_asignacion` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL COMMENT 'NULL = asignación vigente',
  `motivo_devolucion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicion_entrega` enum('bueno','regular','dañado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bueno',
  `condicion_retorno` enum('bueno','regular','dañado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_asig_activo` (`activo_id`),
  KEY `idx_asig_colaborador` (`colaborador_id`),
  KEY `idx_asig_fecha` (`fecha_asignacion`),
  KEY `fk_asig_usuario` (`usuario_sistema_id`),
  CONSTRAINT `fk_asig_activo` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_asig_colab` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_asig_usuario` FOREIGN KEY (`usuario_sistema_id`) REFERENCES `usuarios_sistema` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial completo de asignaciones por activo';

-- Volcando datos para la tabla inventario_ti.asignaciones: ~1 rows (aproximadamente)

-- Volcando estructura para tabla inventario_ti.auditoria
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` int unsigned DEFAULT NULL,
  `usuario_sistema_id` int unsigned DEFAULT NULL,
  `tabla_afectada` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` int unsigned NOT NULL COMMENT 'ID del registro modificado',
  `accion` enum('insertar','actualizar','eliminar','asignar','devolver','baja','importar_excel','imprimir_etiqueta','escaneo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valores_anteriores` json DEFAULT NULL,
  `valores_nuevos` json DEFAULT NULL,
  `ip_cliente` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_activo` (`activo_id`),
  KEY `idx_audit_usuario` (`usuario_sistema_id`),
  KEY `idx_audit_fecha` (`fecha`),
  CONSTRAINT `fk_audit_activo` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_audit_usuario` FOREIGN KEY (`usuario_sistema_id`) REFERENCES `usuarios_sistema` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Trazabilidad completa de cambios en el sistema';

-- Volcando datos para la tabla inventario_ti.auditoria: ~10 rows (aproximadamente)
INSERT INTO `auditoria` (`id`, `activo_id`, `usuario_sistema_id`, `tabla_afectada`, `registro_id`, `accion`, `valores_anteriores`, `valores_nuevos`, `ip_cliente`, `fecha`) VALUES
	(5, NULL, NULL, 'activos', 3, 'insertar', NULL, '{"id": 3, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 03:37:01", "updated_at": "2026-06-02 03:37:01", "num_inventario": "WEB-TEST-6a1e4fdd4c1e8", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 08:37:01'),
	(6, NULL, NULL, 'activos', 4, 'insertar', NULL, '{"id": 4, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 03:38:35", "updated_at": "2026-06-02 03:38:35", "num_inventario": "WEB-TEST-6a1e503b85cb3", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 08:38:35'),
	(7, NULL, NULL, 'activos', 6, 'insertar', NULL, '{"id": 6, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 03:47:28", "updated_at": "2026-06-02 03:47:28", "num_inventario": "WEB-TEST-6a1e5250d4e5c", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 08:47:28'),
	(11, NULL, NULL, 'activos', 8, 'insertar', NULL, '{"id": 8, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 03:56:53", "updated_at": "2026-06-02 03:56:53", "num_inventario": "WEB-TEST-6a1e5485ba27a", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 08:56:53'),
	(18, NULL, NULL, 'activos', 15, 'insertar', NULL, '{"id": 15, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 04:08:13", "updated_at": "2026-06-02 04:08:13", "num_inventario": "WEB-TEST-6a1e572dbcfb8", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 09:08:13'),
	(19, 17, NULL, 'activos', 17, 'insertar', NULL, '{"id": 17, "marca": "hike", "estado": "activo", "modelo": "34", "ram_gb": "12", "hotel_id": "2", "num_serie": "098766", "created_at": "2026-06-02 04:17:20", "procesador": "amd", "updated_at": "2026-06-02 04:17:20", "direccion_ip": "12345678", "codigo_barras": "6789", "direccion_mac": "rr4r", "nombre_equipo": "cam", "observaciones": "rggg5g5g", "almacenamiento": "256", "num_inventario": "123", "tipo_activo_id": "11", "departamento_id": "9", "codigo_interno_ti": "456", "fecha_adquisicion": "2026-06-01 00:00:00", "sistema_operativo": "winndows", "valor_adquisicion": "12"}', '127.0.0.1', '2026-06-02 09:17:20'),
	(23, NULL, NULL, 'activos', 20, 'insertar', NULL, '{"id": 20, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 04:31:36", "updated_at": "2026-06-02 04:31:36", "num_inventario": "WEB-TEST-6a1e5ca8205a8", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 09:31:36'),
	(28, NULL, NULL, 'activos', 24, 'insertar', NULL, '{"id": 24, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 14:43:53", "updated_at": "2026-06-02 14:43:53", "num_inventario": "WEB-TEST-6a1eec298db17", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 19:43:53'),
	(35, NULL, NULL, 'activos', 28, 'insertar', NULL, '{"id": 28, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 14:52:50", "updated_at": "2026-06-02 14:52:50", "num_inventario": "WEB-TEST-6a1eee4269745", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-02 19:52:50'),
	(40, NULL, 3, 'activos', 32, 'insertar', NULL, '{"id": 32, "marca": "HP", "estado": "stock", "modelo": "EliteBook", "hotel_id": 1, "created_at": "2026-06-02 21:48:20", "updated_at": "2026-06-02 21:48:20", "num_inventario": "WEB-TEST-6a1f4fa427722", "tipo_activo_id": 1, "departamento_id": 1}', '127.0.0.1', '2026-06-03 02:48:20'),
	(41, NULL, 4, 'usuarios_sistema', 3, 'actualizar', NULL, '{"password_actualizado": true}', '127.0.0.1', '2026-06-28 11:20:38');

-- Volcando estructura para tabla inventario_ti.contratos
CREATE TABLE IF NOT EXISTS `contratos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` int unsigned NOT NULL,
  `tipo` enum('leasing','mantenimiento','garantia','soporte','otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_contrato` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_proveedor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_proveedor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `moneda` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MXN',
  `estado` enum('vigente','vencido','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vigente',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contratos_activo` (`activo_id`),
  KEY `idx_contratos_fecha` (`fecha_fin`),
  CONSTRAINT `fk_contratos_activo` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contratos de leasing, garantía y mantenimiento por activo';

-- Volcando datos para la tabla inventario_ti.contratos: ~0 rows (aproximadamente)
INSERT INTO `contratos` (`id`, `activo_id`, `tipo`, `proveedor`, `num_contrato`, `contacto_proveedor`, `telefono_proveedor`, `fecha_inicio`, `fecha_fin`, `monto`, `moneda`, `estado`, `notas`, `created_at`) VALUES
	(2, 17, 'mantenimiento', 'Telmex', '2341', '9842056758', '9842056758', '2026-06-02', '2026-06-18', 344.00, 'MXN', 'vigente', NULL, '2026-06-03 02:38:46');

-- Volcando estructura para tabla inventario_ti.etiquetas
CREATE TABLE IF NOT EXISTS `etiquetas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` int unsigned NOT NULL,
  `usuario_sistema_id` int unsigned NOT NULL,
  `tipo_impresion` enum('termica','estandar','pdf') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'termica',
  `fecha_generacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datos_etiqueta` json DEFAULT NULL COMMENT 'num_inv, barcode, serie, nombre, dpto, hotel, codigo_ti',
  PRIMARY KEY (`id`),
  KEY `idx_etiq_activo` (`activo_id`),
  KEY `idx_etiq_fecha` (`fecha_generacion`),
  KEY `fk_etiq_usuario` (`usuario_sistema_id`),
  CONSTRAINT `fk_etiq_activo` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_etiq_usuario` FOREIGN KEY (`usuario_sistema_id`) REFERENCES `usuarios_sistema` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de etiquetas generadas o impresas por activo';

-- Volcando datos para la tabla inventario_ti.etiquetas: ~7 rows (aproximadamente)
INSERT INTO `etiquetas` (`id`, `activo_id`, `usuario_sistema_id`, `tipo_impresion`, `fecha_generacion`, `datos_etiqueta`) VALUES
	(12, 17, 3, 'termica', '2026-06-02 04:17:34', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(13, 17, 3, 'termica', '2026-06-02 04:17:40', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(14, 17, 3, 'termica', '2026-06-02 04:17:42', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(15, 17, 3, 'termica', '2026-06-02 04:17:44', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(16, 17, 3, 'estandar', '2026-06-02 04:17:48', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(17, 17, 3, 'termica', '2026-06-02 04:17:50', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}'),
	(20, 17, 3, 'termica', '2026-06-02 14:51:24', '{"hotel": "Bahia Principe Grand Akumal", "num_serie": "098766", "departamento": "Seguridad / CCTV", "hotel_codigo": "AKU", "codigo_barras": "6789", "nombre_equipo": "cam", "num_inventario": "123", "codigo_interno_ti": "456"}');

-- Volcando estructura para tabla inventario_ti.licencias_software
CREATE TABLE IF NOT EXISTS `licencias_software` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activo_id` int unsigned DEFAULT NULL COMMENT 'NULL = licencia flotante/sin equipo',
  `nombre_software` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fabricante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_licencia` enum('oem','volumen','suscripcion','freeware','otro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'otro',
  `clave_producto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cifrar en producción',
  `num_licencias` int NOT NULL DEFAULT '1',
  `fecha_adquisicion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `proveedor` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `estado` enum('activa','vencida','baja') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lic_activo` (`activo_id`),
  KEY `idx_lic_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `fk_lic_activo` FOREIGN KEY (`activo_id`) REFERENCES `activos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Licencias de software vinculadas a activos o flotantes';

-- Volcando datos para la tabla inventario_ti.licencias_software: ~0 rows (aproximadamente)

-- Volcando estructura para vista inventario_ti.v_activos_detalle
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_activos_detalle` (
	`id` INT UNSIGNED NOT NULL,
	`num_inventario` VARCHAR(1) NOT NULL COMMENT 'Número de inventario corporativo' COLLATE 'utf8mb4_unicode_ci',
	`codigo_interno_ti` VARCHAR(1) NULL COMMENT 'Código interno del área TI' COLLATE 'utf8mb4_unicode_ci',
	`codigo_barras` VARCHAR(1) NULL COMMENT 'Código generado para etiqueta física' COLLATE 'utf8mb4_unicode_ci',
	`num_serie` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_equipo` VARCHAR(1) NULL COMMENT 'Hostname o nombre descriptivo' COLLATE 'utf8mb4_unicode_ci',
	`marca` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`modelo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion_ip` VARCHAR(1) NULL COMMENT 'IPv4 o IPv6' COLLATE 'utf8mb4_unicode_ci',
	`estado` ENUM('activo','baja','mantenimiento','extraviado','stock','prestamo') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_adquisicion` DATE NULL,
	`tipo_activo` VARCHAR(1) NOT NULL COMMENT 'PC, Monitor, Teclado, Mouse, No-break, etc.' COLLATE 'utf8mb4_unicode_ci',
	`categoria_activo` ENUM('equipo_computo','periferico','red','licencia','contrato','otro') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hotel` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hotel_codigo` VARCHAR(1) NOT NULL COMMENT 'Ej: TUL, AKU, TEQ, SIK' COLLATE 'utf8mb4_unicode_ci',
	`departamento` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`colaborador` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`num_empleado` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`email_corporativo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`usuario_ad` VARCHAR(1) NULL COMMENT 'Usuario en Active Directory' COLLATE 'utf8mb4_unicode_ci',
	`observaciones` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`updated_at` TIMESTAMP NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista inventario_ti.v_activos_sin_asignar
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_activos_sin_asignar` (
	`id` INT UNSIGNED NOT NULL,
	`num_inventario` VARCHAR(1) NOT NULL COMMENT 'Número de inventario corporativo' COLLATE 'utf8mb4_unicode_ci',
	`codigo_interno_ti` VARCHAR(1) NULL COMMENT 'Código interno del área TI' COLLATE 'utf8mb4_unicode_ci',
	`codigo_barras` VARCHAR(1) NULL COMMENT 'Código generado para etiqueta física' COLLATE 'utf8mb4_unicode_ci',
	`num_serie` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`nombre_equipo` VARCHAR(1) NULL COMMENT 'Hostname o nombre descriptivo' COLLATE 'utf8mb4_unicode_ci',
	`marca` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`modelo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`direccion_ip` VARCHAR(1) NULL COMMENT 'IPv4 o IPv6' COLLATE 'utf8mb4_unicode_ci',
	`estado` ENUM('activo','baja','mantenimiento','extraviado','stock','prestamo') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_adquisicion` DATE NULL,
	`tipo_activo` VARCHAR(1) NOT NULL COMMENT 'PC, Monitor, Teclado, Mouse, No-break, etc.' COLLATE 'utf8mb4_unicode_ci',
	`categoria_activo` ENUM('equipo_computo','periferico','red','licencia','contrato','otro') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hotel` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`hotel_codigo` VARCHAR(1) NOT NULL COMMENT 'Ej: TUL, AKU, TEQ, SIK' COLLATE 'utf8mb4_unicode_ci',
	`departamento` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`colaborador` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`num_empleado` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`email_corporativo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`usuario_ad` VARCHAR(1) NULL COMMENT 'Usuario en Active Directory' COLLATE 'utf8mb4_unicode_ci',
	`observaciones` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`updated_at` TIMESTAMP NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista inventario_ti.v_contratos_por_vencer
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_contratos_por_vencer` (
	`id` INT UNSIGNED NOT NULL,
	`tipo` ENUM('leasing','mantenimiento','garantia','soporte','otro') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`proveedor` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`num_contrato` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_fin` DATE NULL,
	`dias_restantes` INT NULL,
	`num_inventario` VARCHAR(1) NOT NULL COMMENT 'Número de inventario corporativo' COLLATE 'utf8mb4_unicode_ci',
	`nombre_equipo` VARCHAR(1) NULL COMMENT 'Hostname o nombre descriptivo' COLLATE 'utf8mb4_unicode_ci',
	`hotel` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Volcando estructura para vista inventario_ti.v_licencias_por_vencer
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_licencias_por_vencer` (
	`id` INT UNSIGNED NOT NULL,
	`nombre_software` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`version` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo_licencia` ENUM('oem','volumen','suscripcion','freeware','otro') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_vencimiento` DATE NULL,
	`dias_restantes` INT NULL,
	`num_inventario` VARCHAR(1) NOT NULL COMMENT 'Número de inventario corporativo' COLLATE 'utf8mb4_unicode_ci',
	`nombre_equipo` VARCHAR(1) NULL COMMENT 'Hostname o nombre descriptivo' COLLATE 'utf8mb4_unicode_ci',
	`hotel` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_activos_detalle`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_activos_detalle` AS select `a`.`id` AS `id`,`a`.`num_inventario` AS `num_inventario`,`a`.`codigo_interno_ti` AS `codigo_interno_ti`,`a`.`codigo_barras` AS `codigo_barras`,`a`.`num_serie` AS `num_serie`,`a`.`nombre_equipo` AS `nombre_equipo`,`a`.`marca` AS `marca`,`a`.`modelo` AS `modelo`,`a`.`direccion_ip` AS `direccion_ip`,`a`.`estado` AS `estado`,`a`.`fecha_adquisicion` AS `fecha_adquisicion`,`ta`.`nombre` AS `tipo_activo`,`ta`.`categoria` AS `categoria_activo`,`h`.`nombre` AS `hotel`,`h`.`codigo` AS `hotel_codigo`,`d`.`nombre` AS `departamento`,`c`.`nombre` AS `colaborador`,`c`.`num_empleado` AS `num_empleado`,`c`.`email_corporativo` AS `email_corporativo`,`c`.`usuario_ad` AS `usuario_ad`,`a`.`observaciones` AS `observaciones`,`a`.`updated_at` AS `updated_at` from ((((`activos` `a` join `tipos_activo` `ta` on((`ta`.`id` = `a`.`tipo_activo_id`))) join `hoteles` `h` on((`h`.`id` = `a`.`hotel_id`))) join `departamentos` `d` on((`d`.`id` = `a`.`departamento_id`))) left join `colaboradores` `c` on((`c`.`id` = `a`.`colaborador_id`)));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_activos_sin_asignar`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_activos_sin_asignar` AS select `v_activos_detalle`.`id` AS `id`,`v_activos_detalle`.`num_inventario` AS `num_inventario`,`v_activos_detalle`.`codigo_interno_ti` AS `codigo_interno_ti`,`v_activos_detalle`.`codigo_barras` AS `codigo_barras`,`v_activos_detalle`.`num_serie` AS `num_serie`,`v_activos_detalle`.`nombre_equipo` AS `nombre_equipo`,`v_activos_detalle`.`marca` AS `marca`,`v_activos_detalle`.`modelo` AS `modelo`,`v_activos_detalle`.`direccion_ip` AS `direccion_ip`,`v_activos_detalle`.`estado` AS `estado`,`v_activos_detalle`.`fecha_adquisicion` AS `fecha_adquisicion`,`v_activos_detalle`.`tipo_activo` AS `tipo_activo`,`v_activos_detalle`.`categoria_activo` AS `categoria_activo`,`v_activos_detalle`.`hotel` AS `hotel`,`v_activos_detalle`.`hotel_codigo` AS `hotel_codigo`,`v_activos_detalle`.`departamento` AS `departamento`,`v_activos_detalle`.`colaborador` AS `colaborador`,`v_activos_detalle`.`num_empleado` AS `num_empleado`,`v_activos_detalle`.`email_corporativo` AS `email_corporativo`,`v_activos_detalle`.`usuario_ad` AS `usuario_ad`,`v_activos_detalle`.`observaciones` AS `observaciones`,`v_activos_detalle`.`updated_at` AS `updated_at` from `v_activos_detalle` where ((`v_activos_detalle`.`colaborador` is null) and (`v_activos_detalle`.`estado` = 'stock'));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_contratos_por_vencer`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_contratos_por_vencer` AS select `co`.`id` AS `id`,`co`.`tipo` AS `tipo`,`co`.`proveedor` AS `proveedor`,`co`.`num_contrato` AS `num_contrato`,`co`.`fecha_fin` AS `fecha_fin`,(to_days(`co`.`fecha_fin`) - to_days(curdate())) AS `dias_restantes`,`a`.`num_inventario` AS `num_inventario`,`a`.`nombre_equipo` AS `nombre_equipo`,`h`.`nombre` AS `hotel` from ((`contratos` `co` join `activos` `a` on((`a`.`id` = `co`.`activo_id`))) join `hoteles` `h` on((`h`.`id` = `a`.`hotel_id`))) where ((`co`.`estado` = 'vigente') and (`co`.`fecha_fin` is not null) and (`co`.`fecha_fin` <= (curdate() + interval 60 day))) order by `co`.`fecha_fin`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_licencias_por_vencer`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_licencias_por_vencer` AS select `ls`.`id` AS `id`,`ls`.`nombre_software` AS `nombre_software`,`ls`.`version` AS `version`,`ls`.`tipo_licencia` AS `tipo_licencia`,`ls`.`fecha_vencimiento` AS `fecha_vencimiento`,(to_days(`ls`.`fecha_vencimiento`) - to_days(curdate())) AS `dias_restantes`,`a`.`num_inventario` AS `num_inventario`,`a`.`nombre_equipo` AS `nombre_equipo`,`h`.`nombre` AS `hotel` from ((`licencias_software` `ls` join `activos` `a` on((`a`.`id` = `ls`.`activo_id`))) join `hoteles` `h` on((`h`.`id` = `a`.`hotel_id`))) where ((`ls`.`estado` = 'activa') and (`ls`.`fecha_vencimiento` is not null) and (`ls`.`fecha_vencimiento` <= (curdate() + interval 60 day))) order by `ls`.`fecha_vencimiento`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
