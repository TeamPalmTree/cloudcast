<?php defined('COREPATH') or exit('No direct script access allowed'); ?>

WARNING - 2013-04-15 05:10:30 --> Fuel\Core\Fuel::init - The configured locale en_US is not installed on your system.
WARNING - 2013-04-15 18:07:37 --> Fuel\Core\Fuel::init - The configured locale en_US is not installed on your system.
ERROR - 2013-04-15 18:07:37 --> Error - SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint with query: "CREATE TABLE IF NOT EXISTS `shows` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`start_on` timestamp NOT NULL,
	`duration` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`description` varchar(255) NULL,
	`block_id` int(11) NOT NULL,
	PRIMARY KEY `id` (`id`), 
	FOREIGN KEY (`block_id`) REFERENCES `blocks` (`id`) ON DELETE set null
) DEFAULT CHARACTER SET utf8;" in E:\Users\Alexander\Documents\GDM Radio\Projects\CloudCast\fuel\core\classes\database\pdo\connection.php on line 175
WARNING - 2013-04-15 18:09:57 --> Fuel\Core\Fuel::init - The configured locale en_US is not installed on your system.
