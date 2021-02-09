
ALTER TABLE `user_credentials` CHANGE `expiry_count` `expiry_count` int(11) DEFAULT '0';
ALTER TABLE `user_credentials` CHANGE `expiry_dtime` `expiry_dtime` datetime DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `user_credentials` CHANGE `is_deleted` `is_deleted` tinyint(1) DEFAULT '0';
