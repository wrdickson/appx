DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` text NOT NULL,
  `permission` int(11) NOT NULL,
  `roles` json NOT NULL,
  `email` varchar(144) NOT NULL,
  `registered` datetime NOT NULL,
  `last_activity` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(144) NOT NULL,
  `first_name` varchar(144) NOT NULL,
  `email` varchar(144) NOT NULL DEFAULT '',
  `phone` varchar(45) NOT NULL DEFAULT '',
  `address_1` varchar(144) NOT NULL DEFAULT '',
  `address_2` varchar(144) NOT NULL DEFAULT '',
  `city` varchar(144) NOT NULL DEFAULT '',
  `region` varchar(144) NOT NULL DEFAULT '',
  `country` varchar(144) NOT NULL DEFAULT '',
  `postal_code` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customers` (`id`, `last_name`, `first_name`, `email`, `phone`, `address_1`, `address_2`, `city`, `region`, `country`, `postal_code`) VALUES
(1,	'House Account',	'',	'',	'',	'',	'',	'',	'',	'',	''),
(2,	'Hiker',	'Harry',	'',	'',	'123',	'',	'',	'',	'',	''),
(3,	'Biker',	'Dazeda',	'asdf@something.net',	'509 389 2462',	'123 S Main St',	'Apt 323',	'Moab',	'UT',	'USA',	'84532'),
(4,	'Climber',	'Carl',	'',	'',	'',	'',	'',	'',	'',	''),
(6,	'Bazooka',	'Bobby',	'dsfasda',	'123412',	'PO Box 123',	'',	'asdfasd',	'6686796',	'tuitiuy',	'vcxnxbcv'),
(7,	'Cunningham',	'Carl',	'',	'',	'',	'',	'',	'',	'',	''),
(8,	'Schumacher',	'Shenanigans',	'',	'',	'',	'',	'',	'',	'',	''),
(9,	'Dickson',	'Bill',	'',	'',	'1213 S Hyw 1913',	'',	'Moab',	'UT',	'',	'84532'),
(11,	'Wallbanger',	'Harvey',	'',	'',	'',	'',	'',	'',	'',	''),
(12,	'Dormer',	'Huge',	'',	'',	'',	'',	'',	'',	'',	''),
(13,	'Bentley',	'Scott',	'',	'',	'1213 S Hwy 191',	'',	'',	'',	'',	''),
(14,	'Ottinger',	'Bill',	'',	'',	'',	'',	'',	'',	'',	''),
(15,	'Kane',	'',	'',	'',	'',	'',	'',	'',	'',	''),
(16,	'Materno',	'Jose',	'',	'',	'',	'',	'',	'',	'',	''),
(18,	'Fernandez',	'Felecia',	'',	'',	'',	'',	'',	'',	'',	''),
(21,	'Jimenez',	'Jorge',	'jorge@trekbill.com',	'55 11 22 33 44 55',	'123 Calle b',	'Apartmiento 4',	'San Pedro La Laguna',	'Solala',	'Guatemala',	'abd 6 he 5'),
(22,	'Baloney',	'Bangus',	'',	'',	'',	'',	'',	'',	'',	''),
(23,	'Crew',	'J',	'aa@bb.com',	'12341234',	'',	'',	'',	'',	'',	''),
(24,	'Escobar',	'Pablo',	'aa@bb.com',	'12341234',	'',	'',	'',	'',	'',	''),
(25,	'Burke',	'P',	'',	'',	'',	'',	'',	'',	'',	''),
(26,	'Uuru',	'U',	'',	'',	'',	'',	'',	'',	'',	''),
(27,	'Baloney',	'Biggus',	'bg@ll.com',	'12341234',	'',	'',	'',	'',	'',	''),
(28,	'AAkerman',	'AAron',	'',	'',	'',	'',	'',	'',	'',	''),
(30,	'Springer',	'Clive',	'asdf@asdf.com',	'12341234',	'',	'',	'',	'',	'',	''),
(31,	'Gillespie',	'Happy',	'',	'',	'',	'',	'',	'',	'',	''),
(32,	'Turbid',	'Truly',	'',	'',	'',	'',	'',	'',	'',	''),
(33,	'Beleagered',	'Badly',	'',	'',	'',	'',	'',	'',	'',	''),
(34,	'Eggregious',	'Enormously',	'',	'',	'',	'',	'',	'',	'',	''),
(35,	'Cautions',	'Cathy',	'',	'',	'',	'',	'',	'',	'',	''),
(37,	'Escobar',	'Enrique',	'',	'',	'',	'',	'',	'',	'',	''),
(38,	'Greeley',	'Horace',	'',	'',	'',	'',	'',	'',	'',	''),
(39,	'Hillsborough',	'Winema',	'',	'',	'',	'',	'',	'',	'',	''),
(40,	'Bazooka',	'Yesslin',	'',	'',	'',	'',	'',	'',	'',	''),
(41,	'Monster',	'Ukia',	'',	'',	'',	'',	'',	'',	'',	''),
(42,	'Benson',	'George',	'',	'',	'',	'',	'',	'',	'',	''),
(43,	'Tuesday',	'Tricia',	'',	'',	'',	'',	'',	'',	'',	''),
(80,	'Zalento',	'Zaforia',	'zaput@zippy.com',	'123412345',	'123 S Main',	'Apt 2',	'Zaputnik',	'Zamoria',	'Zambia',	'Z1234'),
(81,	'Bojangels',	'Barney',	'',	'',	'',	'',	'',	'',	'',	''),
(82,	'Darwin',	'Charles',	'',	'',	'',	'',	'',	'',	'',	''),
(83,	'Jefe',	'El',	'fu@yado.com',	'123456',	'',	'',	'',	'',	'',	''),
(84,	'Hatch',	'Hiker',	'hh@trekbill.com',	'',	'',	'',	'',	'',	'',	''),
(85,	'Cinnamon',	'Cindy',	'',	'',	'',	'',	'',	'',	'',	''),
(86,	'Violencia',	'Victoria',	'',	'',	'',	'',	'',	'',	'',	''),
(87,	'Drala',	'Darla',	'',	'',	'',	'',	'',	'',	'',	''),
(92,	'Zatty',	'Kris',	'',	'',	'',	'',	'',	'',	'',	''),
(93,	'Jonathon',	'Casper',	'',	'',	'',	'',	'',	'',	'',	''),
(94,	'Bins',	'Jabari',	'',	'',	'',	'',	'',	'',	'',	''),
(95,	'Marquardt',	'Consuelo',	'',	'',	'',	'',	'',	'',	'',	''),
(96,	'Oberbrunner',	'Beryl',	'',	'',	'',	'',	'',	'',	'',	''),
(97,	'Heidenreich',	'Mercedes',	'',	'',	'',	'',	'',	'',	'',	''),
(98,	'Lesch',	'Carlee',	'',	'',	'',	'',	'',	'',	'',	''),
(99,	'Murphy',	'Emery',	'',	'',	'',	'',	'',	'',	'',	''),
(100,	'Beatty',	'Marlon',	'',	'',	'',	'',	'',	'',	'',	''),
(101,	'jones',	'joe',	'',	'',	'',	'',	'',	'',	'',	''),
(102,	'hackman',	'harry',	'',	'',	'',	'',	'',	'',	'',	''),
(103,	'uhips',	'ursula',	'',	'',	'',	'',	'',	'',	'',	''),
(104,	'Cox',	'Michail',	'',	'',	'',	'',	'',	'',	'',	''),
(105,	'schomot',	'joe',	'',	'',	'',	'',	'',	'',	'',	''),
(106,	'Mason',	'Mulligan',	'',	'',	'',	'',	'',	'',	'',	''),
(108,	'Baxoo',	'Boingo',	'',	'',	'',	'',	'',	'',	'',	''),
(109,	'Hulligan',	'Harry',	'',	'',	'',	'',	'',	'',	'',	''),
(111,	'Rogers',	'Doug',	'',	'',	'',	'',	'',	'',	'',	''),
(112,	'Hawkins',	'Stephen',	'',	'',	'',	'',	'',	'',	'',	''),
(113,	'Bazooka',	'Barney',	'',	'',	'',	'',	'',	'',	'',	''),
(114,	'Cahoonga',	'Carly',	'',	'',	'',	'',	'',	'',	'',	''),
(115,	'Schmulderhulnd',	'Schwancy',	'',	'',	'',	'',	'',	'',	'',	'');

