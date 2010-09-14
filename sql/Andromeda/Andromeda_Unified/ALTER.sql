use kaltura;

#-----------------------------------------------------------------------------
#-- ui_conf
#-----------------------------------------------------------------------------
#updates_2009-08-10_uiconf_version.sql
ALTER TABLE kaltura.ui_conf add column `version` varchar(10);

#-----------------------------------------------------------------------------
#-- batch_job
#-----------------------------------------------------------------------------
#updates_2009-08-17_batch_job_dc.sql
#updates_2009-10-22_batch_tables.sql
#updates_2009-08-17_batch_job_dc.sql
#-----------------------------------------------------------------------------
#-- ALTER TABLE  kaltura.batch_job DROP INDEX  `status_job_type_index`;
#-- ALTER TABLE  kaltura.batch_job DROP INDEX  `created_at_job_type_status_index`;
#-- ALTER TABLE  kaltura.batch_job DROP INDEX  `partner_type_index`;
#-- ALTER TABLE  `batch_job` ADD  `on_stress_divert_to` INT NOT NULL DEFAULT  '0' AFTER  `err_number`;
#-----------------------------------------------------------------------------
# add index for partner & type - appears many time in the slow-query log
#updates_2009-10-28_batch_tables.sql
#updates_2009-11-09_batch_job.sql
#updates_2009-12-07_batch_job_err.sql
#updates_2009-12-10_batch_job.sql
#updates_2009-12-14_batch_job.sql
#updates_2009-12-29_abtch_job_divert.sql
ALTER TABLE  kaltura.batch_job 
  ADD COLUMN dc VARCHAR(2),
  ADD  `created_by` VARCHAR( 20 ) NOT NULL AFTER  `created_at`,
  ADD  `updated_by` VARCHAR( 20 ) NOT NULL AFTER  `updated_at`,
  ADD  `priority` TINYINT NOT NULL AFTER  `updated_by`,
  ADD  `work_group_id` INTEGER NOT NULL AFTER  `priority`,
  ADD  `queue_time` DATETIME NULL DEFAULT NULL AFTER  `work_group_id`,
  ADD  `finish_time` DATETIME NULL DEFAULT NULL AFTER  `queue_time`,
  ADD  `twin_job_id` INTEGER NULL DEFAULT NULL AFTER  `lock_version`,
  ADD  `bulk_job_id` INTEGER NULL DEFAULT NULL AFTER  `twin_job_id`,
  ADD  `root_job_id` INTEGER NULL DEFAULT NULL AFTER  `bulk_job_id`,
  ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `subp_id` ,
  ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id` ,
  ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id`,
  ADD `duplication_key` VARCHAR( 41 ) NOT NULL AFTER `data`,
  ADD `err_type` INTEGER default 0 NOT NULL AFTER `dc`,
  ADD `err_number` INTEGER default 0 NOT NULL AFTER `err_type`,
  ADD `last_scheduler_id` INT default NULL AFTER `batch_index`, 
  ADD `last_worker_id` INT default NULL AFTER `last_scheduler_id`,
  ADD `last_worker_remote` INT default 0 AFTER `last_worker_id`,
  ADD `file_size` INT default NULL AFTER `data`,
  ADD `deleted_at` DATETIME AFTER `updated_by`,
  ADD `on_stress_divert_to` INT NOT NULL DEFAULT  '0' AFTER  `err_number`,
  ADD INDEX `partner_id_index` (`partner_id`),
  ADD INDEX `work_group_id_index_priority` (`work_group_id`,  `priority`),
  ADD INDEX `twin_job_id_index` (`twin_job_id`),
  ADD INDEX `bulk_job_id_index` (`bulk_job_id`), 
  ADD INDEX `root_job_id_index` (`root_job_id`),
  ADD INDEX `parent_job_id_index` (`parent_job_id`);
#  ADD INDEX `partner_type_index` (partner_id,job_type);

#-----------------------------------------------------------------------------
#-- notification
#-----------------------------------------------------------------------------
#updates_2009-08-17_batch_job_dc.sql
#updates_2009-10-28_batch_tables.sql
ALTER TABLE kaltura.notification 
  ADD COLUMN dc VARCHAR(2),
  ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `object_type` ,
  ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id` ,
  ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id` ;


#-----------------------------------------------------------------------------
#-- mail_job
#-----------------------------------------------------------------------------
#updates_2009-08-17_batch_job_dc.sql
#updates_2009-10-28_batch_tables.sql
ALTER TABLE kaltura.mail_job 
  ADD COLUMN dc VARCHAR(2),
  ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `min_send_date`,
  ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id`,
  ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id`;


#-----------------------------------------------------------------------------
#-- entry
#-----------------------------------------------------------------------------
#updates_2009-09-30_access_control_id_on_entry.sql
#updates_2009-10-09-entry_schedule.sql
#ALTER TABLE kaltura.entry ADD COLUMN `entry_schedule_id` INTEGER;
#updates_2009-10-13-conversion_profile_flavor.sql
#	INDEX `entry_FI_4` (`entry_schedule_id`),
#	CONSTRAINT `entry_FK_4`
#		FOREIGN KEY (`entry_schedule_id`)
#		REFERENCES `entry_schedule` (`id`),
#updates_2009-10-18_category.sql
#updates_2009-10-21-entry_schedule_start_end_time.sql
#updates_2009-11-19_flavor_params_ids_on_entry.sql
#updates_2009-12-20_entry_available_from.sql
#updates_2009-11-05-remove_entry_schedule.sql
#ALTER TABLE entry DROP COLUMN `entry_schedule_id`;

