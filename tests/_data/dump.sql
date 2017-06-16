-- MySQL dump 10.13  Distrib 5.5.24, for Win32 (x86)
--
-- Host: nmotion-server.pp.ciklum.com    Database: nmotion
-- ------------------------------------------------------
-- Server version	5.5.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `nmtn_asset`
--

DROP TABLE IF EXISTS `nmtn_asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mime_type` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `is_absolute_path` tinyint(1) NOT NULL,
  `size` int(11) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `md5` varchar(32) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_md5` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_config`
--

DROP TABLE IF EXISTS `nmtn_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `description` text,
  `system` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(25) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='General settings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_country`
--

DROP TABLE IF EXISTS `nmtn_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_log_entry`
--

DROP TABLE IF EXISTS `nmtn_log_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_log_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  `object_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` int(10) NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_class_lookup_idx` (`object_class`),
  KEY `log_date_lookup_idx` (`logged_at`),
  KEY `log_user_lookup_idx` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_meal`
--

DROP TABLE IF EXISTS `nmtn_meal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_meal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `menu_category_id` int(10) unsigned NOT NULL,
  `logo_asset_id` int(10) unsigned DEFAULT NULL,
  `thumb_logo_asset_id` int(10) unsigned DEFAULT NULL,
  `meal_option_default_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(14,4) unsigned NOT NULL DEFAULT '0.0000',
  `discount_percent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL,
  `time_from` mediumint(5) unsigned DEFAULT '0',
  `time_to` mediumint(5) unsigned DEFAULT '0',
  `position` tinyint(3) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meal_unique_name_in_category` (`menu_category_id`,`name`),
  KEY `restaurant_id` (`restaurant_id`),
  KEY `logo_asset_id` (`logo_asset_id`),
  KEY `fk_nmtn_restaurant_nmtn_thumb_logo_asset1` (`thumb_logo_asset_id`),
  KEY `meal_option_default_id` (`meal_option_default_id`),
  CONSTRAINT `fk_nmtn_restaurant_nmtn_thumb_logo_asset1` FOREIGN KEY (`thumb_logo_asset_id`) REFERENCES `nmtn_asset` (`id`),
  CONSTRAINT `nmtn_meal_ibfk_2` FOREIGN KEY (`menu_category_id`) REFERENCES `nmtn_menu_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nmtn_meal_ibfk_3` FOREIGN KEY (`logo_asset_id`) REFERENCES `nmtn_asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nmtn_meal_ibfk_4` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_meal_extra_ingredient`
--

DROP TABLE IF EXISTS `nmtn_meal_extra_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_meal_extra_ingredient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meal_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extra_name_in_meal_unique` (`meal_id`,`name`),
  CONSTRAINT `fk_restaurant_menu_meal_id` FOREIGN KEY (`meal_id`) REFERENCES `nmtn_meal` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_meal_option`
--

DROP TABLE IF EXISTS `nmtn_meal_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_meal_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meal_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(14,4) unsigned NOT NULL DEFAULT '0.0000',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meal_id` (`meal_id`),
  CONSTRAINT `nmtn_meal_option_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `nmtn_meal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_menu_category`
--

