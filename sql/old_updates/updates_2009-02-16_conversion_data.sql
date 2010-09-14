# conversion_profiles and conversion_params

/*Data for the table `conversion_profile` */
insert  into `conversion_profile`
(`partner_id`,`enabled`,`name`,`profile_type`,`commercial_transcoder`,`width`,`height`,`aspect_ratio`,`bypass_flv`,`use_with_bulk`,`created_at`,`updated_at`,`profile_type_suffix`) 
values 
(0,1,'wp_default','wp_default',0,0,0,'2',1,NULL,'2009-02-05 15:44:20','2009-02-05 15:44:20','');


/*Data for the table `conversion_params` */
insert  into `conversion_params`
(`partner_id`,`enabled`,`name`,`profile_type`,`profile_type_index`,`width`,`height`,`aspect_ratio`,`gop_size`,`bitrate`,`qscale`,`file_suffix`,`custom_data`,`created_at`,`updated_at`) 
values 
(0,1,'wp_default','wp_default',1,0,0,'2',100,800,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:32:47','2009-02-05 15:34:31'),
(0,1,'wp_default','wp_defaultedit',1,0,0,'2',100,800,0,'','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:34:41','2009-02-05 15:34:41'),
(0,1,'wp_default','wp_defaultedit',2,0,0,'2',5,800,0,'_edit','a:4:{s:20:\"commercialTranscoder\";s:1:\"0\";s:12:\"ffmpegParams\";s:0:\"\";s:14:\"mencoderParams\";s:0:\"\";s:10:\"flixParams\";s:0:\"\";}','2009-02-05 15:34:57','2009-02-05 15:34:57');

# add 2 inserts so all the automatically created prfiles and params will start from 100000
insert into conversion_profile (`id`) values ('99999');
insert into conversion_params (`id`) values ('99999');
