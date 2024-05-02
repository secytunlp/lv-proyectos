-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.38-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table lv_secyt.facultad
CREATE TABLE IF NOT EXISTS `facultad` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `codigo` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nombre` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cat` enum('Exactas','Naturales','Sociales') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1221 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.facultad: ~19 rows (approximately)
INSERT INTO `facultad` (`id`, `codigo`, `nombre`, `cat`, `created_at`, `updated_at`) VALUES
                                                                                         (164, '1100', 'RECTORADO', 'Sociales', NULL, NULL),
                                                                                         (165, '1101', 'FACULTAD DE CIENCIAS AGRARIAS Y FORESTALES', 'Naturales', NULL, NULL),
                                                                                         (167, '1103', 'FACULTAD DE CIENCIAS VETERINARIAS', 'Naturales', NULL, NULL),
                                                                                         (168, '1104', 'FACULTAD DE ARQUITECTURA Y URBANISMO', 'Sociales', NULL, NULL),
                                                                                         (169, '1105', 'FACULTAD DE INGENIERIA', 'Exactas', NULL, NULL),
                                                                                         (170, '1106', 'FACULTAD DE CIENCIAS EXACTAS', 'Exactas', NULL, NULL),
                                                                                         (171, '1108', 'FACULTAD DE CIENCIAS ASTRONOMICAS Y GEOFISICAS', 'Exactas', NULL, NULL),
                                                                                         (172, '1109', 'FACULTAD DE CIENCIAS ECONOMICAS', 'Sociales', NULL, NULL),
                                                                                         (173, '1110', 'FACULTAD DE CIENCIAS JURIDICAS Y SOCIALES', 'Sociales', NULL, NULL),
                                                                                         (174, '1111', 'FACULTAD DE PERIODISMO Y COMUNICACION SOCIAL', 'Sociales', NULL, NULL),
                                                                                         (175, '1112', 'FACULTAD DE HUMANIDADES Y CIENCIAS DE LA EDUCACION', 'Sociales', NULL, NULL),
                                                                                         (176, '1113', 'FACULTAD DE BELLAS ARTES', 'Sociales', NULL, NULL),
                                                                                         (177, '1114', 'FACULTAD DE CIENCIAS MEDICAS', 'Naturales', NULL, NULL),
                                                                                         (179, '1116', 'FACULTAD DE TRABAJO SOCIAL', 'Sociales', NULL, NULL),
                                                                                         (180, '1117', 'FACULTAD DE ODONTOLOGIA', 'Naturales', NULL, NULL),
                                                                                         (181, '1118', 'FACULTAD DE CIENCIAS NATURALES Y MUSEO', 'Naturales', NULL, NULL),
                                                                                         (187, '1124', 'FACULTAD DE INFORMATICA', 'Exactas', NULL, NULL),
                                                                                         (574, '9999', 'No declarado', NULL, NULL, NULL),
                                                                                         (1220, '1107', 'FACULTAD DE PSICOLOGIA', 'Sociales', NULL, NULL);

-- Dumping structure for table lv_secyt.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table lv_secyt.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.migrations: ~7 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
                                                          (1, '2014_10_12_000000_create_users_table', 1),
                                                          (2, '2014_10_12_100000_create_password_resets_table', 1),
                                                          (3, '2019_08_19_000000_create_failed_jobs_table', 1),
                                                          (4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
                                                          (5, '2022_08_01_225622_create_permission_tables', 1),
                                                          (6, '2022_08_03_233734_add_image_to_users', 2),
                                                          (7, '2024_05_01_162112_create_facultad_table', 2);

-- Dumping structure for table lv_secyt.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
    `permission_id` bigint(20) unsigned NOT NULL,
    `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
    KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
    CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.model_has_permissions: ~0 rows (approximately)

-- Dumping structure for table lv_secyt.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
    `role_id` bigint(20) unsigned NOT NULL,
    `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`role_id`,`model_id`,`model_type`),
    KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
    CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.model_has_roles: ~2 rows (approximately)
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
                                                                        (1, 'App\\Models\\User', 2),
                                                                        (2, 'App\\Models\\User', 3);

