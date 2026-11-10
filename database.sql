-- Schema for the login app. Import with phpMyAdmin or:
--   mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS `login`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `login`;

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName`  VARCHAR(100) NOT NULL,
  `lastName`   VARCHAR(100) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  -- bcrypt hashes are 60 characters; 255 leaves room for stronger algorithms later.
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB;

-- Upgrading a database created by the original md5 version of this app?
-- Run these instead of the CREATE TABLE above. Existing accounts keep
-- working: their md5 hashes are replaced with bcrypt the next time they
-- sign in.
--
--   ALTER TABLE `users` MODIFY `password` VARCHAR(255) NOT NULL;
--   ALTER TABLE `users` ADD UNIQUE KEY `users_email_unique` (`email`);
