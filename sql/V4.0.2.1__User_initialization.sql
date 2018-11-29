
CREATE TABLE `user_account_user_group_assoc` (
  `user_account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `user_group_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_account_id`,`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_account_user_role_assoc` (
  `user_account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `user_role_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_account_id`,`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_accounts` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `date_joined` datetime DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_unique_idx` (`tenant`,`login`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_avatars` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `fileName` varchar(150) NOT NULL DEFAULT '',
  `filePath` varchar(150) NOT NULL DEFAULT '',
  `fileSize` int(11) NOT NULL DEFAULT 0,
  `mimeType` varchar(50) NOT NULL DEFAULT '',
  `creationTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id_unique_idx` (`tenant`,`account_id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_credentials` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(150) NOT NULL DEFAULT '',
  `expiry_count` int(11) NOT NULL DEFAULT 0,
  `expiry_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_group_user_role_assoc` (
  `user_group_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `user_role_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_group_id`,`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_groups` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_messages` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `message` longtext NOT NULL,
  `creation_dtime` datetime DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `message_user_idx` (`tenant`,`account_id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_profiles` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `public_email` varchar(150) DEFAULT '',
  `language` varchar(5) DEFAULT 'fa',
  `timezone` varchar(45) DEFAULT 'Asia/Tehran',
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_roles` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `application` varchar(150) NOT NULL DEFAULT '',
  `code_name` varchar(100) NOT NULL DEFAULT '',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `perme_idx` (`tenant`,`application`,`code_name`),
  KEY `code_name_idx` (`tenant`,`code_name`),
  KEY `application_idx` (`tenant`,`application`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_space` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `space_data` longtext NOT NULL,
  `user` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_idx` (`tenant`,`user`),
  KEY `user_foreignkey_idx` (`user`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_tokens` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(150) NOT NULL DEFAULT '',
  `agent` varchar(100) NOT NULL DEFAULT '',
  `agent_address` varchar(250) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `expiry_count` int(11) NOT NULL DEFAULT 0,
  `expiry_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_unique_idx` (`tenant`,`token`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

