
# ---------------------- playlist 2.5 -----------------------
# kdp kaltura playlist vertical  white  
delete from ui_conf where id=48304;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags,display_in_search) 
values(48304,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/playlist_vertical_white_400_600.xml','Vertical Light', 400,600,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1, 'playlist',2);

# kdp kaltura playlist vertical black  
delete from ui_conf where id=48305;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags,display_in_search) 
values(48305,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/playlist_vertical_dark_400_600.xml','Vertical Dark', 400,600,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1, 'playlist',2);

# kdp kaltura playlist horizontal white  
delete from ui_conf where id=48306;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags,display_in_search) 
values(48306,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/playlist_horizontal_white_740_335.xml','Horizontal Light', 740,335,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1, 'playlist',2);

# kdp kaltura playlist horizontal black  
delete from ui_conf where id=48307;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars, use_cdn, tags,display_in_search) 
values(48307,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/playlist_horizontal_dark_740_335.xml','Horizontal Dark', 740,335,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1, 'playlist',2);
# ---------------------- playlist 2.5 -----------------------


# ---------------------- kdp 2.5 -----------------------
# Dark Player
delete from ui_conf where id=48410;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
values(48410,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/player_dark_400_335.xml','Dark player', 400,335,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1 , "player",2);
insert into widget values('48410','','','',0,0,0,0,48410,'',0,1,null,null,'');

# Light Player
delete from ui_conf where id=48411;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags,display_in_search) 
values(48411,1,0,0,'/web/content/uiconf/kaltura/kmc/embedplayers/player_light_400_335.xml','Light player', 400,335,'','/flash/kdp/v2.5.2.30923/kdp.swf',now(),now(), null, 1 , "player",2);
insert into widget values('48411','','','',0,0,0,0,48411,'',0,1,null,null,'');

# ---------------------- kdp 2.5 -----------------------

INSERT INTO ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,NAME,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn,tags) 
VALUES(48308,1,0,0,'/web/content/uiconf/kaltura/kmc/kdp_kmc_preview_combo.xml ','kmc players', 400,335,'','/flash/kdp/v2.5.2.30923/kdp.swf',NOW(),NOW(), NULL, 1 , "player");

# remove the tags from the old ui_confs so they will not appear in the app studio's lists
update ui_conf set tags="" where id in (48104,48105,48106,48107,48204,48205,48206,48207,48210,48211,48110,48111);



