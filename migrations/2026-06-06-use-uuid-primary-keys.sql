SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `career_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `latest_education` varchar(255) DEFAULT NULL,
  `experience_summary` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `cv_original_name` varchar(255) DEFAULT NULL,
  `cv_mime_type` varchar(255) DEFAULT NULL,
  `cv_size` int(11) DEFAULT NULL,
  `cv_storage_key` varchar(255) DEFAULT NULL,
  `cv_bucket` varchar(255) DEFAULT NULL,
  `cv_url` varchar(255) DEFAULT NULL,
  `cv_signed_url_strategy` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `_uuid_admin_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `admins`;
CREATE TABLE `_uuid_admin_session_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `admin_sessions`;
CREATE TABLE `_uuid_category_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `categories`;
CREATE TABLE `_uuid_news_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `news`;
CREATE TABLE `_uuid_campaign_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `campaigns`;
CREATE TABLE `_uuid_banner_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `hero_banners`;
CREATE TABLE `_uuid_expertise_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `expertises`;
CREATE TABLE `_uuid_client_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `clients`;
CREATE TABLE `_uuid_career_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `careers`;
CREATE TABLE `_uuid_product_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `products`;
CREATE TABLE `_uuid_work_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `works`;
CREATE TABLE `_uuid_contact_message_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `contact_messages`;
CREATE TABLE `_uuid_company_profile_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `company_profiles`;
CREATE TABLE `_uuid_career_application_map` AS SELECT `id` AS `old_id`, UUID() AS `new_id` FROM `career_applications`;

ALTER TABLE `admins` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `admins` t JOIN `_uuid_admin_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `admin_sessions` ADD COLUMN `uuid_id` char(36) NULL, ADD COLUMN `uuid_adminId` char(36) NULL;
UPDATE `admin_sessions` t JOIN `_uuid_admin_session_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;
UPDATE `admin_sessions` t JOIN `_uuid_admin_map` m ON t.`adminId` = m.`old_id` SET t.`uuid_adminId` = m.`new_id`;

ALTER TABLE `categories` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `categories` t JOIN `_uuid_category_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `news` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `news` t JOIN `_uuid_news_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `campaigns` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `campaigns` t JOIN `_uuid_campaign_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `hero_banners` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `hero_banners` t JOIN `_uuid_banner_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `expertises` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `expertises` t JOIN `_uuid_expertise_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `clients` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `clients` t JOIN `_uuid_client_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `careers` ADD COLUMN `uuid_id` char(36) NULL, ADD COLUMN `uuid_categoryId` char(36) NULL;
UPDATE `careers` t JOIN `_uuid_career_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;
UPDATE `careers` t JOIN `_uuid_category_map` m ON t.`categoryId` = m.`old_id` SET t.`uuid_categoryId` = m.`new_id`;

ALTER TABLE `products` ADD COLUMN `uuid_id` char(36) NULL, ADD COLUMN `uuid_categoryId` char(36) NULL;
UPDATE `products` t JOIN `_uuid_product_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;
UPDATE `products` t JOIN `_uuid_category_map` m ON t.`categoryId` = m.`old_id` SET t.`uuid_categoryId` = m.`new_id`;

