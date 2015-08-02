ALTER TABLE `metadata_profile` ADD `file_sync_version` int(11) AFTER `version`;
UPDATE metadata_profile mp SET mp.file_sync_version = mp.version;
