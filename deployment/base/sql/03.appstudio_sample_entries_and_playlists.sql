
insert into kuser (id,screen_name,full_name,puser_id,partner_id) values(1, 'user for template entries', 'user for template entries', '999999', 0);




INSERT IGNORE INTO `entry` 
(id, kshow_id, kuser_id, name, description, type, media_type, data, thumbnail, tags, status, license_type, length_in_msecs, created_at, updated_at, partner_id, display_in_search, subp_id, permissions, moderation_status, moderation_count, modified_at, available_from)
VALUES 
('_KMCLOGO', '_KMCLOGO', 1, 'Kaltura Logo For KMC', 'A black version of the Kaltura animated logo', 1, 1, '&kmc_logo.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now()),
('_KMCLOGO1', '_KMCLOGO1', 1, 'Kaltura Logo For KMC (Black)', 'A black version of the Kaltura animated logo', 1, 1, '&kaltura_logo_animated_black.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now()),
('_KMCLOGO2', '_KMCLOGO2', 1, 'Kaltura Logo For KMC (Blue)', 'A blue version of the Kaltura animated logo', 1, 1, '&kaltura_logo_animated_blue.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now()),
('_KMCLOGO3', '_KMCLOGO3', 1, 'Kaltura Logo For KMC (Green)', 'A green version of the Kaltura animated logo', 1, 1, '&kaltura_logo_animated_green.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now()),
('_KMCLOGO4', '_KMCLOGO4', 1, 'Kaltura Logo For KMC (Pink)', 'A purple version of the Kaltura animated logo', 1, 1, '&kaltura_logo_animated_pink.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now()),
('_KMCLOGO5', '_KMCLOGO5', 1, 'Kaltura Logo For KMC (Red)', 'A red version of the Kaltura animated logo', 1, 1, '&kaltura_logo_animated_red.flv', '100000.jpg', 'logo, animated, demo', 2, null, 2700, now(), now(), 0, 2, 0, 1, 2, 0, null, now());



INSERT INTO file_sync (partner_id,object_type,object_id,STATUS,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,file_root,file_path,file_size)
VALUES
("0","4","_KMCLOGO",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kmc_logo.flv","277975"),
("0","4","_KMCLOGO2",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kaltura_logo_animated_blue.flv","2743980"),
("0","4","_KMCLOGO3",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kaltura_logo_animated_green.flv","2615388"),
("0","4","_KMCLOGO4",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kaltura_logo_animated_pink.flv","2490304"),
("0","4","_KMCLOGO5",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kaltura_logo_animated_red.flv","2966138"),
("0","4","_KMCLOGO1",2,"1","1","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/data/kaltura_logo_animated_black.flv","2898373"),
("0","1","_KMCLOGO",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kmc_logo.jpg","36676"),
("0","1","_KMCLOGO2",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kaltura_logo_animated_blue.jpg","18463"),
("0","1","_KMCLOGO3",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kaltura_logo_animated_green.jpg","17572"),
("0","1","_KMCLOGO4",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kaltura_logo_animated_pink.jpg","16985"),
("0","1","_KMCLOGO5",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kaltura_logo_animated_red.jpg","19930"),
("0","1","_KMCLOGO1",2,"100000","3","0","1",NOW(),NOW(),NOW(),"@WEB_DIR@/","/content/templates/entry/thumbnail/kaltura_logo_animated_black.jpg","19521"
);



INSERT INTO flavor_asset 
(id,partner_id,tags,created_at,updated_at,entry_id,STATUS,VERSION)
VALUES
("_KMCLOGO" , 0 , "web,mbr", NOW() , NOW() , "_KMCLOGO" , 2 , 1 ),
("_KMCLOGO1" , 0 , "web,mbr" , NOW() , NOW() , "_KMCLOGO1" , 2 , 1 ),
("_KMCLOGO2" , 0 , "web,mbr" ,NOW() , NOW() , "_KMCLOGO2" , 2 , 1 ),
("_KMCLOGO3" , 0 , "web,mbr" ,NOW() , NOW() , "_KMCLOGO3" , 2 , 1 ),
("_KMCLOGO4" , 0 , "web,mbr" ,NOW() , NOW() , "_KMCLOGO4" , 2 , 1 ),
("_KMCLOGO5" , 0 , "web,mbr" ,NOW() , NOW() , "_KMCLOGO5" , 2 , 1 );



INSERT IGNORE INTO `file_sync` (partner_id, object_type, object_id, version, object_sub_type, dc, original, created_at, updated_at, ready_at, sync_time, status, file_type, linked_id, link_count, file_root, file_path, file_size) VALUES 
(0,1,'_KMCSPL1','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'@WEB_DIR@/','/content/templates/entry/data/kmc_template_playlist1.txt',49),
(0,1,'_KMCSPL1','100000',1,'1',0,now(),now(),NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),
(0,1,'_KMCSPL2','100000',1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'@WEB_DIR@/','/content/templates/entry/data/kmc_template_playlist2.txt',49),
(0,1,'_KMCSPL2','100000',1,'1',0,now(),now(),NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL);



INSERT IGNORE INTO `entry` 
(id, kshow_id, kuser_id, name, type, media_type, data, tags, status, license_type, length_in_msecs, created_at, updated_at, partner_id, display_in_search, subp_id, permissions, moderation_status, moderation_count, modified_at, available_from)
VALUES 
('_KMCSPL1',NULL,1,'PlaylistDemo1',5,3,'100000.txt','demo 1 for app studio',2,-1,0,now(),now(),0,2,0,1,2,0,now(),'2008-01-26 00:00:00'),
('_KMCSPL2',NULL,1,'PlaylistDemo2',5,3,'100000.txt','demo 2 for app studio',2,-1,0,now(),now(),0,2,0,1,2,0,now(),'2008-01-26 00:00:00');


