# Dark Player
delete from ui_conf where id=48100;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48100,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_dark.xml','kdp kaltura default dark', 400,364,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('48100','','','',0,0,0,0,48100,'',0,1,null,null,'');

# Light Player
delete from ui_conf where id=48101;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(48101,1,0,0,'/web/content/uiconf/kaltura/default_player/kdp_1.2.3/kdp_default_light.xml','kdp kaltura default light', 400,364,'','/flash/kdp/v1.2.3/kdp.swf',now(),now(), null, 1);
insert into widget values('48101','','','',0,0,0,0,48101,'',0,1,null,null,'');
