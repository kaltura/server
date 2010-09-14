<?php
		$adddvdentry = array (
			array ( "dvdEntry_name" , "" , "" ),
			array ( "dvdEntry_mediaType", "select" , "" , "" , "entry_media_type" ),			
			array ( "dvdEntry_tags" , "" , "" ),
			array ( "dvdEntry_dataContent" , "textarea"  , "3,23" ),
			array ( "dvdEntry_indexedCustomData1" , null , 20 ),
			array ( "dvdEntry_groupId" , null , 20 ) ,
			array ( "dvdEntry_partnerData" , null , 20 ) ,
			);
		
			
		$getdvdentry = array (
			array ( "dvdEntry_id" ),
			array ( "detailed" , "" , "1" ),
			);
			
		$updatedvdentry = array (
			array ( "dvdEntry_id" , "" , "" ),
			array ( "dvdEntry_mediaType", "select" , "" , "" , "entry_media_type" ),
			array ( "dvdEntry_name" , "" , "" ),
			array ( "dvdEntry_tags" , "" , "" ),
			array ( "dvdEntry_dataContent" , "textarea"  , "3,23" ),
			array ( "dvdEntry_indexedCustomData1" , null , 20 ),
			array ( "dvdEntry_groupId" , null , 20 ) ,
			array ( "dvdEntry_partnerData" , null , 20 ) ,
			);	

		$listdvdentries = array (
			array ( "detailed" , "" , "1" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
		) ;

				
		$listmydvdentries = array (
			array ( "detailed" , "" , "1" ),
			array ( "detailed_fields" , null , "20" ),			
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__eq_media_type", "select" , "" , "" , "entry_media_type_filter" ),
			array ( "filter__in_media_type", null , "" ),
			array ( "filter__in_indexed_custom_data_1", null , "" ),
			array ( "filter__like_name", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
		) ;

		$adddvdjob = array (
			array ( "entry_id" ),
		) ;
?>