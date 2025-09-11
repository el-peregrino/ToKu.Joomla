-- Sequences
CREATE TABLE IF NOT EXISTS `#__sequences` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `alias` VARCHAR(255) NOT NULL DEFAULT '',
    `access` INT UNSIGNED NOT NULL DEFAULT 1,
    `published` TINYINT(1) NOT NULL DEFAULT 1,
    `type` TINYINT(1) NOT NULL,
    `header` TEXT NOT NULL,
    `footer` TEXT NOT NULL,
    `language` CHAR(7) NOT NULL DEFAULT '*',
    `note` VARCHAR(255) NULL,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT UNSIGNED NOT NULL DEFAULT 0,
    `modified` DATETIME DEFAULT NULL,
    `modified_by` INT UNSIGNED DEFAULT NULL,
    `images` TEXT NOT NULL,
    `params` TEXT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_language` (`language`),
    KEY `idx_published` (`published`),
    KEY `idx_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Sequence Items
CREATE TABLE IF NOT EXISTS `#__sequence_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sequence_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NULL,
    `caption` VARCHAR(255) NULL,
    `heading` VARCHAR(255) NOT NULL,
    `subheading` VARCHAR(255) NULL,
    `body` TEXT NULL,
    `footer` TEXT NULL,
    `links` TEXT NOT NULL,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `access` INT UNSIGNED NOT NULL DEFAULT 1,
    `published` TINYINT(1) NOT NULL DEFAULT 1,
    `note` VARCHAR(255) NULL,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT UNSIGNED NOT NULL DEFAULT 0,
    `modified` DATETIME DEFAULT NULL,
    `modified_by` INT UNSIGNED DEFAULT NULL,
    `ordering` INT UNSIGNED NOT NULL DEFAULT 0,
    `images` TEXT NOT NULL,
    `params` TEXT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sequence_id` (`sequence_id`),
    KEY `idx_published` (`published`),
    KEY `idx_access` (`access`),
    CONSTRAINT `fk_sequence_items`
        FOREIGN KEY (`sequence_id`) REFERENCES `#__sequences`(`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;