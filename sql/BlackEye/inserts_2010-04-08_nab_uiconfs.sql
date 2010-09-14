INSERT INTO ui_conf (id, obj_type, partner_id, subp_id, conf_file_path, name, width, height, html_params, swf_url, created_at, updated_at, conf_vars, use_cdn, tags, custom_data, status, description, display_in_search, creation_mode, version)
VALUES
('48120','8','0','0','/web/content/uiconf/kaltura/kmc/nab/kdp_kmc_content_dark.xml','Live preview player','400','330','','/flash/kdp3/v3.2.0/kdp3.swf',now(),now(),'','1','player',NULL,'2',NULL,'2','1',NULL),
('48121','8','0','0','/web/content/uiconf/kaltura/kmc/nab/kdp_kmc_content_playlist_dark.xml','Live preview playlist','760','330','','/flash/kdp3/v3.2.0/kdp3.swf',now(),now(),'','1','playlist',NULL,'2',NULL,'2','1',NULL);

INSERT IGNORE INTO `file_sync` (partner_id, object_type, object_id, version, object_sub_type, dc, original, created_at, updated_at, ready_at, sync_time, status, file_type, linked_id, link_count, file_root, file_path, file_size)
VALUES
(0,2,'48120',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/uiconf/kaltura/kmc/nab/kdp_kmc_content_dark.xml',6173),
(0,2,'48120',NULL,1,'1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL),
(0,2,'48121',NULL,1,'0',1,now(),now(),now(),NULL,2,1,NULL,NULL,'/web/','content/uiconf/kaltura/kmc/nab/kdp_kmc_content_playlist_dark.xml',10611),
(0,2,'48121',NULL,1,'1','0',NOW(),NOW(),NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL),