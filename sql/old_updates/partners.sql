# plymedia
delete from partner where id=101;
insert into partner (id,partner_name , url2, secret , admin_secret ,appear_in_search , created_At , updated_at , partner_alias  ,ks_max_expiry_in_seconds , create_user_on_demand, prefix, description, moderate_content, notify, custom_data , service_config_id) values (101 , "plymedia" , "" , "1234567" , "1234567" , "1" , now() , now() , "plymedia" , 864000 , 1 , "plymedia" , "plymedia - subtitles and translation" , 0 ,	0 , "" , "services_plymedia.ct" );

# taboola
delete from partner where id=102;
insert into partner (id,partner_name , url2, secret , admin_secret ,appear_in_search , created_At , updated_at , partner_alias  ,ks_max_expiry_in_seconds , create_user_on_demand, prefix, description, moderate_content, notify, custom_data , service_config_id) values (102 , "taboola" , "" , "1234567" , "1234567" , "1" , now() , now() , "taboola" , 864000 , 1 , "taboola" , "taboola - related" , 0 , 0 , "" , "services_taboola.ct");
		
# flattening		
delete from partner where id=103;
insert into partner (id,partner_name , url2, secret , admin_secret ,appear_in_search , created_At , updated_at , partner_alias  ,ks_max_expiry_in_seconds , create_user_on_demand, prefix, description, moderate_content, notify, custom_data , service_config_id) values (103 , "flattening" , "" , "1234567" , "1234567" , "1" , now() , now() , "flattening" , 864000 , 1 , "flattening" , "flattening " , 0 , 0 , "" , "services_flattening.ct");