DROP TABLE IF EXISTS `folios`;
CREATE TABLE `folios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` int(11) NOT NULL,
  PRIMARY KEY (`id`,`customer`),
  KEY `customer` (`customer`),
  CONSTRAINT `folios_ibfk_1` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(144) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `autoload` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `space_types`;
CREATE TABLE `space_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `display_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `space_types` (`id`, `title`, `is_active`, `display_order`) VALUES
(1,	'Dorm Bed',	1,	10),
(2,	'Room',	1,	20),
(3,	'Cabin',	1,	30),
(4,	'Camping',	1,	40),
(5,	'House',	1,	50),
(6,	'No Preference',	1,	1000);

DROP TABLE IF EXISTS `root_spaces`;
CREATE TABLE `root_spaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `space_type` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `child_of` int(11) NOT NULL,
  `show_children` tinyint(4) NOT NULL,
  `people` int(11) NOT NULL,
  `beds` int(11) NOT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `is_unassigned` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `space_type` (`space_type`),
  CONSTRAINT `root_spaces_ibfk_1` FOREIGN KEY (`space_type`) REFERENCES `space_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` int(11) NOT NULL,
  `is_assigned` tinyint(4) NOT NULL,
  `space_type_pref` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  `space_code` json NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `people` int(11) NOT NULL,
  `beds` int(11) NOT NULL,
  `history` json NOT NULL,
  `status` int(11) NOT NULL,
  `notes` json NOT NULL,
  PRIMARY KEY (`id`,`folio`),
  KEY `folio` (`folio`),
  KEY `space_id` (`space_id`),
  KEY `space_type_pref` (`space_type_pref`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`folio`) REFERENCES `folios` (`id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`space_id`) REFERENCES `root_spaces` (`id`),
  CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`space_type_pref`) REFERENCES `space_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `root_spaces` (`id`, `space_type`, `title`, `child_of`, `show_children`, `people`, `beds`, `display_order`, `is_active`, `is_unassigned`) VALUES
(1,	2,	'1 - Room',	0,	1,	4,	4,	100,	1,	0),
(35,	5,	'Castle',	0,	1,	0,	0,	1000,	1,	0),
(40,	2,	'Castle - Room 1',	35,	0,	4,	4,	1005,	1,	0),
(42,	1,	'Castle - 1 - Bed 2',	40,	1,	1,	1,	1020,	1,	0),
(43,	1,	'Castle - 1 - Bed 3',	40,	1,	0,	0,	1030,	1,	0),
(44,	1,	'Castle - 1 - Bed 4',	40,	1,	0,	0,	1040,	1,	0),
(45,	2,	'Castle - Room 2',	35,	1,	0,	0,	1200,	1,	0),
(51,	2,	'3 - Room',	0,	0,	0,	0,	301,	1,	0),
(54,	2,	'Castle - Room 3',	35,	0,	0,	0,	1300,	1,	0),
(58,	2,	'Castle- Room 4',	35,	1,	0,	0,	1400,	1,	0),
(68,	2,	'Castle - Room 5',	35,	1,	4,	2,	1500,	1,	0),
(71,	2,	'2 - Room',	0,	1,	4,	2,	200,	1,	0),
(86,	1,	'Castle 5 - Bed 1',	68,	1,	1,	1,	1530,	1,	0),
(87,	1,	'Castle 5 - Bed 2',	68,	0,	1,	1,	1540,	1,	0),
(88,	1,	'Castle 5 - Bed 3',	68,	0,	1,	1,	1550,	1,	0),
(90,	5,	'Not Assigned',	0,	0,	10,	10,	1,	1,	1),
(100,	1,	'Castle - 1 -  Bed 1',	40,	0,	1,	1,	1010,	1,	0),
(101,	3,	'Land Yacht',	0,	0,	2,	1,	1600,	1,	0);

DROP TABLE IF EXISTS `pos_payment_type`;
CREATE TABLE `pos_payment_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `pos_payment_type` (`id`, `payment_title`, `is_active`) VALUES
(1,	'Cash',	1),
(2,	'Credit Card',	1);

