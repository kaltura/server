# KDP for KMC 
delete from ui_conf where id=190;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at,conf_vars,use_cdn)  
values(190,1,1,100,'/web/content/uiconf/kaltura/kmc/kdp_kmc_content.xml','kdp for kmc','400','420','','/flash/kdp/v1.1.18/kdp.swf',now(),now(),'',1);
delete from widget where id = '190';
insert into widget values ('190',190,'','190',1,100,0,0,190,'',0,1,now(),now(),'');