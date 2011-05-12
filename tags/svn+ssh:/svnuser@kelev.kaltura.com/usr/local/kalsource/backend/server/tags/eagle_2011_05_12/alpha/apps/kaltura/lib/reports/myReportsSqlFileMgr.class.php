<?php
class myReportsSqlFileMgr
{
	const NO_TEXT_SUFFIX = "no_text";
	const FOR_OBJECTS_SUFFIX = "for_objects";
	
	public static function getSqlFilePath ( $type_str , $flavor_str , $add_search_text , $object_ids )
	{
		$res = self::getSqlFilePathImpl( $type_str , $flavor_str , $add_search_text , $object_ids );
KalturaLog::log ( __METHOD__. ": [$type_str] [$flavor_str] [$add_search_text] [$object_ids] -> [$res]" );		
		return $res;
	}
	
	private static function getSqlFilePathImpl ( $type_str , $flavor_str , $add_search_text , $object_ids , $recursion_count = 0)
	{
//KalturaLog::log ( __LINE__ . ": [$type_str] [$flavor_str] [$add_search_text] [$object_ids] [$recursion_count]" );		
		$recursion_count++;
		if ( $recursion_count > 5) 
		{
			throw new Exception ("Cannot find config for [$type_str] , [$flavor_str]" ); 
		}
		 	
		$config = self::getFileNameMappingConfig( 
			$type_str , 
			$flavor_str , 
			$add_search_text ? "" : "_" . self::NO_TEXT_SUFFIX , 
			$object_ids ? "_" . self::FOR_OBJECTS_SUFFIX : "" );
		
		if ( $config === null )
		{
			if ( $object_ids )
			{
				// search again without the _object addition
				$config = self::getFileNameMappingConfig( 
					$type_str , 
					$flavor_str , 
					$add_search_text ? "" : "_" . self::NO_TEXT_SUFFIX , 
					"" );
		
				if ( $config === null )
				{
					throw new Exception ( "cannot find mapping for [$type_str][$flavor_str]" );
				}
			}
			else
			{
				throw new Exception ( "cannot find mapping for [$type_str][$flavor_str]" );
			}
		}
		
//echo "[[$config]]</br>";		
		if ( $config === "" )
		{
			$path_prefix =  dirname (__FILE__ ) . "/{$type_str}/{$type_str}_$flavor_str";
			if ( $add_search_text )
				return $path_prefix . ".sql" ;
			else
				return $path_prefix . "_" . self::NO_TEXT_SUFFIX . ".sql" ;			
		}
		else
		{
			// override the defaults
			if ( $config[0] == "!" ) // use an alias
			{
				// an alias to some other path
				$parts = explode ( "/" , substr ( $config , 1 ) ); // find the '/' character in the rest of the string
				if ( count($parts) == 1 )
				{
//echo $parts[0]; die();					
					// use the configuration of same report type but other flavor - use false as $no_text_indicator
					return self::getSqlFilePathImpl ( $type_str , $parts[0] , true , $object_ids , $recursion_count );
				}
				elseif ( count($parts) == 2 )
				{
					// use the configuraiton of some other report_type and other flavor - use false as $no_text_indicator
					return self::getSqlFilePathImpl ( $parts[0] , $parts[1] , true , $object_ids , $recursion_count );
				}
			}
			else
			{
				$parts = explode ( "/" , $config  ); // find the '/' character in the rest of the string
				if ( count($parts) == 1 )
				{
					// use the configuration of same report type but fixed text of file name
					$path_prefix =  dirname (__FILE__ ) . "/{$type_str}/{$type_str}_{$parts[0]}";
				}
				elseif ( count($parts) == 2 )
				{
					// use the configuration of some other report type and fixed text of file name
					$path_prefix =  dirname (__FILE__ ) . "/{$parts[0]}/{$parts[0]}_{$parts[1]}";
				}
				
				return $path_prefix . ".sql" ;
			}
		}

	}

	
	private static function getFileNameMappingConfig ( $type_str , $flavor_str , $no_text , $for_objects )
	{
		$map = array (
			"content_contributions" => array (
				"detail" => "",
				"detail_no_text" => "!detail",		
				"count" => "",
				"count_no_text" => "!count",		
				"graph" => "",
				"graph_no_text" => "!graph",
				"total" => "",
				"total_no_text" => "!total",
			) ,
			"content_dropoff" => array (
				"detail" => "",
				"detail_no_text" => "",	
				"count" => "",
				"count_no_text" => "",	
				"graph" => "graph_and_total",
				"graph_no_text" => "graph_and_total_no_text",
				"total" => "graph_and_total",
				"total_no_text" => "graph_and_total_no_text",
			) ,	
			"content_interactions" => array (	
				"detail" => "",
				"detail_no_text" => "",
				"count" => "",
				"count_no_text" => "",						
				"graph" => "",
				"graph_no_text" => "",
				"total" => "",
				"total_no_text" => "",			
			),	
			"map_overlay" => array (	
				"detail" => "",
				"detail_no_text" => "!detail",	
				"detail_for_objects" => "detail_for_objects",
				"detail_no_text_for_objects" => "detail_for_objects",
				"count" => "",
				"count_no_text" => "!count",
				"count_for_objects" => "count_for_objects",					
				"count_no_text_for_objects" => "count_for_objects",
				"graph" => "map",
				"graph_no_text" => "map",
				"total" => "",
				"total_no_text" => "!total",			
			),
			"top_content" => array (	
				"detail" => "",
				"detail_no_text" => "",	
				"count" => "",
				"count_no_text" => "",
				"graph" => "",
				"graph_no_text" => "",
				"total" => "",
				"total_no_text" => "",			
			),
			"top_contributors" => array (
				"detail" => "",
				"detail_no_text" => "!detail",	
				"count" => "",
				"count_no_text" => "!count",	
				"graph" => "",
				"graph_no_text" => "!graph",
				"total" => "",
				"total_no_text" => "!total",				
			),				
			"top_syndication" => array (
				"detail" => "",
				"detail_no_text" => "!detail",		
				"detail_for_objects" => "detail_for_objects",
				"count" => "",
				"count_no_text" => "!count",
				"count_for_objects" => "count_for_objects",					
				"count_no_text_for_objects" => "count_for_objects",
				"graph" => "!graph_no_text",
				"graph_no_text" => "",
				"total" => "!total_no_text",
				"total_no_text" => "",	
			),
			"system_generic_partner" => array (
				"detail" => "system/generic_partner_detail",
				"detail_no_text" => "!detail",
				"count" => "system/generic_partner_count",
				"count_no_text" => "!count",
			),		
			"system_generic_partner_type" => array (
				"detail" => "system/generic_partner_type_detail",
				"detail_no_text" => "!detail",
				"count" => "system/generic_partner_type_count",
				"count_no_text" => "!count",
			),	
			"admin_console" => array (
				"detail" => "detail",
				"detail_no_text" => "!detail",
				"count" => "count",
				"count_no_text" => "!count",
			),		
		);

		if ( isset ( $map[$type_str] ) )
			$report_type_mapping =  $map[$type_str] ;
		else
			throw new Exception ( "Cannot find mapping for [$type_str]" );
			
		if ( $no_text )
			$flavor_str = $flavor_str . $no_text;
		if ( $for_objects )	
			$flavor_str = $flavor_str . $for_objects;
			
		if ( isset ( $report_type_mapping[$flavor_str ]))
			return $report_type_mapping[$flavor_str];
		else
			return null; 

	}
}
?>