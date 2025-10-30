-- Création des tables pour le système Analytics RGPD

-- Table: cookie_consent
CREATE TABLE IF NOT EXISTS `cookie_consent` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `consent_token` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(255) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `necessary_cookies` TINYINT(1) NOT NULL DEFAULT 1,
  `analytics_cookies` TINYINT(1) NOT NULL DEFAULT 0,
  `marketing_cookies` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY(`id`),
  UNIQUE INDEX `UNIQ_C2A6458C1F72369` (`consent_token`),
  INDEX `idx_created_at` (`created_at`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Table: analytics
CREATE TABLE IF NOT EXISTS `analytics` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `session_id` VARCHAR(255) NOT NULL,
  `consent_token` TEXT DEFAULT NULL,
  `page_url` TEXT NOT NULL,
  `page_title` TEXT DEFAULT NULL,
  `referrer` TEXT DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `ip_address` TEXT DEFAULT NULL,
  `device` TEXT DEFAULT NULL COMMENT 'mobile, tablet, desktop',
  `browser` TEXT DEFAULT NULL,
  `operating_system` TEXT DEFAULT NULL,
  `time_on_page` INT DEFAULT NULL COMMENT 'En secondes',
  `scroll_depth` INT DEFAULT NULL COMMENT 'Pourcentage de scroll (0-100)',
  `utm_source` VARCHAR(255) DEFAULT NULL,
  `utm_medium` VARCHAR(255) DEFAULT NULL,
  `utm_campaign` VARCHAR(255) DEFAULT NULL,
  `country` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(255) DEFAULT NULL,
  `is_bounce` TINYINT(1) NOT NULL DEFAULT 0,
  `custom_data` JSON DEFAULT NULL,
  `visited_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_visited_at` (`visited_at`),
  INDEX `idx_page_url` (`page_url`(255)),
  INDEX `idx_consent_token` (`consent_token`(255))
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Données de test (optionnel)
-- INSERT INTO `analytics` (`session_id`, `page_url`, `page_title`, `device`, `browser`, `operating_system`, `time_on_page`, `scroll_depth`, `is_bounce`, `visited_at`) 
-- VALUES 
-- ('test_session_1', '/formation', 'Nos formations', 'desktop', 'Chrome', 'Windows', 120, 75, 0, NOW()),
-- ('test_session_2', '/', 'Accueil', 'mobile', 'Safari', 'iOS', 30, 25, 1, NOW());

