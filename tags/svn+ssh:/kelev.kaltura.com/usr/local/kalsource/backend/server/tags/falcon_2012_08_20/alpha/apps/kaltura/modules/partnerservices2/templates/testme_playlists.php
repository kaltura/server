<?php

		$playlist_filter = array ( 
			array ( TESTME_GROUP_START , "filter1" , true , false ),  // switch off the filter by default
			array ( "._eq_id", null , "6" ),
			array ( "._eq_user_id", null , "6" ),
			array ( "._eq_media_type", "select" , "" , "" , "entry_media_type_filter" ),
			array ( "._in_media_type", null , "" ),
			array ( "._like_tags", null , "20" ),
			array ( "._like_name", null , "20" ),
			array ( "._mlikeor_name", null , "20" ),
			array ( "._mlikeor_tags", null , "20" ),
			array ( "._mlikeor_admin_tags", null , "20" ),
			array ( "._mlikeor_tags-admin_tags", null , "20" ),
			array ( "._mlikeand_tags", null , "20" ),
			array ( "._mlikeand_admin_tags", null , "20" ),
			array ( "._mlikeand_tags-admin_tags", null , "20" ),
			array ( "._matchand_search_text", null , "20" ),
			array ( "._matchor_search_text", null , "20" ),
			array ( "._gte_created_at", null , "20" ),
			array ( "._lte_created_at", null , "20" ),
			array ( "._limit", null , "3" ),
			array ( "._order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
			array ( TESTME_GROUP_END , "filter1_" , false ),
		);
		
		$executeplaylist = array_merge ( array (
			array ( "playlist_id" , "" , "" ),
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "fp", "" , "6" , "filter" , "" , "the filter-prefix before the index" ),
			) , $playlist_filter );
		
		$addplaylist = array (
			array ( "playlist_name" , "" , "" ),
			array ( "playlist_dataContent" , "textarea"  , "3,23" ),
			array ( "playlist_mediaType", "select" , "" , "" , "playlist_media_type" ),			
			array ( "playlist_tags" , "" , "" ),
			array ( "playlist_indexedCustomData1" , null , 20 ),
			array ( "playlist_groupId" , null , 20 ) ,
			array ( "playlist_partnerData" , null , 20 ) ,
			array ( "playlist_adminTags" , null , 20 , "" , "" , "Can be set only by admins" ) ,			
			array ( "update_stats" , "select" , "" , "false" , "boolean_type" ) ,
			);
		
			
		$getplaylist = array (
			array ( "playlist_id" ),
			array ( "detailed" , "" , "1" ),
			);
			
		$updateplaylist = array (
			array ( "playlist_id" , "" , "" ),
			array ( "playlist_mediaType", "select" , "" , "" , "playlist_media_type" ),
			array ( "playlist_name" , "" , "" ),
			array ( "playlist_tags" , "" , "" ),
			array ( "playlist_dataContent" , "textarea"  , "10,25" ),
			array ( "playlist_indexedCustomData1" , null , 20 ),
			array ( "playlist_groupId" , null , 20 ) ,
			array ( "playlist_partnerData" , null , 20 ) ,
			array ( "playlist_adminTags" , null , 20 , "" , "" , "Can be set only by admins" ) ,			
			array ( "allow_empty_field" , "select" , "" , "false" , "boolean_type" ) ,
			array ( "update_stats" , "select" , "" , "false" , "boolean_type" ) ,
			);	

		$listplaylists = array (
			array ( "detailed" , "" , "1" ),
			array ( "detailed_fields" , null , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( TESTME_GROUP_START , "filter" , true , false ),  // switch off the filter by default
			array ( "._eq_id", null , "6" ),
			array ( "._eq_user_id", null , "6" ),
			array ( "._eq_media_type", "select" , "" , "" , "playlist_media_type_filter" ),			
			array ( "._like_name", null , "20" ),
			array ( "._gte_created_at", null , "20" ),
			array ( "._lte_created_at", null , "20" ),
			array ( "._order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
			array ( TESTME_GROUP_END , "filter" , true , false ),  // switch off the filter by default
		) ;

		$deleteplaylist = array (
			array ( "playlist_id" ),
		);
	
		$executeplaylistfromcontent =	array_merge ( array (
			array ( "playlist_mediaType", "select" , "" , "" , "playlist_media_type" ),
			array ( "playlist_dataContent" , "textarea"  , "10,25" ),
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "fp", "" , "6" , "filter" , "" , "the filter-prefix before the index" ),
		) , $playlist_filter );

		$getplayliststatsfromcontent =	array_merge ( array (
			array ( "playlist_mediaType", "select" , "" , "" , "playlist_media_type" ),
			array ( "playlist_dataContent" , "textarea"  , "10,25" ),
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "fp", "" , "6" , "filter" , "" , "the filter-prefix before the index" ),
		) , $playlist_filter );
		
?>