<?php
	$multi_request_1 = array (
		array ( "global_partner_id" , "" , "5" , "1" ) ,
		array ( "global_subp_id" , "" , "5" , "100") ,
		array ( "global_uid" , "" , "10" , "2" ) ,
		array ( "request1_service" , "" , "15" , "startsession" ) ,
		array ( "request1_secret" , "" , "34" , "11111" ) ,
		array ( "request2_service" , "" , "15" , "getentry" ) ,
		array ( "request2_entry_id" ),
		array ( "request2_detailed" , "" , "1" ),
		array ( "request2_ks" , "" , "40" , "{response1.result.ks}" ) ,
		array ( "request3_service" , "" , "15" , "updateentry" ) ,
		array ( "request3_ks" , "" , "40" , "{response1.result.ks}" ) ,
		array ( "request3_entry_id" , "" , "30" , "{response2.result.entry.id}" ) ,
		array ( "request3_entry_name" , "" , "40" , "{response2.result.entry.name}4" ) ,

	) ;

	$multi_request_2 = array (
		array ( "request1_service" , "" , "15" , "getentry" ) ,
		array ( "request1_detailed" , "" , "1" ),
		array ( "request1_entry_id" , "" , "30" , "" ) ,
		array ( "request1_version" , "" , "34" , "-1" ) ,
		array ( "request2_service" , "" , "15" , "getkshow" ) ,
		array ( "request2_kshow_id" , "" , "30" , "{response1.result.entry.kshowId}" ) ,			
		array ( "request3_service" , "" , "15" , "getallentries" ) ,
		array ( "request3_kshow_id" , "" , "30" , "{response2.result.kshow.id}" ) ,
		array ( "request3_entry_typee" , "" , "40" , "{response1.result.entry.type}" ) ,
		array ( "request3_list_type" , "" , "40" , "4" ) ,
	);

?>