
CREATE TABLE `user_emails` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL DEFAULT '',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_email_unique_idx` (`email`,`account_id`,`tenant`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_phones` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(32) NOT NULL,
  `type` varchar(64) NOT NULL DEFAULT '',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_phone_unique_idx` (`phone`,`account_id`,`tenant`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_addresses` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(64) DEFAULT '',
  `province` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  `address` varchar(512) DEFAULT '',
  `postal_code` varchar(16) DEFAULT '',
  `location` geometry DEFAULT NULL,
  `type` varchar(64) NOT NULL DEFAULT '',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