-- Dumping structure for table lv_secyt.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.password_resets: ~0 rows (approximately)

-- Dumping structure for table lv_secyt.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.permissions: ~12 rows (approximately)
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
                                                                                       (1, 'rol-listar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (2, 'rol-crear', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (3, 'rol-editar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (4, 'rol-eliminar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (5, 'usuario-listar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (6, 'usuario-crear', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (7, 'usuario-editar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (8, 'usuario-eliminar', 'web', '2022-05-01 14:57:00', '2022-05-01 14:57:00'),
                                                                                       (9, 'solicitud-listar', 'web', '2024-05-01 19:23:05', '2024-05-01 19:23:05'),
                                                                                       (10, 'solicitud-crear', 'web', '2024-05-01 19:23:05', '2024-05-01 19:23:05'),
                                                                                       (11, 'solicitud-editar', 'web', '2024-05-01 19:23:05', '2024-05-01 19:23:05'),
                                                                                       (12, 'solicitud-eliminar', 'web', '2024-05-01 19:23:05', '2024-05-01 19:23:05');

-- Dumping structure for table lv_secyt.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tokenable_id` bigint(20) unsigned NOT NULL,
    `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `abilities` text COLLATE utf8mb4_unicode_ci,
    `last_used_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table lv_secyt.roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.roles: ~2 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
                                                                                 (1, 'Administrador', 'web', '2024-05-01 14:56:03', '2024-05-01 14:56:07'),
                                                                                 (2, 'Solicitante', 'web', '2024-05-01 19:23:41', '2024-05-01 19:23:41');

-- Dumping structure for table lv_secyt.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
    `permission_id` bigint(20) unsigned NOT NULL,
    `role_id` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`permission_id`,`role_id`),
    KEY `role_has_permissions_role_id_foreign` (`role_id`),
    CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.role_has_permissions: ~12 rows (approximately)
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
                                                                    (1, 1),
                                                                    (2, 1),
                                                                    (3, 1),
                                                                    (4, 1),
                                                                    (5, 1),
                                                                    (6, 1),
                                                                    (7, 1),
                                                                    (8, 1),
                                                                    (9, 2),
                                                                    (10, 2),
                                                                    (11, 2),
                                                                    (12, 2);

-- Dumping structure for table lv_secyt.users
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `image` text COLLATE utf8mb4_unicode_ci,
    `facultad_id` bigint(20) unsigned DEFAULT NULL,
    `cuil` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Formato: xx-xxxxxxxx-x',
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `cuil` (`cuil`),
    KEY `FK_users_facultad` (`facultad_id`),
    CONSTRAINT `FK_users_facultad` FOREIGN KEY (`facultad_id`) REFERENCES `facultad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lv_secyt.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `image`, `facultad_id`, `cuil`) VALUES
                                                                                                                                                               (2, 'Marcos Piñero', 'marcosp@presi.unlp.edu.ar', NULL, '$2y$10$cJDkxSIl5xhMYKMV4.Eqle3TSSB9MZ/SxzqVkX/hsV6HOMARQPlme', NULL, '2024-05-01 14:55:19', '2024-05-01 18:53:35', '1714587783.jpg', 187, '20-25174805-6'),
                                                                                                                                                               (3, 'María José Novillo', 'mpinia@hotmail.com', NULL, '$2y$10$./Dp/MN7JGTnWocEmzjr5.NsZQSwX3aEg3M59zXlz5ISmXXR11LVy', 'PfQzjRok8JhUfBs09m7dRC3QjiQg4fi2bqRttFP1U7ThS05tGVbJjG2YihAr', '2024-05-01 21:47:03', '2024-05-01 23:54:58', NULL, 179, '27-27388858-1'),
                                                                                                                                                               (7, 'María Alicia Sferra', 'marcos.pinero1976@gmail.com', NULL, '$2y$10$afcSH5cO4bdlUdQtc67baOYVzGpknv4mNUbkSc6lnwyDM1AkJIoeO', NULL, '2024-05-02 01:35:54', '2024-05-02 01:35:54', NULL, NULL, '27-10211830-3');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
