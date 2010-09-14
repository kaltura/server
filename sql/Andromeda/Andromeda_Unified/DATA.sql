use kaltura;

START TRANSACTION;

#-----------------------------------------------------------------------------
#-- ui_conf
#-----------------------------------------------------------------------------
/*
#updates_2009-08-04_minisite_uiconf.sql
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
VALUES(71200,2,0,0,'/web/content/uiconf/kaltura/minisite/kcw/kcw.xml','minisite kcw',680,480,NULL,'/flash/kcw/v2.0.4/ContributionWizard.swf',now(),now(), null, 1);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
VALUES(71201,2,0,0,'/web/content/uiconf/kaltura/minisite/kcw/kcw_se.xml','minisite kcw in kse',680,480,NULL,'/flash/kcw/v2.0.4/ContributionWizard.swf',now(),now(), null, 1);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
VALUES(71300,3,0,0,'/web/content/uiconf/kaltura/minisite/kse/kse.xml','minisite kse',890,546,NULL,'/flash/kse/v2.1.5/simpleeditor.swf',now(),now(), null, 1);
*/
#updates_2009-12-08_kmc_kae_kse_kcw.sql
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48601,3,0,0,'/web/content/uiconf/kaltura/kmc/kse/kse_kmc_v216.xml','Andromeda KSE For KMC', 0,0,'','/flash/kse/v2.1.8/simpleeditor.swf',NOW(),NOW(), NULL, 1, "andromeda_kse_for_kmc", 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48602,4,0,0,'/web/content/uiconf/kaltura/kmc/kae/kae_kmc_v1015.xml','Andromeda KAE For KMC', 0,0,'','/flash/kae/v1.0.15.1/KalturaAdvancedVideoEditor.swf',NOW(),NOW(), NULL, 1, "andromeda_kae_for_kmc", 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48611,2,0,0,'/web/content/uiconf/kaltura/kmc/kcw/kcw_kmc-kse_light.xml','Andromeda KCW For KSE In KMC', 0,0,'','/flash/kcw/v2.0.7/ContributionWizard.swf',NOW(),NOW(), NULL, 1, "", 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48612,2,0,0,'/web/content/uiconf/kaltura/kmc/kcw/kcw_kmc-kae_dark.xml','Andromeda KCW For KAE In KMC', 0,0,'','/flash/kcw/v2.0.7/ContributionWizard.swf',NOW(),NOW(), NULL, 1, "", 2);
#updates_2009-12-24_kmc_kdp_fixed_defaults.sql
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48503,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content.xml','KDP For KMC Drill Down', 0,0,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, null, 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48501,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_light.xml','KDP3 light player', 400,335,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, 'player', 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48502,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_dark.xml','KDP3 dark player', 400,335,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, 'player', 2);
#updates_2009-12-27_kmc_kdp_playlist.sql
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48504,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_playlist_light.xml','KDP3 Light Playlist', 600,330,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, 'playlist', 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48505,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_playlist_dark.xml','KDP3 Dark Playlist', 600,330,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, 'playlist', 2);
#updates_2009-12-30_kmc_kdp_moderation_and_flavors.sql
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48506,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_moderation.xml','KDP For KMC Moderation', 0,0,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, null, 2);
INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48507,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_flavors.xml','KDP For KMC Flavors Preview', 0,0,'','/flash/kdp3/v0.2.6/kdp3.swf',NOW(),NOW(), NULL, 1, null, 2);
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf', name = 'KDP3 Light Player' WHERE id = 48501 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf', name = 'KDP3 Dark Player' WHERE id = 48502 LIMIT 1;
/*
#-- updates_2009-12-30_kmc_kdp_upgrade_to_0.2.6.sql
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.6/kdp3.swf' WHERE id = 48501 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.6/kdp3.swf' WHERE id = 48502 LIMIT 1;
#UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf' WHERE id = 48503 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.6/kdp3.swf' WHERE id = 48503 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.6/kdp3.swf' WHERE id = 48504 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.6/kdp3.swf' WHERE id = 48505 LIMIT 1;
*/