#Drop Existing Indexes
ALTER TABLE kaltura.entry 
  DROP INDEX `kshow_rank_index`,
  DROP INDEX `kshow_views_index`,
  DROP INDEX `kshow_votes_index`,
  DROP INDEX `views_index`,
  DROP INDEX `votes_index`,
  DROP INDEX `entry_FI_2`,
  DROP INDEX `partner_id_index`,
  DROP INDEX `kshow_index`,
  DROP INDEX `kshow_index_2`,
  DROP INDEX `partner_created_at_index`,
  DROP INDEX `partner_created_at_status_type_index`,
  DROP INDEX `type_kuser_id_index`,
  DROP INDEX `created_index`,
  DROP INDEX `status_created_index`,
  DROP INDEX `type_status_created_index`,
  DROP INDEX `created_at_index`,
  DROP INDEX `partner_group_index`,
#  DROP INDEX `int_id_index`,
  DROP INDEX `partner_kuser_indexed_custom_data_index`,
  DROP INDEX `partner_status_index`,
  DROP INDEX `partner_moderation_status`,
  DROP INDEX `partner_modified_at_index`,
  DROP INDEX `partner_status_media_type_index`,
  DROP INDEX `modified_at_index`,
  DROP INDEX `search_text_index`;
#Add new fields
ALTER TABLE kaltura.entry 
  ADD `access_control_id` INTEGER,
  ADD	`conversion_profile_id` INTEGER,
  ADD categories VARCHAR(4096) AFTER conversion_profile_id,
  ADD categories_ids VARCHAR(1024) AFTER categories,
  ADD search_text_discrete VARCHAR(4096) AFTER categories_ids,
  ADD `start_date` DATETIME default null,
  ADD `end_date` DATETIME default null,
  ADD `flavor_params_ids` VARCHAR(512) AFTER `search_text_discrete`,
  ADD `available_from` DATETIME AFTER `end_date`;

#Return Indexes 
ALTER TABLE kaltura.entry
#  ADD INDEX `kshow_rank_index` (`kshow_id`,`rank`),
#  ADD INDEX `kshow_views_index` (`kshow_id`,`views`),
#  ADD INDEX `kshow_votes_index` (`kshow_id`,`votes`),
#  ADD INDEX `views_index` (`views`),
#  ADD INDEX `votes_index` (`votes`),
  ADD INDEX `entry_FI_2` (`kuser_id`),
#  ADD INDEX `partner_id_index` (`partner_id`,`id`) USING BTREE,
  ADD INDEX `kshow_index` (`partner_id`,`kshow_id`,`subp_id`),
  ADD INDEX `kshow_index_2` (`partner_id`,`kshow_id`,`status`,`subp_id`),
#  ADD INDEX `partner_created_at_index` (`partner_id`,`created_at`),
  ADD INDEX `partner_created_at_status_type_index` (`partner_id`,`created_at`,`status`,`type`),
#  ADD INDEX `type_kuser_id_index` (`type`,`kuser_id`),
#  ADD INDEX `created_index` (`created_at`),
  ADD INDEX `status_created_index` (`status`,`created_at`),
  ADD INDEX `type_status_created_index` (`type`,`status`,`created_at`),
  ADD INDEX `created_at_index` (`created_at`),
  ADD INDEX `partner_group_index` (`partner_id`,`group_id`),
#  ADD INDEX `int_id_index` (`int_id`),
  ADD INDEX `partner_kuser_indexed_custom_data_index` (`partner_id`,`kuser_id`,`indexed_custom_data_1`),
  ADD INDEX `partner_status_index` (`partner_id`,`status`),
  ADD INDEX `partner_moderation_status` (`partner_id`,`moderation_status`),
  ADD INDEX `partner_modified_at_index` (`partner_id`,`modified_at`),
  ADD INDEX `partner_status_media_type_index` (`partner_id`,`status`,`media_type`),
  ADD INDEX `modified_at_index` (`modified_at`),
  ADD FULLTEXT INDEX `search_text_index` (`search_text`),
# + add the new ones
  ADD INDEX `updated_at_index` (`updated_at`),
  ADD INDEX `entry_FI_3` (`access_control_id`),
	ADD CONSTRAINT `entry_FK_3`
		FOREIGN KEY (`access_control_id`)
		REFERENCES `access_control` (`id`),
  ADD FULLTEXT INDEX search_text_discrete_index (search_text_discrete),
  ADD INDEX `entry_FI_5` (`conversion_profile_id`),
	ADD CONSTRAINT `entry_FK_5`
		FOREIGN KEY (`conversion_profile_id`)
		REFERENCES `conversion_profile_2` (`id`);

#-----------------------------------------------------------------------------
#-- partner
#-----------------------------------------------------------------------------
#updates_2009-10-22_batch_tables.sql
ALTER TABLE  kaltura.partner 
  ADD  `priority_group_id` INTEGER NULL AFTER  `monitor_usage`,
  ADD  `work_group_id` INTEGER NULL AFTER  `priority_group_id`,
  ADD  `kmc_version` VARCHAR(15) default "1" ;
/*
#updates_2009-12-30_partner.sql - uploaded to production with VAR Support
  ADD `partner_group_type` SMALLINT default 1,
	ADD `partner_parent_id` INTEGER default null,
	ADD KEY `partner_parent_index`(`partner_parent_id`);
*/

#-----------------------------------------------------------------------------
#-- conversion_profile
#-----------------------------------------------------------------------------
ALTER TABLE conversion_profile ADD COLUMN `conversion_profile_2_id` integer DEFAULT NULL;



#-----------------------------------------------------------------------------
#-- track_entry (exists already in the table)
#-----------------------------------------------------------------------------
ALTER TABLE track_entry ADD COLUMN user_ip VARCHAR(20);
