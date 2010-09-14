
INSERT INTO file_sync (partner_id,object_type,object_id,STATUS,VERSION,object_sub_type,dc,original,created_at,updated_at,ready_at,file_root,file_path,file_size)
VALUES
("0","4","_KMCLOGO",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo.flv","277975"),
("0","4","_KMCLOGO2",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo_animated_blue.flv","2743980"),
("0","4","_KMCLOGO3",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo_animated_green.flv","2615388"),
("0","4","_KMCLOGO4",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo_animated_pink.flv","2490304"),
("0","4","_KMCLOGO5",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo_animated_red.flv","2966138"),
("0","4","_KMCLOGO1",2,"1","1","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/data/kmc_logo_animated_black.flv","2898373"),
("0","1","_KMCLOGO",2,"0","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnail/kmc_logo.jpg","36676"),
("0","1","_KMCLOGO2",2,"0","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnail/kmc_logo_animated_blue.jpg","18463"),
("0","1","_KMCLOGO3",2,"0","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnail/kmc_logo_animated_green.jpg","17572"),
("0","1","_KMCLOGO4",2,"0","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnaila/kmc_logo_animated_pink.jpg","16985"),
("0","1","_KMCLOGO5",2,"0","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnail/kmc_logo_animated_red.jpg","19930"),
("0","1","_KMCLOGO1",2,"100000","3","0","1",NOW(),NOW(),NOW(),"/web","/content/templates/entry/thumbnail/kmc_logo_animated_black.jpg","19521"
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