ALTER TABLE `works` ADD COLUMN `uuid_id` char(36) NULL, ADD COLUMN `uuid_categoryId` char(36) NULL;
UPDATE `works` t JOIN `_uuid_work_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;
UPDATE `works` t JOIN `_uuid_category_map` m ON t.`categoryId` = m.`old_id` SET t.`uuid_categoryId` = m.`new_id`;

ALTER TABLE `contact_messages` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `contact_messages` t JOIN `_uuid_contact_message_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `company_profiles` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `company_profiles` t JOIN `_uuid_company_profile_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `career_applications` ADD COLUMN `uuid_id` char(36) NULL;
UPDATE `career_applications` t JOIN `_uuid_career_application_map` m ON t.`id` = m.`old_id` SET t.`uuid_id` = m.`new_id`;

ALTER TABLE `admin_sessions` DROP FOREIGN KEY `admin_sessions_admin_fk`;
ALTER TABLE `careers` DROP FOREIGN KEY `careers_category_fk`;
ALTER TABLE `products` DROP FOREIGN KEY `products_category_fk`;
ALTER TABLE `works` DROP FOREIGN KEY `works_category_fk`;

DROP INDEX `admin_sessions_admin_id_idx` ON `admin_sessions`;
DROP INDEX `careers_category_created_idx` ON `careers`;
DROP INDEX `products_category_created_idx` ON `products`;
DROP INDEX `works_category_created_idx` ON `works`;

ALTER TABLE `admins` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `admins` DROP PRIMARY KEY;
ALTER TABLE `admins` DROP COLUMN `id`;
ALTER TABLE `admins` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `admins` ADD PRIMARY KEY (`id`);

ALTER TABLE `admin_sessions` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `admin_sessions` DROP PRIMARY KEY;
ALTER TABLE `admin_sessions` DROP COLUMN `id`;
ALTER TABLE `admin_sessions` DROP COLUMN `adminId`;
ALTER TABLE `admin_sessions` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `admin_sessions` CHANGE COLUMN `uuid_adminId` `adminId` char(36) NOT NULL;
ALTER TABLE `admin_sessions` ADD PRIMARY KEY (`id`);
CREATE INDEX `admin_sessions_admin_id_idx` ON `admin_sessions` (`adminId`);
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_admin_fk`
  FOREIGN KEY (`adminId`) REFERENCES `admins` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categories` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `categories` DROP PRIMARY KEY;
ALTER TABLE `categories` DROP COLUMN `id`;
ALTER TABLE `categories` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `categories` ADD PRIMARY KEY (`id`);

ALTER TABLE `news` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `news` DROP PRIMARY KEY;
ALTER TABLE `news` DROP COLUMN `id`;
ALTER TABLE `news` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `news` ADD PRIMARY KEY (`id`);

ALTER TABLE `campaigns` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `campaigns` DROP PRIMARY KEY;
ALTER TABLE `campaigns` DROP COLUMN `id`;
ALTER TABLE `campaigns` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `campaigns` ADD PRIMARY KEY (`id`);

ALTER TABLE `hero_banners` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `hero_banners` DROP PRIMARY KEY;
ALTER TABLE `hero_banners` DROP COLUMN `id`;
ALTER TABLE `hero_banners` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `hero_banners` ADD PRIMARY KEY (`id`);

ALTER TABLE `expertises` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `expertises` DROP PRIMARY KEY;
ALTER TABLE `expertises` DROP COLUMN `id`;
ALTER TABLE `expertises` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `expertises` ADD PRIMARY KEY (`id`);

ALTER TABLE `clients` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `clients` DROP PRIMARY KEY;
ALTER TABLE `clients` DROP COLUMN `id`;
ALTER TABLE `clients` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `clients` ADD PRIMARY KEY (`id`);

ALTER TABLE `careers` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `careers` DROP PRIMARY KEY;
ALTER TABLE `careers` DROP COLUMN `id`;
ALTER TABLE `careers` DROP COLUMN `categoryId`;
ALTER TABLE `careers` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `careers` CHANGE COLUMN `uuid_categoryId` `categoryId` char(36) NULL;
ALTER TABLE `careers` ADD PRIMARY KEY (`id`);
CREATE INDEX `careers_category_created_idx` ON `careers` (`categoryId`, `createdAt`);
ALTER TABLE `careers`
  ADD CONSTRAINT `careers_category_fk`
  FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `products` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `products` DROP PRIMARY KEY;
ALTER TABLE `products` DROP COLUMN `id`;
ALTER TABLE `products` DROP COLUMN `categoryId`;
ALTER TABLE `products` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `products` CHANGE COLUMN `uuid_categoryId` `categoryId` char(36) NULL;
ALTER TABLE `products` ADD PRIMARY KEY (`id`);
CREATE INDEX `products_category_created_idx` ON `products` (`categoryId`, `createdAt`);
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_fk`
  FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `works` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `works` DROP PRIMARY KEY;
ALTER TABLE `works` DROP COLUMN `id`;
ALTER TABLE `works` DROP COLUMN `categoryId`;
ALTER TABLE `works` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `works` CHANGE COLUMN `uuid_categoryId` `categoryId` char(36) NULL;
ALTER TABLE `works` ADD PRIMARY KEY (`id`);
CREATE INDEX `works_category_created_idx` ON `works` (`categoryId`, `createdAt`);
ALTER TABLE `works`
  ADD CONSTRAINT `works_category_fk`
  FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `contact_messages` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `contact_messages` DROP PRIMARY KEY;
ALTER TABLE `contact_messages` DROP COLUMN `id`;
ALTER TABLE `contact_messages` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `contact_messages` ADD PRIMARY KEY (`id`);

ALTER TABLE `company_profiles` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `company_profiles` DROP PRIMARY KEY;
ALTER TABLE `company_profiles` DROP COLUMN `id`;
ALTER TABLE `company_profiles` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `company_profiles` ADD PRIMARY KEY (`id`);

ALTER TABLE `career_applications` MODIFY `id` int(11) NOT NULL;
ALTER TABLE `career_applications` DROP PRIMARY KEY;
ALTER TABLE `career_applications` DROP COLUMN `id`;
ALTER TABLE `career_applications` CHANGE COLUMN `uuid_id` `id` char(36) NOT NULL;
ALTER TABLE `career_applications` ADD PRIMARY KEY (`id`);

DROP TABLE `_uuid_admin_map`;
DROP TABLE `_uuid_admin_session_map`;
DROP TABLE `_uuid_category_map`;
DROP TABLE `_uuid_news_map`;
DROP TABLE `_uuid_campaign_map`;
DROP TABLE `_uuid_banner_map`;
DROP TABLE `_uuid_expertise_map`;
DROP TABLE `_uuid_client_map`;
DROP TABLE `_uuid_career_map`;
DROP TABLE `_uuid_product_map`;
DROP TABLE `_uuid_work_map`;
DROP TABLE `_uuid_contact_message_map`;
DROP TABLE `_uuid_company_profile_map`;
DROP TABLE `_uuid_career_application_map`;

SET FOREIGN_KEY_CHECKS = 1;