DROP TABLE IF EXISTS `nmtn_menu_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_menu_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `time_from` mediumint(5) unsigned DEFAULT '0',
  `time_to` mediumint(5) unsigned DEFAULT '0',
  `discount_percent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL,
  `position` tinyint(3) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `restaurant_menu_category_name_unique` (`restaurant_id`,`name`),
  KEY `fk_restaurant_menu_category_restaurant1_idx` (`restaurant_id`),
  CONSTRAINT `fk_menu_restaurant1` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_order`
--

DROP TABLE IF EXISTS `nmtn_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `master_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `restaurant_id` int(10) unsigned NOT NULL,
  `service_type_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = inHouse; 2 = takeaway; 3 = roomService',
  `table_number` varchar(255) NOT NULL,
  `order_status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `product_total` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Product total amount without TAX',
  `discount_percent` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount in percent',
  `discount` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Discount amount without TAX',
  `tax_percent` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'Tax in percent',
  `sales_tax` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Order TAX amount',
  `tips` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Tips',
  `order_total` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'Order total for payment',
  `takeaway_pickup_time` int(5) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_user1_idx` (`user_id`),
  KEY `fk_order_restaurant1_idx` (`restaurant_id`),
  KEY `order_status_id` (`order_status_id`),
  KEY `order_master_id_fk` (`master_id`),
  KEY `fk_order_restaurant_service_type1_idx` (`service_type_id`),
  CONSTRAINT `fk_order_restaurant1` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`),
  CONSTRAINT `fk_order_user1` FOREIGN KEY (`user_id`) REFERENCES `nmtn_user` (`id`),
  CONSTRAINT `nmtn_order_ibfk_1` FOREIGN KEY (`order_status_id`) REFERENCES `nmtn_order_status` (`id`),
  CONSTRAINT `nmtn_order_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `nmtn_restaurant_service_type` (`id`),
  CONSTRAINT `order_master_id_fk` FOREIGN KEY (`master_id`) REFERENCES `nmtn_order` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_order_meal`
--

DROP TABLE IF EXISTS `nmtn_order_meal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_order_meal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `discount_percent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `meal_option_price` decimal(14,2) unsigned DEFAULT NULL,
  `meal_option_name` varchar(255) DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `meal_id` int(10) unsigned DEFAULT NULL,
  `meal_option_id` int(10) unsigned DEFAULT NULL,
  `meal_comment` varchar(255) DEFAULT NULL,
  `quantity` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nmtn_order_meal_nmtn_order1_idx` (`order_id`),
  KEY `fk_nmtn_order_meal_nmtn_restaurant_menu_meal1_idx` (`meal_id`),
  KEY `fk_nmtn_order_meal_nmtn_meal_option1_idx` (`meal_option_id`),
  CONSTRAINT `fk_nmtn_order_meal_nmtn_meal_option1` FOREIGN KEY (`meal_option_id`) REFERENCES `nmtn_meal_option` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_order_meal_nmtn_order1` FOREIGN KEY (`order_id`) REFERENCES `nmtn_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_order_meal_nmtn_restaurant_menu_meal1` FOREIGN KEY (`meal_id`) REFERENCES `nmtn_meal` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_order_meal_extra_ingredient`
--

DROP TABLE IF EXISTS `nmtn_order_meal_extra_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_order_meal_extra_ingredient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `order_meal_id` int(10) unsigned NOT NULL,
  `meal_extra_ingredient_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nmtn_order_meal_extra_ingridient_nmtn_meal_extra_ingredi_idx` (`meal_extra_ingredient_id`),
  KEY `fk_nmtn_order_meal_extra_ingridient_nmtn_order_meal1_idx` (`order_meal_id`),
  CONSTRAINT `fk_nmtn_order_meal_extra_ingridient_nmtn_meal_extra_ingredient1` FOREIGN KEY (`meal_extra_ingredient_id`) REFERENCES `nmtn_meal_extra_ingredient` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_order_meal_extra_ingridient_nmtn_order_meal1` FOREIGN KEY (`order_meal_id`) REFERENCES `nmtn_order_meal` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_order_status`
--

DROP TABLE IF EXISTS `nmtn_order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_order_status` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_payment`
--

DROP TABLE IF EXISTS `nmtn_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned DEFAULT NULL COMMENT 'payment callback response parameter "orderid"',
  `status` varchar(36) DEFAULT NULL,
  `amount` decimal(14,2) unsigned DEFAULT NULL,
  `fee` decimal(14,2) unsigned DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `test` varchar(3) DEFAULT NULL,
  `transaction` varchar(20) DEFAULT NULL,
  `acquirer` varchar(20) DEFAULT NULL,
  `card_number_masked` varchar(50) DEFAULT NULL,
  `exp_month` varchar(2) DEFAULT NULL,
  `exp_year` varchar(2) DEFAULT NULL,
  `card_type_name` varchar(20) DEFAULT NULL,
  `merchant` varchar(50) DEFAULT NULL,
  `ticket` varchar(100) DEFAULT NULL,
  `all_parameters` text NOT NULL,
  `payment_comment` text,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `nmtn_payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `nmtn_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant`
