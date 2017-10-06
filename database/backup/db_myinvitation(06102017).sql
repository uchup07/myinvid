/*
 Navicat Premium Data Transfer

 Source Server         : Localhost
 Source Server Type    : MySQL
 Source Server Version : 50635
 Source Host           : localhost
 Source Database       : db_myinvitation

 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : utf-8

 Date: 10/06/2017 15:04:43 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `activity_log`
-- ----------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `content_type` varchar(72) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `action` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `data` text COLLATE utf8mb4_unicode_ci,
  `language_key` tinyint(1) DEFAULT NULL,
  `public` tinyint(1) DEFAULT NULL,
  `developer` tinyint(1) DEFAULT NULL,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Records of `activity_log`
-- ----------------------------
BEGIN;
INSERT INTO `activity_log` VALUES ('1', '1', 'Setting', '1', 'Update', 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'meta_keywords\' in \'field list\' (SQL: update `settings` set `title` = asdasad, `updated_at` = 2017-10-05 09:35:13, `meta_keywords` = , `meta_description` =  where `id` = 1)', 'ada kesalahan pada saat Perubahan data', null, null, null, null, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:55.0) Gecko/20100101 Firefox/55.0', '2017-10-05 09:35:13', '2017-10-05 09:35:13');
COMMIT;

-- ----------------------------
--  Table structure for `bridesmaids`
-- ----------------------------
DROP TABLE IF EXISTS `bridesmaids`;
CREATE TABLE `bridesmaids` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `gender` char(1) DEFAULT NULL COMMENT 'P = PRIA, W = WANITA',
  `name` varchar(100) DEFAULT NULL,
  `file` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_bridesmaids_id` (`id`) USING BTREE,
  KEY `idx_website_id` (`website_id`) USING BTREE,
  FULLTEXT KEY `idx_content` (`gender`,`name`,`file`),
  CONSTRAINT `bridesmaids_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `bridesmaids`
-- ----------------------------
BEGIN;
INSERT INTO `bridesmaids` VALUES ('1', '1', '2', 'P', 'Yudi Hermawan', '1506871715-barind.jpg', '2017-10-01 15:28:35', '1', '2017-10-01 15:28:35', '1');
COMMIT;

-- ----------------------------
--  Table structure for `confirmation_types`
-- ----------------------------
DROP TABLE IF EXISTS `confirmation_types`;
CREATE TABLE `confirmation_types` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `is_active` int(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_confirmation_type` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `confirmation_types`
-- ----------------------------
BEGIN;
INSERT INTO `confirmation_types` VALUES ('1', 'Website', '0', '2017-08-20 22:32:10', null, null, null), ('2', 'SMS/Whatsapp', '0', '2017-08-20 22:32:28', null, null, null), ('3', 'Email', '0', '2017-08-20 22:32:39', null, null, null);
COMMIT;

-- ----------------------------
--  Table structure for `confirmations`
-- ----------------------------
DROP TABLE IF EXISTS `confirmations`;
CREATE TABLE `confirmations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `invoice_id` bigint(20) NOT NULL,
  `confirmation_type_id` int(1) NOT NULL DEFAULT '1',
  `bank_information` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `account_of_bank` varchar(25) DEFAULT NULL,
  `total` float(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `approved` int(1) DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`),
  KEY `user_id` (`user_id`),
  KEY `confirmation_type_id` (`confirmation_type_id`),
  KEY `approved` (`approved`),
  CONSTRAINT `confirmations_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `confirmations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `confirmations_ibfk_3` FOREIGN KEY (`confirmation_type_id`) REFERENCES `confirmation_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `confirmations_ibfk_4` FOREIGN KEY (`approved`) REFERENCES `status_approves` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `data_blashs`
-- ----------------------------
DROP TABLE IF EXISTS `data_blashs`;
CREATE TABLE `data_blashs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_data_id` (`id`) USING BTREE,
  KEY `idx_website_id` (`website_id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  FULLTEXT KEY `idx_content` (`name`,`email`,`mobile`),
  CONSTRAINT `data_blashs_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `data_blashs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `email_autoreminders`
-- ----------------------------
DROP TABLE IF EXISTS `email_autoreminders`;
CREATE TABLE `email_autoreminders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `reminder_id` int(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`),
  KEY `reminder_id` (`reminder_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `email_autoreminders_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `email_autoreminders_ibfk_2` FOREIGN KEY (`reminder_id`) REFERENCES `reminders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `email_autoreminders_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `email_formats`
-- ----------------------------
DROP TABLE IF EXISTS `email_formats`;
CREATE TABLE `email_formats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `attachment` text COMMENT 'link to media/email',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email_format_id` (`id`) USING BTREE,
  KEY `website_id` (`website_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `idx_content` (`title`,`content`,`attachment`),
  CONSTRAINT `email_formats_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `email_formats_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `gallerys`
-- ----------------------------
DROP TABLE IF EXISTS `gallerys`;
CREATE TABLE `gallerys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `file` text COMMENT 'Link to media/gallery',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gallery_id` (`id`) USING BTREE,
  KEY `website_id` (`website_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  FULLTEXT KEY `file` (`file`),
  CONSTRAINT `key_website_id` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `gallerys`
-- ----------------------------
BEGIN;
INSERT INTO `gallerys` VALUES ('15', '1', '2', '1506584114-20160225072514.jpg', '2017-09-28 07:35:14', '1', '2017-09-28 07:35:14', '1'), ('16', '1', '2', '1506869127-20160225072552.jpg', '2017-10-01 14:45:27', '1', '2017-10-01 14:45:27', '1');
COMMIT;

-- ----------------------------
--  Table structure for `invoice_details`
-- ----------------------------
DROP TABLE IF EXISTS `invoice_details`;
CREATE TABLE `invoice_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `price` float(10,0) DEFAULT NULL,
  `discount` decimal(3,2) DEFAULT NULL,
  `additional` float(10,0) DEFAULT NULL,
  `additional_note` varchar(255) DEFAULT NULL,
  `total` float(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoice_details_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `invoice_details`
-- ----------------------------
BEGIN;
INSERT INTO `invoice_details` VALUES ('1', '16', 'Template Lilac', '500000', '0.00', '0', '', '500000', '2017-10-06 04:27:52', '1', '2017-10-06 04:27:52', null);
COMMIT;

-- ----------------------------
--  Table structure for `invoices`
-- ----------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `invoice_number` varchar(25) DEFAULT NULL,
  `domain_info` int(1) DEFAULT NULL COMMENT '1 = Subdomain, 2 = Domain',
  `domain` varchar(100) DEFAULT NULL,
  `additional_price` float(15,0) DEFAULT NULL,
  `additional_note` varchar(255) DEFAULT NULL,
  `discount` decimal(3,2) DEFAULT NULL,
  `total` float(15,0) DEFAULT '0',
  `paid` enum('No','Yes') DEFAULT 'No' COMMENT 'Yes, No',
  `date_transaction` datetime DEFAULT NULL,
  `date_expired` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_invoice_id` (`id`) USING BTREE,
  KEY `idx_website_id` (`website_id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  FULLTEXT KEY `idx_invoice_number` (`invoice_number`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `invoices`
-- ----------------------------
BEGIN;
INSERT INTO `invoices` VALUES ('16', '1', '2', '1710060016', '1', 'http://ambarbarind.myinvitation.id', '0', null, '0.00', '500000', 'No', '2017-10-06 04:27:52', '2017-10-13 04:27:52', '2017-10-06 04:27:52', '1', '2017-10-06 04:27:52', '1');
COMMIT;

-- ----------------------------
--  Table structure for `menus`
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `menus`
-- ----------------------------
BEGIN;
INSERT INTO `menus` VALUES ('1', 'Admin', 'menu/show', 'menu-view', 'fa fa-cog', '0017-07-25 14:44:00', '0017-07-25 14:44:00'), ('2', 'Website', null, 'website-menu', 'fa fa-cloud', '2017-09-16 13:51:36', '2017-09-16 13:51:58'), ('3', 'Master', null, 'master-menu-view', 'glyphicon glyphicon-hdd', '2017-10-02 08:10:05', '2017-10-02 08:10:05'), ('4', 'Transaction', null, 'transaction-view', 'fa fa-th-large', '2017-10-05 11:08:39', '2017-10-05 11:08:39');
COMMIT;

-- ----------------------------
--  Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Records of `migrations`
-- ----------------------------
BEGIN;
INSERT INTO `migrations` VALUES ('1', '2017_08_20_161431_entrust_setup_tables', '1'), ('2', '2013_09_12_234559_create_activity_log_table', '2'), ('3', '2016_05_12_142519_alter_activity_log_table_add_additional_fields', '2');
COMMIT;

-- ----------------------------
--  Table structure for `permission_role`
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Records of `permission_role`
-- ----------------------------
BEGIN;
INSERT INTO `permission_role` VALUES ('1', '1'), ('2', '1'), ('3', '1'), ('4', '1'), ('5', '1'), ('6', '1'), ('7', '1'), ('8', '1'), ('9', '1'), ('10', '1'), ('11', '1'), ('12', '1'), ('13', '1'), ('14', '1'), ('15', '1'), ('16', '1'), ('17', '1'), ('18', '1'), ('19', '1'), ('20', '1'), ('21', '1'), ('22', '1'), ('23', '1'), ('24', '1'), ('25', '1'), ('26', '1'), ('27', '1'), ('28', '1'), ('29', '1'), ('30', '1'), ('31', '1'), ('32', '1'), ('33', '1'), ('34', '1'), ('22', '2'), ('23', '2'), ('30', '2'), ('33', '2'), ('34', '2');
COMMIT;

-- ----------------------------
--  Table structure for `permissions`
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Records of `permissions`
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES ('1', 'permission-add', 'Permission Add', 'Access to add Permission', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('2', 'permission-edit', 'Permission Edit', 'access to edit Permission', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('3', 'permission-delete', 'Permission Delete', 'Access to delete Permission', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('4', 'permission-view', 'Permission View', 'Access to view Permission form', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('5', 'role-view', 'Role View', 'Access to view Role Page', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('6', 'role-add', 'Role Add', 'Access to add Role', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('7', 'role-edit', 'Role Edit', 'Access to edit Role', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('8', 'role-delete', 'Role Delete', 'Access to delete Role', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('9', 'menu-view', 'Menu View', 'Access to View Page Menu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('10', 'menu-add', 'Menu Add', 'Access to form add menu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('11', 'menu-edit', 'Menu Edit', 'Access to Edit Menu Form', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('12', 'menu-delete', 'Menu Delete', 'Access to Delete Menu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('13', 'submenu-view', 'Submenu View', 'Access to View Submenu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('14', 'submenu-add', 'Submenu Add', 'Access to Add Form Submenu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('15', 'submenu-edit', 'Submenu Edit', 'Access to Edit Submenu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('16', 'submenu-delete', 'Submenu Delete', 'Access to Delete Submenu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('17', 'user-view', 'Access to View User Menu', 'Access to View User Menu', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0'), ('18', 'thirdmenu-view', 'Thirdmenu View', 'Access to view Thirdmenu', '2017-08-21 14:17:29', '2017-08-21 14:17:55', '0'), ('19', 'thirdmenu-add', 'Thirdmenu Add', 'Access to Add New Thirdmenu', '2017-08-21 14:18:25', '2017-08-22 04:08:32', '18'), ('20', 'thirdmenu-edit', 'Thirdmenu Edit', 'Access to Edit Thirdmenu', '2017-08-21 14:18:56', '2017-08-22 04:08:51', '18'), ('21', 'thirdmenu-delete', 'Thirdmenu Delete', 'Access to Delete Thirdmenu', '2017-08-21 14:19:32', '2017-08-22 04:09:14', '18'), ('22', 'website-menu', 'Website Menu', 'Website Menu', '2017-09-16 13:11:42', '2017-09-16 13:11:42', '0'), ('23', 'website-createnew', 'Create New Website', 'Create New Website', '2017-09-16 13:14:52', '2017-09-16 13:14:52', '22'), ('24', 'master-menu-view', 'Menu Master', 'Menu Master', '2017-10-02 08:07:31', '2017-10-02 08:07:31', '0'), ('25', 'template-view', 'Template View', 'Template View', '2017-10-02 08:07:52', '2017-10-02 08:07:52', '0'), ('26', 'template-add', 'Add Template', 'Add Template', '2017-10-02 08:08:20', '2017-10-02 08:08:20', '25'), ('27', 'template-edit', 'Edit Template', 'Edit Template', '2017-10-02 08:08:40', '2017-10-02 08:08:40', '25'), ('28', 'template-inactive', 'Inactive Template', 'Inactive Template', '2017-10-02 08:09:00', '2017-10-02 08:09:00', '25'), ('29', 'template-delete', 'Delete Template', 'Delete Template', '2017-10-02 08:09:18', '2017-10-02 08:09:18', '25'), ('30', 'website-manage', 'Manage Website', 'Manage Website', '2017-10-04 07:35:08', '2017-10-04 07:36:59', '22'), ('31', 'setting-view', 'Setting Project', 'Setting Project', '2017-10-05 09:11:24', '2017-10-05 09:11:24', '0'), ('32', 'setting-edit', 'Edit Setting Project', 'Edit Setting Project', '2017-10-05 09:11:50', '2017-10-05 09:11:50', '31'), ('33', 'transaction-view', 'Transaction Menu', 'Transaction Menu', '2017-10-05 11:05:32', '2017-10-05 11:05:32', '0'), ('34', 'invoice-show', 'Invoice List', 'Invoice List', '2017-10-05 11:10:06', '2017-10-05 11:10:06', '33');
COMMIT;

-- ----------------------------
--  Table structure for `reminders`
-- ----------------------------
DROP TABLE IF EXISTS `reminders`;
CREATE TABLE `reminders` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) DEFAULT NULL,
  `day` int(6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_reminder_id` (`id`) USING BTREE,
  FULLTEXT KEY `idsx_content_reminder` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `role_user`
-- ----------------------------
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `user_id` bigint(20) NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  KEY `role_id_2` (`role_id`),
  CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `role_user`
-- ----------------------------
BEGIN;
INSERT INTO `role_user` VALUES ('1', '1'), ('4', '2');
COMMIT;

-- ----------------------------
--  Table structure for `roles`
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
--  Records of `roles`
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES ('1', 'admin', 'Admin', 'Admin', '2017-08-21 13:47:15', '2017-08-21 13:47:17'), ('2', 'member', 'Member', 'Member Account', '2017-10-04 10:37:19', '2017-10-04 10:37:19');
COMMIT;

-- ----------------------------
--  Table structure for `settings`
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(75) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `logo` text,
  `icon` text,
  `misi` text,
  `visi` text,
  `phone` varchar(25) DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `whatsapp` varchar(25) DEFAULT NULL,
  `facebook` text,
  `twitter` text,
  `instagram` text,
  `address` text,
  `linkedin` text,
  `googleplus` text,
  `email` varchar(75) DEFAULT NULL,
  `developer` varchar(150) DEFAULT NULL,
  `location` text,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `google_analytic` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `creted_by` int(6) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `settings`
-- ----------------------------
BEGIN;
INSERT INTO `settings` VALUES ('1', 'MyInvitation', 'myinvitation.id', 'wedding, online, invitation', 'Wedding Online Invitation', 'wedding, online, invitation', 'Wedding Online Invitation', '1507202689-Logo-315X315.png', '1507197823-Logo-126-x-100.png', '<p>Tessting</p>', '<p>Tessting</p>', '0251 8312163', '+6281908884313', '+6281908884313', '1', '2', '3', '<p>Bogor Baru B VII No. 7<br />\r\nBogor</p>', null, '4', 'barindra1988@gmail.com', null, 'Jalan Bogor Baru Utara, Tegallega, Bogor City, West Java, Indonesia', '-6.33000800', '106.81302700', '<script>\r\n  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){\r\n  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\r\n  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\r\n  })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');\r\n\r\n  ga(\'create\', \'UA-106002769-1\', \'auto\');\r\n  ga(\'send\', \'pageview\');\r\n\r\n</script>', null, null, '2017-10-05 11:24:49', null);
COMMIT;

-- ----------------------------
--  Table structure for `status_approves`
-- ----------------------------
DROP TABLE IF EXISTS `status_approves`;
CREATE TABLE `status_approves` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  `is_active` int(1) DEFAULT NULL COMMENT '0 = active, 1 = inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_status_approve_id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `status_approves`
-- ----------------------------
BEGIN;
INSERT INTO `status_approves` VALUES ('1', 'Pending', '0', '2017-08-20 22:30:33', null, null, null), ('2', 'Approved', '0', '2017-08-20 22:31:00', null, null, null), ('3', 'Rejected', '0', '2017-08-20 22:31:12', null, null, null);
COMMIT;

-- ----------------------------
--  Table structure for `story_of_loves`
-- ----------------------------
DROP TABLE IF EXISTS `story_of_loves`;
CREATE TABLE `story_of_loves` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `website_id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `file` text,
  `video` text,
  `date_story` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_storyoflove_id` (`id`) USING BTREE,
  KEY `website_id` (`website_id`),
  KEY `idx_user_id` (`user_id`) USING BTREE,
  FULLTEXT KEY `idx_content` (`title`,`description`,`file`,`video`),
  CONSTRAINT `story_of_loves_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `story_of_loves`
-- ----------------------------
BEGIN;
INSERT INTO `story_of_loves` VALUES ('6', '1', '2', 'Pertemuan', 'Bertemu Di starbucks Bidakara', '1506704803-20160225072514.jpg', 'Tesss', '2017-03-22', '2017-09-29 08:45:52', '1', '2017-09-29 17:07:14', '1'), ('7', '1', '2', 'Pertemuan kedua', 'Bertemu untuk makan malam', null, null, '2017-09-20', '2017-09-29 17:18:43', '1', '2017-09-29 17:18:43', '1');
COMMIT;

-- ----------------------------
--  Table structure for `submenus`
-- ----------------------------
DROP TABLE IF EXISTS `submenus`;
CREATE TABLE `submenus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `submenus`
-- ----------------------------
BEGIN;
INSERT INTO `submenus` VALUES ('1', null, 'Add New', 'subemnu/add', 'fa fa-folder-open', '', 'submenu-add', '0017-07-25 14:56:00', '2017-08-21 15:10:46'), ('2', '1', 'Role', 'role/show', 'fa fa-cogs', '', 'role-view', '0017-07-25 14:58:00', '0017-07-25 14:58:00'), ('3', '1', 'Permission', 'permission/show', 'fa fa-unlock-alt', '', 'permission-view', '0017-07-25 15:32:00', '0017-07-25 15:32:00'), ('4', '1', 'Submenu', 'submenu/show', 'fa fa-folder-open', '', 'submenu-view', '0017-07-25 15:34:00', '0017-07-25 15:34:00'), ('5', '1', 'Thirdmenu', 'thirdmenu/show', 'fa fa-folder-open', null, 'thirdmenu-view', '2017-08-21 14:20:32', '2017-08-21 14:20:32'), ('6', '1', 'Menu', 'menu/show', 'fa fa-folder-open', null, 'menu-view', '2017-09-16 20:41:35', '2017-09-16 20:41:37'), ('7', '2', 'Create', 'website/create', 'fa fa-plus-square', null, 'website-createnew', '2017-09-16 13:53:15', '2017-09-16 13:53:15'), ('8', '3', 'Template', 'template/show', 'icon-screen-desktop', null, 'template-view', '2017-10-02 08:11:08', '2017-10-02 08:11:08'), ('9', '2', 'Manage', 'website/manage/show', 'fa fa-exchange', null, 'website-manage', '2017-10-04 07:35:59', '2017-10-04 09:50:32'), ('10', '1', 'Setting', 'setting/show', 'fa fa-cog', null, 'setting-view', '2017-10-05 09:12:17', '2017-10-05 09:12:17'), ('11', '4', 'Invoice List', 'invoice/show', 'fa fa-file', null, 'invoice-show', '2017-10-05 11:12:47', '2017-10-05 11:12:47');
COMMIT;

-- ----------------------------
--  Table structure for `templates`
-- ----------------------------
DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `demo_url` text,
  `price_up` float(10,0) DEFAULT NULL,
  `price` float(10,0) DEFAULT NULL,
  `preview_desktop` text,
  `preview_tablet` text,
  `preview_mobile` text,
  `is_active` int(1) DEFAULT NULL COMMENT '0 = active, 1 = inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(6) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_template_id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `templates`
-- ----------------------------
BEGIN;
INSERT INTO `templates` VALUES ('2', 'Lilac', 'lilac', null, '350000', '500000', '1506959199-Desktop.png', '1506959199-Tablet.png', '1506959199-Mobile.png', '1', '2017-10-02 15:46:39', '1', '2017-10-02 15:46:39', '1');
COMMIT;

-- ----------------------------
--  Table structure for `thirdmenus`
-- ----------------------------
DROP TABLE IF EXISTS `thirdmenus`;
CREATE TABLE `thirdmenus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) DEFAULT NULL,
  `submenu_id` int(11) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `thirdmenus`
-- ----------------------------
BEGIN;
INSERT INTO `thirdmenus` VALUES ('1', '1', '4', 'Add', 'subemnu/add', 'fa fa-folder-open', null, 'submenu-add', '2017-08-21 15:08:14', '2017-08-21 15:11:56');
COMMIT;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `date_of_bird` date DEFAULT NULL,
  `place_of_bird` varchar(150) DEFAULT NULL,
  `address` text,
  `city` varchar(150) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email_token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(150) DEFAULT NULL,
  `is_active` varchar(1) DEFAULT NULL COMMENT '0 = inactive, 1 active',
  `last_login` datetime DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'link to media/users',
  `wallet` float(15,0) DEFAULT NULL,
  `wallet_date` datetime DEFAULT NULL,
  `wallet_realtime` float(15,0) DEFAULT NULL,
  `wallet_update` datetime DEFAULT NULL,
  `up` int(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_id` (`id`) USING BTREE,
  KEY `idx_wallet` (`wallet_realtime`,`wallet_update`) USING BTREE,
  KEY `idx_up` (`up`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `users`
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'Barindra', 'barindra1988@gmail.com', '$2y$10$TXTSsJhhNW1U61.15Y59RejS5J1utOSawNvmteDS8S3f/4HpMb26S', '1988-07-19', 'Bogor', 'Bogor Baru B VII No. 7<br>', 'BOGOR', null, null, 'lJHz2bzUeqiI6fT4HYyOKCi9mM9DzMMLkh0rx3EZrNWUDH5y8oKFzK0yvHgj', null, null, '', null, null, null, null, null, null, '2017-08-20 16:11:28', null, '2017-08-22 16:33:00'), ('4', 'Barind testing', 'testing.website1988@gmail.com', '$2y$10$Yu5b8FKfYh8MgEdCiXQmjOdMXyuCfOBigJNnt7UaZ6W9j0xcwG5Qe', null, null, null, null, null, 'dGVzdGluZy53ZWJzaXRlMTk4OEBnbWFpbC5jb20=', null, '1', null, null, '0', '2017-10-04 10:43:31', '0', '2017-10-04 10:43:31', '0', null, '2017-10-04 10:43:31', null, '2017-10-04 10:43:31');
COMMIT;

-- ----------------------------
--  Table structure for `verified_codes`
-- ----------------------------
DROP TABLE IF EXISTS `verified_codes`;
CREATE TABLE `verified_codes` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) DEFAULT NULL,
  `code` varchar(4) DEFAULT NULL,
  `expired_code` datetime DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(6) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_code` (`id`) USING BTREE,
  KEY `mobile` (`mobile`) USING BTREE,
  KEY `id_user` (`id`) USING BTREE,
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `ode` (`code`),
  CONSTRAINT `key_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `verified_codes`
-- ----------------------------
BEGIN;
INSERT INTO `verified_codes` VALUES ('1', '081908884313', '3884', '1183-08-24 15:03:59', '1', '2017-08-24 16:08:03', '1', '2017-08-24 16:08:03', '1'), ('2', '081908884313', '7135', '2094-08-24 15:03:59', '1', '2017-08-24 16:23:14', '1', '2017-08-24 16:23:14', '1'), ('3', '081908884313', '5242', '2176-08-24 15:03:59', '1', '2017-08-24 16:24:36', '1', '2017-08-24 16:24:36', '1'), ('4', '081908884313', '1833', '2276-08-24 15:03:59', '1', '2017-08-24 16:26:16', '1', '2017-08-24 16:26:16', '1'), ('5', '081908884313', '5025', '2828-08-24 15:03:59', '1', '2017-08-24 16:35:28', '1', '2017-08-24 16:35:28', '1'), ('6', '081908884313', '1095', '3278-08-24 15:03:59', '1', '2017-08-24 16:42:58', '1', '2017-08-24 16:42:58', '1'), ('7', '081908884313', '7078', '3359-08-24 15:03:59', '1', '2017-08-24 16:44:19', '1', '2017-08-24 16:44:19', '1'), ('8', '081908884313', '5096', '3390-08-24 15:03:59', '1', '2017-08-24 16:44:50', '1', '2017-08-24 16:44:50', '1'), ('9', '081908884313', '4280', '3429-08-24 15:03:59', '1', '2017-08-24 16:45:29', '1', '2017-08-24 16:45:29', '1'), ('10', '081908884313', '2709', '3445-08-24 15:03:59', '1', '2017-08-24 16:45:45', '1', '2017-08-24 16:45:45', '1'), ('11', '081908884313', '3487', '3507-08-24 15:03:59', '1', '2017-08-24 16:46:47', '1', '2017-08-24 16:46:47', '1'), ('12', '081908884313', '7434', '3636-08-24 15:03:59', '1', '2017-08-24 16:48:56', '1', '2017-08-24 16:48:56', '1'), ('13', '081908884313', '7118', '3682-08-24 15:03:59', '1', '2017-08-24 16:49:42', '1', '2017-08-24 16:49:42', '1');
COMMIT;

-- ----------------------------
--  Table structure for `websites`
-- ----------------------------
DROP TABLE IF EXISTS `websites`;
CREATE TABLE `websites` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `boy_name` varchar(150) DEFAULT NULL,
  `boy_nick_name` varchar(6) DEFAULT NULL,
  `boy_email` varchar(150) DEFAULT NULL,
  `boy_mobile` varchar(15) DEFAULT NULL,
  `boy_dob` date DEFAULT NULL,
  `boy_description` text,
  `boy_image` text COMMENT 'Link to media/website',
  `boy_facebook_url` text,
  `boy_twitter_url` text,
  `boy_instagram_url` text,
  `girl_name` varchar(150) DEFAULT NULL,
  `girl_nick_name` varchar(6) DEFAULT NULL,
  `girl_email` varchar(150) DEFAULT NULL,
  `girl_mobile` varchar(15) DEFAULT NULL,
  `girl_dob` date DEFAULT NULL,
  `girl_description` text,
  `girl_image` text,
  `girl_facebook_url` text,
  `girl_twitter_url` text,
  `girl_instagram_url` text,
  `ceremony_start` datetime DEFAULT NULL,
  `ceremony_end` datetime DEFAULT NULL,
  `ceremony_place` varchar(150) DEFAULT NULL,
  `ceremony_address` varchar(255) DEFAULT NULL,
  `ceremony_googlemaps` text,
  `ceremony_waze` text,
  `wedding_start` datetime DEFAULT NULL,
  `wedding_end` datetime DEFAULT NULL,
  `wedding_place` varchar(150) DEFAULT NULL,
  `wedding_address` varchar(255) DEFAULT NULL,
  `wedding_googlemaps` text,
  `wedding_waze` text,
  `template_id` int(6) DEFAULT NULL,
  `domain` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `is_active` int(1) DEFAULT '0' COMMENT '0 = inactive, 1 = active',
  `is_active_date` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `inactive` int(11) DEFAULT '0' COMMENT '0 = active, 1 = inactive',
  `inactive_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_website_id` (`id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  KEY `idx_template_id` (`template_id`) USING BTREE,
  CONSTRAINT `key_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `websites`
-- ----------------------------
BEGIN;
INSERT INTO `websites` VALUES ('1', '1', 'Barindra Maslo', 'Barin', 'barindra1988@gmail.com', '+6281908884313', '1988-07-19', 'Testing Barind', null, '123', '456', '789', 'Ambar Rita Swastika', 'ambar', 'ambar.swastika@gmail.com', '+6285779005817', '1988-01-13', 'Testing Ambar', null, '1234', '654', '321', '2018-02-27 15:00:00', '2018-02-27 16:00:00', 'Anjungan Jawa Tengah', 'Taman Mini', '123', '3241', '2018-02-27 19:05:00', '2018-02-27 21:05:00', 'Anjungan jawa Tengha', 'Taman Mini', '987', '654', null, null, '2017-09-27 08:35:49', '1', '2017-09-27 08:35:49', '1', null, null, null, null, null), ('2', '1', 'Barindra Maslo', 'barin', 'barindra1988@gmail.com', '+6281908884313', '1988-07-19', 'Testing barind', '1506502509-couple2.JPG', '123', '456', '789', 'Ambar Rita Swastika', 'ambar', 'ambar.swastika@gmail.com', '+6285779005817', '1988-02-13', 'Testing ambar', '1506502509-Ambar.JPG', '123', '12314', '1231231', '2018-02-27 14:55:00', '2018-02-27 15:55:00', 'Anjungan jawa tengah', 'TMII', '1231', '123131', '2018-02-27 15:55:00', '2018-02-27 15:55:00', 'Anjungan jawa tengah', 'TMII', '123', '1232131', '2', 'ambarbarind', '2017-09-27 08:55:09', '1', '2017-10-05 03:41:40', '1', '0', null, null, '0', null);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