#-----------------------------------------------------------------------------
#-- partner
#-----------------------------------------------------------------------------
#updates_2009-09-06_batch_partner_insert.sql
INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-1, 'batch partner', '', NULL, 'a92e32b463cd86182051c5821278fe0c', 'c2d5c06481e0a444ea8c3f7f0dab16bd', -1, 0, 0, NULL, '2009-10-06 05:24:22', '2009-10-06 05:24:22', '74cea349eb7add28efdebbb3bf5b3ddd', NULL, 86400, 1, '-10', 'batch admin', 'batch@kaltura.com', 'Build-in partner - used for batch operations', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_batch.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);
#updates_2010-01-04_admin_console_partner_insert.sql
INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-2,  'admin console', '', NULL, '5678', '90210', -1, 0, 0, NULL, NOW(), NOW(), '1234', NULL, 86400, 1, '-10', 'console admin', 'console@kaltura.com', 'Build-in partner - used for admin console', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_console.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);

#-----------------------------------------------------------------------------
#-- admin_kuser
#-----------------------------------------------------------------------------
# the IDs where chosen to fit in a "hole" between existing IDs to prevent breaking of the replication 
INSERT INTO `admin_kuser` ( `id`, `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99998 , NULL, 'batch admin', 'batch@kaltura.com', '117f0fc066b96a00ad8b49756be489c569318581', '7c394ce97c51b2109ffa6511f175cc6e', NULL, NULL, '2009-10-06 05:24:23', '2009-10-06 05:24:23', -1);
#updates_2010-01-04_admin_console_partner_insert.sql
INSERT INTO `admin_kuser` (`id`,  `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99999 , NULL , 'console admin', 'console@kaltura.com', '117f0fc066b96a00ad8b49756be489c569318581', '7c394ce97c51b2109ffa6511f175cc6e', NULL, NULL, NOW(), NOW(), -2);


#-----------------------------------------------------------------------------
#-- entry
#-----------------------------------------------------------------------------
#updates_2009-10-07_fix_moderation_status.sql
update entry set moderation_status = 1, status = 2 where status = 5;
#updates_2009-12-20_entry_available_from.sql
update entry set available_from = start_date;
update entry set available_from = created_at where available_from is null;
#updates_2009-10-04_fix_plays_views_where_null.sql
#update entry set views = 0 where views is null;
#update entry set plays = 0 where plays is null;
#updates_2009-12-21_entry_plays_views_votes_null_fix.sql
update entry set plays = 0 where plays is null;
update entry set views = 0 where views is null;
update entry set votes = 0 where votes is null;


