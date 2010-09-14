<?php
		
		$adduiconf = array (
			array ( "uiconf_name" , "" , "" ),
			array ( "uiconf_objType", "select" , "" , "" , "uiconf_obj_type" ),
			array ( "uiconf_width" , "" , "4" , ""),		
			array ( "uiconf_height" , "" , "4" , ""),
			array ( "uiconf_tags" , "" , "" ),
			array ( "uiconf_htmlParams" , "" , "" ),
			array ( "uiconf_confFile" , "textarea"  , "3,23" ),
			array ( "uiconf_confFileFeatures" , "textarea"  , "3,23" ),
//			array ( "uiconf_confFilePath" , "" , "20" ),
			array ( "uiconf_swfUrl" , "" , "20" ),
			array ( "uiconf_swfUrlVersion" , "" , "10" ),
			array ( "uiconf_autoplay" , "select" , "2" , "" , "boolean_int_type"  ),
			array ( "uiconf_automuted" , "select" , "2" , "" , "boolean_int_type"  ),
			array ( "uiconf_creationMode" , "select" , "2" , "1" , "uiconf_creation_mode"  ),
			);

		$cloneuiconf = array (
			array ( "uiconf_id" , "" , "" ),
			array ( "detailed" , "" , "1" ),
/*			
			array ( "uiconf_objType", "select" , "" , "" , "uiconf_obj_type" ),
			array ( "uiconf_width" , "" , "4" , "400"),		
			array ( "uiconf_height" , "" , "4" , "300"),
			array ( "uiconf_tags" , "" , "" ),
			array ( "uiconf_htmlParams" , "" , "" ),
			array ( "uiconf_confFile" , "textarea"  , "3,23" ),
			array ( "uiconf_swfUrl" , "" , "20" ),
			array ( "uiconf_swfUrlVersion" , "" , "10" ),
			array ( "uiconf_autoplay" , "select" , "2" , "1" , "boolean_int_type"  ),
			array ( "uiconf_creationMode" , "select" , "2" , "1" , "boolean_type"  ),
*/
			);
			
		$updateuiconf = array (
			array ( "uiconf_id" , "" , "" ),
			array ( "uiconf_name" , "" , "" ),
			array ( "uiconf_objType", "select" , "" , "" , "uiconf_obj_type" ),
			array ( "uiconf_width" , "" , "4" , ""),		
			array ( "uiconf_height" , "" , "4" , ""),
			array ( "uiconf_tags" , "" , "" ),
			array ( "uiconf_htmlParams" , "" , "" ),
//			array ( "uiconf_confFilePath" , "" , "20" ),
			array ( "uiconf_confFile" , "textarea"  , "3,23" ),
			array ( "uiconf_confFileFeatures" , "textarea"  , "3,23" ),
			array ( "uiconf_swfUrl" , "" , "20" ),
			array ( "uiconf_swfUrlVersion" , "" , "10" ),
			array ( "uiconf_autoplay" , "select" , "2" , "0" , "boolean_int_type"  ),
			array ( "uiconf_automuted" , "select" , "2" , "0" , "boolean_int_type"  ),
			array ( "uiconf_creationMode" , "select" , "2" , "1" , "uiconf_creation_mode"  ),
			array ( "allow_empty_field" , "select" , "" , "false" , "boolean_type" ) ,
			);
			
		$listuiconfs = array (
			array ( "detailed" , "" , "1" ),
			array ( "detailed_fields" , null , "20" ),
			array ( "page" , "" , "2" , "1"),
			array ( "page_size" , "" , "2" , "10" ),
			array ( "filter__eq_id", null , "6" ),
			array ( "filter__eq_obj_type", "select" , "" , "" , "uiconf_obj_type_filter" ),			
			array ( "filter__like_name", null , "20" ),
			array ( "filter__mlikeor_tags", null , "20" ),
			array ( "filter__gte_created_at", null , "20" ),
			array ( "filter__lte_created_at", null , "20" ),
			array ( "filter__in_display_in_search", null , "1" ),
			array ( "filter__in_creation_mode", null , "1" ),
			array ( "filter__order_by" , "select" , "2" , "1" , "uiconf_filter_order_by"  ),
		) ;

		$deleteuiconf = array (
			array ( "uiconf_id" , "" , "" ),
			array ( "detailed" , "" , "1" )
		);	
		
		$getuiconf = array (
			array ( "ui_conf_id" ),
			array ( "detailed" , "" , "1" ),
		);
		
?>