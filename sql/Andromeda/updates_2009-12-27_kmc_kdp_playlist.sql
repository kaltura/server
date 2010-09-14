INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48504,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_playlist_light.xml','KDP3 Light Playlist', 600,330,'','/flash/kdp3/v0.2.5/kdp3.swf',NOW(),NOW(), NULL, 1, 'playlist', 2);

INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
VALUES(48505,1,0,0,'/web/content/uiconf/kaltura/kmc/content/kdp_kmc_content_playlist_dark.xml','KDP3 Dark Playlist', 600,330,'','/flash/kdp3/v0.2.5/kdp3.swf',NOW(),NOW(), NULL, 1, 'playlist', 2);

UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf', name = 'KDP3 Light Player' WHERE id = 48501 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf', name = 'KDP3 Dark Player' WHERE id = 48502 LIMIT 1;
UPDATE ui_conf SET swf_url = '/flash/kdp3/v0.2.5/kdp3.swf' WHERE id = 48503 LIMIT 1;