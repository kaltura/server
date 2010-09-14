# added a ui_conf for the kcw of kmc - swf version v1.6.5 rather than v1.6.4
delete from ui_conf where id=36201;
insert into ui_conf (id,obj_type,partner_id,subp_id,conf_file_path,name,width,height,html_params,swf_url,created_at,updated_at, conf_vars, use_cdn) 
values(36201,2,0,0,'/web/content/uiconf/kaltura/samplekit/kcw_2.6.4/kcw_samplekit.xml','samplekit cw',680,480,NULL,'/flash/kcw/v1.6.5/ContributionWizard.swf',now(),now(), null, 1);