--

DROP TABLE IF EXISTS `nmtn_restaurant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `admin_user_id` int(10) unsigned NOT NULL,
  `restaurant_address_id` int(10) unsigned NOT NULL,
  `facebook_place_id` varchar(32) DEFAULT NULL,
  `full_description` text NOT NULL,
  `logo_asset_id` int(10) unsigned DEFAULT NULL,
  `feedback_url` varchar(120) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `time_zone` varchar(100) DEFAULT 'Europe/Copenhagen',
  `check_out_time` int(5) unsigned NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_house` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `takeaway` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `room_service` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ta_member` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `email` varchar(250) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `site_url` varchar(120) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `contact_person_email` varchar(120) DEFAULT NULL,
  `contact_person_phone` varchar(40) DEFAULT NULL,
  `legal_entity` varchar(50) DEFAULT NULL,
  `invoicing_period` varchar(50) DEFAULT NULL,
  `vat_no` varchar(8) NOT NULL,
  `reg_no` varchar(4) NOT NULL,
  `konto_no` varchar(15) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_nmtn_restaurant_nmtn_user1_idx` (`admin_user_id`),
  UNIQUE KEY `fk_nmtn_restaurant_nmtn_restaurant_address1_idx` (`restaurant_address_id`),
  KEY `fk_nmtn_restaurant_nmtn_logo_asset1` (`logo_asset_id`),
  CONSTRAINT `fk_nmtn_restaurant_nmtn_logo_asset1` FOREIGN KEY (`logo_asset_id`) REFERENCES `nmtn_asset` (`id`),
  CONSTRAINT `fk_nmtn_restaurant_nmtn_restaurant_address1` FOREIGN KEY (`restaurant_address_id`) REFERENCES `nmtn_restaurant_address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_restaurant_nmtn_user1` FOREIGN KEY (`admin_user_id`) REFERENCES `nmtn_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant_address`
--