#-----------------------------------------------------------------------------
#-- flavor_params
#-----------------------------------------------------------------------------
#updates_2009-11-15_flavor_params_data.sql
insert into flavor_params (partner_id, name, tags, description, ready_behavior, created_at, updated_at, deleted_at, is_default, format, video_codec, video_bitrate, audio_codec, audio_bitrate, audio_channels, audio_sample_rate, width, height, frame_rate, gop_size, conversion_engines, conversion_engines_extra_params, custom_data) VALUES
('0', 'HD', 'hd,web', 'High definition description goes here.', '0', now(), now(), NULL, '1', 'mp4', 'h264', '4000', 'mp3', '192', '2', '44000', '1920', '1080', '25', '50', '1,2,3', NULL, NULL), 
('0', 'HQ', 'web', 'High quality description goes here.', '0', now(), now(), NULL, '1', 'mp4', 'h264', '2500', 'mp3', '128', '2', '44000', '1280', '720', '25', '50', '1,2,3', NULL, NULL), 
('0', 'Normal - big', 'web', 'Normal quality big screen description goes here.', '0', now(), now(), NULL, '1', 'mp4', 'h264', '1350', 'mp3', '96', '2', '44000', '1280', '720', '25', '50', '1,2,3', NULL, NULL), 
('0', 'Normal - small', 'web', 'Normal quality small screen description goes here.', '0', now(), now(), NULL, '1', 'mp4', 'h264', '750', 'mp3', '96', '2', '44000', '512', '288', '25', '50', '1,2,3', NULL, NULL), 
('0', 'Low - small', 'web', 'Low quality description goes here.', '0', now(), now(), NULL, '1', 'mp4', 'h264', '400', 'mp3', '96', '2', '44000', '512', '288', '25', '50', '1,2,3', NULL, NULL);
#updates_2009-11-25_fix_flavor_params_audio_sample_rate.sql
update flavor_params set audio_sample_rate = '44100' where audio_sample_rate = '44000';
#updates_2009-11-29_fix_flavor_params_fps.sql
update flavor_params set frame_rate = '0' where frame_rate = '25';
#updates_2009-12-27_flavor_params_names.sql
UPDATE flavor_params SET NAME = 'High – Large' WHERE NAME = 'HQ' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'Normal – Large' WHERE NAME = 'Normal – big' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'Normal – Small' WHERE NAME = 'Normal – small' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'Basic – Small' WHERE NAME = 'Low – small' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'Editable' WHERE NAME = 'Edit' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'HQ MP4 for Export' WHERE NAME = 'MP4 HQ Export' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'HQ AVI for Export' WHERE NAME = 'AVI HQ Export' AND partner_id = 0;
#updates_2009-12-30_flavor_params_names.sql
UPDATE flavor_params SET NAME = 'Standard – Large' WHERE NAME = 'Normal – Large' AND partner_id = 0;
UPDATE flavor_params SET NAME = 'Standard – Small' WHERE NAME = 'Normal – Small' AND partner_id = 0;

#-----------------------------------------------------------------------------
#-- flavor_asset
#-----------------------------------------------------------------------------
#updates_2010_01_04_admin_console_partner_insert.sql
UPDATE flavor_asset SET tags = CONCAT(tags, ",mbr") WHERE tags LIKE '%web%';
#updates_2010-01-05_mediainfo_flavor_id.sql
UPDATE	media_info mi,  flavor_asset fa
SET		mi.flavor_asset_id = fa.id
WHERE	mi.flavor_asset_id = fa.int_id;
UPDATE	flavor_params_output fpo,  flavor_asset fa
SET		fpo.flavor_asset_id = fa.id
WHERE	fpo.flavor_asset_id = fa.int_id;
UPDATE	flavor_params_output fpo,  flavor_asset fa
SET		fpo.flavor_asset_version = fa.version
WHERE	fpo.flavor_asset_id = fa.id;
UPDATE	file_sync fs,  flavor_asset fa
SET		fs.object_id = fa.id
WHERE	fs.object_id = fa.int_id
AND		fs.object_type = 4;


#-----------------------------------------------------------------------------
#-- flavor_params_output
#-----------------------------------------------------------------------------
#updates_2010_01_04_admin_console_partner_insert.sql
UPDATE flavor_params_output SET tags = CONCAT(tags, ",mbr") WHERE tags LIKE '%web%';

#-----------------------------------------------------------------------------
#-- conversion_profile_2
#-----------------------------------------------------------------------------
#updates_2009-11-17_default_conversion_profile.sql
insert into conversion_profile_2 (partner_id, name, created_at, updated_at, deleted_at, description, crop_left, crop_top, crop_width, crop_height, clip_start, clip_duration) VALUES
('99', 'Default', now(), now(), NULL, 'The default set of flavors. If not specified otherwise all media uploaded will be converted based on the definition in this profile', '-1', '-1', '-1', '-1', '-1', '-1');
#updates_2009-12-27_conversion_profile_input_tags.sql
UPDATE conversion_profile_2 SET input_tags_map = 'web' WHERE input_tags_map IS NULL;


Commit;