DROP TABLE IF EXISTS `pos_tax_group`;
CREATE TABLE `pos_tax_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(244) NOT NULL,
  `tax_types` json NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pos_product_group`;
CREATE TABLE `pos_product_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_product_subgroup`;
CREATE TABLE `pos_product_subgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `subgroup_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_product_subgroup` (`group_id`),
  CONSTRAINT `pos_product_subgroup_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `pos_product_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_product`;
CREATE TABLE `pos_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_subgroup` int(11) NOT NULL,
  `product_code` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` int(11) NOT NULL,
  `tax_group` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_code` (`product_code`),
  KEY `product_subgroup` (`product_subgroup`),
  KEY `pos_product` (`tax_group`),
  CONSTRAINT `pos_product_ibfk_1` FOREIGN KEY (`tax_group`) REFERENCES `pos_tax_group` (`id`),
  CONSTRAINT `pos_product_ibfk_2` FOREIGN KEY (`product_subgroup`) REFERENCES `pos_product_subgroup` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_sale`;
CREATE TABLE `pos_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` int(11) NOT NULL,
  `sold_by` int(11) NOT NULL,
  `date_posted` datetime NOT NULL,
  `sale_subtotal` int(11) NOT NULL,
  `sale_tax` int(11) NOT NULL,
  `sale_total` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `folio` (`folio`),
  KEY `sold_by` (`sold_by`),
  CONSTRAINT `pos_sale_ibfk_1` FOREIGN KEY (`folio`) REFERENCES `folios` (`id`),
  CONSTRAINT `pos_sale_ibfk_2` FOREIGN KEY (`sold_by`) REFERENCES `accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_sale_item`;
CREATE TABLE `pos_sale_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_sold` int(11) NOT NULL,
  `price_per_unit` int(11) NOT NULL,
  `item_subtotal` int(11) NOT NULL,
  `item_tax_amount` int(11) NOT NULL,
  `item_tax_spread` json NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_item` (`sale_id`),
  KEY `pos_sale_item` (`product_id`),
  CONSTRAINT `pos_sale_item_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pos_sale` (`id`),
  CONSTRAINT `pos_sale_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `pos_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_payment`;
CREATE TABLE `pos_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `payment_amount` int(11) NOT NULL,
  `payment_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `payment_type` (`payment_type`),
  CONSTRAINT `pos_payment_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pos_sale` (`id`),
  CONSTRAINT `pos_payment_ibfk_2` FOREIGN KEY (`payment_type`) REFERENCES `pos_payment_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pos_tax_type`;
CREATE TABLE `pos_tax_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(244) NOT NULL,
  `rate` float NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
