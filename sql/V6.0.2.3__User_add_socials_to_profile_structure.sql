
ALTER TABLE `user_profiles` 
  ADD COLUMN `twitter` varchar(128) DEFAULT '' AFTER `public_email`,
  ADD COLUMN `linkedin` varchar(128) DEFAULT '' AFTER `public_email`,
  ADD COLUMN `facebook` varchar(128) DEFAULT '' AFTER `public_email`,
  ADD COLUMN `instagram` varchar(128) DEFAULT '' AFTER `public_email`;