DROP TABLE IF EXISTS `nmtn_restaurant_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `latitude` double(9,6) DEFAULT NULL,
  `longitude` double(9,6) DEFAULT NULL,
  `address_line1` varchar(120) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nmtn_restaurant_address_nmtn_latitude_longitude1_idx` (`latitude`,`longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant_checkin`
--

DROP TABLE IF EXISTS `nmtn_restaurant_checkin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant_checkin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `service_type_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = inHouse; 2 = takeaway; 3 = roomService',
  `table_number` varchar(255) NOT NULL,
  `checked_out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nmtn_restaurant_checkin_nmtn_restaurant1_idx` (`restaurant_id`),
  KEY `fk_nmtn_restaurant_checkin_nmtn_user1_idx` (`user_id`),
  KEY `fk_nmtn_restaurant_checkin_nmtn_restaurant_service_type1_idx` (`service_type_id`),
  CONSTRAINT `fk_nmtn_restaurant_checkin_nmtn_restaurant1` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_restaurant_checkin_nmtn_user1` FOREIGN KEY (`user_id`) REFERENCES `nmtn_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `nmtn_restaurant_checkin_ibfk_1` FOREIGN KEY (`service_type_id`) REFERENCES `nmtn_restaurant_service_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant_operation_time`
--

DROP TABLE IF EXISTS `nmtn_restaurant_operation_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant_operation_time` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `day_of_the_week` tinyint(1) unsigned NOT NULL COMMENT 'ISO-8601 numeric representation of the day of the week; 1 (for Monday) through 7 (for Sunday); use WEEKDAY() + 1',
  `time_from` mediumint(5) unsigned DEFAULT '0',
  `time_to` mediumint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_nmtn_restaurant_operation_time_nmtn_restaurant1_idx` (`restaurant_id`),
  KEY `restaurant_operation_time` (`restaurant_id`,`day_of_the_week`,`time_from`,`time_to`),
  CONSTRAINT `fk_nmtn_restaurant_operation_time_nmtn_restaurant1` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant_service_type`
--

DROP TABLE IF EXISTS `nmtn_restaurant_service_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant_service_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_restaurant_staff`
--

DROP TABLE IF EXISTS `nmtn_restaurant_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_restaurant_staff` (
  `restaurant_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`restaurant_id`,`user_id`),
  KEY `fk_nmtn_user1_idx` (`user_id`),
  KEY `fk_nmtn_restaurant1_idx` (`restaurant_id`),
  CONSTRAINT `fk_nmtn_restaurant1` FOREIGN KEY (`restaurant_id`) REFERENCES `nmtn_restaurant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nmtn_user1` FOREIGN KEY (`user_id`) REFERENCES `nmtn_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_user`
--

DROP TABLE IF EXISTS `nmtn_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `username_canonical` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_canonical` varchar(255) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `registered` tinyint(1) unsigned NOT NULL,
  `registration_origin` enum('Nmotion','Facebook') DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `role` varchar(100) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_nmtn_user1_idx` (`username_canonical`),
  UNIQUE KEY `uniq_nmtn_user2_idx` (`email_canonical`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nmtn_user_device`
--

DROP TABLE IF EXISTS `nmtn_user_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmtn_user_device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_identity` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_identity` (`device_identity`),
  KEY `fk_nmtn_user_device_nmtn_user1_idx` (`user_id`),
  CONSTRAINT `fk_nmtn_user_device_nmtn_user1` FOREIGN KEY (`user_id`) REFERENCES `nmtn_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'nmotion'
--
/*!50003 DROP FUNCTION IF EXISTS `GEO_KM_DISTANCE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ALLOW_INVALID_DATES,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 FUNCTION `GEO_KM_DISTANCE`(
 a_latitude_1 double,
 a_longitude_1 double,
 a_latitude_2 double,
 a_longitude_2 double
 ) RETURNS double
    NO SQL
    DETERMINISTIC
    COMMENT 'Function returns distance in kilometers between two points each is set by (latitude, longitude):\n a_latitude_1 double  - first point  latitude\n a_longitude_1 double - first point  longitude\n a_latitude_2 double  - second point latitude\n a_longitude_2 double - second point longitude\n '
BEGIN
 DECLARE earch_radius_km int   DEFAULT 6371;
 RETURN earch_radius_km
 * acos(
 cos(radians(a_latitude_1)) * cos(radians(a_latitude_2)) * cos(radians(a_longitude_2) - radians(a_longitude_1))
 + sin(radians(a_latitude_1)) * sin(radians(a_latitude_2))
 );
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GEO_KM_TO_DEGREES` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ALLOW_INVALID_DATES,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 FUNCTION `GEO_KM_TO_DEGREES`(a_distance_km double) RETURNS double
    NO SQL
    DETERMINISTIC
    COMMENT 'Function returns equivalent degrees from distance is set in kilometers (see comment in the routine body):\n a_distance_km double distance in kilometers\n '
BEGIN
 RETURN a_distance_km / 111.12;
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `AGGREGATE_PAYMENTS` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ALLOW_INVALID_DATES,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `AGGREGATE_PAYMENTS`(
 IN period VARCHAR(2),
 IN restaurant int
 )
    READS SQL DATA
    COMMENT 'Function returnsaggregated payments for paricular restarant):\n IN period VARCHAR(2) period indicator: ''d'' for day, ''w'' for week, ''2w'' for 2 weeks, ''m'' for month and ''y'' for year\n IN restaurant int restaurant id\n '
BEGIN
 DECLARE group_by VARCHAR(200) DEFAULT 'year, month, week, day';
 IF period = 'w' THEN SET group_by = 'year, month, week';
 ELSEIF period = '2w' THEN SET group_by = 'year, month, week';
 ELSEIF period = 'm' THEN SET group_by = 'year, month';
 ELSEIF period = 'y' THEN SET group_by = 'year';
 END IF;
 SET @query = CONCAT("
 SELECT
 DATE_FORMAT(FROM_UNIXTIME(p.updated_at), '%Y') AS year,
 DATE_FORMAT(FROM_UNIXTIME(p.updated_at), '%m') AS month,
 IF (DATE_FORMAT(FROM_UNIXTIME(p.updated_at), '%u') = '00', '01',DATE_FORMAT(FROM_UNIXTIME(p.updated_at), '%u')) AS week,
 DATE_FORMAT(FROM_UNIXTIME(p.updated_at), '%d') AS day,
 SUM(o.product_total) AS productTotal,
 SUM(o.discount) AS discount,
 SUM(o.sales_tax) AS salesTax,
 SUM(o.order_total) AS orderTotal,
 SUM(IF(o.master_id IS NULL, 1, 0)) AS numberOfOrders,
 GROUP_CONCAT(IF(o.master_id IS NULL, o.id, NULL)) AS orderIds
 FROM nmtn_order o
 INNER JOIN
 (
 SELECT `order_id`, `updated_at`
 FROM `nmtn_payment`
 WHERE `id` IN
 (
 SELECT MAX(`id`)
 FROM `nmtn_payment`
 GROUP BY `order_id`
 )
 AND `status` = 'ACCEPTED'
 ) p ON (p.order_id = o.id OR p.order_id = o.master_id)
 WHERE o.restaurant_id = ", restaurant,"
 GROUP BY ", group_by,"
 ORDER BY p.updated_at DESC
 ");

 IF period = '2w' THEN
 SET @query = CONCAT("
 SELECT
 g.year AS year,
 g.month AS month,
 g.week AS week,
 CEIL(g.week/2) AS 2week,
 g.day AS day,
 SUM(g.productTotal) AS productTotal,
 SUM(g.discount) AS discount,
 SUM(g.salesTax) AS salesTax,
 SUM(g.orderTotal) AS orderTotal,
 SUM(g.numberOfOrders) AS numberOfOrders,
 GROUP_CONCAT(g.orderIds) AS orderIds
 FROM (", @query, ") AS g
 GROUP BY year DESC, 2week DESC
 ");
 END IF;

 PREPARE stmt FROM @query;
 EXECUTE stmt;

 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GEO_KM_NEARBY_RESTAURANTS` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ALLOW_INVALID_DATES,ERROR_FOR_DIVISION_BY_ZERO,TRADITIONAL,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 PROCEDURE `GEO_KM_NEARBY_RESTAURANTS`(
 IN a_name VARCHAR(200),
 IN a_latitude double,
 IN a_longitude double,
 IN a_distance_km double,
 IN a_limit int,
 IN a_offset int
 )
    READS SQL DATA
    COMMENT 'Function searches by name, meal''s name and description and returns nearby restaurants ids with distance\n to point is set by (latitude, longitude):\n IN a_name VARCHAR(200)   search text for restaurant''s name and meal''s name and description (used LIKE %{a_name}%)\n IN a_regexp VARCHAR(200) regexp for restaurant''s name and meal''s name and description\n IN a_latitude double     latitude  of the position\n IN a_longitude double    longitude of the position\n IN a_distance_km double  search radius (disntance) in kilometers\n IN a_limit int           LIMIT  (LIMIT of clause SELECT)\n IN a_offset int          OFFSET (LIMIT of clause SELECT)\n '
BEGIN
 DECLARE v_like VARCHAR(202) DEFAULT CONCAT('%', COALESCE(a_name, ''), '%');
 DECLARE v_radius_degrees DOUBLE DEFAULT GEO_KM_TO_DEGREES(a_distance_km);
 SELECT
 r.id,
 ROUND(GEO_KM_DISTANCE(a.latitude, a.longitude, a_latitude, a_longitude), 3) AS distance
 FROM nmtn_restaurant AS r
 JOIN nmtn_restaurant_address       AS a ON (r.restaurant_address_id = a.id)
 JOIN nmtn_menu_category AS c ON (r.id = c.restaurant_id)
 JOIN nmtn_meal     AS m ON (c.id = m.menu_category_id)
 WHERE
 a.latitude BETWEEN  a_latitude - v_radius_degrees AND a_latitude + v_radius_degrees
 AND a.longitude BETWEEN  a_longitude - v_radius_degrees AND a_longitude + v_radius_degrees
 AND r.visible AND c.visible AND m.visible
 AND (r.name LIKE v_like OR m.name LIKE v_like OR m.description LIKE v_like)
 GROUP BY r.id
 HAVING distance < a_distance_km
 ORDER BY distance, r.name
 LIMIT a_offset, a_limit;
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-16 12:22:53

INSERT INTO `nmtn_country`
  (`id`, `name`)
VALUES
  (1, 'Denmark'),
  (2, 'Sweden'),
  (3, 'Germany'),
  (4, 'Norway');

INSERT INTO `nmtn_config` (name, value, description, system, type, created_at, updated_at) VALUES
  ('android_app_url', 'https://play.google.com/store/apps/details?id=com.nmotion.android', 'Link to the app in the Google Play market', true, 'text', 1368801088, NULL),
  ('ios_app_url', 'http://itunes.com/apps/nmotion', 'Link to the app in the Apple store', true, 'text', 1368801110, NULL);

/* USER FIXTURES */

INSERT INTO `nmtn_user`
SET
  `username` = 'sshu.69@ciklum.com',
  `username_canonical` = 'sshu.69@ciklum.com',
  `email` = 'sshu.69@ciklum.com',
  `email_canonical` = 'sshu.69@ciklum.com',
  `first_name` = 'test',
  `last_name` = 'sshu.69',
  `registered` = 1,
  `password` = '22d7fe8c185003c98f97e5d6ced420c7',
  `role` = 'ROLE_SOLUTION_ADMIN',
  `roles` = 'a:3:{i:0;s:21:"ROLE_RESTAURANT_GUEST";i:1;s:21:"ROLE_RESTAURANT_ADMIN";i:2;s:19:"ROLE_SOLUTION_ADMIN";}',
  `enabled` = 1,
  `salt` = '',
  `locked` = 0,
  `expired` = 0,
  `credentials_expired` = 0;


INSERT INTO `nmtn_user`
SET
  `username` = 'bo.test.solution.admin@nmotion.pp.ciklum.com',
  `username_canonical` = 'bo.test.solution.admin@nmotion.pp.ciklum.com',
  `email` = 'bo.test.solution.admin@nmotion.pp.ciklum.com',
  `email_canonical` = 'bo.test.solution.admin@nmotion.pp.ciklum.com',
  `first_name` = 'solution',
  `last_name` = 'admin',
  `registered` = 1,
  `password` = '22d7fe8c185003c98f97e5d6ced420c7',
  `role` = 'ROLE_SOLUTION_ADMIN',
  `roles` = 'a:3:{i:0;s:21:"ROLE_RESTAURANT_GUEST";i:1;s:21:"ROLE_RESTAURANT_ADMIN";i:2;s:19:"ROLE_SOLUTION_ADMIN";}',
  `enabled` = 1,
  `salt` = '',
  `locked` = 0,
  `expired` = 0,
  `credentials_expired` = 0;

INSERT INTO `nmtn_order_status` (`id`, `name`, `description`)
VALUES
  (1, 'new order', 'new order, not sent to payment system'),
  (2, 'pending payment', 'order sent to payment system'),
  (3, 'paid', 'successful payment confirmed'),
  (4, 'failed', NULL),
  (5, 'cancelled', 'payment cancelled'),
  (6, 'sent to printer', 'order sent to mobile printer');

INSERT INTO `nmtn_config` (name, value, description, system, type, created_at, updated_at) VALUES
('sales_tax', '25', 'VAT in Denmark', true, 'integer', 1359642291, NULL),
('nmotion_discount', '5', 'Discount that is applied to all meals in all restaurants', true, 'integer', 1359642425, NULL),
('restaurant_search_radius', '2000', 'Default radius (in kms) for search of restaurants', true, 'integer', 1363882366, NULL);

INSERT INTO `nmtn_restaurant_service_type` (`id`, `name`, `description`) VALUES
(1, 'inHouse', 'ordinary ordering food sitting at a restaurant''s table'),
(2, 'takeaway', 'ability to pick up restaurant''s food'),
(3, 'roomService', 'ability to ship restaurant''s food to the hotel rooms');


-- VERY IMPORTANT! DO NOT REMOVE THIS!!!
set autocommit=1;
