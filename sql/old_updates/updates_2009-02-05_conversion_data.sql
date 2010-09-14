# conversion_profiles and conversion_params

/*Data for the table `conversion_profile` */
insert  into `conversion_profile`
(`id`,`partner_id`,`enabled`,`name`,`profile_type`,`commercial_transcoder`,`width`,`height`,`aspect_ratio`,`bypass_flv`,`use_with_bulk`,`created_at`,`updated_at`,`profile_type_suffix`) 
values 
(1,0,1,'low','low',0,0,0,'2',0,1,'2008-11-24 13:40:45','2009-02-05 15:30:10','edit'),
(2,0,1,'med','med',0,0,0,'2',0,NULL,'2009-02-05 15:23:07','2009-02-05 15:24:55','edit'),
(3,0,1,'high','high',0,0,0,'2',0,NULL,'2009-02-05 15:40:27','2009-02-05 15:40:27','edit'),
(4,0,1,'hd','hd',1,0,0,'2',0,NULL,'2009-02-05 15:43:01','2009-02-05 15:43:01',''),
(5,0,1,'download','download',0,0,0,'2',1,NULL,'2009-02-05 15:44:20','2009-02-05 15:44:20','');


/*Data for the table `conversion_params` */
insert  into `conversion_params`
(`id`,`partner_id`,`enabled`,`name`,`profile_type`,`profile_type_index`,`width`,`height`,`aspect_ratio`,`gop_size`,`bitrate`,`qscale`,`file_suffix`,`custom_data`,`created_at`,`updated_at`) 
values 
(1,0,1,'low_play','low',1,0,0,'2',100,400,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2008-11-24 11:54:15','2009-02-05 15:06:16'),
(2,0,1,'low_play','lowedit',1,0,0,'2',100,400,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:30:04','2009-02-05 15:30:04'),
(3,0,1,'low_edit','lowedit',2,0,0,'2',5,400,0,'_edit','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:17:40','2009-02-05 15:30:28'),
(4,0,1,'med_play','med',1,0,0,'2',100,800,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:32:47','2009-02-05 15:34:31'),
(5,0,1,'med_play','mededit',1,0,0,'2',100,800,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:34:41','2009-02-05 15:34:41'),
(6,0,1,'med_edit','mededit',2,0,0,'2',5,800,0,'_edit','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:34:57','2009-02-05 15:34:57'),
(7,0,1,'high_play','high',1,0,0,'2',100,1200,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:41:33','2009-02-05 15:41:33'),
(8,0,1,'high_play','highedit',1,0,0,'2',100,1200,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:41:48','2009-02-05 15:41:48'),
(9,0,1,'high_edit','highedit',2,0,0,'2',5,1200,0,'_edit','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:41:59','2009-02-05 15:41:59'),
(10,0,1,'hd','hd',1,0,0,'2',300,40000,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"1\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:43:35','2009-02-05 15:43:35'),
(11,0,1,'download','download',1,0,0,'2',300,0,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:44:51','2009-02-05 15:44:51');