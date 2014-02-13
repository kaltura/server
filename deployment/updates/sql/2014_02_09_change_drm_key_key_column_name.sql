alter table `drm_key` change `key` `drm_key` VARCHAR(128)  NOT NULL;
alter table `drm_key add `parent_id` INTEGER AFTER `drm_key`;