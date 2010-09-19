


INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-1, 'batch partner', '', NULL, '@BATCH_PARTNER_SECRET@', '@BATCH_PARTNER_ADMIN_SECRET@', -1, 0, 0, NULL, NOW(), NOW(), '@BATCH_PARTNER_PARTNER_ALIAS@', NULL, 86400, 1, '-10', 'batch admin', '@BATCH_ADMIN_MAIL@', 'Build-in partner - used for batch operations', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_batch.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);

INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-2,  'admin console', '', NULL, '@ADMIN_CONSOLE_PARTNER_SECRET@', '@ADMIN_CONSOLE_PARTNER_ADMIN_SECRET@', -1, 0, 0, NULL, NOW(), NOW(), '@ADMIN_CONSOLE_PARTNER_ALIAS@', NULL, 86400, 1, '-10', 'console admin', '@ADMIN_CONSOLE_ADMIN_MAIL@', 'Build-in partner - used for admin console', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_console.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);



INSERT INTO `admin_kuser` ( `id`, `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99998 , NULL, 'batch admin', '@BATCH_KUSER_MAIL@', '@BATCH_KUSER_SHA1@', '@BATCH_KUSER_SALT@', NULL, NULL, NOW(), NOW(), -1);

INSERT INTO `admin_kuser` (`id`,  `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99999 , NULL , 'console admin', '@ADMIN_CONSOLE_KUSER_MAIL@', '@ADMIN_CONSOLE_KUSER_SHA1@', '@ADMIN_CONSOLE_KUSER_SALT@', NULL, NULL, NOW(), NOW(), -2);



insert into `system_user` (`email`, `first_name`, `last_name`, `sha1_password`, `salt`, `created_by`, `status`, `is_primary`, `status_updated_at`, `created_at`, `updated_at`, `deleted_at`, `role`) 
values('@SYSTEM_USER_ADMIN_EMAIL@','admin','admin','@SYSTEM_USER_ADMIN_SHA1@','@SYSTEM_USER_ADMIN_SALT@','0','1','1',NULL,NOW(),NOW(),NULL,'admin');


-- Insert the template partner

INSERT INTO `partner` VALUES
(99,'Template KMC account',NULL,NULL,md5('@TEMPLATE_PARTNER_SECRET@'),md5('@TEMPLATE_PARTNER_ADMIN_SECRET@'),-1,2,0,NULL,now(), now(),'@TEMPLATE_PARTNER_ALIAS@',
NULL,86400,1,'17','Template KMC account','@TEMPLATE_PARTNER_MAIL@','Template KMC account is used to load the default content, players & playlists for a new partner',1,0,0,
'a:3:{s:24:\"defConversionProfileType\";s:3:\"med\";s:22:\"defaultAccessControlId\";i:1;s:26:\"defaultConversionProfileId\";i:1;}',NULL,1,NULL,1,NULL,NULL,0,1,0,0,NULL,NULL,1,NULL,NULL,1,NULL,'1');



INSERT INTO `admin_kuser` VALUES (36734,'Template','Template','@TEMPLATE_PARTNER_MAIL@','@TEMPLATE_ADMIN_KUSER_SHA1@','@TEMPLATE_ADMIN_KUSER_SALT@',NULL,NULL,now(),now(),99);

INSERT INTO kuser (id,screen_name,full_name,email,puser_id, status,partner_id)
VALUES (99, 'template kuser', 'template kuser', '@TEMPLATE_KUSER_MAIL@', '1', 1, 99);



INSERT INTO widget (id, int_id, source_widget_id, root_widget_id, partner_id, subp_id, kshow_id, entry_id, ui_conf_id, custom_data, security_type, security_policy, created_at, updated_at, partner_data) VALUES
('_99', '1', NULL, NULL, '99', '9900', NULL, NULL, '200', NULL, NULL, NULL, NOW(), NOW(), NULL);



