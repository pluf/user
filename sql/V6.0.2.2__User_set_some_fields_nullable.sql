
ALTER TABLE `user_profiles` CHANGE `last_name` `last_name` varchar(100) DEFAULT '';

ALTER TABLE `user_emails` CHANGE `type` `type` varchar(64) DEFAULT '';
ALTER TABLE `user_emails` CHANGE `is_verified` `is_verified` tinyint(1) DEFAULT 0;

ALTER TABLE `user_phones` CHANGE `type` `type` varchar(64) DEFAULT '';
ALTER TABLE `user_phones` CHANGE `is_verified` `is_verified` tinyint(1) DEFAULT 0;

ALTER TABLE `user_addresses` CHANGE `type` `type` varchar(64) DEFAULT '';
ALTER TABLE `user_addresses` CHANGE `is_verified` `is_verified` tinyint(1) DEFAULT 0;