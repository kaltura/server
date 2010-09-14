

insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('900','14','0','0','content/uiconf/kaltura/silver-light/default.xml','Silver-Light Player','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','slp','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');
insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('901','14','0','0','content/uiconf/kaltura/silver-light/gray.xml','Silver-Light Gray Player','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','slp','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');
insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('902','14','0','0','content/uiconf/kaltura/silver-light/black.xml','Silver-Light Black Player','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','slp','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');


insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','900','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','900','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/default.xml','187368');

insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','901','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','901','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/gray.xml','248851');
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','901','1','2','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','901','1','2','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/gray.features.xml','16794');

insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','902','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','902','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/black.xml','206325');
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','902','1','2','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','902','1','2','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/black.features.xml','16791');




insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('903','14','0','0','content/uiconf/kaltura/silver-light/default.playlist.xml','Silver-Light Playlist','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','sll','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');
insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('904','14','0','0','content/uiconf/kaltura/silver-light/gray.playlist.xml','Silver-Light Gray Playlist','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','sll','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');
insert into `ui_conf` (`id`, `obj_type`, `partner_id`, `subp_id`, `conf_file_path`, `name`, `width`, `height`, `html_params`, `swf_url`, `created_at`, `updated_at`, `conf_vars`, `use_cdn`, `tags`, `custom_data`, `status`, `description`, `display_in_search`, `creation_mode`, `version`) 
values('905','14','0','0','content/uiconf/kaltura/silver-light/black.playlist.xml','Silver-Light Black Playlist','400','620',NULL,'/flash/slp/vx.x.x/KalturaPlayer.xap',NOW(),NOW(),'','0','sll','a:1:{s:17:\"minRuntimeVersion\";s:11:\"4.0.50303.0\";}','2',NULL,'2','3','1');


insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','903','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','903','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/default.playlist.xml','187368');

insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','904','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','904','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/gray.playlist.xml','248851');
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','904','1','2','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','904','1','2','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/gray.features.playlist.xml','16794');

insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','905','1','1','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','905','1','1','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/black.playlist.xml','206325');
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','905','1','2','1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL);
insert into `file_sync` (`partner_id`, `object_type`, `object_id`, `version`, `object_sub_type`, `dc`, `original`, `created_at`, `updated_at`, `ready_at`, `sync_time`, `status`, `file_type`, `linked_id`, `link_count`, `file_root`, `file_path`, `file_size`) 
values('0','2','905','1','2','0','1',NOW(),NOW(),NOW(),NULL,'2','1',NULL,NULL,'/web/','content/uiconf/kaltura/silver-light/black.features.playlist.xml','16791');

