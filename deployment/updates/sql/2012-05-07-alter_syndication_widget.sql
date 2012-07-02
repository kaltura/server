ALTER TABLE `syndication_feed` 
ADD COLUMN `privacy_context` VARCHAR(255),
ADD COLUMN `enforce_entitlement` TINYINT default 1;
