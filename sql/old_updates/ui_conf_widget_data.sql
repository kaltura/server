insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1,NULL,NULL,NULL,'/web/kaltura/alpha/web/swf/kdp/layout7.xml','Create Ui Conf!','400','425',NULL,'http:#kaldev.kaltura.com/swf/kdp/Main.swf','2008-03-13 14:12:53','2008-03-13 14:12:53');

delete from ui_conf where id=2;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(2,2,0,0,'/web/content/uiconf/kaltura/cw_generic.xml','generic_cw','680','400',NULL,NULL,'2008-03-20 15:02:05','2008-03-20 15:02:05');

delete from ui_conf where id=22;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(22,2,0,0,'/web/content/uiconf/kaltura/generic/cw/v1.5.4/cw_upload_only.xml','generic_cw upload only','680','400',NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf','2009-03-08 15:02:05','2009-03-08 15:02:05');


delete from ui_conf where id=3;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(3,2,0,0,'/web/content/uiconf/kaltura/cw_weplay.xml','weplay_cw','680','400',NULL,NULL,'2008-03-23 15:34:22','2008-03-23 15:34:22');

delete from ui_conf where id=100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(100,1,0,0,'/web/content/uiconf/kaltura/kdp_demo.xml','kdp_demo','400','420','','/swf/kdp/kdp.swf','2008-04-07 16:18:09','2008-04-07 16:18:09');

# kaltura generic se - will always be up-to-date with the kse swf
delete from ui_conf where id=401;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(401,3,0,0,'/web/content/uiconf/kaltura/generic/se/se_generic.xml','generic se','890','546','','/flash/kse/v2.1.3/simpleeditor.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

# kaltura generic cw - will always be up-to-date with the kcw swf
delete from ui_conf where id=402;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(402,2,0,0,'/web/content/uiconf/kaltura/generic/cw/cw_generic.xml','generic cw','680','400','','/flash/kcw/v1.5.4/ContributionWizard.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

delete from ui_conf where id=150;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(150,1,1,100,'/web/content/uiconf/kaltura/kdp_test.xml','wiki standard','400','420','','/flash/kdp/v1.0.14/kdp.swf',now(),now(),'',1);
delete from widget where id = '150';
insert into widget values ('150',150,'','150',1,100,0,0,150,'',0,1,now(),now(),'');


# KDP for KMC 
delete from ui_conf where id=199;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(199,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_regular.xml','kdp for kmc','410','364','','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'',1);
delete from widget where id = '190';
insert into widget values ('190',190,'','190',1,100,0,0,190,'',0,1,now(),now(),'');

# old 199
#delete from ui_conf where id=199;
#insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
#values(199,1,1,100,'/web/content/uiconf/kaltura/generic/kdp/kdp_regular.xml','kaltura kdp regular','400','362','','/flash/kdp/v1.2.6/kdp.swf',now(),now(),'',1);

# same as 48110 - the new 199 
delete from ui_conf where id=199;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(199,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/kdp_default_dark.xml','kdp kaltura default dark', 400,332,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);


# this will be the same as 48100 - the new default player
delete from ui_conf where id=48100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48100,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_dark.xml','kdp kaltura default dark', 400,364,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1);
delete from ui_conf where id=199;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(199,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/kdp_default_dark.xml','kdp kaltura default dark', 400,332,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);

#
# WIKI
#

delete from ui_conf where id=201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(201,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_standard.xml','wiki standard','400','420','','/flash/kdp/v1.0.5/kdp.swf','2008-04-13 17:34:01','2008-04-13 17:34:01','',1);
delete from widget where id = '201';
insert into widget values ('201',207,'','201',0,0,0,0,201,'',0,1,now(),now(),'');

delete from ui_conf where id=202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(202,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_compare.xml','wiki compare','400','420','','/flash/kdp/v1.0.5/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',1);
delete from widget where id = '202';
insert into widget values ('202',208,'','202',0,0,0,0,202,'',0,1,now(),now(),'');

delete from ui_conf where id=203;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(203,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_preview.xml','wiki preview','400','420','','/flash/kdp/v1.0.5/kdp.swf','2008-04-13 17:34:38','2008-04-13 17:34:38','',1);
delete from widget where id = '203';
insert into widget values ('203',209,'','203',0,0,0,0,203,'',0,1,now(),now(),'');

delete from ui_conf where id=204;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(204,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_embed.xml','wiki embed','400','394','','/flash/kdp/v1.0.5/kdp.swf','2008-04-13 17:34:55','2008-04-13 17:34:55','',1);
delete from widget where id = '204';
insert into widget values ('204',120,'','204',0,0,0,0,204,'',0,1,now(),now(),'');

delete from ui_conf where id=205;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(205,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_standard_medium.xml','wiki standard_medium','267','303',NULL,NULL,NULL,NULL);
delete from widget where id = '205';
insert into widget values ('205',209,'','205',0,0,0,0,205,'',0,1,now(),now(),'');


delete from ui_conf where id=206;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(206,1,0,0,'/web/content/uiconf/kaltura/generic/kdp_generic_preview.xml','kdp_generic_preview','400','420','','/swf/kdp/kdp.swf','2008-04-13 17:34:55','2008-04-13 17:34:55');
delete from widget where id = '206';
insert into widget values ('206',209,'','206',0,0,0,0,206,'',0,1,now(),now(),'');


delete from ui_conf where id=210;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(210,2,0,0,'/web/content/uiconf/kaltura/wiki/cw_wiki.xml','wiki cw','680','400','','/flash/kcw/v1.5/ContributionWizard.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

delete from ui_conf where id=220;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(220,3,0,0,'/web/content/uiconf/kaltura/wiki/se_wiki.xml','wiki se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

#
# the old wordpress ui conf
#
delete from ui_conf where id=300; 
delete from widget where id = '300';

#
# demo pages
#
delete from ui_conf where id=301;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(301,2,0,0,'/web/content/uiconf/kaltura/demopages/kdp_demopages_full.xml','demo pages full','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
delete from widget where id = '301';
insert into widget values ('301',209,'','301',13,13000,0,0,301,'',0,1,now(),now(),'');

delete from ui_conf where id=302;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(302,2,0,0,'/web/content/uiconf/kaltura/demopages/kdp_demopages_player_only.xml','demo pages player only','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
delete from widget where id = '302';
insert into widget values ('302',209,'','302',13,13000,0,0,302,'',0,1,now(),now(),'');

# 
# corp ui conf
#
delete from ui_conf where id = 303;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (303,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_player_only.xml','corp player only','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
delete from widget where id = '303';
insert into widget values ('303',209,'','303',0,0,0,0,303,'',0,1,now(),now(),'');

delete from ui_conf where id = 304;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (304,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_full.xml','corp full','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
delete from widget where id = '304';
insert into widget values ('304',209,'','304',0,0,0,0,304,'',0,1,now(),now(),'');

delete from ui_conf where id = 305;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (305,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_corp_homepage.xml','corp homepage','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
delete from widget where id = '305';
insert into widget values ('305',209,'','305',0,0,0,0,305,'',0,1,now(),now(),'');

delete from ui_conf where id = 306;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (306,2,0,0,'/web/content/uiconf/kaltura/corp/cw_corp.xml','corp cw',680,480,NULL,'/swf/ContributionWizard.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');

delete from ui_conf where id = 307;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (307,2,0,0,'/web/content/uiconf/kaltura/corp/se_corp.xml','corp se','890','546','','/swf/simpleeditor.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');


#
# new corp - demo players
#

delete from ui_conf where id = 310;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (310,1,0,0,'/web/content/uiconf/kaltura/corp_demos/telenovela/kdp_kd_telenovela.xml','kdp kd telenovela','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '310';
insert into widget values ('310',310,'','310',0,0,0,0,310,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 311;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (311,1,0,0,'/web/content/uiconf/kaltura/corp_demos/remixamerica/kdp_kd_remixamerica.xml','kdp kd remixamerica','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '311';
insert into widget values ('311',311,'','311',0,0,0,0,311,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 312;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (312,1,0,0,'/web/content/uiconf/kaltura/corp_demos/wordpress/kdp_kd_wordpress.xml','kdp kd wordpress','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '312';
insert into widget values ('312',312,'','312',0,0,0,0,312,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 313;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (313,1,0,0,'/web/content/uiconf/kaltura/corp_demos/wiki/kdp_kd_wiki.xml','kdp kd wiki','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '313';
insert into widget values ('313',313,'','313',200,0,0,0,313,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from widget where id = '313_101384';
insert into widget values ('313_101384','','','',18,0,101384,0,313,'',0,1,now(),now(),'');

delete from ui_conf where id = 314;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (314,1,0,0,'/web/content/uiconf/kaltura/corp_demos/pepsi/kdp_kd_pepsi.xml','kdp kd pepsi','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '314';
insert into widget values ('314',314,'','314',688,0,0,0,314,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 315;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (315,1,0,0,'/web/content/uiconf/kaltura/corp_demos/corp/kdp_kd_corp.xml','kdp kd corp','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00','',1);
delete from widget where id = '315';
insert into widget values ('315',315,'','315',0,0,0,0,315,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id = 316;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (316,1,0,0,'/web/content/uiconf/kaltura/corp_demos/kdp_plymedia.xml','kdp plymedia demo','0','0','','/flash/kdp/v1.0.15/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00','',1);
delete from widget where id = '316';
insert into widget values ('316',316,'','316',0,0,0,0,316,'',0,1,now(),now(),'');

delete from ui_conf where id=317;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(317,1,0,0,'/web/content/uiconf/kaltura/corp_demos/kdp_taboola.xml','kdp taboola demo','0','0','','/flash/kdp/v1.0.15/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',1);
delete from widget where id = '317';
insert into widget values ('317',317,'','317',0,0,0,0,317,'',0,1,now(),now(),'');

delete from ui_conf where id = 318;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (318,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_player_only_tiny.xml','corp player only','0','0','','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id = '318';
insert into widget values ('318',318,'','318',0,0,0,0,318,'',0,1,now(),now(),'');

# demo players with ads

delete from ui_conf where id=320;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(320,1,0,0,'/web/content/uiconf/kaltura/corp_demos/adaptv/kdp_kd_adaptv.xml','kd kdp adaptv','','',NULL,'/flash/kdp/v1.0.5/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00','',1);
delete from widget where id = '320';
insert into widget values ('320',320,'','320',0,0,0,0,320,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

delete from ui_conf where id=321;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(321,1,0,0,'/web/content/uiconf/kaltura/corp_demos/tremor/kdp_kd_tremor.xml','kd kdp tremor','','',NULL,'/flash/kdp/v1.0.7/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00','',1);
delete from widget where id = '321';
insert into widget values ('321',321,'','321',0,0,0,0,321,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');


delete from ui_conf where id = 330;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (330,2,0,0,'/web/content/uiconf/kaltura/corp_demos/kdp_wordpress_v1.1_dark.xml','corp wordpress dark','0','0','','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id = '330';
insert into widget values ('330',330,'','330',0,0,0,0,330,'',0,1,now(),now(),'');

#
# new corp
#

delete from ui_conf where id = 350;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (350,2,0,0,'/web/content/uiconf/kaltura/corp_new/cw.xml','corp cw',680,480,NULL,'/flash/kcw/v1.5/ContributionWizard.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');

delete from ui_conf where id = 360;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (360,2,0,0,'/web/content/uiconf/kaltura/corp_new/se.xml','corp se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');

delete from ui_conf where id = 370;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (370,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_homepage.xml','corp homepage','0','0','','/flash/kdp/v1.0.1/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');
delete from widget where id = '370';
insert into widget values ('370',370,'','370',0,0,0,0,370,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id = 371;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (371,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_player_only.xml','corp player only','0','0','','/flash/kdp/v1.0.1/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');
delete from widget where id = '371';
insert into widget values ('371',371,'','371',0,0,0,0,371,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id = 372;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (372,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_full.xml','corp full','0','0','','/flash/kdp/v1.0.1/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');
delete from widget where id = '372';
insert into widget values ('372',372,'','372',0,0,0,0,372,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id = 373;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (373,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_full_embeded.xml','corp full embeded','0','0','','/flash/kdp/v1.0.1/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');
delete from widget where id = '373';
insert into widget values ('373',373,'','373',0,0,0,0,373,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id = 374;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (374,2,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_player_only_tiny.xml','corp player only tiny','0','0','','/flash/kdp/v1.0.1/kdp.swf','2008-06-25 16:00:00','2008-06-25 16:00:00');
delete from widget where id = '374';
insert into widget values ('374',374,'','374',0,0,0,0,374,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

delete from ui_conf where id=380;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(380,2,0,0,'/web/content/uiconf/kaltura/corp_new/kcw_2.6.4/kcw.xml','corp cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=381;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(381,3,0,0,'/web/content/uiconf/kaltura/corp_new/kse_2.1.1/kse.xml','corp simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);

delete from ui_conf where id=382;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(382,1,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_1.1.11/kdp_v2.1_dark.xml','corp dark player v2.1',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '382';
insert into widget values ('382',382,'','382',0,0,0,0,382,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

delete from ui_conf where id=383;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(383,1,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_1.1.11/kdp_bigthink_player.xml','corp bigthink player without logo',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '383';
insert into widget values ('383',383,'','383',0,0,0,0,383,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

delete from ui_conf where id=384;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(384,1,0,0,'/web/content/uiconf/kaltura/corp_new/kdp_1.1.14/kdp_playlist.xml','kdp playlist', 400,320,'','/flash/kdp/v1.1.14/kdp.swf',now(),now(), null, 1);
delete from widget where id=384;
insert into widget values(384,384,'',384,0,100,0,0,384,'',0,1,null,null,'');

delete from widget where id = '385';
insert into widget values ('385','','','',0,0,0,0,43101,'',0,1,'2008-06-25 16:00:00','2008-06-25 16:00:00','');

#
# simple editor generic
#
delete from ui_conf where id=400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(400,3,0,0,'/web/content/uiconf/kaltura/se_generic.xml','generic simple editor','890','546','','/swf/simpleeditor.swf','2008-04-21 11:14:22','2008-04-21 11:14:22');


#
# wordpress
#

# wordpress kdp ui conf
delete from ui_conf where id=500;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(500,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress.xml','wordpress','','',NULL,'/flash/kdp/v1.0.5/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);
delete from widget where id = '500';
insert into widget values ('500',500,'','500',0,0,0,0,500,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# wordpress cw ui conf
delete from ui_conf where id=501;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(501,1,0,0,'/web/content/uiconf/kaltura/wordpress/cw_wordpress.xml','wordpress cw',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);
delete from widget where id = '501';
insert into widget values ('501',501,'','501',0,0,0,0,501,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# wordpress se ui conf
delete from ui_conf where id=502;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(502,3,0,0,'/web/content/uiconf/kaltura/wordpress/se_wordpress.xml','wordpress simple editor',890,546,'','/flash/kse/v2.0.6/simpleeditor.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);

# wordpress cw ui conf for comments
delete from ui_conf where id=503;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(503,1,0,0,'/web/content/uiconf/kaltura/wordpress/cw_wordpress_comments.xml','wordpress cw',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);
delete from widget where id = '503';
insert into widget values ('503',503,'','503',0,0,0,0,503,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# wordpress cw ui conf opened in se
delete from ui_conf where id=504;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(504,1,0,0,'/web/content/uiconf/kaltura/wordpress/cw_wordpress_in_se.xml','wordpress cw',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);
delete from widget where id = '504';
insert into widget values ('504',504,'','504',0,0,0,0,504,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# wordpress kdp wide for HD demo
delete from ui_conf where id=505;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(505,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_wide.xml','wordpress','','',NULL,'/swf/kdp/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '505';
insert into widget values ('505',505,'','505',0,0,0,0,505,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# kdp for share (large)
delete from ui_conf where id=506;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(506,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_share_large.xml','wordpress share large',410,364,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);

# kdp for share (small)
delete from ui_conf where id=507;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(507,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_share_small.xml','wordpress share small',250,244,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);

# kdp for wordpress v1.1 white blue
delete from ui_conf where id=510;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(510,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_v1.1_whiteblue.xml','wordpress v1.1',410,364,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);
delete from widget where id = '510';
insert into widget values ('510',510,'','510',0,0,0,0,510,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress share v1.1 white blue
delete from ui_conf where id=511;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(511,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_share_v1.1_whiteblue.xml','wordpress share v1.1',250,244,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);

# kdp for wordpress v1.1 dark
delete from ui_conf where id=512;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(512,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_v1.1_dark.xml','wordpress v1.1',410,364,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);
delete from widget where id = '512';
insert into widget values ('512',512,'','512',0,0,0,0,512,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress share v1.1 dark
delete from ui_conf where id=513;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(513,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_share_v1.1_dark.xml','wordpress share v1.1',250,244,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);

# kdp for wordpress v1.1 gray
delete from ui_conf where id=514;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(514,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_v1.1_gray.xml','wordpress v1.1',410,364,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);
delete from widget where id = '514';
insert into widget values ('514',514,'','514',0,0,0,0,514,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress share v1.1 gray
delete from ui_conf where id=515;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(515,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress_share_v1.1_gray.xml','wordpress share v1.1',250,244,NULL,'/flash/kdp/v1.0.15/kdp.swf','2008-09-01 15:30:00','2008-09-01 15:30:00', null, 1);


#
# wordpress 2.1
#

# kdp for wordpress v2.1 whiteblue
delete from ui_conf where id=520;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(520,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_1.1.11/kdp_wordpress_v2.1_whiteblue.xml','wordpress v2.1',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '520';
insert into widget values ('520',520,'','520',0,0,0,0,520,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress v2.1 dark
delete from ui_conf where id=521;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(521,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_1.1.11/kdp_wordpress_v2.1_dark.xml','wordpress v2.1',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '521';
insert into widget values ('521',521,'','521',0,0,0,0,521,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress v2.1 gray
delete from ui_conf where id=522;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(522,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_1.1.11/kdp_wordpress_v2.1_gray.xml','wordpress v2.1',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '522';
insert into widget values ('522',522,'','522',0,0,0,0,522,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');

# kdp for wordpress v2.2 for thumbnails
delete from ui_conf where id=523;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(523,1,0,0,'/web/content//uiconf/kaltura/wordpress/kdp_1.1.11/kdp_wordpress_thumb_v2.1_gray.xml','wordpress v2.2 thumbnails',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '523';
insert into widget values ('523',523,'','523',0,0,0,0,523,'',0,1,now(),now(),'');


#
# wordpress 2.3
#
# blue
delete from ui_conf where id=530;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(530,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_2.0.12/kdp_wordpress_v2_blue.xml','wordpress v2.3',410,364,NULL,'/flash/kdp/v2.0.12/kdp.swf','2009-05-17 15:30:00','2009-05-17 15:30:00', null, 1);

# dark
delete from ui_conf where id=531;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(531,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_2.0.12/kdp_wordpress_v2_dark.xml','wordpress v2.3',410,364,NULL,'/flash/kdp/v2.0.12/kdp.swf','2009-05-17 15:30:00','2009-05-17 15:30:00', null, 1);

# light
delete from ui_conf where id=532;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(532,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_2.0.12/kdp_wordpress_v2_light.xml','wordpress v2.3',410,364,NULL,'/flash/kdp/v2.0.12/kdp.swf','2009-05-17 15:30:00','2009-05-17 15:30:00', null, 1);

# thumbnail
delete from ui_conf where id=533;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(533,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_2.0.12/kdp_wordpress_v2_thumbnail.xml','wordpress v2.3',410,364,NULL,'/flash/kdp/v2.0.12/kdp.swf','2009-05-17 15:30:00','2009-05-17 15:30:00', null, 1);

#
# drupal
#

# kdp
delete from ui_conf where id=600;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(600,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_drupal.xml','drupal kdp','','',NULL,'/flash/kdp/v1.0.3/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);
delete from widget where id = '600';
insert into widget values ('600',600,'','600',0,0,0,0,600,'',0,1,'2008-06-10 15:30:00','2008-06-10 15:30:00','');

# cw
delete from ui_conf where id=601;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(601,2,0,0,'/web/content/uiconf/kaltura/drupal/cw_drupal.xml','drupal cw',680,480,NULL,'/flash/kcw/v1.5/ContributionWizard.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

# cw opened in se
delete from ui_conf where id=602;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(602,2,0,0,'/web/content/uiconf/kaltura/drupal/cw_drupal_in_se.xml','drupal cw',680,480,NULL,'/flash/kcw/v1.5/ContributionWizard.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

# se
delete from ui_conf where id=603;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(603,3,0,0,'/web/content/uiconf/kaltura/drupal/se_drupal.xml','drupal simple editor',890,546,'','/flash/kse/v2.0.3/simpleeditor.swf','2008-05-19 15:30:00','2008-05-19 15:30:00', null, 1);

# players
delete from ui_conf where id=604;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(604,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_dark_remix.xml','drupal dark kdp (remix)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

delete from ui_conf where id=605;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(605,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_dark_view.xml','drupal dark kdp (view)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

delete from ui_conf where id=606;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(606,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_gray_remix.xml','drupal gray kdp (remix)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

delete from ui_conf where id=607;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(607,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_gray_view.xml','drupal gray kdp (view)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

delete from ui_conf where id=608;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(608,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_whiteblue_remix.xml','drupal white-blue kdp (remix)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);

delete from ui_conf where id=609;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(609,1,0,0,'/web/content/uiconf/kaltura/drupal/kdp_1.1.11/kdp_drupal_v2.1_whiteblue_view.xml','drupal white-blue kdp (view)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', null, 1);


# TESTS

delete from ui_conf where id=1001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1001,2,0,0,'/web/content/uiconf/dev/tests/cw_generic.xml','wiki cw','680','400','','','2008-04-13 17:34:26','2008-04-13 17:34:26');

delete from ui_conf where id=1002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1002,1,0,0,'/web/content/uiconf/dev/tests/kdp_reshet_player.xml','wordpress','','',NULL,'/swf/kdp/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '1002';
insert into widget values ('1002',1002,'','1002',0,0,0,0,1002,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

delete from ui_conf where id=1003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1003,2,0,0,'/web/content/uiconf/kaltura/cw_generic.xml','cw generic2','680','400','','/swf/cw-test/ContributionWizard.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

delete from widget where id = '1004';
insert into widget values ('1004',0,'','1004',1,36400,0,0,303,'',0,1,now(),now(),'');

delete from ui_conf where id=1005;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1005,1,0,0,'/web/content/uiconf/dev/tests/kdp_eitan.xml','kdp eitan','','',NULL,'/swf/kdp/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '1005';
insert into widget values ('1005',1005,'','1005',0,0,0,0,1005,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

delete from ui_conf where id=1006;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1006,1,0,0,'/web/content/uiconf/dev/tests/kdp_adaptv_player.xml','kdp adaptv','','',NULL,'/swf/kdp/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '1006';
insert into widget values ('1006',1006,'','1006',528,0,0,0,1006,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

delete from ui_conf where id=1007;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1007,1,0,0,'/web/content/uiconf/remixamerica/kdp_remix_minimal.xml','test remix','','',NULL,'/flash/kdp/v1.0.8/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '1007';
insert into widget values ('1007',1007,'','1007',528,0,0,0,1007,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

delete from ui_conf where id=1050;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1050,3,0,0,'/web/content/uiconf/dev/se_dev.xml','se dev', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);

delete from ui_conf where id=1100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(1100,2,0,0,'/web/content/uiconf/dev/graphics/cw_generic.xml','cw dev graphics','680','400','','/flash/kcw/dev/ContributionWizard.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');

delete from ui_conf where id=1102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1102,3,0,0,'/web/content/uiconf/dev/graphics/kse_2.1.3/kse.xml','se dev', 890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'',1);

delete from ui_conf where id=1150;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1150,3,0,0,'/web/content/uiconf/dev/tests/se_generic.xml','se generic tests', 890,546,'','/flash/kse/v2.1.0/simpleeditor.swf',now(),now(),'',1);

# demo widget for reshet using wordpress layout
delete from widget where id = '10007'; 
insert into widget values ('10007',10007,'','10007',13,13000,0,0,500,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for Taboola
delete from ui_conf where id=1200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1200,2,0,0,'/web/content/uiconf/dev/tests/taboola/kdp_taboola.xml','kdp taboola','680','400','','/demos/taboola/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1200';
insert into widget values ('1200',1200,'','1200',528,0,0,0,1200,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for Tremor
delete from ui_conf where id=1210;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1210,2,0,0,'/web/content/uiconf/dev/tests/tremor/kdp_tremor.xml','kdp tremor','680','400','','/demos/tremor/boaz/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1210';
insert into widget values ('1210',1210,'','1210',528,0,0,0,1210,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for Nuconomy
delete from ui_conf where id=1220;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1220,2,0,0,'/web/content/uiconf/dev/tests/nuconomy/kdp_nuconomi.xml','kdp tremor','680','400','','/demos/nuconomy/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1220';
insert into widget values ('1220',1220,'','1220',528,0,0,0,1220,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for Nuconomy + Adaptv
delete from ui_conf where id=1221;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1221,2,0,0,'/web/content/uiconf/dev/tests/nuconomy/kdp_nuconomi_stats.xml','kdp nuconomi stats','680','400','','/demos/nuconomy/demo1/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1221';
insert into widget values ('1221',1221,'','1221',528,0,0,0,1221,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for pbnation + adaptv
delete from ui_conf where id=1230;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1230,2,0,0,'/web/content/uiconf/dev/tests/pbnation/kdp_pbnation_adpatv.xml','kdp pbnation','680','400','','/demos/pbnation/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1230';
insert into widget values ('1230',1230,'','1230',528,0,0,0,1230,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# demo widget for plymedia
delete from ui_conf where id=1240;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(1240,2,0,0,'/web/content/uiconf/dev/tests/plymedia/kdp_plymedia.xml','kdp plymedia','680','400','','/demos/plymedia/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',0);
delete from widget where id = '1240';
insert into widget values ('1240',1240,'','1240',528,0,0,0,1240,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');

# OLD REMIX AMERICA

delete from ui_conf where id=10000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(10000,3,0,0,'/web/content/uiconf/remixamerica/se_remix_america.xml','remixamerica simple editor',890,546,'','/swf/simpleeditor.swf','2008-04-21 09:45:10','2008-04-21 09:45:10');

delete from ui_conf where id=10001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(10001,2,0,0,'/web/content/uiconf/remixamerica/cw_remix_response.xml','remixamerica response cw',680,480,'','/swf/ContributionWizard.swf','2008-04-21 10:03:56','2008-04-21 10:03:56');

delete from ui_conf where id=10002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(10002,3,0,0,'/web/content/uiconf/remixamerica/se_remix_america_dev.xml','remixamerica simple editor',890,546,'','/flash/kse/v2.0.5/simpleeditor.swf','2008-04-21 09:45:10','2008-04-21 09:45:10');

delete from ui_conf where id=10003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(10003,2,0,0,'/web/content/uiconf/remixamerica/cw_remix_adveditor.xml','remixamerica adveditor cw',680,480,'','/swf/ContributionWizard.swf','2008-04-21 10:03:56','2008-04-21 10:03:56');

delete from ui_conf where id=10004;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(10004,1,0,0,'/web/content/uiconf/remixamerica/kdp_remix_minimal.xml','remix hd demo','','',NULL,'/swf/kdp/kdp.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');
delete from widget where id = '10004';
insert into widget values ('10004',10004,'','10004',528,52800,0,0,10004,'',0,1,'2008-05-19 15:30:00','2008-05-19 15:30:00','');


#
# WePlay (519)
#

delete from ui_conf where id=11001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11001,2,0,0,'/web/content/uiconf/weplay/cw_weplay.xml','weplay_cw','680','400',NULL,NULL,'2008-04-29 05:37:37','2008-04-29 05:37:37');

delete from ui_conf where id=11002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11002,2,0,0,'/web/content/uiconf/weplay/cw_weplay_nosources.xml','weplay cw with only upload','680','400',NULL,NULL,'2008-04-29 05:37:37','2008-04-29 05:37:37');

delete from ui_conf where id=11003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11003,2,0,0,'/web/content/uiconf/weplay/cw_weplay_sandbox.xml','weplay cw on sandbox','680','400',NULL,NULL,'2008-04-29 05:37:37','2008-04-29 05:37:37','',0);

delete from ui_conf where id=11004;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11004,2,0,0,'/web/content/uiconf/weplay/cw_weplay_sandbox2.xml','weplay cw 1.6.3 on sandbox','680','400','','/flash/kcw/v1.6.3/ContributionWizard.swf','2008-04-29 05:37:37','2008-04-29 05:37:37','',0);

delete from ui_conf where id=11005;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11005,2,300,30000,'/web/content/uiconf/weplay/kcw_album_1.6.3/kcw_weplay_albums.xml','weplay kcw album 1.6.3','680','400','','/flash/kcw/v1.6.3/ContributionWizard.swf','2008-04-29 05:37:37','2008-04-29 05:37:37','',1);

delete from ui_conf where id=11006;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11006,2,0,0,'/web/content/uiconf/weplay/cw_weplay_sandbox2.xml','weplay cw 1.6.4 on sandbox','680','400','','/flash/kcw/v1.6.4/ContributionWizard.swf','2008-04-29 05:37:37','2008-04-29 05:37:37','',0);

delete from ui_conf where id=11007;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11007,2,300,30000,'/web/content/uiconf/weplay/kcw_album_1.6.4/kcw_weplay_albums.xml','weplay kcw album 1.6.4','680','400','','/flash/kcw/v1.6.4/ContributionWizard.swf','2008-04-29 05:37:37','2008-04-29 05:37:37','',1);

delete from ui_conf where id=11008;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11008,2,0,0,'/web/content/uiconf/weplay/kcw_album_1.6.4/kcw_weplay_albums.xml','weplay cw with 1gb limit','680','400','','/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(),'',0);

delete from ui_conf where id=11101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11101,2,0,0,'/web/content/uiconf/weplay/kdp_weplay_minimal.xml','kdp weplay minimal','400','359','','/swf/kdp/kdp.swf','2008-04-29 05:37:37','2008-04-29 05:37:37');

delete from ui_conf where id=11110;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11110,2,0,0,'/web/content/uiconf/weplay/kdp_weplay_minimal_2.xml','kdp weplay minimal 2','400','337','','/flash/kdp/v1.0.15/kdp.swf','2008-04-29 05:37:37','2008-04-29 05:37:37');

delete from ui_conf where id=11115;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11115,2,0,0,'/web/content/uiconf/weplay/kdp_weplay_minimal_no_header.xml','kdp weplay minimal no header','400','337','','/flash/kdp/v1.0.15/kdp.swf','2008-04-29 05:37:37','2008-04-29 05:37:37');

# weplay new version of player
delete from ui_conf where id=11116;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn,tags,custom_data,status)  
values(11116,2,0,0,'/web/content/uiconf/weplay/kdp_weplay_minimal_no_header.xml','kdp weplay minimal no header','400','337','','/flash/kdp/v1.2.3/kdp.swf',now(),now(),null,1,null,null,2);

delete from ui_conf where id=11117;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(11117,2,0,0,'/web/content/uiconf/weplay/kdp_1.2.3/kdp_weplay_minimal_watermark.xml','kdp weplay minimal watermark','400','337','','/flash/kdp/v1.2.3/kdp.swf',now(),now(),null,1);

delete from widget where id=11116;
insert into widget values(11116,11116,'',11116,300,30000,0,0,11116,'',0,1,null,null,'');

delete from widget where id=11117;
insert into widget values(11117,11117,'',11117,300,30000,0,0,11117,'',0,1,null,null,'');

delete from widget where id=11117;
insert into widget values(11117,11117,'',11117,519,51900,0,0,11117,'',0,1,null,null,'');

delete from widget where id=11101;
insert into widget values(11101,11101,'',11101,519,51900,0,0,11101,'',0,1,null,null,'');

delete from widget where id=11102;
insert into widget values(11102,11102,'',11102,642,64200,0,0,11101,'',0,1,null,null,'');

delete from widget where id=11103;
insert into widget values(11103,11103,'',11103,300,30000,0,0,11101,'',0,1,null,null,'');

# sandbox
delete from widget where id=11115;
insert into widget values(11115,11115,'',11115,519,51900,0,0,11115,'',0,1,null,null,'');

# production
delete from widget where id=11115;
insert into widget values(11115,11115,'',11115,300,30000,0,0,11115,'',0,1,null,null,'');


delete from ui_conf where id=11500;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(11500,5,0,0,'/web/content/uiconf/weplay/uploader.xml','weplay uploader','','','','/flash/kupload/v1.0.6/KUpload.swf',now(),now());

#
# Remix America (315,387)
#
delete from ui_conf where id=12001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(12001,2,0,0,'/web/content/uiconf/remixamerica/cw_remix_response.xml','cw remix response', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=12002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(12002,2,0,0,'/web/content/uiconf/remixamerica/cw_remix.xml','cw remix', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=12003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(12003,2,0,0,'/web/content/uiconf/remixamerica/cw_remix_test.xml','cw remix test', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=12004;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(12004,2,0,0,'/web/content/uiconf/remixamerica/cw_remix_upload.xml','cw remix upload', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=12101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(12101,1,0,0,'/web/content/uiconf/remixamerica/kdp_remix_minimal.xml','kdp remix minimal', 400,320,'','/swf/kdp/kdp.swf',now(),now());

delete from widget where id=12101;
insert into widget values(12101,12101,'',12101,315,31500,0,0,12101,'',0,1,null,null,'');

delete from ui_conf where id=12102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(12102,1,0,0,'/web/content/uiconf/remixamerica/kdp_remix_embed.xml','kdp remix embed', 400,358,'','/swf/kdp/kdp.swf',now(),now());

delete from widget where id=12102;
insert into widget values(12102,12102,'',12102,315,31500,0,0,12102,'',0,1,null,null,'');

delete from ui_conf where id=12201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)
values(12201,3,387,38700,'/web/content/uiconf/remixamerica/se_remix_america_v2-0-5.xml','remixamerica simple editor',890,546,'','/flash/kse/v2.0.5/simpleeditor.swf','2008-04-21 09:45:10','2008-04-21 09:45:10','',1);

#
# Telenovela 3 layouts
#
delete from ui_conf where id=13001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(13001,2,593,59300,'/web/content/uiconf/telenovela/kcw_telenovela.xml','kcw telenovela', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(),'',1);

delete from ui_conf where id=13002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(13002,2,593,59300,'/web/content/uiconf/telenovela/kcw_1.6.5/kcw_telenovela.xml','kcw telenovela', 680,400,'','/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(),'',1);

delete from ui_conf where id=13004;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(13004,2,593,59300,'/web/content/uiconf/telenovela/kcw_telenovela_2.xml','kcw telenovela', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(),'',1);


delete from ui_conf where id = 13000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (13000,1,0,0,'/web/content/uiconf/telenovela/kdp_telenovela.xml','telenovela 3 layouts','0','0','','/swf/kdp/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00');
delete from widget where id = '13000';
insert into widget values ('13000',13000,'','13000',0,0,0,0,13000,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 13101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (13101,1,0,0,'/web/content/uiconf/telenovela/kdp_telenovela_2.xml','kdp telenovela final','0','0','','/flash/kdp/v1.0.5/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '13101';
insert into widget values ('13101',13101,'','13101',593,59300,0,0,13101,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id = 13102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (13102,1,0,0,'/web/content/uiconf/telenovela/kdp_1.1.3/kdp_telenovela_3_players.xml','kdp telenovela plymedia','0','0','','/flash/kdp/v1.1.7/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
# widget 13102 has a special security_type=3 
delete from widget where id = '13102';
insert into widget values ('13102',13102,'','13102',593,59300,0,0,13102,'',3,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

# 13103 will point to ui_conf_id 13103
delete from ui_conf where id = 13103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (13103,1,0,0,'/web/content/uiconf/telenovela/kdp_1.1.3/kdp_telenovela_3_players.xml','kdp telenovela plymedia','0','0','','/flash/kdp/v1.1.7/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '13103';
insert into widget values ('13103',13103,'','13103',593,59300,0,0,13103,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

# 13104 used for testing kdp version 1.1.8 on dorimedia site
delete from ui_conf where id = 13104;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (13104,1,0,0,'/web/content/uiconf/telenovela/kdp_1.1.3/kdp_telenovela_3_players.xml','kdp telenovela plymedia','0','0','','/flash/kdp/v1.1.8/kdp.swf','2008-05-15 12:56:00','2008-05-15 12:56:00','',1);
delete from widget where id = '13104';
insert into widget values ('13104',13104,'','13104',593,59300,0,0,13104,'',0,1,'2008-05-15 12:56:00','2008-05-15 12:56:00','');

delete from ui_conf where id=13201; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(13201,3,593,59300,'/web/content/uiconf/telenovela/se_telenovela.xml','metacafe simple editor for integration only',890,546,'','/flash/kse/v2.0.5/simpleeditor.swf','2008-07-16 11:30:00','2008-07-16 11:30:00');

delete from ui_conf where id=13202; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(13202,3,593,59300,'/web/content/uiconf/telenovela/kse_telenovela.xml','telenovela simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now());

#
# pb nation (392)
#

delete from ui_conf where id=14000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(14000,2,0,0,'/web/content/uiconf/pbnation/cw_pbnation.xml','cw pbnation', 680,400,'','/swf/ContributionWizard.swf',now(),now());

delete from ui_conf where id=14101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(14101,1,0,0,'/web/content/uiconf/pbnation/kdp_pbnation_player_only.xml','kdp pbnation player only', 400,320,'','/swf/kdp/kdp.swf',now(),now());

delete from widget where id=14101;
insert into widget values(14101,14101,'',14101,392,39200,0,0,14101,'',0,1,null,null,'');

delete from ui_conf where id=14102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(14102,1,0,0,'/web/content/uiconf/pbnation/kdp_pbnation_with_ads.xml','kdp pbnation player width ads', 400,320,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());

delete from widget where id=14102;
insert into widget values(14102,14102,'',14102,392,39200,0,0,14102,'',0,1,null,null,'');

#
# blastbeat (395)
#

delete from ui_conf where id=15000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(15000,2,0,0,'/web/content/uiconf/blastbeat/cw_blastspace.xml','cw blastspace', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=15001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(15001,2,0,0,'/web/content/uiconf/blastbeat/cw_blastbeat.xml','cw blastbeat', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=15002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(15002,2,0,0,'/web/content/uiconf/blastbeat/cw_blastbeat_blastspace.xml','cw blastbeat/blastspace', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=15003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(15003,2,0,0,'/web/content/uiconf/blastbeat/cw_blastbeat_branded.xml','cw blastbeat/blastspace', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=15101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15101,1,0,0,'/web/content/uiconf/blastbeat/kdp_blastbeat_player_only.xml','kdp blastbeat player only', 400,320,'','/flash/kdp/v1.0.5/kdp.swf',now(),now());

delete from ui_conf where id=15102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15102,1,0,0,'/web/content/uiconf/blastbeat/kdp_blastspace_player_only.xml','kdp blastspace player only', 400,320,'','/flash/kdp/v1.0.5/kdp.swf',now(),now());

delete from ui_conf where id=15104;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15104,1,0,0,'/web/content/uiconf/blastbeat/kdp_blastbeat_branded.xml','kdp blastbeat player only', 380,319,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());

delete from ui_conf where id=15105;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15105,1,0,0,'/web/content/uiconf/blastbeat/kdp_blastbeat_branded_with_share.xml','kdp blastspace player only', 380,319,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());

delete from ui_conf where id=15106;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15106,1,0,0,'/web/content/uiconf/blastbeat/kdp_1.2.3/kdp_blastbeat_player_ads.xml','kdp blastspace ads', 380,319,'','/flash/kdp/v1.2.3/kdp.swf',now(),now());

delete from ui_conf where id = 15200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (15200,3,0,0,'/web/content/uiconf/blastbeat/se_blastspace.xml','blastspace se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');

delete from ui_conf where id = 15201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (15201,3,0,0,'/web/content/uiconf/blastbeat/se_blastbeat.xml','blastbeat se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');

delete from ui_conf where id = 15202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (15202,3,0,0,'/web/content/uiconf/blastbeat/se_blastbeat_blastspace.xml','blastspace se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');

delete from ui_conf where id = 15203;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values (15203,3,395,39500,'/web/content/uiconf/blastbeat/se_blastbeat_branded.xml','blastspace se','890','546','','/flash/kse/v2.0/simpleeditor.swf','2008-06-15 14:30:00','2008-06-15 14:30:00');


delete from widget where id=15102;
insert into widget values(15102,15102,'',15102,395,39500,0,0,15102,'',0,1,null,null,'');

delete from widget where id=15101;
insert into widget values(15101,15101,'',15101,812,81200,0,0,15101,'',0,1,null,null,'');

delete from widget where id=15103;
insert into widget values(15103,15103,'',15103,395,39500,0,0,15103,'',0,1,null,null,'');

delete from ui_conf where id=15103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(15103,1,0,0,'/web/content/uiconf/blastbeat/kdp_blastspace_remix_button.xml','kdp blastspace with remix button', 400,320,'','/swf/kdp/kdp.swf',now(),now());

delete from widget where id=15104;
insert into widget values(15104,15104,'',15104,395,39500,0,0,15104,'',0,1,null,null,'');

delete from widget where id=15105;
insert into widget values(15105,15105,'',15105,395,39500,0,0,15105,'',0,1,null,null,'');

delete from widget where id=15106;
insert into widget values(15106,15106,'',15106,812,81200,0,0,15104,'',0,1,null,null,'');

delete from widget where id=15107;
insert into widget values(15107,15107,'',15107,812,81200,0,0,15105,'',0,1,null,null,'');

delete from widget where id='395_15106';
insert into widget values('395_15106','','','',395,39500,0,0,15106,'',0,1,null,null,'');

delete from widget where id='812_15106';
insert into widget values('812_15106','','','',812,81200,0,0,15106,'',0,1,null,null,'');


delete from ui_conf where id=15400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(15400,4,0,0,'/web/content/uiconf/blastbeat/kae_1.0.10/kae_generic_generic.xml','blastbeat ae',750,640,'','/flash/kae/v1.0.10/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

#
# aha (684)
#

delete from ui_conf where id=16000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(16000,2,0,0,'/web/content/uiconf/aha/cw_aha.xml','cw aha', 680,400,'','/swf/ContributionWizard.swf',now(),now());

delete from ui_conf where id=16101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(16101,1,0,0,'/web/content/uiconf/aha/kdp_aha_player_only.xml','kdp aha player only', 400,320,'','/swf/kdp-080605/kdp/kdp.swf',now(),now());

delete from widget where id=16101;
insert into widget values(16101,16101,'',16101,684,68400,0,0,16101,'',0,1,null,null,'');

delete from ui_conf where id=16102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(16102,1,0,0,'/web/content/uiconf/aha/kdp_aha_player_only_1.xml','kdp aha player only', 400,320,'','/flash/kdp/v1.0/kdp.swf',now(),now());

delete from widget where id=16102;
insert into widget values(16102,16102,'',16102,684,68400,0,0,16102,'',0,1,null,null,'');


#
# pepsi (688)
#

delete from ui_conf where id=17000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(17000,2,0,0,'/web/content/uiconf/pepsi/cw_pepsi.xml','cw pepsi', 680,400,'','/swf/ContributionWizard.swf',now(),now());

delete from ui_conf where id=17101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(17101,1,0,0,'/web/content/uiconf/pepsi/kdp_pepsi_player_only.xml','kdp pepsi player only', 320,279,'','/swf/kdp-080612/kdp/kdp.swf',now(),now());

delete from widget where id=17101;
insert into widget values(17101,17101,'',17101,688,68800,0,0,17101,'',0,1,null,null,'');


#
# freelancers (738)
#

delete from ui_conf where id=18000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(18000,2,0,0,'/web/content/uiconf/freelancers/cw_freelancers_generic.xml','cw freelancers', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=18101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(18101,1,0,0,'/web/content/uiconf/freelancers/kdp_freelancers_player.xml','kdp freelancers player', 320,279,'','/flash/kdp/v1.0.3/kdp.swf',now(),now());

delete from widget where id=18101;
insert into widget values(18101,18101,'',18101,738,73800,0,0,18101,'',0,1,null,null,'');

delete from widget where id=18102;
insert into widget values(18102,18102,'',18102,532,53200,0,0,18101,'',0,1,null,null,'');

#
# QA ()
#
# moved to the qa project qa/scripts/ui_conf_widget_data.sql
	


#
# Metacafe (sandbox 536, production 870)
#

# se production 
delete from ui_conf where id=20001; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(20001,3,870,87000,'/web/content/uiconf/metacafe/se_metacafe.xml','metacafe simple editor',890,546,'','/flash/kse/v2.0.8/simpleeditor.swf','2008-05-19 15:30:00','2008-05-19 15:30:00');

# se integration
delete from ui_conf where id=20002; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(20002,3,536,53600,'/web/content/uiconf/metacafe/se_metacafe_integration.xml','metacafe simple editor for integration only',890,546,'','/flash/kse/v2.0/simpleeditor.swf','2008-06-01 11:30:00','2008-06-01 11:30:00');

# cw production 
delete from ui_conf where id=20101; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(20101,2,870,87000,'/web/content/uiconf/metacafe/cw_metacafe.xml','metacafe cw',680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf','2008-06-01 11:30:00','2008-06-01 11:30:00');

# cw integration
delete from ui_conf where id=20102; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(20102,2,536,53600,'/web/content/uiconf/metacafe/cw_metacafe_integration.xml','metacafe cw for integration',680,400,'','/flash/kcw/v1.5/ContributionWizard.swf','2008-06-01 11:30:00','2008-06-01 11:30:00');

# kdp
delete from widget where id=20201;
insert into widget values(20201,20201,'',20201,536,53600,0,0,20201,'',0,1,null,null,'');
delete from ui_conf where id=20201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(20201,1,536,53600,'/web/content/uiconf/metacafe/kdp_metacafe.xml','metacafe kdp', 400,320,'','/flash/kdp/v1.0.4/kdp.swf','2008-07-06 17:00:00','2008-07-06 17:00:00','',1);

# kdp specialized metacafe version
delete from ui_conf where id=20202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(20202,1,870,87000,'/web/content/uiconf/metacafe/kdp_metacafe.xml','metacafe kdp', 400,320,'','/flash/kdp/v1.0.8/kdp_metacafe.swf','2008-07-06 17:00:00','2008-07-06 17:00:00','',1);
delete from widget where id=20202;
insert into widget values(20202,20202,'',20202,870,87000,0,0,20202,'',0,1,null,null,'');

# 
# Dance Tech
#

delete from ui_conf where id=21001; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(21001,3,792,79200,'/web/content/uiconf/dancetech/se_dancetech.xml','dance tech simple editor',890,546,'','/flash/kse/v2.0/simpleeditor.swf','2008-06-01 19:00:00','2008-06-01 19:00:00','',0);

delete from ui_conf where id=21101; 
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(21101,2,792,79200,'/web/content/uiconf/dancetech/cw_dancetech.xml','dance tech cw',680,400,'','/flash/kcw/v1.5/ContributionWizard.swf','2008-06-01 19:00:00','2008-06-01 19:00:00','',0);

delete from widget where id=21201;
insert into widget values(21201,21201,'',21201,792,79200,0,0,21201,'',0,1,null,null,'');

delete from ui_conf where id=21201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(21201,1,792,79200,'/web/content/uiconf/dancetech/kdp_dancetech.xml','dance tech kdp', 400,320,'','/flash/kdp/v1.0.3/kdp.swf','2008-06-01 19:00:00','2008-06-01 19:00:00','',0);

#
# federated media (sandbox 537, production 945)
#

delete from ui_conf where id=22000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(22000,2,0,0,'/web/content/uiconf/federatedmedia/cw_federatedmedia_generic.xml','cw federatedmedia', 680,400,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=22101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(22101,1,0,0,'/web/content/uiconf/federatedmedia/kdp_federatedmedia_player.xml','kdp federatedmedia player', 320,279,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());

delete from widget where id=22101;
insert into widget values(22101,22101,'',22101,537,53700,0,0,22101,'',0,1,null,null,'');

delete from ui_conf where id=22201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(22201,3,0,0,'/web/content/uiconf/federatedmedia/se_federatedmedia.xml','se federatedmedia', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);

#
# fuse (813) on sandbox
#

delete from ui_conf where id=23000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(23000,2,813,81300,'http://dev.fusedevelopment.com/Lucozade2008/cw_fuse.xml','cw fuse', '680','400' ,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=23100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(23100,1,813,81300,'http://dev.fusedevelopment.com/Lucozade2008/kdp_fuse_player_only.xml','kdp fuse player only', 400,320,'','/flash/kdp/v1.0.5/kdp.swf',now(),now());

delete from widget where id=23100;
insert into widget values(23100,23100,'',23100,813,81300,0,0,23100,'',0,1,null,null,'');


#
# fuse (1147) on production
#


delete from ui_conf where id=23000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(23000,2,1147,114700,'http://dev.fusedevelopment.com/Lucozade2008/cw_fuse.xml','cw fuse', '680','400' ,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=23100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(23100,1,1147,114700,'/web/content/uiconf/fuse/kdp_fuse_player_only.xml','kdp fuse player only', 400,320,'','/flash/kdp/v1.0.9/kdp.swf',now(),now());

delete from widget where id=23100;
insert into widget values(23100,23100,'',23100,1147,114700,0,0,23100,'',0,1,null,null,'');

#
# hmd (819) on sandbox & production
#

delete from ui_conf where id=24000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(24000,2,819,81900,'/web/content/uiconf/hmd/cw_hmd.xml','cw hmd', '680','400' ,'','/flash/kcw/v1.5/ContributionWizard.swf',now(),now());

delete from ui_conf where id=24001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(24001,2,819,81900,'/web/content/uiconf/hmd/cw_hmd_1.6.xml','cw hmd 1.6', '680','400' ,'','/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(),'',1);

delete from ui_conf where id=24100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(24100,1,819,81900,'/web/content/uiconf/hmd/kdp_hmd.xml','kdp hmd', 400,320,'','/flash/kdp/v1.0.5/kdp.swf',now(),now());
delete from widget where id=24100;
insert into widget values(24100,24100,'',24100,819,81900,0,0,24100,'',0,1,null,null,'');

delete from ui_conf where id=24101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(24101,1,819,81900,'/web/content/uiconf/hmd/kdp_HMD_r1.xml','kdp hmd', 400,320,'','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id=24101;
insert into widget values(24101,24101,'',24101,819,81900,0,0,24101,'',0,1,null,null,'');

delete from ui_conf where id=24201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(24201,3,819,81900,'/web/content/uiconf/hmd/se_hmd.xml','se hmd', 890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'',1);

#
# metacafe heroes (1343) on production
#

delete from ui_conf where id=25100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(25100,1,1343,134300,'/web/content/uiconf/metacafe/heroes/kdp_heroes.xml','kdp heroes', 400,320,'','/flash/kdp/v1.0.9/kdp.swf',now(),now());
delete from widget where id=25100;
insert into widget values(25100,25100,'',25100,1343,134300,0,0,25100,'',0,1,null,null,'');

delete from ui_conf where id=25201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(25201,3,1343,134300,'/web/content/uiconf/metacafe/heroes/se_heroes.xml','se heroes', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);

#
# dremak (530) on sandbox
#
delete from ui_conf where id=26000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(26000,2,530,53000,'/web/content/uiconf/dremak/cw_dremak.xml','cw dremak', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(),'',1);

delete from ui_conf where id=26101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(26101,1,530,53000,'/web/content/uiconf/dremak/kdp_dremak.xml','kdp dremak', 476,434,'','/flash/kdp/v1.0.11/kdp.swf',now(),now(),'',1);

delete from widget where id=26101;
insert into widget values(26101,26101,'',26101,530,53000,0,0,26101,'',0,1,null,null,'');

delete from ui_conf where id=26201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(26201,3,530,53000,'/web/content/uiconf/dremak/se_dremak.xml','se dremak', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);


#
# dremak (6095) on production
#
delete from ui_conf where id=26000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(26000,2,6095,609500,'/web/content/uiconf/dremak/cw_dremak.xml','cw dremak', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(),'',1);

delete from ui_conf where id=26101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(26101,1,6095,609500,'/web/content/uiconf/kdp_1.2.3/kdp_dremak.xml','kdp dremak', 300,308,'','/flash/kdp/v1.1.13/kdp.swf',now(),now(),'',1);

delete from widget where id=26101;
insert into widget values(26101,26101,'',26101,6095,609500,0,0,26101,'',0,1,null,null,'');

delete from ui_conf where id=26201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(26201,3,6095,609500,'/web/content/uiconf/dremak/se_dremak.xml','se dremak', 890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'',1);

#
# athletix nation (820) on SANDBOX
#

delete from ui_conf where id=27000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(27000,2,820,82000,'/web/content/uiconf/athletixnation/cw_athletixnation.xml','cw athletixnation', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=27100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(27100,1,820,82000,'/web/content/uiconf/athletixnation/kdp_athletixnation.xml','kdp athletixnation', 320,279,'','/flash/kdp/v1.0.11/kdp.swf',now(),now());

delete from widget where id=27100;
insert into widget values(27100,27100,'',27100,820,82000,0,0,27100,'',0,1,null,null,'');

delete from ui_conf where id=27101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(27101,1,820,82000,'/web/content/uiconf/athletixnation/kdp_athletixnation_player_only.xml','kdp athletixnation player only', 320,279,'','/flash/kdp/v1.0.11/kdp.swf',now(),now());

delete from widget where id=27101;
insert into widget values(27101,27101,'',27101,820,82000,0,0,27101,'',0,1,null,null,'');

delete from ui_conf where id=27200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(27200,3,820,82000,'/web/content/uiconf/athletixnation/se_athletixnation.xml','se athletixnation', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);


#
# athletix nation (2217) on PRODUCTION
#

delete from ui_conf where id=27000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(27000,2,2217,221700,'/web/content/uiconf/athletixnation/cw_athletixnation.xml','cw athletixnation', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=27100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(27100,1,2217,221700,'/web/content/uiconf/athletixnation/kdp_athletixnation.xml','kdp athletixnation', 320,279,'','/flash/kdp/v1.0.11/kdp.swf',now(),now());

delete from widget where id=27100;
insert into widget values(27100,27100,'',27100,2217,221700,0,0,27100,'',0,1,null,null,'');

delete from ui_conf where id=27101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27101,1,2217,221700,'/web/content/uiconf/athletixnation/kdp_athletixnation_player_only.xml','kdp athletixnation player only', 320,279,'','/flash/kdp/v1.0.11/kdp.swf',now(),now(),'',1);

delete from widget where id=27101;
insert into widget values(27101,27101,'',27101,2217,221700,0,0,27101,'',0,1,null,null,'');

delete from ui_conf where id=27200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(27200,3,2217,221700,'/web/content/uiconf/athletixnation/se_athletixnation.xml','se athletixnation', 890,546,'','/flash/kse/v2.0.8/simpleeditor.swf',now(),now(),'',1);

delete from ui_conf where id=27102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27102,1,2217,221700,'/web/content/uiconf/athletixnation/kdp_athletixnation_ads.xml','kdp athletixnation ads', 320,279,'','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id=27102;
insert into widget values(27102,27102,'',27102,2217,221700,0,0,27102,'',0,1,null,null,'');

delete from ui_conf where id=27103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27103,1,2217,221700,'/web/content/uiconf/athletixnation/kdp_athletixnation_ads_nuconomy.xml','kdp athletixnation ads nuconomy', 320,279,'','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id=27103;
insert into widget values(27103,27103,'',27103,2217,221700,0,0,27103,'',0,1,null,null,'');

delete from ui_conf where id=27104;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27104,1,2217,221700,'/web/content/uiconf/athletixnation/kdp_1.1.13/athletix_new_player.xml','kdp athletixnation new player', 320,279,'','/flash/kdp/v1.1.13/kdp.swf',now(),now(),'',1);
delete from widget where id=27104;
insert into widget values(27104,27104,'',27104,2217,221700,0,0,27104,'',0,1,null,null,'');

delete from ui_conf where id=27105;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27105,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.1.13/npgco/stjoe/kdp_stjoe.xml','kdp athletixnation stjoe', 320,279,'','/flash/kdp/v1.1.13/kdp.swf',now(),now(),'',1);

delete from ui_conf where id=27106;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27106,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/kdp_athletixnation_playlist_vertical.xml','kdp athletixnation playlist vertical', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27107;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27107,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/kdp_athletixnation_playlist_horizontal.xml','kdp athletixnation playlist horizontal', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27108;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27108,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.5/kdp_athletixnation_playlist_vertical.xml','kdp athletixnation playlist vertical', 0,0,'','/flash/kdp/v1.2.5.23822/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27109;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27109,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.5/kdp_athletixnation_playlist_horizontal.xml','kdp athletixnation playlist horizontal', 0,0,'','/flash/kdp/v1.2.5.23822/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27110;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27110,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/stjoe/kdp_athletixnation_stjoe_playlist_vertical.xml','kdp athletixnation stjoe playlist vertical', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27111,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/stjoe/kdp_athletixnation_stjoe_playlist_horizontal.xml','kdp athletixnation stjoe playlist horizontal', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27112;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27112,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.5/kdp_athletixnation_nbc_playlist_vertical.xml','kdp athletixnation nbc playlist vertical', 0,0,'','/flash/kdp/v1.2.5.23822/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27113;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27113,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.5/kdp_athletixnation_nbc_playlist_horizontal.xml','kdp athletixnation nbc playlist horizontal', 0,0,'','/flash/kdp/v1.2.5.23822/kdp.swf',now(),now(),'autoPlay=1',1);

delete from ui_conf where id=27114;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27114,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/kdp_athletixnation_playlist_vertical.xml','kdp athletixnation playlist vertical', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'',1);

delete from ui_conf where id=27115;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(27115,1,0,0,'/web/content/uiconf/athletixnation/kdp_1.2.3/kdp_athletixnation_playlist_horizontal.xml','kdp athletixnation playlist horizontal', 0,0,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(),'',1);

# 2 new playlists based on v2.0.x
delete from ui_conf where id=27116;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27116,1,0,0,'/web/content/uiconf/athletixnation/kdp_2.0.1/playlist_athletixnation_horizontal.xml','kdp kaltura playlist vertical black', 655,300,'','/flash/kdp/v2.0.1.24586/kdp.swf',now(),now(), 'autoPlay=1', 1, 'playlist');

delete from ui_conf where id=27117;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27117,1,0,0,'/web/content/uiconf/athletixnation/kdp_2.0.1/playlist_athletixnation_vertical.xml','kdp kaltura playlist vertical black', 300,553,'','/flash/kdp/v2.0.1.24586/kdp.swf',now(),now(), 'autoPlay=1', 1, 'playlist');

delete from ui_conf where id=27118;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27118,1,0,0,'/web/content/uiconf/athletixnation/kdp_2.0.1/playlist_athletixnation_vertical.xml','kdp kaltura playlist vertical black', 300,553,'','/flash/kdp/v2.0.1.24892/kdp.swf',now(),now(), null, 1, 'playlist');

delete from ui_conf where id=27119;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27119,1,0,0,'/web/content/uiconf/athletixnation/kdp_2.0.2/pl_vertical_defaultIr.xml','kdp kaltura playlist vertical ir', 300,553,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1, 'playlist');

delete from ui_conf where id=27201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27201,1,2217,0,'/web/content/uiconf/athletixnation/kdp_2.0.2/plain_player.xml','kdp player', 400,362,'','/flash/kdp/v2.0.3/kdp.swf',now(),now(), null, 1, 'player');
insert into widget values('_2217_27201','_2217_27201','','_2217_27201',2217,221700,0,0,27201,'',0,1,null,null,'');
insert into widget values('_6028_27201','','','',6028,602800,0,0,27201,'',0,1,null,null,'');

delete from ui_conf where id=27202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27202,1,2217,0,'/web/content/uiconf/athletixnation/kdp_2.0.2/an_vertical_playlist_tabs_with_adap.xml','kdp player', 400,600,'','/flash/kdp/v2.0.3/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('_2217_27202','_2217_27202','','_2217_27202',2217,221700,0,0,27202,'',0,1,null,null,'');
insert into widget values('_2217_27202_1','','','',2217,221700,0,0,27202,'',0,1,null,null,'');
insert into widget values('_6028_27202','','','',6028,602800,0,0,27202,'',0,1,null,null,'');

delete from ui_conf where id=27203;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(27203,1,2217,0,'/web/content/uiconf/athletixnation/kdp_2.0.2/an_horizontal_playlist_tabs.xml','kdp player', 600,300,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('_2217_27203','_2217_27203','','_2217_27203',2217,221700,0,0,27203,'',0,1,null,null,'');
insert into widget values('_2217_27203_1','','','',2217,221700,0,0,27203,'',0,1,null,null,'');
insert into widget values('_6028_27203','','','',6028,602800,0,0,27203,'',0,1,null,null,'');

delete from ui_conf where id=27204;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags,custom_data,status,description)
values(27204,1,2217,0,'/web/content/uiconf/athletixnation/kdp_2.0.1/playlist_athletixnation_horizontal.xml','kdp player', 655,300,'','/flash/kdp/v2.0.1.24596/kdp.swf',now(),now(), null, 1, 'playlist',null,2,null);

#
# athletix nation customers on SANDBOX (naming convention: _PARTNERID_UICONFID_NUMBER)
#

# partner 832 ui conf 27102
delete from widget where id='_832_27102_1';
insert into widget values('_832_27102_1','','','',832,83200,0,0,27102,'',0,1,null,null,'');

#
# athletix nation customers on PRODUCTION (naming convention: _PARTNERID_UICONFID_NUMBER)
#

# partner 2217 ui conf 27102
delete from widget where id='_2217_27102_1';
insert into widget values('_2217_27102_1','','','',2217,221700,0,0,27102,'',0,1,null,null,'');

# partner 6028 ui conf 27102
delete from widget where id='_6028_27102_1';
insert into widget values('_6028_27102_1','','','',6028,602800,0,0,27102,'',0,1,null,null,'');

# partner 6028 ui conf 27105
delete from widget where id='_6028_27105_1';
insert into widget values('_6028_27105_1','','','',6028,602800,0,0,27105,'',0,1,null,null,'');

# partner 6028 kaltura generic (brightcove) bright player
delete from widget where id='6028_43101';
insert into widget values('6028_43101','','','',6028,602800,0,0,43101,'',0,1,null,null,'');
# partner 6028 kaltura generic (brightcove) bright playlist
delete from widget where id='6028_43102';
insert into widget values('6028_43102','','','',6028,602800,0,0,43102,'',0,1,null,null,'');
# partner 6028 kaltura generic (brightcove) bright playlist player
delete from widget where id='6028_43103';
insert into widget values('6028_43103','','','',6028,602800,0,0,43103,'',0,1,null,null,'');
# partner 6028 kaltura generic (brightcove) dark player
delete from widget where id='6028_43111';
insert into widget values('6028_43111','','','',6028,602800,0,0,43111,'',0,1,null,null,'');
# partner 6028 kaltura generic (brightcove) dark playlist
delete from widget where id='6028_43112';
insert into widget values('6028_43112','','','',6028,602800,0,0,43112,'',0,1,null,null,'');
# partner 6028 kaltura generic (brightcove) dark playlist player
delete from widget where id='6028_43113';
insert into widget values('6028_43113','','','',6028,602800,0,0,43113,'',0,1,null,null,'');

# partner 6028 playlist vertical (27106)
delete from widget where id='6028_27106';
insert into widget values('6028_27106','','','',6028,602800,0,0,27106,'',0,1,null,null,'');

# partner 6028 playlist horizontal (27107)
delete from widget where id='6028_27107';
insert into widget values('6028_27107','','','',6028,602800,0,0,27107,'',0,1,null,null,'');

# partner 2217 playlist vertical (27106)
insert into widget values('2217_27106_1','','','',2217,221700,0,0,27106,'',0,1,null,null,'');
# ....... lots of widgets......
insert into widget values('2217_27106_200','','','',2217,221700,0,0,27106,'',0,1,null,null,'');

# partner 2217 playlist horizontal (27107)
insert into widget values('2217_27107_1','','','',2217,221700,0,0,27107,'',0,1,null,null,'');
# ....... lots of widgets......
insert into widget values('2217_27107_200','','','',2217,221700,0,0,27107,'',0,1,null,null,'');


# partner 2217 playlist vertical (27108)
insert into widget values('2217_27108_1','','','',2217,221700,0,0,27108,'',0,1,null,null,'');
# ....... lots of widgets......
insert into widget values('2217_27108_200','','','',2217,221700,0,0,27108,'',0,1,null,null,'');


# partner 2217 playlist horizontal (27109)
insert into widget values('2217_27109_1','','','',2217,221700,0,0,27109,'',0,1,null,null,'');
# ....... lots of widgets......
insert into widget values('2217_27109_200','','','',2217,221700,0,0,27109,'',0,1,null,null,'');

# partner 6028 playlist vertical (27110)
insert into widget values('6028_27110_1','','','',6028,602800,0,0,27110,'',0,1,null,null,'');

# partner 6028 playlist horizontal (27111)
insert into widget values('6028_27111_1','','','',6028,602800,0,0,27111,'',0,1,null,null,'');

# partner 2217 playlist vertical (27112)
insert into widget values('2217_27112_1','','','',2217,221700,0,0,27112,'',0,1,null,null,'');

# partner 2217 playlist horizontal (27113)
insert into widget values('2217_27113_1','','','',2217,221700,0,0,27113,'',0,1,null,null,'');

# partner 2217 playlist vertical (27114)
insert into widget values('2217_27114_1','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_2','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_3','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_4','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_5','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_6','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_7','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_8','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_9','','','',2217,221700,0,0,27114,'',0,1,null,null,'');
insert into widget values('2217_27114_10','','','',2217,221700,0,0,27114,'',0,1,null,null,'');


# partner 2217 playlist horizontal (27115)
insert into widget values('2217_27115_1','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_2','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_3','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_4','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_5','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_6','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_7','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_8','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_9','','','',2217,221700,0,0,27115,'',0,1,null,null,'');
insert into widget values('2217_27115_10','','','',2217,221700,0,0,27115,'',0,1,null,null,'');

insert into widget values('2217_27119_1','','','',2217,221700,0,0,27119,'',0,1,null,null,'');

insert into widget values('6028_27119_1','','','',2217,221700,0,0,27119,'',0,1,null,null,'');



#
# WIKIHOW
#
delete from widget where id=209;
insert into widget values(209,209,'',209,328,32800,0,0,209,'',0,1,null,null,'');

delete from ui_conf where id=209;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(209,1,328,32800,'/web/content/uiconf/wikihow/kdp_wikihow_wiki_standard.xml','wiki standard','400','420','','/flash/kdp/v1.0.14/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26','',1);

#
# dorimedia extranet (2460) on production
#

delete from ui_conf where id=28000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(28000,2,2460,246000,'/web/content/uiconf/dorimediaextranet/kcw_dorimediaextranet.xml','cw dorimedia extranet', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id = 28100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (28100,1,2460,246000,'/web/content/uiconf/dorimediaextranet/kdp_dorimediaextranet.xml','kdp dorimedia extranet','0','0','','/flash/kdp/v1.0.15/kdp.swf',now(),now(),'',1);
delete from widget where id = '28100';
insert into widget values ('28100',28100,'','28100',2460,246000,0,0,28100,'',0,1,now(),now(),'');


#
# Big Think (2139) on production, only widget id linked to wordpress ui conf - OLD version
#
delete from widget where id = '29100';
insert into widget values ('29100',29100,'','29100',2139,213900,0,0,511,'',0,1,now(),now(),'');

#
# Big Think (2953) on production (For production)
#
delete from ui_conf where id=29110;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn) 
values(29110,1,2953,295300,'/web/content/uiconf/bigthink/kdp_bigthink_player.xml','kdp big think', 400,320,'','/flash/kdp/v1.1.5/kdp.swf',now(),now(),'',1);
delete from widget where id = '29110';
insert into widget values ('29110',29110,'','29110',2953,295300,0,0,29110,'',0,1,now(),now(),'');

delete from ui_conf where id = 29111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (29111,1,2953,29530,'/web/content/uiconf/bigthink/kdp_bigthink_embed_player.xml','kdp bigthink','0','0','','/flash/kdp/v1.1.5/kdp.swf',now(),now(),'',1);
delete from widget where id = '29111';
insert into widget values ('29111',29111,'','29111',2953,295300,0,0,29111,'',0,1,now(),now(),'');

delete from ui_conf where id = 29112;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values (29112,1,2953,29530,'/web/content/uiconf/bigthink/kdp_bigthink_player_moderator.xml','kdp bigthink','0','0','','/flash/kdp/v1.1.5/kdp.swf',now(),now(),'',1);
delete from widget where id = '29112';
insert into widget values ('29112',29112,'','29112',2953,295300,0,0,29112,'',0,1,now(),now(),'');

delete from ui_conf where id=29200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(29200,2,2953,295300,'/web/content/uiconf/bigthink/cw_bigthink.xml','cw bigthink', 680,400,'','/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now());

delete from ui_conf where id=29300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(29300,3,2953,295300,'/web/content/uiconf/bigthink/se_bigThink.xml','se bigthink', 890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'',1);

#
# Big Think (5308) on production (For staging) widgets defined at 37xxx
#


#
# metacafe heroes (3304) on production
#
delete from ui_conf where id=30100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(30100,1,3304,330400,'/web/content/uiconf/metacafe/ghosthouse/kdp_ghosthouse.xml','kdp ghosthouse', 400,320,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());
delete from widget where id=30100;
insert into widget values(30100,30100,'',30100,3304,330400,0,0,30100,'',0,1,null,null,'');

delete from ui_conf where id=30201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(30201,3,3304,330400,'/web/content/uiconf/metacafe/ghosthouse/se_ghosthouse.xml','se ghosthouse', 890,546,'','/flash/kse/v2.1.1/simpleeditor.swf',now(),now(),'',1);

#
# KMC (Kaltura Management Console)
#
delete from ui_conf where id=31000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(31000,1,0,0,'/web/content/uiconf/kaltura/kmc/kdp_kmc_preview_player.xml','kdp kmc', 400,320,'','/flash/kdp/v1.0.15/kdp.swf',now(),now());
delete from widget where id=31000;
insert into widget values(31000,31000,'',31000,0,0,0,0,31000,'',0,1,null,null,'');

# playlist ui
delete from ui_conf where id=31001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn,display_in_search,custom_data) 
values(31001,1,0,0,'/web/content/uiconf/kaltura/kmc/appstudio/playlist_ui.xml','playlist ui', 0,0,'','',now(),now(),'',1,2,'a:1:{s:12:"creationMode";i:2;}');

# player ui
delete from ui_conf where id=31002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn,display_in_search,custom_data) 
values(31002,1,0,0,'/web/content/uiconf/kaltura/kmc/appstudio/player_ui.xml','player ui', 0,0,'','',now(),now(),'',1,2,'a:1:{s:12:"creationMode";i:2;}');

# multiplaylist ui
delete from ui_conf where id=31003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn,display_in_search,custom_data) 
values(31003,1,0,0,'/web/content/uiconf/kaltura/kmc/appstudio/multiplaylist_ui.xml','multiplaylist ui', 0,0,'','',now(),now(),'',1,2,'a:1:{s:12:"creationMode";i:2;}');

#
# Assaf's Unknown Partner (5145) on production
#
delete from widget where id = '32000';
insert into widget values ('32000',32000,'','32000',5145,514500,0,0,514,'',0,1,now(),now(),'');

#
# Plymedia widget for entering subtitles
#
delete from ui_conf where id=33000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(33000,2,0,0,'/web/content/uiconf/plymedia/kdp/kdp_plymedia.xml','kdp plymedia','680','400','','/flash/kdp/v1.0.15/kdp.swf',now(), now(),'',1);
delete from widget where id = '33000';
insert into widget values ('33000',33000,'','33000',0,0,0,0,33000,'',0,1,now(),now(),'');


# first ae - currently for partner 0 (should be used on qac only, it duplicates other ids)
delete from ui_conf where id=504;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(504,4,0,0,'/web/content/uiconf/droga5/net10/kae_1.0.5/kae_net10_generic.xml','droga5 ae',750,640,'','/flash/kae/v1.0.5/KalturaAdvancedVideoEditor.swf',now(),now());

delete from ui_conf where id=505;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(505,4,0,0,'/web/content/uiconf/droga5/net10/kae_1.0.6/kae_net10_generic.xml','droga5 ae',750,640,'','/flash/kae/v1.0.6/KalturaAdvancedVideoEditor.swf',now(),now());

#
# Spill.TV (4513) on production
#
delete from ui_conf where id=34000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(34000,2,0,0,'/web/content/uiconf/spilltv/kdp_spilltv.xml','kdp spill tv','680','400','','/flash/kdp/v1.0.15/kdp.swf',now(), now(),'',1);
delete from widget where id = '34000';
insert into widget values ('34000',34000,'','34000',4513,451300,0,0,34000,'',0,1,now(),now(),'');

# worldfocus dev (4926) on production 
# worldfocus prod (5588) on production
# ui conf not binded to partner id
delete from ui_conf where id=35000;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)   
values(35000,2,0,0,'/web/content/uiconf/worldfocus/cw_worldfocus_comments.xml','cw worldfocus', 680,400,'','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(), null, 1);


#
# sample kit
#
delete from ui_conf where id=36200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,display_in_search) 
values(36200,2,0,0,'/web/content/uiconf/kaltura/samplekit/kcw_2.6.4/kcw_samplekit.xml','samplekit cw',680,480,NULL,'/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1,2);

delete from ui_conf where id=36201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(36201,2,0,0,'/web/content/uiconf/kaltura/samplekit/kcw_2.6.4/kcw_samplekit.xml','samplekit cw',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=36202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,display_in_search) 
values(36202,2,0,0,'/web/content/uiconf/kaltura/kmc/kcw/kcw_kmc.xml','cw for KMC',680,480,NULL,'/flash/kcw/v1.6.5.26617/ContributionWizard.swf',now(),now(), null, 1,2);

# new CW for KMC
delete from ui_conf where id=36203;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,display_in_search) 
values(36203,2,0,0,'/web/content/uiconf/kaltura/kmc/kcw/kcw_kmc_204.xml','new cw 2.0.4 for KMC',680,480,NULL,'/flash/kcw/v2.0.4/ContributionWizard.swf',now(),now(), null, 1,2);

delete from ui_conf where id=36300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,display_in_search) 
values(36300,3,0,0,'/web/content/uiconf/kaltura/samplekit/kse_2.1.1/kse_samplekit.xml','samplekit simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1,2);


#
# Big Think (5308) widgets linked to (2953) uiconfs
#
delete from widget where id = '37110';
insert into widget values ('37110',37110,'','37110',5308,530800,0,0,29110,'',0,1,now(),now(),'');

delete from widget where id = '37111';
insert into widget values ('37111',37111,'','37111',5308,530800,0,0,29111,'',0,1,now(),now(),'');

delete from widget where id = '37112';
insert into widget values ('37112',37112,'','37112',5308,530800,0,0,29112,'',0,1,now(),now(),'');

#
# Footbo (8304)
#

delete from ui_conf where id=38100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38100,1,0,0,'/web/content/uiconf/footbo/kdp_1.1.20/kdp_footbo_playlist.xml','kdp footbo', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id=38100;
insert into widget values(38100,38100,'',38100,8304,830400,0,0,38100,'',0,1,null,null,'');

delete from ui_conf where id=38101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38101,1,0,0,'/web/content/uiconf/footbo/kdp_1.1.20/kdp_footbo_player_only.xml','kdp footbo', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id=38101;
insert into widget values(38101,38101,'',38101,8304,830400,0,0,38101,'',0,1,null,null,'');

delete from ui_conf where id=38102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38102,1,0,0,'/web/content/uiconf/footbo/kdp_1.1.20/kdp_footbo_playlist_only.xml','kdp footbo playlist only', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), 'k_pl_standAlone=true', 1);
delete from widget where id=38102;
insert into widget values(38102,38102,'',38102,8304,830400,0,0,38102,'',0,1,null,null,'');

delete from ui_conf where id=38110;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38110,1,0,0,'/web/content/uiconf/footbo/kdp_2.0.2/playlist.xml','kdp footbo', 400,320,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), 'k_pl_standAlone=true', 1);
delete from widget where id=38110;
insert into widget values(38110,38110,'',38110,8304,830400,0,0,38110,'',0,1,null,null,'');

delete from ui_conf where id=38111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38111,1,0,0,'/web/content/uiconf/footbo/kdp_2.0.2/player.xml','kdp footbo', 400,320,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
delete from widget where id=38111;
insert into widget values(38111,38111,'',38111,8304,830400,0,0,38111,'',0,1,null,null,'');

delete from ui_conf where id=38112;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38112,1,0,0,'/web/content/uiconf/footbo/kdp_2.0.2/vertical_player_playlist.xml','kdp footbo playlist only', 400,320,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
delete from widget where id=38112;
insert into widget values(38112,38112,'',38112,8304,830400,0,0,38112,'',0,1,null,null,'');

delete from ui_conf where id=38200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38200,2,0,0,'/web/content/uiconf/footbo/kcw_2.6.4/kcw_footbo.xml','footbo cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=38201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38201,2,0,0,'/web/content/uiconf/footbo/kcw_2.6.4/kcw_footbo_de.xml','footbo cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=38202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(38202,2,0,0,'/web/content/uiconf/footbo/kcw_2.6.4/kcw_footbo_es.xml','footbo cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);


# 
# droga (8446)
#

delete from widget where id = '_8446_512_1';
insert into widget values ('_8446_512_1','','','',8446,844600,0,0,512,'',0,1,now(),now(),'');

delete from ui_conf where id=39100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(39100,1,0,0,'/web/content/uiconf/droga5/kdp_1.2.0/kdp_red_droga.xml','kdp droga', 400,320,'','/flash/kdp/v1.2.0/kdp.swf',now(),now(), null, 1);
delete from widget where id='_8446_39100';
insert into widget values('_8446_39100','','','',8446,844600,0,0,39100,'',0,1,null,null,'');

delete from ui_conf where id=39101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(39101,1,0,0,'/web/content/uiconf/droga5/kdp_1.2.0/kdp_red_droga_embed.xml','kdp droga', 496,450,'','/flash/kdp/v1.2.0/kdp.swf',now(),now(), null, 1);
delete from widget where id='_8446_39101';
insert into widget values('_8446_39101','','','',8446,844600,0,0,39101,'',0,1,null,null,'');

delete from ui_conf where id=39102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(39102,1,0,0,'/web/content/uiconf/droga5/kdp_1.2.3/kdp_red_droga.xml','kdp droga', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id='_8446_39102';
insert into widget values('_8446_39102','','','',8446,844600,0,0,39102,'',0,1,null,null,'');

# !!!!!!!!!!!!!!!!!!!!!!! carefull if need to create id 39200, it is used for Open Source Cinema (6858)

delete from ui_conf where id=39300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(39300,3,0,0,'/web/content/uiconf/droga5/kse_2.1.1/kse_droga.xml','droga simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);

delete from ui_conf where id=39400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(39400,4,0,0,'/web/content/uiconf/droga5/net10/kae_1.0.8/kae_net10_generic.xml','droga5 ae',750,640,'','/flash/kae/v1.0.8.23835/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=39401;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(39401,4,0,0,'/web/content/uiconf/droga5/net10/kae_1.0.12/kae_net10_generic.xml','droga5 ae',750,640,'','/flash/kae/v1.0.12/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);


#
# Red Dodo (3213)
#
delete from ui_conf where id=40100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(40100,1,0,0,'/web/content/uiconf/reddodo/kdp_reddodo.xml','kdp reddodo', 400,320,'','/flash/kdp/v1.1.16/kdp.swf',now(),now(), null, 1);
delete from widget where id='_3213_40100';
insert into widget values('_3213_40100','','','',3213,321300,0,0,40100,'',0,1,null,null,'');

# red dodo kdp 2.0.X
delete from ui_conf where id=40101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(40101,1,0,0,'/web/content/uiconf/reddodo/sparkeo/kdp_2.0.2/kdp_reddodo.xml','kdp reddodo', 400,320,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
delete from widget where id='_3213_40101';
insert into widget values('_3213_40101','','','',3213,321300,0,0,40101,'',0,1,null,null,'');

#sparkeo cw
delete from ui_conf where id=40102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(40102,2,0,0,'/web/content/uiconf/sparkeo/kcw_1.6.5/kcw_all_media.xml','sparkeo cw',680,360,NULL,'/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1);


#
# lullabot (10191) ui confs only, widgets are the default created by drupal
#

update widget set ui_conf_id = '41001' where id = '_10191_604' limit 1;
update widget set ui_conf_id = '41002' where id = '_10191_605' limit 1;
update widget set ui_conf_id = '41003' where id = '_10191_606' limit 1;
update widget set ui_conf_id = '41004' where id = '_10191_607' limit 1;


delete from ui_conf where id=41001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(41001,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_dark_remix.xml','drupal lullabot dark kdp (remix)','','',NULL,'/flash/kdp/v1.2.3/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);

delete from ui_conf where id=41002;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(41002,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_dark_view.xml','drupal lullabot dark kdp (view)','','',NULL,'/flash/kdp/v1.2.3/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);

delete from ui_conf where id=41003;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(41003,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_gray_remix.xml','drupal lullabot gray kdp (remix)','','',NULL,'/flash/kdp/v1.2.3/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);

delete from ui_conf where id=41004;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(41004,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_gray_view.xml','drupal lullabot gray kdp (view)','','',NULL,'/flash/kdp/v1.2.3/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);

#delete from ui_conf where id=41005;
#insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
#values(41005,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_whiteblue_remix.xml','drupal lullabot white-blue kdp (remix)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);

#delete from ui_conf where id=41006;
#insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
#values(41006,1,0,0,'/web/content/uiconf/lullabot/kdp_1.1.11/kdp_drupal_v2.1_whiteblue_view.xml','drupal lullabot white-blue kdp (view)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-06-10 15:30:00','2008-06-10 15:30:00', 'wideScreen=1', 1);


#
# Application Studio
#
delete from ui_conf where id=41100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(41100,1,0,0,'/web/content/uiconf/kaltura/aps/aps_kdp_layout.xml','kdp aos', 400,320,'','/flash/kdp/v1.1.15/kdp.swf',now(),now(), null, 1);
delete from widget where id='41100';
insert into widget values('41100','','','',1,100,0,0,41100,'',0,1,null,null,'');


#
# Sailing demo (demo entry is in partner id 0, so player is linked with partner 0 too)
#

delete from ui_conf where id=42001;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(42001,1,0,0,'/web/content/uiconf/demos/sailing/kdp_1.1.11/kdp_bigthink_player.xml','',410,364,NULL,'/flash/kdp/v1.1.11/kdp.swf','2008-11-30 15:30:00','2008-11-30 15:30:00', null, 1);
delete from widget where id = '42001';
insert into widget values ('42001','','','',0,0,0,0,42001,'',0,1,'2008-09-01 15:30:00','2008-09-01 15:30:00','');


#
# Kaltura Generic Playlist (Brighcove customers)
#
delete from ui_conf where id=43101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43101,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_bright_player_only.xml','kdp generic bright player only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

delete from ui_conf where id=43102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43102,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_bright_playlist_player.xml','kdp generic bright playlist only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), 'k_pl_standAlone=true', 1);

delete from ui_conf where id=43103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43103,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_bright_playlist_player.xml','kdp generic bright playlist only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

delete from ui_conf where id=43111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43111,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_dark_player_only.xml','kdp generic dark player only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

delete from ui_conf where id=43112;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43112,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_dark_playlist_player.xml','kdp generic dark playlist only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), 'k_pl_standAlone=true', 1);

delete from ui_conf where id=43113;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(43113,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_dark_playlist_player.xml','kdp generic dark playlist only', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
#
# Brightcove customers
#
# Down Range Television (11843)
delete from widget where id='11843_43101';
insert into widget values('11843_43101','','','',11843,1184300,0,0,43101,'',0,1,null,null,'');

delete from widget where id='11843_43103';
insert into widget values('11843_43103','','','',11843,1184300,0,0,43103,'',0,1,null,null,'');

delete from widget where id='11843_43111';
insert into widget values('11843_43111','','','',11843,1184300,0,0,43111,'',0,1,null,null,'');

delete from widget where id='11843_43113';
insert into widget values('11843_43113','','','',11843,1184300,0,0,43113,'',0,1,null,null,'');

# Real Fresh TV (12001)
delete from widget where id='12001_43101';
insert into widget values('12001_43101','','','',12001,1200100,0,0,43101,'',0,1,null,null,'');

delete from widget where id='12001_43103';
insert into widget values('12001_43103','','','',12001,1200100,0,0,43103,'',0,1,null,null,'');

delete from widget where id='12001_43111';
insert into widget values('12001_43111','','','',12001,1200100,0,0,43111,'',0,1,null,null,'');

delete from widget where id='12001_43113';
insert into widget values('12001_43113','','','',12001,1200100,0,0,43113,'',0,1,null,null,'');

# Dubious Entertainment (12003)
delete from widget where id='12003_43101';
insert into widget values('12003_43101','','','',12003,1200300,0,0,43101,'',0,1,null,null,'');

delete from widget where id='12003_43103';
insert into widget values('12003_43103','','','',12003,1200300,0,0,43103,'',0,1,null,null,'');

delete from widget where id='12003_43111';
insert into widget values('12003_43111','','','',12003,1200300,0,0,43111,'',0,1,null,null,'');

delete from widget where id='12003_43113';
insert into widget values('12003_43113','','','',12003,1200300,0,0,43113,'',0,1,null,null,'');

# Anglican TV (12410)
delete from widget where id='12410_43101';
insert into widget values('12410_43101','','','',12410,1241000,0,0,43101,'',0,1,null,null,'');

delete from widget where id='12410_43103';
insert into widget values('12410_43103','','','',12410,1241000,0,0,43103,'',0,1,null,null,'');

delete from widget where id='12410_43111';
insert into widget values('12410_43111','','','',12410,1241000,0,0,43111,'',0,1,null,null,'');

delete from widget where id='12410_43113';
insert into widget values('12410_43113','','','',12410,1241000,0,0,43113,'',0,1,null,null,'');

#
# BCM Production - 11939
#
delete from widget where id='11939_510';
insert into widget values('11939_510','','','',11939,1193900,0,0,510,'',0,1,null,null,'');

delete from widget where id='11939_512';
insert into widget values('11939_512','','','',11939,1193900,0,0,512,'',0,1,null,null,'');

delete from widget where id='11939_514';
insert into widget values('11939_514','','','',11939,1193900,0,0,514,'',0,1,null,null,'');

delete from ui_conf where id=44100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44100,1,0,0,'/web/content/uiconf/bcm/kdp_1.2.0/kdp_whiteblue.xml','kdp bcm', 400,320,'','/flash/kdp/v1.2.0/kdp.swf',now(),now(), null, 1);
delete from widget where id=44100;
insert into widget values(44100,'','','',11939,1193900,0,0,44100,'',0,1,null,null,'');

delete from ui_conf where id=44101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44101,1,0,0,'/web/content/uiconf/bcm/kdp_1.2.3/kdp_bcm.xml','kdp bcm', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id=44101;
insert into widget values(44101,'','','',11939,1193900,0,0,44101,'',0,1,null,null,'');

delete from ui_conf where id=44200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44200,2,11939,1193900,'/web/content/uiconf/bcm/kcw_1.5.4/cw_bcm.xml','bcm cw',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=44201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44201,2,11939,1193900,'/web/content/uiconf/bcm/kcw_1.6.4/kcw_bcm_style2.xml','bcm cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=44300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44300,3,11939,1193900,'/web/content/uiconf/bcm/kse_2.1.1/kse_samplekit.xml','bcm simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);

delete from ui_conf where id=44301;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(44301,3,11939,1193900,'/web/content/uiconf/bcm/kse_2.1.3/kse_bcm.xml','bcm simple editor',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);


#
# Kaltura New Generics (45xxx)
#

# White Player
delete from ui_conf where id=45100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(45100,1,0,0,'/web/content/uiconf/kaltura/generic/kdp_1.2.3/kdp_white.xml','kdp kaltura generic white', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

delete from widget where id='_0_45100';
insert into widget values('_0_45100','','','',0,0,0,0,45100,'',0,1,null,null,'');

# White Player embed
delete from ui_conf where id=45101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(45101,1,0,0,'/web/content/uiconf/kaltura/generic/kdp_1.2.3/kdp_white_embeded.xml','kdp kaltura generic white (embed)', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

# Player + Playlist 
delete from ui_conf where id=45102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(45102,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_generic1_playlistPlayer.xml','kdp kaltura generic playlist', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);

delete from widget where id='_0_45102';
insert into widget values('_0_45102','','','',0,0,0,0,45102,'',0,1,null,null,'');

# Player (For embed from playlist)
delete from ui_conf where id=45103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(45103,1,0,0,'/web/content/uiconf/kaltura/generic/kdp/kdp_1.2.0/kdp_generic1_player.xml','kdp kaltura generic player (embed)', 400,329,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);


#
# Tierra Innovations (10426)
#
delete from ui_conf where id=46100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(46100,1,0,0,'/web/content/uiconf/tierra-wb/kdp_1.2.3/kdp_minimal.xml','kdp tierra wb', 400,375,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id='46100';
insert into widget values('46100','','','',10426,1042600,0,0,46100,'',0,1,null,null,'');

delete from ui_conf where id=46200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(46200,2,0,0,'/web/content/uiconf/tierra-wb/kcw_1.5.4/kcw.xml','kcw tierra with wordpress skin',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(), null, 1);


#
# Kaltura Generic AE
#
delete from ui_conf where id=47400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(47400,4,0,0,'/web/content/uiconf/kaltura/generic/kae_1.0.10/kae_generic_generic.xml','ae',750,640,'','/flash/kae/v1.0.10.23714/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);


#
# Kaltura Default Players (Yet another default players)
#

# Dark Player
delete from ui_conf where id=48100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48100,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_dark.xml','kdp kaltura default dark', 400,364,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1);
insert into widget values('48100','','','',0,0,0,0,48100,'',0,1,null,null,'');

# Light Player
delete from ui_conf where id=48101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48101,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_light.xml','kdp kaltura default light', 400,364,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1);
insert into widget values('48101','','','',0,0,0,0,48101,'',0,1,null,null,'');

# Dark Player Minimal
delete from ui_conf where id=48102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48102,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_dark_minimal.xml','kdp kaltura default dark minimal', 400,332,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1);
insert into widget values('48102','','','',0,0,0,0,48102,'',0,1,null,null,'');

# Light Player Minimal 
delete from ui_conf where id=48103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48103,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_light_minimal.xml','kdp kaltura default light minimal', 400,332,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1);
insert into widget values('48103','','','',0,0,0,0,48103,'',0,1,null,null,'');

# Playlist Horizontal Dark
delete from ui_conf where id=48104;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48104,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.6/kdp_default_playlist_dark_horizontal.xml','kdp kaltura playlist horizontal black', 655,300,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('48104','','','',0,0,0,0,48104,'',0,1,null,null,'playlist');

# Playlist Horizontal Light
delete from ui_conf where id=48105;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48105,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.6/kdp_default_playlist_light_horizontal.xml','kdp kaltura playlist horizontal black', 655,300,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('48105','','','',0,0,0,0,48105,'',0,1,null,null,'playlist');

# Playlist Vertical Dark
delete from ui_conf where id=48106;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48106,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.6/kdp_default_playlist_dark_vertical.xml','kdp kaltura playlist vertical black', 300,553,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('48106','','','',0,0,0,0,48106,'',0,1,null,null,'playlist');

# Playlist Vertical Ligh
delete from ui_conf where id=48107;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48107,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.6/kdp_default_playlist_light_vertical.xml','kdp kaltura playlist vertical black', 300,553,'','/flash/kdp/v1.2.6/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('48107','','','',0,0,0,0,48107,'',0,1,null,null,'playlist');

# ---------------------- playlist 2.0.0 -----------------------
# kdp kaltura playlist vertical black compact no title no tabs  
delete from ui_conf where id=48204;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags) 
values(48204,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_vertical_compactIr_noTitle_noTabs.xml','Vertical Compact', 400,600,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1, 'playlist');

# kdp kaltura playlist vertical black default no tabs  
delete from ui_conf where id=48205;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags) 
values(48205,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_vertical_defaultIr_noTitle_noTabs.xml','Vertical', 400,600,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1, 'playlist');

# kdp kaltura playlist vertical black compact no title 
delete from ui_conf where id=48206;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags) 
values(48206,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_horizontal_defaultIr_noTabs_noTitle.xml','Horizontal', 660,272,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1, 'playlist');

# kdp kaltura playlist vertical black compact no title 
delete from ui_conf where id=48207;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags) 
values(48207,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_horizontal_compactlIr_noTitle_noTabs.xml','Horizontal Compact', 724,322,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1, 'playlist');

# kdp kaltura playlist default horizontal
delete from ui_conf where id=48210;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48210,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_horizontal_defaultIr_light.xml','playlist playlist no tabs bright', 400,322,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), "", 1, "");

# kdp kaltura playlist default horizontal
delete from ui_conf where id=48211;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(48211,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/pl_horizontal_defaultIr.xml','playlist playlist no tabs ', 400,322,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), "", 1, "");

# ---------------------- playlist 2.0.0 -----------------------


# ---------------------- kdp 2.0.0 -----------------------
# Dark Player
delete from ui_conf where id=48110;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags) 
values(48110,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/kdp_default_dark.xml','Dark player', 400,332,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1 , "player");
insert into widget values('48110','','','',0,0,0,0,48110,'',0,1,null,null,'');

# Light Player
delete from ui_conf where id=48111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags) 
values(48111,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_2.0.0/kdp_default_light.xml','Light player', 400,332,'','/flash/kdp/v2.1.2.29057/kdp.swf',now(),now(), null, 1 , "player");
insert into widget values('48111','','','',0,0,0,0,48111,'',0,1,null,null,'');

# ---------------------- kdp 2.0.0 -----------------------

#
# Nanco Bandai (16410)
#
delete from widget where id='16410_512';
insert into widget values('16410_512','','','',16410,1641000,0,0,512,'',0,1,null,null,'');


#
# Always on
#
delete from ui_conf where id=49100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(49100,1,0,0,'/web/content/uiconf/alwayson/kdp_1.2.3/kdp_default_light.xml','kdp always on', 400,362,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('49100','','','',0,0,0,0,49100,'',0,1,null,null,'');

#
# Max Network (17003)
#
delete from ui_conf where id=50100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(50100,1,0,0,'/web/content/uiconf/max_life_network/kdp/kdp_1.2.3/kdp_max_network_pink_playlist.xml','playlist max life network pink', 400,322,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id='50100';
insert into widget values('50100','','','',17003,1700300,0,0,50100,'',0,1,null,null,'');

delete from ui_conf where id=50101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(50101,1,0,0,'/web/content/uiconf/max_life_network/kdp/kdp_1.2.3/kdp_max_network_pink.xml','kdp max life network pink', 400,322,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
delete from widget where id='50101';
insert into widget values('50101','','','',17003,1700300,0,0,50101,'',0,1,null,null,'');


#
# Bangor Daily News (17005)
#
insert into widget values('17005_48103 ','','','',17005,1700500,0,0,48103,'',0,1,null,null,'');

delete from ui_conf where id=51100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(51100,1,0,0,'/web/content/uiconf/bangor-news/v1.2.3/kdp_playlistOnly.xml','playlist bangor daily news', 400,322,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), "k_pl_standAlone=true", 1, "playlist");
insert into widget values('51100','','','',17005,1700500,0,0,51100,'',0,1,null,null,'');

delete from ui_conf where id=51101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(51101,1,0,0,'/web/content/uiconf/bangor-news/kdp/v2.0.2/playlist_only_bright.xml','playlist playlist only bright', 400,322,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), "", 1, "playlist");
insert into widget values('51101','','','',17005,1700500,0,0,51101,'',0,1,null,null,'');

delete from ui_conf where id=51102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(51102,1,0,0,'/web/content/uiconf/bangor-news/kdp/v2.0.2/horizontal_playlist_notabs_bright.xml','playlist playlist no tabs bright', 400,322,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), "", 1, "playlist");
insert into widget values('51102','','','',17005,1700500,0,0,51102,'',0,1,null,null,'');



#
# Open Source Cinema (6858)
#
delete from widget where id = '_6858_512_1';
insert into widget values ('_6858_512_1','','','',6858,685800,0,0,512,'',0,1,now(),now(),'');

# 39xxx is droga's space!!! Assaf said that open source cinema is not using 39200 for CW. They are using drupal's CW
delete from ui_conf where id=39200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(39200,2,0,0,'/web/content/uiconf/opensourcecinema/kcw_2.6.4/kcw_opensourcecinema.xml','open source cinema cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=52200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(52200,2,0,0,'/web/content/uiconf/opensourcecinema/kcw_2.6.4/kcw_opensourcecinema.xml','open source cinema cw',680,480,NULL,'/flash/kcw/v1.6.4/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=52100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(52100,1,0,0,'/web/content/uiconf/opensourcecinema/kdp_1.2.3/kdp_opensourcecinema.xml','kdp open source cinema', 400,332,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('52100','','','',6858,685800,0,0,52100,'',0,1,null,null,'');


# Culture TV (12638)
delete from widget where id='12638_43101';
insert into widget values('12638_43101','','','',12638,1263800,0,0,43101,'',0,1,null,null,'');

delete from widget where id='12638_43103';
insert into widget values('12638_43103','','','',12638,1263800,0,0,43103,'',0,1,null,null,'');

delete from widget where id='12638_43111';
insert into widget values('12638_43111','','','',12638,1263800,0,0,43111,'',0,1,null,null,'');

delete from widget where id='12638_43113';
insert into widget values('12638_43113','','','',12638,1263800,0,0,43113,'',0,1,null,null,'');

delete from ui_conf where id=53100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(53100,1,0,0,'/web/content/uiconf/culturetv/kdp_1.2.3/kdp_default_dark.xml','kdp culture tv', 400,362,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('53100','','','',12638,1263800,0,0,53100,'',0,1,null,null,'');

delete from ui_conf where id=53101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn, tags) 
values(53101,1,0,0,'/web/content/uiconf/culturetv/kdp_1.2.3/kdp_default_dark_playlist.xml','kdp culture tv', 700,332,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1, 'playlist');
insert into widget values('53101','','','',12638,1263800,0,0,53101,'',0,1,null,null,'');

# 2Tor (17291)
delete from ui_conf where id=54100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54100,1,0,0,'/web/content/uiconf/2tor/kdp_1.2.3/kdp_2tor_player_no_report.xml','kdp 2tor', 400,332 ,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);
insert into widget values('54100','','','',17291,1729100,0,0,54100,'',0,1,null,null,'');
insert into widget values('54104','','','',20772,2077200,0,0,54100,'',0,1,null,null,'');
insert into widget values('54108','','','',25912,2591200,0,0,54100,'',0,1,null,null,'');
insert into widget values('54112','','','',25913,2591300,0,0,54100,'',0,1,null,null,'');

delete from ui_conf where id=54101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54101,1,0,0,'/web/content/uiconf/2tor/kdp_1.2.3/kdp_2tor_player_no_capture.xml','kdp 2tor with report', 400,332 ,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);
insert into widget values('54101','','','',17291,1729100,0,0,54101,'',0,1,null,null,'');
insert into widget values('54105','','','',20772,2077200,0,0,54101,'',0,1,null,null,'');
insert into widget values('54109','','','',25912,2591200,0,0,54101,'',0,1,null,null,'');
insert into widget values('54113','','','',25913,2591300,0,0,54101,'',0,1,null,null,'');

delete from ui_conf where id=54102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54102,1,0,0,'/web/content/uiconf/2tor/kdp_1.2.3/kdp_2tor_player_no_report_no_lb.xml','kdp 2tor no lb', 400,332 ,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);
insert into widget values('54102','','','',17291,1729100,0,0,54102,'',0,1,null,null,'');
insert into widget values('54106','','','',20772,2077200,0,0,54102,'',0,1,null,null,'');
insert into widget values('54110','','','',25912,2591200,0,0,54102,'',0,1,null,null,'');
insert into widget values('54114','','','',25913,2591300,0,0,54102,'',0,1,null,null,'');

delete from ui_conf where id=54103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54103,1,0,0,'/web/content/uiconf/2tor/kdp_1.2.3/kdp_2tor_player_no_capture_no_lb.xml','kdp 2tor with report no lb', 400,332 ,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);
insert into widget values('54103','','','',17291,1729100,0,0,54103,'',0,1,null,null,'');
insert into widget values('54107','','','',20772,2077200,0,0,54103,'',0,1,null,null,'');
insert into widget values('54111','','','',25912,2591200,0,0,54103,'',0,1,null,null,'');
insert into widget values('54115','','','',25913,2591300,0,0,54103,'',0,1,null,null,'');

delete from ui_conf where id=54104;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54104,1,20772,2077200,'/web/content/uiconf/2tor_certificationmap/kdp_2.0.1/kdp_certificationmap_default.xml','kdp 2tor with report no lb', 480,390 ,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);



delete from ui_conf where id=54200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54200,2,0,0,'/web/content/uiconf/2tor/kcw_1.6.4/kcw_style1_allMedia.xml','kcw 2tor all media',0,0,'','/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=54201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54201,2,0,0,'/web/content/uiconf/2tor/kcw_1.6.4/kcw_style1_video.xml','kcw 2tor video',0,0,'','/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=54202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54202,2,0,0,'/web/content/uiconf/2tor/kcw_1.6.4/kcw_style1_image.xml','kcw 2tor image',0,0,'','/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=54203;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54203,2,0,0,'/web/content/uiconf/2tor/kcw_1.6.4/certificationmap/kcw_style1_allMedia.xml','kcw 2tor allmedia',0,0,'','/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=54204;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54204,2,0,0,'/web/content/uiconf/2tor/kcw_1.6.4/kcw_style1_video_webcam.xml','kcw 2tor video webcam only',0,0,'','/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

#
# 2tor AE
#
delete from ui_conf where id=54300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(54300,4,0,0,'/web/content/uiconf/2tor/kae_1.0.10/kae_2tor_general.xml','ae',750,640,'','/flash/kae/v1.0.10.23714/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

# 2tor new KDP
delete from ui_conf where id=54400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54400,1,0,0,'/web/content/uiconf/2tor/new_kdp/player_carousel.xml','kdp for sync entries', 425,400,'','/flash/kdp/dev/2tor_sync/carousel/kdp.swf',now(),now(), null, 1);
insert into widget (id,source_widget_id,root_widget_id,partner_id,subp_id,kshow_id,entry_id,ui_conf_id,custom_data,security_type,security_policy,created_at,updated_at,partner_data) 
values('54400','','',0,0,0,0,54400,'',0,1,null,null,'');


delete from ui_conf where id=54401;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(54401,1,0,0,'/web/content/uiconf/2tor/new_kdp/player_no_carousel.xml','kdp for sync entries, no carousel', 425,400,'','/flash/kdp/dev/2tor_sync/no_carousel/kdp.swf',now(),now(), null, 1);
insert into widget(id,source_widget_id,root_widget_id,partner_id,subp_id,kshow_id,entry_id,ui_conf_id,custom_data,security_type,security_policy,created_at,updated_at,partner_data) 
values('54401','','',0,0,0,0,54401,'',0,1,null,null,'');


# 2tor kuploader
delete from ui_conf where id=54500;
insert into ui_conf (id, obj_type, partner_id, subp_id, conf_file_path, name, swf_url, display_in_search,creation_mode) 
values(54500, 5, 0, 0, '/web/content/uiconf/2tor/kuploader.xml','2tor ppt kuploader', '/flash/kupload/v1.0.7/KUpload.swf',2,1);

# 2tor kuploader - swfs only
delete from ui_conf where id=54501;
insert into ui_conf (id, obj_type, partner_id, subp_id, conf_file_path, name, swf_url, display_in_search,creation_mode) 
values(54501, 5, 0, 0, '/web/content/uiconf/2tor/kuploader_swf.xml','2tor swf kuploader', '/flash/kupload/v1.0.7/KUpload.swf',2,1);


# SaysMe (17953)
delete from ui_conf where id=55500;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(55500,5,0,0,'/web/content/uiconf/saysmetv/uploader.xml','saysme uploader','','','','/flash/kupload/v1.0.6/KUpload.swf',now(),now());

# saysme simple editor
delete from ui_conf where id=55501;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,tags)  
values(55501,3,0,0,'/web/content/uiconf/saysmetv/demo/kse_2.1.3/kse_saysmetv.xml','saysme simpleeditor','','','','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'simpleeditor default');

# saysme simple editor
delete from ui_conf where id=55502;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,tags)  
values(55502,3,0,0,'/web/content/uiconf/saysmetv/demo/kse_2.1.3/wrigleys/kse_saysmetv_wrigleys.xml','saysme simpleeditor','','','','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'simpleeditor wrigleys');

# saysme simple editor
delete from ui_conf where id=55503;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,tags)  
values(55503,3,0,0,'/web/content/uiconf/saysmetv/demo/kse_2.1.3/nikon/kse_saysmetv_nikon.xml','saysme simpleeditor','','','','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'simpleeditor Nikon');

# Madam Tussauds (19053)
delete from ui_conf where id=56100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(56100,1,0,0,'/web/content/uiconf/graphico/madam_tussauds/kdp_1.2.3/kdp_white_pink_remix.xml','kdp madam tussauds white pink remix', 400,362,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('56100','','','',19053,1905300,0,0,56100,'',0,1,null,null,'');

delete from ui_conf where id=56101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(56101,1,0,0,'/web/content/uiconf/graphico/madam_tussauds/kdp_1.2.3/kdp_white_pink_share.xml','kdp madam tussauds white pink share', 400,332,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('56101','','','',19053,1905300,0,0,56101,'',0,1,null,null,'');

delete from ui_conf where id=56102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(56102,1,0,0,'/web/content/uiconf/graphico/madam_tussauds/kdp_2.0.2/kdp_white_pink_remix.xml','kdp madam tussauds white pink remix', 400,362,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('56102','','','',19053,1905300,0,0,56102,'',0,1,null,null,'');

delete from ui_conf where id=56103;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(56103,1,0,0,'/web/content/uiconf/graphico/madam_tussauds/kdp_2.0.2/kdp_white_pink_share.xml','kdp madam tussauds white pink share', 400,332,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('56103','','','',19053,1905300,0,0,56103,'',0,1,null,null,'');

delete from ui_conf where id=56200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(56200,2,19053,1905300,'/web/content/uiconf/graphico/madam_tussauds/kcw_1.6.5/kcw_video_only.xml','kcw madam tussauds',0,0,'','/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=65300;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(65300,3,19053,1905300,'/web/content/uiconf/graphico/madam_tussauds/kse_2.1.1/kse_style1.xml','bcm madam tussauds',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);

delete from ui_conf where id=65301;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(65301,3,19053,1905300,'/web/content/uiconf/graphico/madam_tussauds/kse_2.1.1/kse_style2.xml','bcm madam tussauds 2',890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(), null, 1);

# Dev Hive (19511)
delete from ui_conf where id=57100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(57100,1,0,0,'/web/content/uiconf/devhive/kdp_1.2.3/kdp_fast_forwards.xml','kdp devhive fast forwards', 400,332,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('57100','','','',19511,1951100,0,0,57100,'',0,1,null,null,'');

delete from ui_conf where id=57101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(57101,1,0,0,'/web/content/uiconf/devhive/kdp_1.2.3/kdp_with_share.xml','kdp devhive share', 400,332,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('57101','','','',19511,1951100,0,0,57101,'',0,1,null,null,'');

delete from ui_conf where id=57200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(57200,2,0,0,'/web/content/uiconf/devhive/kcw/1.6.5/kcw_style1_video.xml','kcw devhive',0,0,'','/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1);

# ChupaChups (16412)
delete from ui_conf where id=58100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(58100,1,0,0,'/web/content/uiconf/chupa_chups/kdp_2.0.1/kdp_chupa_gray.xml','kdp chupa gray', 430,520,'','/flash/kdp/v2.0.3/kdp.swf',now(),now(), null, 1);
insert into widget values('58100','','','',16412,1641200,0,0,58100,'',0,1,null,null,'');

delete from ui_conf where id=58101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(58101,1,0,0,'/web/content/uiconf/chupa_chups/kdp_2.0.1/kdp_chupa_orange.xml','kdp chupa orange',  430,520,'','/flash/kdp/v2.0.3/kdp.swf',now(),now(), null, 1);
insert into widget values('58101','','','',16412,1641200,0,0,58101,'',0,1,null,null,'');

delete from ui_conf where id=58400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58400,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.11/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/v1.0.11/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58401;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58401,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.13_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.13_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

/*missing version 14 - roman*/
delete from ui_conf where id=58403;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58403,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.14_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.15_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58404;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58404,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.16_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.16_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58405;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58405,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.16_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.17_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58406;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58406,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.16_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.18_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58407;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(58407,4,0,0,'/web/content/uiconf/chupa_chups/kae_v1.0.16_chupa/kae_chupa_chups_generic.xml','chupa ae',750,640,'','/flash/kae/kae_v1.0.20_chupa/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

delete from ui_conf where id=58200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(58200,2,0,0,'/web/content/uiconf/chupa_chups/kcw_1.6.5/kcw_style1_allmedia.xml','kcw chupa',0,0,'','/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1);



# Metacafe Last House (20328) on production
delete from ui_conf where id=59100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(59100,1,0,0,'/web/content/uiconf/metacafe/lasthouse/kdp_1.2.3/kdp_lasthouse.xml','kdp lasthouse', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now());
delete from widget where id=59100;
insert into widget values(59100,59100,'',59100,20328,2032800,0,0,59100,'',0,1,null,null,'');

delete from ui_conf where id=59200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(59200,3,0,0,'/web/content/uiconf/metacafe/lasthouse/kse_2.1.3/kse_lasthouse.xml','se ghosthouse', 890,546,'','/flash/kse/v2.1.3/simpleeditor.swf',now(),now(),'',1);


# Skadaddle Media (20283)
delete from ui_conf where id=60500;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(60500,5,0,0,'/web/content/uiconf/skadaddle/uploader.xml','skadaddle uploader','','','','/flash/kupload/v1.0.6/KUpload.swf',now(),now());

delete from ui_conf where id=60200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(60200,2,0,0,'/web/content/uiconf/skadaddle/cw_image.xml','skadaddle kcw','','','','/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now());

# Afro Samurai (21054)
delete from ui_conf where id=61100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at) 
values(61100,1,0,0,'/web/content/uiconf/namcobandai/afrosamurai/kdp_1.2.3/kdp_afrosamurai.xml','kdp afro samurai', 400,320,'','/flash/kdp/v1.2.3/kdp.swf',now(),now());
delete from widget where id=61100;
insert into widget values(61100,61100,'',61100,21054,2105400,0,0,61100,'',0,1,null,null,'');

delete from ui_conf where id=61400;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(61400,4,0,0,'/web/content/uiconf/namcobandai/afrosamurai/kae_1.0.10/kae_afrosamurai_general.xml','afro samurai ae',750,640,'','/flash/kae/v1.0.10.23714/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

# SKIP 65xxx (It was used in Madam Tussauds)

delete from ui_conf where id=62200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(62200,2,11939,1193900,'/web/content/uiconf/fleishmanhillard/cw_upload_only.xml','bcm cw',680,480,NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf',now(),now(), null, 1);

#22388 - fleishmanhillard
delete from ui_conf where id=223882;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at)  
values(223882,2,22388,2238800,'/web/content/uiconf/fleishmanhillard/cw_upload_only.xml','fleishman hillard upload only','680','400',NULL,'/flash/kcw/v1.5.4/ContributionWizard.swf','2009-03-08 15:02:05','2009-03-08 15:02:05');

delete from ui_conf where id=223881;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(223881,1,0,0,'/web/content/uiconf/fleishmanhillard/kdp_1.1.11/kdp_drupal_v2.1_gray_view.xml','fleishmanhillard gray kdp (view)','','',NULL,'/flash/kdp/v1.1.11/kdp.swf',now(),now(), null, 1);

# dragon fruit - 19289
delete from ui_conf where id=192891;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(192891,1,19289,1928900,'/web/content//uiconf/dragonfruit/kdp/kdp_2.0.2/kdp_dragonfruit.xml','dragon fruit dark kdp ','400','332',NULL,'/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values("_19289_192891",19289192891,'',"_19289_192891",19289,1928900,0,0,192891,'',0,1,null,null,'');

# filmrookie - 17411
delete from ui_conf where id=174111;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(174111,1,17411,17411900,'/web/content/uiconf/filmrookie/kdp/kdp_2.0.2/kdp_filmrookie.xml','filmrookie dark kdp ','400','362',NULL,'/flash/kdp/v2.0.3/kdp.swf',now(),now(), null, 1);
insert into widget values("_17411_174111",17411174111,'',"_17411_17411",17411,1741100,0,0,174111,'',0,1,null,null,'');

delete from ui_conf where id=174112;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(174112,2,17411,1741100,'/web/content/uiconf/filmrookie/kcw/v1.6.5/kcw_style1_allMedia.xml','filmrookie cw',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=174113;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn)  
values(174113,4,0,0,'/web/content/uiconf/filmrookie/kae_1.0.10/kae_filmrookie_generic.xml','ae',750,640,'','/flash/kae/v1.0.10.23714/KalturaAdvancedVideoEditor.swf',now(),now(), null, 1);

# all hip hop - 19650
delete from ui_conf where id=66100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(66100,1,0,0,'/web/content/uiconf/allhiphop/kdp_v2.0.3/vertical_player_playlist.xml','kdp all hip hop', 425,400,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('66100','','','',19650,1965000,0,0,66100,'',0,1,null,null,'');

delete from ui_conf where id=66101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(66101,1,0,0,'/web/content/uiconf/allhiphop/kdp_v2.0.3/horizontal_player_playlist.xml','kdp all hip hop', 425,350,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('66101','','','',19650,1965000,0,0,66101,'',0,1,null,null,'');

delete from ui_conf where id=66102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(66102,1,0,0,'/web/content/uiconf/allhiphop/kdp_v2.0.3/player.xml','kdp all hip hop', 425,400,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('66102','','','',19650,1965000,0,0,66102,'',0,1,null,null,'');

# dogoodertv (23707)
delete from ui_conf where id=67100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(67100,1,0,0,'/web/content/uiconf/dogoodertv/kdp_2.0.0/kdp_default_light.xml','kdp dogoodertv', 400,364,'','/flash/kdp/v2.0.1/kdp.swf',now(),now(), null, 1);
insert into widget values('67100','','','',23707,23707,0,0,67100,'',0,1,null,null,'');

delete from ui_conf where id=67200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(67200,2,0,0,'/web/content/uiconf/dogoodertv/kcw_1.6.5/kcw_style1_video.xml','dogoodertv cw',680,480,NULL,'/flash/kcw/v1.6.5.24461/ContributionWizard.swf',now(),now(), null, 1);

# Denizen player (22438)
delete from ui_conf where id=68100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(68100,1,0,0,'/web/content/uiconf/denizen/kdp_v2.0.2/player.xml','kdp Denizen', 400,364,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('68100','','','',22438,2243800,0,0,68100,'',0,1,null,null,'');
insert into widget values('_22438_68100','','','',22438,2243800,0,0,68100,'',0,1,null,null,'');

# LEUPOLD partner id - 20271
delete from ui_conf where id=69100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(69100,1,0,0,'/web/content/uiconf/leupold/kdp_v2.0.2/vertical_player_playlist.xml','kdp leupold', 425,400,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('_20271_69100','','','',20271,2027100,0,0,69100,'',0,1,null,null,'');

delete from ui_conf where id=69101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(69101,1,0,0,'/web/content/uiconf/leupold/kdp_v2.0.2/horizontal_player_playlist.xml','kdp leupold', 425,350,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('_20271_69101','','','',20271,2027100,0,0,69101,'',0,1,null,null,'');

delete from ui_conf where id=69102;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(69102,1,0,0,'/web/content/uiconf/leupold/kdp_v2.0.2/player.xml','kdp leupold', 425,400,'','/flash/kdp/v2.0.2/kdp.swf',now(),now(), null, 1);
insert into widget values('_20271_69102','','','',20271,2027100,0,0,69102,'',0,1,null,null,'');

# WhichBox (22561)
delete from ui_conf where id=70200;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(70200,2,0,0,'/web/content/uiconf/whichboxmedia/kcw_1.6.5/kcw_style1_audio.xml','whichbox kcm style1 audio',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=70201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(70201,2,0,0,'/web/content/uiconf/whichboxmedia/kcw_1.6.5/kcw_style1_image.xml','whichbox kcm style1 image',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

delete from ui_conf where id=70202;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(70202,2,0,0,'/web/content/uiconf/whichboxmedia/kcw_1.6.5/kcw_style1_video.xml','whichbox kcm style1 video',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);

