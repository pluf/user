
ALTER TABLE `user_profiles` 
  ADD COLUMN `birthday` date AFTER `timezone`,
  ADD COLUMN `weight` decimal(32,8) DEFAULT 0.0 AFTER `timezone`,
  ADD COLUMN `gender` varchar(16) DEFAULT '' AFTER `timezone`,
  ADD COLUMN `national_code` varchar(32) DEFAULT '' AFTER `timezone`;