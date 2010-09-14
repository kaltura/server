# conversion_profiles and conversion_params

# must have the id 0 - the kaltura default
insert into `conversion_profile` values 
(1,0,1,'download','download',0,0,0,'',1,0,now(),now()),
(2,0,1,'low','low',0,0,0,'',1,1,now(),now()),
(3,0,1,'med','med',0,0,0,'',1,1,now(),now()),
(4,0,1,'high','high',0,0,0,'',1,1,now(),now()),
(5,0,1,'hd','hd',1,0,0,'',1,1,now(),now()) ;

insert into `conversion_params` values 
(1,0,1,'low_play','low',1,400,,'1',5,500,0,'','',now(),now()),
(2,0,1,'low_edit','low',1,400,,'1',25,500,0,'_edit','',now(),now()),
(3,0,1,'low_thumbs','low',5,100,76,'1',300,200,0,'_thumbs','',now(),now()),
(4,0,1,'med','med',1,600,,'1',300,800,0,'','',now(),now()),
(5,0,1,'high','high',1,600,,'1',300,1200,,'','',now(),now()),
(6,0,1,'hd','hd',1,1200,675,'2',300,20000,0,'','',now(),now()),
(7,0,1,'download','download',3,100,75,'1',300,200,0,'','',now(),now());