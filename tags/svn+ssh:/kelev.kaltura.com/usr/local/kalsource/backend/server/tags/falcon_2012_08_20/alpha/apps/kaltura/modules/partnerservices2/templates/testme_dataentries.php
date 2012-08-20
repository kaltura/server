<?php
		$adddataentry = array (
			array ( "entry_name" , "" , "" ),
			array ( "entry_mediaType", "select" , "" , "" , "entry_media_type" ),			
			array ( "entry_tags" , "" , "" ),
			array ( "entry_dataContent" , "textarea"  , "3,23" ),
			array ( "entry_indexedCustomData1" , null , 20 ),
			array ( "entry_groupId" , null , 20 ) ,
			array ( "entry_partnerData" , null , 20 ) ,
			);
		
			
		$getdataentry = array (
			array ( "entry_id" ),
			array ( "detailed" , "" , "1" ),
			);
			
		$updatedataentry = array (
			array ( "entry_id" , "" , "" ),
			array ( "entry_mediaType", "select" , "" , "" , "entry_media_type" ),
			array ( "entry_name" , "" , "" ),
			array ( "entry_tags" , "" , "" ),
			array ( "entry_dataContent" , "textarea"  , "3,23" ),
			array ( "entry_indexedCustomData1" , null , 20 ),
			array ( "entry_groupId" , null , 20 ) ,
			array ( "entry_partnerData" , null , 20 ) ,
			);	

		$listdataentries = array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__in_indexed_custom_data_1", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
		) ;
		
		$deletedataentry = array (
			array ( "entry_id" ),
		);
		

?>