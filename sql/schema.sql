-- =====================================================================
-- SLEEVE - Personal Vinyl Record Archive + Jacket Design Portfolio
-- Schema definition
--
-- Engine: InnoDB
-- Charset: utf8mb4 / utf8mb4_unicode_ci
-- Target: MySQL 5.7+ (InfinityFree compatible)
--
-- Constraints (hosting):
--   - No views, stored procedures, or triggers
--   - All FKs use ON DELETE CASCADE for child rows
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `design_images`;
DROP TABLE IF EXISTS `designs`;
DROP TABLE IF EXISTS `album_moods`;
DROP TABLE IF EXISTS `tracks`;
DROP TABLE IF EXISTS `guide_posts`;
DROP TABLE IF EXISTS `albums`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- albums
-- ---------------------------------------------------------------------
CREATE TABLE `albums` (
  `id`            INT             NOT NULL AUTO_INCREMENT,
  `slug`          VARCHAR(255)    NOT NULL,
  `title`         VARCHAR(255)    NOT NULL,
  `artist`        VARCHAR(255)    NOT NULL,
  `release_year`  YEAR            DEFAULT NULL,
  `format`        ENUM('Vinyl','CD') NOT NULL DEFAULT 'Vinyl',
  `genre`         VARCHAR(100)    DEFAULT NULL,
  `cover_path`    VARCHAR(500)    DEFAULT NULL,
  `note`          TEXT            DEFAULT NULL,
  `is_limited`    TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_albums_slug` (`slug`),
  KEY `idx_albums_genre` (`genre`),
  KEY `idx_albums_format` (`format`),
  KEY `idx_albums_release_year` (`release_year`),
  KEY `idx_albums_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- tracks
-- ---------------------------------------------------------------------
CREATE TABLE `tracks` (
  `id`        INT              NOT NULL AUTO_INCREMENT,
  `album_id`  INT              NOT NULL,
  `position`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `title`     VARCHAR(255)     NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tracks_album_id` (`album_id`),
  KEY `idx_tracks_album_position` (`album_id`, `position`),
  CONSTRAINT `fk_tracks_album`
    FOREIGN KEY (`album_id`) REFERENCES `albums`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- album_moods  (many-to-many style, mood is a free-text tag)
-- ---------------------------------------------------------------------
CREATE TABLE `album_moods` (
  `album_id` INT          NOT NULL,
  `mood`     VARCHAR(100) NOT NULL,
  PRIMARY KEY (`album_id`, `mood`),
  KEY `idx_album_moods_mood` (`mood`),
  CONSTRAINT `fk_album_moods_album`
    FOREIGN KEY (`album_id`) REFERENCES `albums`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- designs
-- ---------------------------------------------------------------------
CREATE TABLE `designs` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `slug`       VARCHAR(255) NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `tools`      VARCHAR(500) DEFAULT NULL,
  `thumb_path` VARCHAR(500) DEFAULT NULL,
  `intent`     TEXT         DEFAULT NULL,
  `process`    TEXT         DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_designs_slug` (`slug`),
  KEY `idx_designs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- design_images
-- ---------------------------------------------------------------------
CREATE TABLE `design_images` (
  `id`         INT              NOT NULL AUTO_INCREMENT,
  `design_id`  INT              NOT NULL,
  `image_path` VARCHAR(500)     NOT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_design_images_design_id` (`design_id`),
  KEY `idx_design_images_sort` (`design_id`, `sort_order`),
  CONSTRAINT `fk_design_images_design`
    FOREIGN KEY (`design_id`) REFERENCES `designs`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- guide_posts
-- ---------------------------------------------------------------------
CREATE TABLE `guide_posts` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `slug`       VARCHAR(255) NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `category`   VARCHAR(100) DEFAULT NULL,
  `body`       TEXT         DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_guide_posts_slug` (`slug`),
  KEY `idx_guide_posts_category` (`category`),
  KEY `idx_guide_posts_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
