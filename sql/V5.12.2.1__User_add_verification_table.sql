
CREATE TABLE `user_verifications` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL DEFAULT '',
  `subject_class` varchar(50) DEFAULT '',
  `subject_id` int(11) NOT NULL DEFAULT 0,
  `expiry_count` int(11) NOT NULL DEFAULT 0,
  `expiry_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_dtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_id` mediumint(9) unsigned NOT NULL DEFAULT 0,
  `tenant` mediumint(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verification_code_unique_idx` (`tenant`,`subject_class`,`subject_id`, `code`),
  KEY `account_id_foreignkey_idx` (`account_id`),
  KEY `tenant_foreignkey_idx` (`tenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
