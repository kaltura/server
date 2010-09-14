# template partner 99
INSERT INTO `partner` VALUES
(99,'Template Partner',NULL,NULL,md5('templatesecret'),md5('templateadminsecret'),-1,2,0,NULL,now(), now(),'97385113302981a3c601ef230faab181',
NULL,86400,1,'17','Template Partner','template@kaltura.com','Template partner is used to load the default content, players & playlists for a new partner',1,0,0,
'a:3:{s:24:\"defConversionProfileType\";s:3:\"med\";s:22:\"defaultAccessControlId\";i:1;s:26:\"defaultConversionProfileId\";i:1;}',NULL,1,NULL,1,NULL,NULL,0,1,0,0,NULL,NULL,1,NULL,NULL,1,NULL,'1');
#-- admin_kuser
INSERT INTO `admin_kuser` VALUES (36734,'Template Partner','Template Partner','template@kaltura.com','1ce3d92783a8c623ee3b0bef161d787d7c465633','1e4040a591f081f2fa0f8418a3d763be',NULL,NULL,now(),now(),99);

INSERT INTO kuser (id,screen_name,full_name,email,puser_id, status,partner_id)
VALUES (99, 'template kuser', 'template kuser', 'templatekuser@kaltura.com', '1', 1, 99);

/** Template entries **/
INSERT INTO `entry` VALUES
('_TPNWQV400','a2w4knohjw',99,'Normal web quality video (400kbps)',1,1,'100000.flv','100000.jpg',9,0,0,0,0,0,'fish',NULL,2,5,NULL,'http://www.kaltura.com/content/zbale/roman/sy939vx7ro_croped.flv',-1,NULL,29780,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101446\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"480\";s:5:\"width\";s:3:\"640\";s:12:\"storage_size\";i:1867662;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Normal web quality video (400kbps) fish fish',NULL,NULL,1,NULL,3,NULL,4267710,NULL,'',NULL,'fish',6,0,now(),'1',1,NULL,'fish','4','_CAT_4 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPEWQ1200','p5okv2jqyu',99,'Excellent web quality video (1200kbps)',1,1,'100000.flv','100000.jpg',4,0,0,0,0,0,'fish',NULL,2,5,NULL,'http://www.kaltura.com/content/zbale/roman/sy939vx7ro_croped.flv',-1,NULL,29780,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101447\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"480\";s:5:\"width\";s:3:\"640\";s:12:\"storage_size\";i:4825651;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Excellent web quality video (1200kbps) fish fish',NULL,NULL,1,NULL,4,NULL,4267712,NULL,'',NULL,'fish',6,0,now(),'1',1,NULL,'fish','4','_CAT_4 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSKL_PIC','t60g67o1g8',99,'Sample Kaltura Logo',1,2,'100000.png','100000.jpg',1,0,0,0,0,0,'logo, kaltura, image',NULL,2,1,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101444\";s:7:\"puserId\";N;s:6:\"height\";i:260;s:5:\"width\";i:350;s:12:\"storage_size\";i:21236;}','_KAL_NET_ _99_ _MEDIA_TYPE_2|  Sample Kaltura Logo logo, kaltura, image Kaltura logo image',NULL,NULL,1,NULL,1,'conversionQuality:101444;',4267713,NULL,'Kaltura logo',NULL,'image',2,0,now(),'1',1,NULL,'image','3','_CAT_3 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSKALVID','hmuuutws7k',99,'Sample Kaltura Animated Logo',1,1,'100000.flv','100000.jpg',2,0,0,0,0,0,'kaltura, logo, anmated, video',NULL,2,1,NULL,NULL,NULL,NULL,5068,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:6:\"101445\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"300\";s:5:\"width\";s:3:\"400\";s:12:\"storage_size\";i:335684;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Sample Kaltura Animated Logo kaltura, logo, anmated, video Kaltura animated logo video',NULL,NULL,1,NULL,2,'conversionQuality:101445;',4267715,NULL,'Kaltura animated logo',NULL,'video',6,0,now(),'1',1,NULL,'video','2','_CAT_2 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPSBBBTHD','corx3i318g',99,'Sample Big Buck Bunny Trailer (HD)',1,1,'100000.flv','100000.jpg',3,0,0,0,0,0,'hd content, video, bunny',NULL,2,5,NULL,'http://mirror.cs.umn.edu/blender.org/peach/trailer/trailer_720p.mov',-1,NULL,33023,now(),now(),99,2,9900,'a:5:{s:18:\"conversion_quality\";s:1:\"4\";s:7:\"puserId\";N;s:6:\"height\";s:3:\"720\";s:5:\"width\";s:4:\"1280\";s:12:\"storage_size\";i:11582028;}','_KAL_NET_ _99_ _MEDIA_TYPE_1|  Sample Big Buck Bunny Trailer (HD) hd content, video, bunny Big buck bunny trailer in HD hd content,video',NULL,NULL,1,NULL,3,NULL,4267716,NULL,'Big buck bunny trailer in HD',NULL,'hd content,video',6,0,now(),'1',1,NULL,'hd content,video','1,2','_CAT_1 _CAT_2 _DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPRSVLIST','-1',99,'Recent Sea Videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267718,NULL,'',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMYIMGPL','-1',99,'My images',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267720,NULL,'All my images 2',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMRVLIST','-1',99,'Most recent videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267721,NULL,'10 most  recent videos',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMPSLIST','-1',99,'Manual playlist sample',5,3,'100000.txt',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267722,NULL,'A manual playlist sample',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now()),
('_TPMPVLIST','-1',99,'Most popular videos',5,10,'100000.xml',NULL,0,0,0,0,0,0,'',NULL,2,NULL,NULL,NULL,NULL,NULL,0,now(),now(),99,2,9900,'a:1:{s:7:\"puserId\";N;}',NULL,NULL,NULL,1,NULL,0,NULL,4267723,NULL,'Most Popular videos (top 30)',NULL,'',2,0,now(),'1',1,NULL,'','','_DURATION_short _FLAVOR_',NULL,NULL,NULL,now());
# entry assets     
INSERT INTO `flavor_asset`(id, partner_id, tags, created_at, updated_at, deleted_at, entry_id, flavor_params_id, status, version, description, width, height, bitrate, frame_rate, size, is_original, file_ext, container_format, video_codec_id)
VALUES
('FA_SBBBTHD',99,'mbr,web',now(),now(),NULL,'_TPSBBBTHD',0,2,'100000','',1280,720,2721,25,11264,0,'flv','flash video','vp6'),
('FA_EWQ1200',99,'mbr,web',now(),now(),NULL,'_TPEWQ1200',0,2,'100000','',640,480,1172,25,4710,0,'flv','flash video','h.263'),
('FA_SKALVID',99,'mbr,web',now(),now(),NULL,'_TPSKALVID',0,2,'100000','',400,300,391,25,328,0,'flv','flash video','h.263'),
('FA_NWQV400',99,'mbr,web',now(),now(),NULL,'_TPNWQV400',0,2,'100000','',640,480,391,25,1822,0,'flv','flash video','h.263');
#asset files
INSERT INTO `file_sync` (partner_id,object_type,object_id,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,STATUS,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,4,'FA_SBBBTHD','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,497,'/web/','content/templates/entry/data/SampleBigBuckBunnyTrailer.flv',11582028),
(99,4,'FA_NWQV400','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,502,'/web/','content/templates/entry/data/NormalWebQualityVideo.flv',1867662),
(99,4,'FA_EWQ1200','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'/web/','content/templates/entry/data/ExcellentWebQualityVideo.flv',4825651),
(99,4,'FA_SKALVID','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,501,'/web/','content/templates/entry/data/SampleKalturaAnimatedLogo.flv',335684);
# entry files
INSERT INTO `file_sync` (partner_id,object_type,object_id,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,STATUS,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,1,'_TPMRVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,498,'/web/','content/templates/entry/data/MostRecentVideos.xml',276),
(99,1,'_TPMPSLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,498,'/web/','content/templates/entry/data/ManualPlaylistSample.txt',54),
(99,1,'_TPSKL_PIC','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'/web/','content/templates/entry/data/SampleKalturaLogo.png',10618),
(99,1,'_TPMPVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'/web/','content/templates/entry/data/MostPopularVideos.xml',271),
(99,1,'_TPMYIMGPL','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'/web/','content/templates/entry/data/MyImages.xml',286),
(99,1,'_TPSBBBTHD','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,499,'/web/','content/templates/entry/thumbnail/SampleBigBuckBunnyThumb.jpg',270089),
(99,1,'_TPEWQ1200','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,504,'/web/','content/templates/entry/thumbnail/ExcellentWebQualityVideoThumb.jpg',45457),
(99,1,'_TPSKALVID','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,500,'/web/','content/templates/entry/thumbnail/SampleKalturaAnimatedLogoThumb.jpg',13617),
(99,1,'_TPSKL_PIC','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/thumbnail/SampleKalturaLogoThumb.jpg',1667),
(99,1,'_TPNWQV400','100000',3,'0',1,now(),now(),now(),NULL,2,1,NULL,511,'/web/','content/templates/entry/thumbnail/NormalWebQualityVideoThumb.jpg',42262),
(99,1,'_TPRSVLIST','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,498,'/web/','content/templates/entry/data/RecentSeaVideos.xml',280);
 

/** Template uiconfs **/
INSERT INTO `ui_conf` VALUES
(1000000,1,99,0,'content/templates/entry/uiconf/WidescreenPlayerLightSkin.xml','Widescreen player - light skin','400','290',NULL,'/flash/kdp/v2.7.0/kdp.swf','2009-08-30 04:26:22','2009-11-15 00:00:00',NULL,1,'Player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,0,2,NULL),
(1000001,1,99,0,'content/templates/entry/uiconf/WidescreenPlaylistPlayerDarkSkin.xml','Widescreen playlist player - dark skin, minimal','750','266',NULL,'/flash/kdp/v2.7.0/kdp.swf','2009-08-30 04:32:41','2009-11-15 00:00:00',NULL,1,'Playlist','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,0,2,NULL),
(1000002,1,99,0,'content/templates/entry/uiconf/MultiplePlaylistsDarkSkin.xml','Multiple playlists - dark skin','750','373',NULL,'/flash/kdp/v2.7.0/kdp.swf','2009-08-30 04:35:18','2009-11-15 00:00:00',NULL,1,'Playlist','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,0,2,NULL),
(1000003,1,99,0,'content/templates/entry/uiconf/PlayerWithNoShareButton.xml','Player with no share button','400','335',NULL,'/flash/kdp/v2.7.0/kdp.swf','2009-08-30 07:30:46','2009-11-15 00:00:00',NULL,1,'Player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,0,2,NULL),
(1000004,1,99,0,'content/templates/entry/uiconf/PlayerWithCustomWatermark.xml','Player with custom watermark','400','335',NULL,'/flash/kdp/v2.7.0/kdp.swf','2009-08-30 07:39:16','2009-11-15 00:00:00',NULL,1,'Player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,0,2,NULL);
#uiconf filesyncs
INSERT INTO `file_sync` (partner_id,object_type,object_id,version,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,status,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,2,'1000000',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/WidescreenPlayerLightSkin.xml',12077),
(99,2,'1000001',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/WidescreenPlaylistPlayerDarkSkin.xml',14006),
(99,2,'1000002',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/MultiplePlaylistsDarkSkin.xml',15925),
(99,2,'1000003',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/PlayerWithNoShareButton.xml',10618),
(99,2,'1000004',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/PlayerWithCustomWatermark.xml',11789),
(99,2,'1000000',NULL,2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/WidescreenPlayerLightSkin.features.xml',16292),
(99,2,'1000001',NULL,2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/WidescreenPlaylistPlayerDarkSkin.features.xml',19301),
(99,2,'1000002',NULL,2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/MultiplePlaylistsDarkSkin.features.xml',20249),
(99,2,'1000003',NULL,2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/PlayerWithNoShareButton.features.xml',16285),
(99,2,'1000004',NULL,2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/entry/uiconf/PlayerWithCustomWatermark.features.xml',16268);
       

/** Template access control **/
INSERT INTO `access_control`
VALUES (1,99,'Default','default access control profile',now(),now(),NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/** Template converions profile **/
insert into conversion_profile_2 (id,partner_id, name, created_at, updated_at, deleted_at, description, crop_left, crop_top, crop_width, crop_height, clip_start, clip_duration) VALUES
(1,'99', 'Default', now(), now(), NULL, 'The default set of flavors. If not specified otherwise all media uploaded will be converted based on the definition in this profile', '-1', '-1', '-1', '-1', '-1', '-1');
#updates_2009-12-27_conversion_profile_input_tags.sql
UPDATE conversion_profile_2 SET input_tags_map = 'web' WHERE input_tags_map IS NULL;

#flavor_params_conversion_profile
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 0, 0, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 1, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 2, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 3, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 4, 2, null, now(), now());
insert into flavor_params_conversion_profile( conversion_profile_id, flavor_params_id, ready_behavior, force_none_complied, created_at, updated_at)
values(1, 5, 2, null, now(), now());


# KDP3 app-studio uiconfs
INSERT INTO `ui_conf` ()
VALUES
(1000005,1,99,9900,'content/templates/uiconf/kdp3/widescreenLightskin.xml','Widescreen player - light skin','400','285',NULL,'/flash/kdp3/v3.1.6/kdp3.swf',now(),now(),'',1,'kdp3,player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,2,2,'2'),
(1000006,1,99,9900,'content/templates/uiconf/kdp3/widescreenPlaylistDark.xml','Widescreen playlist player - dark skin, minimal','740','255',NULL,'/flash/kdp3/v3.1.6/kdp3.swf',now(),now(),'',1,'kdp3,playlist','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,2,2,'4'),
(1000007,1,99,9900,'content/templates/uiconf/kdp3/playerNoShare.xml','Player with no share button','400','360',NULL,'/flash/kdp3/v3.1.6/kdp3.swf','2010-02-03 10:31:32','2010-03-11 10:07:58','',1,'kdp3,player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,2,2,'2'),
(1000008,1,99,9900,'content/templates/uiconf/kdp3/WatermarkPlayer.xml','Player with custom watermark','400','330',NULL,'/flash/kdp3/v3.1.6/kdp3.swf','2010-02-03 10:44:35','2010-03-11 10:00:26','',1,'kdp3,player','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',2,NULL,2,2,'4'),
(1000009,1,99,9900,'content/templates/uiconf/kdp3/MultiPlaylists.xml','Multiple playlists - dark skin','740','330',NULL,'/flash/kdp3/v3.1.6/kdp3.swf','2010-02-03 10:47:49','2010-03-11 04:19:57',NULL,1,'kdp3,playlist','a:2:{s:8:\"autoplay\";s:5:\"false\";s:9:\"automuted\";s:5:\"false\";}',3,NULL,1,2,'1');

INSERT INTO `file_sync` (partner_id,object_type,object_id,version,object_sub_type,dc,original,created_at,updated_at,ready_at,sync_time,status,file_type,linked_id,link_count,file_root,file_path,file_size)
VALUES
(99,2,'1000005','1',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/widescreenLightskin.xml',12792),
(99,2,'1000005','1',2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/widescreenLightskin.features.xml',12561),
(99,2,'1000006','1',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/widescreenPlaylistDark.xml',14431),
(99,2,'1000006','1',2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/widescreenPlaylistDark.features.xml',15946),
(99,2,'1000007','1',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/playerNoShare.xml',11215),
(99,2,'1000007','1',2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/playerNoShare.features.xml',12586),
(99,2,'1000008','1',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/WatermarkPlayer.xml',12725),
(99,2,'1000008','1',2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/WatermarkPlayer.features.xml',12585),
(99,2,'1000009','1',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/MultiPlaylists.xml',15150),
(99,2,'1000009','1',2,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/templates/uiconf/kdp3/MultiPlaylists.features.xml',15919);