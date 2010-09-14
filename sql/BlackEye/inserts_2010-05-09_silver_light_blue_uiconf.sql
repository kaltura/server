

insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('906','14','0','0','content/uiconf/kaltura/silver-light/default.xml','Silver-Light Player, blue skin','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','slp','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');


insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','906','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','906','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/blue.xml','248851');
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','906','1','2','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','906','1','2','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/blue.features.xml','16794');
