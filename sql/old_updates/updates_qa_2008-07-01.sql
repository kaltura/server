delete from partner where id=55;

insert into partner 
	(id,partner_name , url2, secret , admin_secret ,appear_in_search , created_At , updated_at , partner_alias  ,ks_max_expiry_in_seconds , create_user_on_demand, prefix, description, moderate_content, notify, custom_data )
values 
	(55 , "qa" , "http://localhost/qa/notifications.php" , "1234567890qwertyuiop" , "1234567890qwertyuiop" , "1" , now() , now() , "dkfhalkjgf" , 86400 , 1 , 					"aq" , "kaltura'a qa site" , 0 ,		1 , "" );
		


