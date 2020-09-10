
CREATE TABLE `user_oauth2_servers` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT '',
  `description` varchar(2048) DEFAULT '',
  `symbol` varchar(256) DEFAULT '',
  `client_id` varchar(256) NOT NULL DEFAULT '',
  `client_secret` varchar(1024) NOT NULL DEFAULT '',
  `meta` varchar(3000) DEFAULT '',
  `engine` varchar(64) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modif_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `oauth2server_engine_idx` (`tenant`,`engine`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_oauth2_connections` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL DEFAULT '',
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `server_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth2connection_account_server_idx` (`tenant`,`account_id`, `server_id`),
  UNIQUE KEY `oauth2connection_username_server_idx` (`tenant`,`username`, `server_id`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `server_id_foreignkey_idx` (`server_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