INSERT INTO `entry` VALUES
('_TPNWQV400','a2w4knohjw',99,'Normal web quality video (400kbps)',1,1,'100000.flv','100000.jpg',9,0,0,0,0,0,'fish',NULL,2,5,NULL,NULL,-1,NULL,29780,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101446\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"480\";s:5:\"width\";s:3:\"640\";s:12:\"storage_size\";i:1867662;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Normal web quality video (400kbps) fish fish',NULL,NULL,1,NULL,3,NULL,4267710,NULL,'',NULL,'fish',6,0,now(),'1',1,NULL,'fish','4','_CAT_4 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPEWQ1200','p5okv2jqyu',99,'Excellent web quality video (1200kbps)',1,1,'100000.flv','100000.jpg',4,0,0,0,0,0,'fish',NULL,2,5,NULL,NULL,-1,NULL,29780,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101447\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"480\";s:5:\"width\";s:3:\"640\";s:12:\"storage_size\";i:4825651;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Excellent web quality video (1200kbps) fish fish',NULL,NULL,1,NULL,4,NULL,4267712,NULL,'',NULL,'fish',6,0,now(),'1',1,NULL,'fish','4','_CAT_4 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSKL_PIC','t60g67o1g8',99,'Sample Kaltura Logo',1,2,'100000.png','100000.jpg',1,0,0,0,0,0,'logo, kaltura, image',NULL,2,1,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101444\";s:7:\"puserId\";N;s:6:\"height\";i:260;s:5:\"width\";i:350;s:12:\"storage_size\";i:21236;}','_KAL_NET_ _99_ _MEDIA_TYPE_2|  Sample Kaltura Logo logo, kaltura, image Kaltura logo image',NULL,NULL,1,NULL,1,'conversionQuality:101444;',4267713,NULL,'Kaltura logo',NULL,'image',2,0,now(),'1',1,NULL,'image','3','_CAT_3 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSKALVID','hmuuutws7k',99,'Sample Kaltura Animated Logo',1,1,'100000.flv','100000.jpg',2,0,0,0,0,0,'kaltura, logo, anmated, video',NULL,2,1,NULL,NULL,NULL,NULL,5068,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101445\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"300\";s:5:\"width\";s:3:\"400\";s:12:\"storage_size\";i:335684;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Sample Kaltura Animated Logo kaltura, logo, anmated, video Kaltura animated logo video',NULL,NULL,1,NULL,2,'conversionQuality:101445;',4267715,NULL,'Kaltura animated logo',NULL,'video',6,0,now(),'1',1,NULL,'video','2','_CAT_2 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSBBBTHD','corx3i318g',99,'Sample Big Buck Bunny Trailer (HD)',1,1,'100000.flv','100000.jpg',3,0,0,0,0,0,'hd content, video, bunny',NULL,2,5,NULL,'http://mirror.cs.umn.edu/blender.org/peach/trailer/trailer_720p.mov',-1,NULL,33023,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:1:\"4\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"720\";s:5:\"width\";s:4:\"1280\";s:12:\"storage_size\";i:11582028;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Sample Big Buck Bunny Trailer (HD) hd content, video, bunny Big buck bunny trailer in HD hd content,video',NULL,NULL,1,NULL,3,NULL,4267716,NULL,'Big buck bunny trailer in HD',NULL,'hd content,video',6,0,now(),'1',1,NULL,'hd content,video','1,2','_CAT_1 _CAT_2 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPRSVLIST','-1',99,'Recent Sea Videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267718,NULL,'',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMYIMGPL','-1',99,'My images',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267720,NULL,'All my images 2',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMRVLIST','-1',99,'Most recent videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267721,NULL,'10 most  recent videos',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMPVLIST','-1',99,'Most popular videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267723,NULL,'Most Popular videos (top 30)',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now());


   
INSERT INTO `flavor_asset`(id, partner_id, tags, created_at, updated_at, deleted_at, entry_id, flavor_params_id, status, version, description, width, height, bitrate, frame_rate, size, is_original, file_ext, container_format, video_codec_id)
VALUES
('FA_SBBBTHD',99,'mbr,web',now(),now(),NULL,'_TPSBBBTHD',0,2,'100000','',1280,720,2721,25,11264,0,'flv','flash video','vp6'),
('FA_EWQ1200',99,'mbr,web',now(),now(),NULL,'_TPEWQ1200',0,2,'100000','',640,480,1172,25,4710,0,'flv','flash video','h.263'),
('FA_SKALVID',99,'mbr,web',now(),now(),NULL,'_TPSKALVID',0,2,'100000','',400,300,391,25,328,0,'flv','flash video','h.263'),
('FA_NWQV400',99,'mbr,web',now(),now(),NULL,'_TPNWQV400',0,2,'100000','',640,480,391,25,1822,0,'flv','flash video','h.263');



INSERT INTO `file_sync` (partner_id,object_type,object_id,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,STATUS,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,4,'FA_SBBBTHD','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,497,'@WEB_DIR@/','content/templates/entry/data/SampleBigBuckBunnyTrailer.flv',11582028),
(99,4,'FA_NWQV400','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,502,'@WEB_DIR@/','content/templates/entry/data/NormalWebQualityVideo.flv',1867662),
(99,4,'FA_EWQ1200','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'@WEB_DIR@/','content/templates/entry/data/ExcellentWebQualityVideo.flv',4825651),
(99,4,'FA_SKALVID','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,501,'@WEB_DIR@/','content/templates/entry/data/SampleKalturaAnimatedLogo.flv',335684);



INSERT INTO `file_sync` (partner_id,object_type,object_id,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,STATUS,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,1,'_TPMRVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,498,'@WEB_DIR@/','content/templates/entry/data/MostRecentVideos.xml',276),
(99,1,'_TPSKL_PIC','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'@WEB_DIR@/','content/templates/entry/data/SampleKalturaLogo.png',10618),
(99,1,'_TPMPVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'@WEB_DIR@/','content/templates/entry/data/MostPopularVideos.xml',271),
(99,1,'_TPMYIMGPL','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'@WEB_DIR@/','content/templates/entry/data/MyImages.xml',286),
(99,1,'_TPSBBBTHD','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'@WEB_DIR@/','content/templates/entry/thumbnail/SampleBigBuckBunnyThumb.jpg',270089),
(99,1,'_TPEWQ1200','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,504,'@WEB_DIR@/','content/templates/entry/thumbnail/ExcellentWebQualityVideoThumb.jpg',45457),
(99,1,'_TPSKALVID','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'@WEB_DIR@/','content/templates/entry/thumbnail/SampleKalturaAnimatedLogoThumb.jpg',13617),
(99,1,'_TPSKL_PIC','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'@WEB_DIR@/','content/templates/entry/thumbnail/SampleKalturaLogoThumb.jpg',1667),
(99,1,'_TPNWQV400','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,511,'@WEB_DIR@/','content/templates/entry/thumbnail/NormalWebQualityVideoThumb.jpg',42262),
(99,1,'_TPRSVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,498,'@WEB_DIR@/','content/templates/entry/data/RecentSeaVideos.xml',280);
 

INSERT INTO `access_control`
VALUES (1,99,'Default','default access control profile',now(),now(),NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);



insert into conversion_profile_2 (id,partner_id, name, created_at, updated_at, deleted_at, description, crop_left, crop_top, crop_width, crop_height, clip_start, clip_duration) VALUES
(1,'99', 'Default', now(), now(), NULL, 'The default set of flavors. If not specified otherwise all media uploaded will be converted based on the definition in this profile', '-1', '-1', '-1', '-1', '-1', '-1');


UPDATE conversion_profile_2 SET input_tags_map = 'web' WHERE input_tags_map IS NULL;



insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 0, 0, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 5, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 7, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 9, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 10, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 11, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 12, 2, null, now(), now());
