<?php
		$addconversionprofile = array (
			array ( "conversionProfile_name" , "" , "" ),
			array ( "conversionProfile_profileType", "select" , "" , "pne" , "conversion_profile_type" ),			
			array ( "conversionProfile_commercialTranscoder" ,"select" , "" , "0" , "boolean_int_type" ),	
			array ( "conversionProfile_aspectRatio", "select" , "" , "1" , "conversion_profile_aspect_ratio" ),
			array ( "conversionProfile_width" ,null , "6" ),
			array ( "conversionProfile_height" ,null , "6" ),
			array ( "conversionProfile_bypassFlv" ,"select" , "" , "0" , "boolean_int_type" ),			
			array ( "conversionProfile_profileTypeSuffix" , "" , "" ),
			);
		
			 
		$getconversionprofile = array (
			array ( "conversionProfile_id" ),
			array ( "detailed" , "" , "1" ),
			);
			
		$listconversionprofiles = array (
			array ( "detailed" , "" , "1" ),
			array ( "detailed_fields" , null , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_user_id", null , "6" ),
			array ( "filter__eq_media_type", "select" , "" , "" , "playlist_media_type_filter" ),			
			array ( "filter__like_name", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "entries_filter_order_by"  ),
		) ;

		
?>