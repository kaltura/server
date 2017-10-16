<?php
class myReportsSqlFileMgr
{
	const NO_TEXT_SUFFIX = "no_text";
	const FOR_OBJECTS_SUFFIX = "for_objects";
	const NO_FILTER_SUFFIX = "without_filter";
	const FOR_OBJECTS_INDEX_SUFFIX = "for_objects_index";

	public static function getSqlFilePath ( $type_str , $flavor_str , $add_search_text , $object_ids, $input_filter )
	{
		$res = self::getSqlFilePathImpl( $type_str , $flavor_str , $add_search_text , $object_ids,  $input_filter );
KalturaLog::log ( __METHOD__. ": [$type_str] [$flavor_str] [$add_search_text] [$object_ids] -> [$res]" );		
		return $res;
	}
	
	private static function getSqlFilePathImpl ( $type_str , $flavor_str , $add_search_text , $object_ids , $input_filter, $recursion_count = 0)
	{		
		$recursion_count++;
		if ( $recursion_count > 5) 
		{
			throw new Exception ("Cannot find config for [$type_str] , [$flavor_str]" ); 
		}
		
		$has_filter = ( $input_filter->keywords != "" || $input_filter->categories != "" || $object_ids || $input_filter->getFilterBy() != "");
		
		$config = self::getFileNameMappingConfig( 
			$type_str , 
			$flavor_str , 
			$add_search_text ? "" : "_" . self::NO_TEXT_SUFFIX , 
			$object_ids ? "_" . self::FOR_OBJECTS_SUFFIX : "",
			$has_filter,
			$input_filter->getFilterBy());
		
		if ( $config === null )
		{
			if ( $object_ids )
			{
				// use entry index in case there are less than 10 entries in the filter, else use the PRIMARY index
				if ($type_str === "top_content" && $flavor_str === "detail" && substr_count($object_ids, ",") < 10) {
					$config = self::getFileNameMappingConfig( 
					$type_str , 
					$flavor_str , 
					$add_search_text ? "" : "_" . self::FOR_OBJECTS_INDEX_SUFFIX , 
					"", 
					$has_filter,
					$input_filter->getFilterBy() );	
				} else {
				// search again without the _object addition
				$config = self::getFileNameMappingConfig( 
					$type_str , 
					$flavor_str , 
					$add_search_text ? "" : "_" . self::NO_TEXT_SUFFIX , 
					"", 
					$has_filter,
					$input_filter->getFilterBy() );
		        }
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
					// use the configuration of same report type but other flavor - use false as $no_text_indicator
					return self::getSqlFilePathImpl ( $type_str , $parts[0] , true , $object_ids , $input_filter, $recursion_count );
				}
				elseif ( count($parts) == 2 )
				{
					// use the configuraiton of some other report_type and other flavor - use false as $no_text_indicator
					return self::getSqlFilePathImpl ( $parts[0] , $parts[1] , true , $object_ids , $input_filter, $recursion_count );
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

	
	private static function getFileNameMappingConfig ( $type_str , $flavor_str , $no_text , $for_objects, $has_filter, $filter_by )
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
				"graph_without_filter" => "graph_and_total_no_filter",
				"total" => "graph_and_total",
				"total_no_text" => "graph_and_total_no_text",
				"total_without_filter" => "graph_and_total_no_filter",
			) ,	
			"content_interactions" => array (	
				"detail" => "",
				"detail_no_text" => "",
				"count" => "",
				"count_no_text" => "",						
				"graph" => "",
				"graph_no_text" => "",
				"graph_without_filter" => "graph_no_filter",
				"total" => "",
				"total_no_text" => "",			
				"total_without_filter" => "total_no_filter",
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
				"detail_for_objects_index" => "detail_for_objects_index",	
				"count" => "",
				"count_no_text" => "",
				"graph" => "",
				"graph_no_text" => "",
				"graph_without_filter" => "graph_no_filter",
				"total" => "",
				"total_no_text" => "",		
				"total_without_filter" => "total_no_filter",
				"detail_by_app" => "detail_by_app",
				"detail_by_context" => "detail_by_context",
				"detail_by_context_by_app" => "detail_by_app",
				"total_by_app" => "total_by_app",
				"total_by_context" => "total_by_context",
				"total_by_context_by_app" => "total_by_app",
				"count_by_app" => "count_by_app",
				"count_by_context" => "count_by_context",
			 	"count_by_context_by_app" => "count_by_app",
				"graph_by_app" => "user_engagement/graph_by_app",
				"graph_by_context" => "user_engagement/graph_by_context",
			 	"graph_by_context_by_app" => "user_engagement/graph_by_context_by_app",
			),
			"top_contributors" => array (
				"detail" => "",
				"detail_by_user" => "detail_by_user",
				"detail_no_text" => "!detail",	
				"count" => "",
				"count_by_user" => "count_by_user",
				"count_no_text" => "!count",	
				"graph" => "",
				"graph_no_text" => "!graph",
				"total" => "",
				"total_no_text" => "!total",				
			),
			"top_creators" => array (
				"detail" => "",
				"detail_by_user" => "detail_by_user",
				"detail_no_text" => "!detail",	
				"count" => "",
				"count_by_user" => "count_by_user",
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
			"partner_bandwidth_usage" => array (
				"detail_no_text" => "",
				"count_no_text" => "",
				"total" => "",
				"total_no_text" => "",
				"graph" => "",
				"graph_no_text" => "!graph",
			),
			"partner_usage_dashboard" => array (
				"detail_no_text" => "",
				"count_no_text" => "",
			),
			"partner_usage" => array (
				"detail_no_text" => "detail_no_text",
				"count_no_text" => "count_no_text",
				"total_no_text" => "total_no_text",
				"graph_no_text" => "graph_no_text",
			),
			"peak_storage" => array (
				"total_no_text" => "total_no_text",
			),
			"var_usage" => array (
				"detail_no_text" => "detail_no_text",
				"count_no_text" => "count_no_text",
				"total_no_text" => "partner_usage/total_no_text",
			),
			
			"user_engagement_unique" => array (
				"total_without_filter" => "user_engagement/unique_total_by_context",
				"total_by_context" => "user_engagement/unique_total_by_context",
				"total_by_context_for_objects" => "user_engagement/unique_total_by_context_for_objects",
				"total_by_app" => "user_engagement/unique_total_by_app",
				"total_by_app_for_objects" => "user_engagement/unique_total_by_app_for_objects",
				"total_by_context_by_app" => "user_engagement/unique_total_by_app",
				"total_by_context_by_app_for_objects" => "user_engagement/unique_total_by_app_for_objects",
				"total_by_user" => "user_engagement/unique_total_by_user",
				"total_by_context_by_user" => "user_engagement/unique_total_by_user",
				"total_by_user_by_app" => "user_engagement/unique_total_by_user_by_app",
				"total_by_context_by_user_by_app" => "user_engagement/unique_total_by_user_by_app",
				"total_no_text" => "user_engagement/unique_total_by_context"
			),
			"user_engagement" => array (
				"graph_no_text" => "graph_no_text",
				"graph_without_filter" => "graph_by_context",
			    "graph_by_context" => "graph_by_context",
			    "graph_by_context_for_objects" => "graph_by_context_for_objects",
				"graph_by_user" => "graph_by_user",
				"graph_by_context_by_user" => "graph_by_user",
				"graph_by_app" => "graph_by_app",
				"graph_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_context_by_app" => "graph_by_app",
			    "graph_by_context_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_user_by_app" => "graph_by_user_by_app",
			    "graph_by_context_by_user_by_app" => "graph_by_user_by_app",
				"total_no_text" => "total_no_text",
				"total_without_filter" => "total_no_filter",
			    "total_by_context" => "total_by_context",
				"total_by_context_for_objects" => "total_by_context_for_objects",
				"total_by_user" => "total_by_user",
			    "total_by_context_by_user" => "total_by_user", 
				"total_by_app" => "total_by_app",
				"total_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_context_by_app" => "total_by_app",
				"total_by_context_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_user_by_app" => "total_by_user_by_app",
				"total_by_context_by_user_by_app" => "total_by_user_by_app",
				"detail_no_text" => "detail_no_text",
				"detail_by_user" => "detail_by_user",
			    "detail_by_context_by_user" => "detail_by_user", 
				"detail_by_app" => "detail_by_app",
			    "detail_by_context_by_app" => "detail_by_app",
				"detail_by_context" => "detail_by_context",
				"detail_by_user_by_app" => "detail_by_user_by_app",	
				"detail_by_context_by_user_by_app" => "detail_by_user_by_app",
				"count_no_text" => "count_no_text",
				"count_by_user" => "count_by_user",
			    "count_by_context_by_user" => "count_by_user", 
				"count_by_app" => "count_by_app",
			    "count_by_context_by_app" => "count_by_app",
				"count_by_context" => "count_by_context",
				"count_by_user_by_app" => "count_by_user_by_app",	
				"count_by_context_by_user_by_app" => "count_by_user_by_app",
			),
			"specific_user_engagement" => array (
				"detail_by_user" => "detail_by_user",
				"detail_by_context_by_user" => "detail_by_user",
				"detail_by_user_by_app" => "detail_by_user_by_app",	
				"detail_by_context_by_user_by_app" => "detail_by_user_by_app",
				"count_by_user" => "count_by_user",
				"count_by_context_by_user" => "count_by_user",
				"count_by_user_by_app" => "count_by_user_by_app",	
				"count_by_context_by_user_by_app" => "count_by_user_by_app",
				"graph_by_user" => "user_engagement/graph_by_user",
				"graph_by_context_by_user" => "user_engagement/graph_by_user",
				"graph_by_user_by_app" => "user_engagement/graph_by_user_by_app",
			    "graph_by_context_by_user_by_app" => "user_engagement/graph_by_user_by_app",
				"total_by_user" => "user_engagement/total_by_user",
			    "total_by_context_by_user" => "user_engagement/total_by_user", 
				"total_by_user_by_app" => "user_engagement/total_by_user_by_app",
				"total_by_context_by_user_by_app" => "user_engagement/total_by_user_by_app",
			),
			"user_top_content" => array (
				"graph_no_text" => "user_engagement/graph_no_text",
				"graph_without_filter" => "user_engagement/graph_by_context",
			    "graph_by_context" => "user_engagement/graph_by_context",
			    "graph_by_context_for_objects" => "user_engagement/graph_by_context_for_objects",
				"graph_by_user" => "user_engagement/graph_by_user",
				"graph_by_context_by_user" => "user_engagement/graph_by_user",
				"graph_by_app" => "user_engagement/graph_by_app",
				"graph_by_app_for_objects" => "user_engagement/graph_by_app_for_objects",
				"graph_by_context_by_app" => "user_engagement/graph_by_app",
			    "graph_by_context_by_app_for_objects" => "user_engagement/graph_by_app_for_objects",
				"graph_by_user_by_app" => "user_engagement/graph_by_user_by_app",
			    "graph_by_context_by_user_by_app" => "user_engagement/graph_by_user_by_app",
				"total_no_text" => "user_engagement/total_no_text",
				"total_without_filter" => "user_engagement/total_by_context",
			    "total_by_context" => "user_engagement/total_by_context",
				"total_by_context_for_objects" => "user_engagement/total_by_context_for_objects",
				"total_by_user" => "user_engagement/total_by_user",
			    "total_by_context_by_user" => "user_engagement/total_by_user", 
				"total_by_app" => "user_engagement/total_by_app",
			    "total_by_app_for_objects" => "user_engagement/total_by_app_for_objects",
				"total_by_context_by_app" => "user_engagement/total_by_app",
				"total_by_context_by_app_for_objects" => "user_engagement/total_by_app_for_objects",
				"total_by_user_by_app" => "user_engagement/total_by_user_by_app",
				"total_by_context_by_user_by_app" => "user_engagement/total_by_user_by_app",
				"detail_no_text" => "user_engagement/detail_no_text",
				"detail_by_user" => "user_engagement/detail_by_user",
			    "detail_by_context_by_user" => "user_engagement/detail_by_user", 
				"detail_by_app" => "user_engagement/detail_by_app",
			    "detail_by_context_by_app" => "user_engagement/detail_by_app",
				"detail_by_context" => "user_engagement/detail_by_context",
				"detail_by_user_by_app" => "user_engagement/detail_by_user_by_app",	
				"detail_by_context_by_user_by_app" => "user_engagement/detail_by_user_by_app",
				"count_no_text" => "user_engagement/count_no_text",
				"count_by_user" => "user_engagement/count_by_user",
			    "count_by_context_by_user" => "user_engagement/count_by_user", 
				"count_by_app" => "user_engagement/count_by_app",
			    "count_by_context_by_app" => "user_engagement/count_by_app",
				"count_by_context" => "user_engagement/count_by_context",
				"count_by_user_by_app" => "user_engagement/count_by_user_by_app",	
				"count_by_context_by_user_by_app" => "user_engagement/count_by_user_by_app",
			),
			"user_content_dropoff" => array (
				"graph_no_text" => "graph_no_text",
				"graph_without_filter" => "graph_by_context",
			    "graph_by_context" => "graph_by_context",
			    "graph_by_context_for_objects" => "graph_by_context_for_objects",
				"graph_by_user" => "graph_by_user",
				"graph_by_context_by_user" => "graph_by_user",
				"graph_by_app" => "graph_by_app",
				"graph_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_context_by_app" => "graph_by_app",
			    "graph_by_context_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_user_by_app" => "graph_by_user_by_app",
			    "graph_by_context_by_user_by_app" => "graph_by_user_by_app",
				"total_no_text" => "total_no_text",
				"total_without_filter" => "total_by_context",
			    "total_by_context" => "total_by_context",
				"total_by_context_for_objects" => "total_by_context_for_objects",
				"total_by_user" => "total_by_user",
			    "total_by_context_by_user" => "total_by_user", 
				"total_by_app" => "total_by_app",
				"total_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_context_by_app" => "total_by_app",
				"total_by_context_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_user_by_app" => "total_by_user_by_app",
				"total_by_context_by_user_by_app" => "total_by_user_by_app",
				"detail_no_text" => "detail_no_text",
				"detail_by_user" => "detail_by_user",
			    "detail_by_context_by_user" => "detail_by_user", 
				"detail_by_app" => "detail_by_app",
			    "detail_by_context_by_app" => "detail_by_app",
				"detail_by_context" => "detail_by_context",
				"detail_by_user_by_app" => "detail_by_user_by_app",	
				"detail_by_context_by_user_by_app" => "detail_by_user_by_app",
				"count_no_text" => "count_no_text",
				"count_by_user" => "count_by_user",
			    "count_by_context_by_user" => "count_by_user", 
				"count_by_app" => "count_by_app",
			    "count_by_context_by_app" => "count_by_app",
				"count_by_context" => "count_by_context",
				"count_by_user_by_app" => "count_by_user_by_app",	
				"count_by_context_by_user_by_app" => "count_by_user_by_app",
			),
			"user_content_dropoff_unique" => array (
				"total_without_filter" => "user_content_dropoff/unique_total_by_context",
				"total_by_context" => "user_content_dropoff/unique_total_by_context",
				"total_by_context_for_objects" => "user_content_dropoff/unique_total_by_context_for_objects",
				"total_by_app" => "user_content_dropoff/unique_total_by_app",
				"total_by_app_for_objects" => "user_content_dropoff/unique_total_by_app_for_objects",
				"total_by_context_by_app" => "user_content_dropoff/unique_total_by_app",
				"total_by_context_by_app_for_objects" => "user_content_dropoff/unique_total_by_app_for_objects",
				"total_by_user" => "user_content_dropoff/unique_total_by_user",
				"total_by_context_by_user" => "user_content_dropoff/unique_total_by_user",
				"total_by_user_by_app" => "user_content_dropoff/unique_total_by_user_by_app",
				"total_by_context_by_user_by_app" => "user_content_dropoff/unique_total_by_user_by_app",
				"total_no_text" => "user_content_dropoff/unique_total_by_context"
			),
			"user_content_interactions" => array (
				"graph_no_text" => "graph_no_text",
				"graph_without_filter" => "graph_by_context",
			    "graph_by_context" => "graph_by_context",
			    "graph_by_context_for_objects" => "graph_by_context_for_objects",
				"graph_by_user" => "graph_by_user",
				"graph_by_context_by_user" => "graph_by_user",
				"graph_by_app" => "graph_by_app",
				"graph_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_context_by_app" => "graph_by_app",
			    "graph_by_context_by_app_for_objects" => "graph_by_app_for_objects",
				"graph_by_user_by_app" => "graph_by_user_by_app",
			    "graph_by_context_by_user_by_app" => "graph_by_user_by_app",
				"total_no_text" => "total_no_text",
				"total_without_filter" => "total_by_context",
			    "total_by_context" => "total_by_context",
				"total_by_context_for_objects" => "total_by_context_for_objects",
				"total_by_user" => "total_by_user",
			    "total_by_context_by_user" => "total_by_user", 
				"total_by_app" => "total_by_app",
				"total_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_context_by_app" => "total_by_app",
				"total_by_context_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_user_by_app" => "total_by_user_by_app",
				"total_by_context_by_user_by_app" => "total_by_user_by_app",
				"detail_no_text" => "detail_no_text",
				"detail_by_user" => "detail_by_user",
			    "detail_by_context_by_user" => "detail_by_user", 
				"detail_by_app" => "detail_by_app",
			    "detail_by_context_by_app" => "detail_by_app",
				"detail_by_context" => "detail_by_context",
				"detail_by_user_by_app" => "detail_by_user_by_app",	
				"detail_by_context_by_user_by_app" => "detail_by_user_by_app",	
				"count_no_text" => "count_no_text",
				"count_by_user" => "count_by_user",
			    "count_by_context_by_user" => "count_by_user", 
				"count_by_app" => "count_by_app",
			    "count_by_context_by_app" => "count_by_app",
				"count_by_context" => "count_by_context",
				"count_by_user_by_app" => "count_by_user_by_app",	
				"count_by_context_by_user_by_app" => "count_by_user_by_app",
			),
			"user_content_interactions_unique" => array (
				"total_without_filter" => "user_content_interactions/unique_total_by_context",
				"total_by_context" => "user_content_interactions/unique_total_by_context",
				"total_by_context_for_objects" => "user_content_interactions/unique_total_by_context_for_objects",
				"total_by_app" => "user_content_interactions/unique_total_by_app",
				"total_by_app_for_objects" => "user_content_interactions/unique_total_by_app_for_objects",
				"total_by_context_by_app" => "user_content_interactions/unique_total_by_app",
				"total_by_context_by_app_for_objects" => "user_content_interactions/unique_total_by_app_for_objects",
				"total_by_user" => "user_content_interactions/unique_total_by_user",
				"total_by_context_by_user" => "user_content_interactions/unique_total_by_user",
				"total_by_user_by_app" => "user_content_interactions/unique_total_by_user_by_app",
				"total_by_context_by_user_by_app" => "user_content_interactions/unique_total_by_user_by_app",
				"total_no_text" => "user_content_interactions/unique_total_by_context"
			),
			"applications" => array (
				"detail_without_filter" => "detail_no_filter",
				"count_without_filter" => "count_no_filter"
			),
			"user_usage" => array (
				"graph_without_filter" => "graph_no_filter",
				"graph_by_user" => "graph_by_user",
				"total_without_filter" => "total_no_filter",
			    "total_by_user" => "total_by_user",
				"detail_without_filter" => "detail_no_filter",
				"detail_by_user" => "detail_by_user",
				"count_without_filter" => "count_no_filter",
				"count_by_user" => "count_by_user",
				"base_total_without_filter" => "base_total_no_filter",
				"base_total_by_user" => "base_total_by_user"
			),
			"specific_user_usage" => array (
				"graph_by_user" => "user_usage/graph_by_user",
			    "total_by_user" => "user_usage/total_by_user",
				"detail_by_user" => "detail_by_user",
				"count_by_user" => "count_by_user",
				"base_total_by_user" => "user_usage/base_total_by_user"
			),
			"platforms" => array (
				"graph" => "graph",
				"graph_without_filter" => "graph",
				"graph_no_text_for_objects" => "graph_for_objects",	
				"graph_by_app" => "graph_by_app",
				"graph_by_app_for_objects" => "graph_by_app_for_objects",
				"total" => "total",
				"total_without_filter" => "total",
				"total_by_app" => "total_by_app",
				"total_no_text" => "total",
				"detail" => "detail",
				"detail_without_filter" => "detail",
				"detail_no_text_for_objects" => "detail_for_objects",	
				"detail_by_app" => "detail_by_app",
				"detail_by_app_for_objects" => "detail_by_app_for_objects",
				"count" => "count",
				"count_without_filter" => "count",
				"count_no_text_for_objects" => "count_for_objects",	
				"count_by_app" => "count_by_app",
				"count_by_app_for_objects" => "count_by_app_for_objects",
			),
			"os" => array (
				"graph" => "graph",
				"graph_without_filter" => "graph",
				"graph_by_app" => "graph_by_app",
				"total" => "total",
				"total_without_filter" => "total",
				"total_by_app" => "total_by_app",
				"detail" => "detail",
				"detail_without_filter" => "detail",
				"detail_by_app" => "detail_by_app",
				"count" => "count",
				"count_without_filter" => "count",
				"count_by_app" => "count_by_app",
			),
			"browsers" => array (
				"graph" => "graph",
				"graph_without_filter" => "graph",
				"graph_by_app" => "graph_by_app",
				"total" => "total",
				"total_without_filter" => "total",
				"total_by_app" => "total_by_app",
				"detail" => "detail",
				"detail_without_filter" => "detail",
				"detail_by_app" => "detail_by_app",
				"count" => "count",
				"count_without_filter" => "count",
				"count_by_app" => "count_by_app",
			),
			"live" => array (
				"graph" => "graph",
				"graph_without_filter" => "graph",
				"graph_no_text" => "graph",
				"total" => "total",
				"total_without_filter" => "total",
				"total_no_text" => "total",
				"detail" => "detail",
				"detail_without_filter" => "detail",
				"detail_no_text" => "detail",
				"count" => "count",
				"count_without_filter" => "count",
				"count_no_text" => "count",
			),
			"top_playback_context" => array (
				"graph_no_text" => "user_engagement/graph_no_text",
				"graph_without_filter" => "user_engagement/graph_by_context",
			    "graph_by_context" => "user_engagement/graph_by_context",
			    "graph_by_context_for_objects" => "user_engagement/graph_by_context_for_objects",
				"graph_by_context_by_user" => "user_engagement/graph_by_user",
				"graph_by_context_by_user_for_objects" => "user_engagement/graph_by_user",
				"graph_by_context_by_app" => "user_engagement/graph_by_app",
				"graph_by_context_by_app_for_objects" => "user_engagement/graph_by_app_for_objects",
				"graph_by_context_by_user_by_app" => "user_engagement/graph_by_user_by_app",
				"graph_by_context_by_user_by_app_for_objects" => "user_engagement/graph_by_user_by_app",
				"graph_by_user" => "user_engagement/graph_by_user",
				"graph_by_user_for_objects" => "user_engagement/graph_by_user",
				"graph_by_user_by_app" => "user_engagement/graph_by_user_by_app",
				"graph_by_user_by_app_for_objects" => "user_engagement/graph_by_user_by_app",
				"graph_by_app" => "user_engagement/graph_by_app",
				"graph_by_app_for_objects" => "user_engagement/graph_by_app_for_objects",
				"total_no_text" => "user_engagement/total_no_text",
				"total_without_filter" => "user_engagement/total_by_context",
			    "total_by_context" => "user_engagement/total_by_context",
			    "total_by_context_for_objects" => "total_by_context_for_objects",
				"total_by_context_by_user" => "total_by_user",
				"total_by_context_by_user_for_objects" => "total_by_user",
				"total_by_context_by_app" => "user_engagement/total_by_app",
				"total_by_context_by_app_for_objects" => "total_by_app_for_objects",
				"total_by_context_by_user_by_app" => "total_by_user_by_app",
				"total_by_context_by_user_by_app_for_objects" => "total_by_user_by_app",
				"total_by_user" => "total_by_user",
				"total_by_user_for_objects" => "total_by_user",
				"total_by_user_by_app" => "total_by_user_by_app",
				"total_by_user_by_app_for_objects" => "total_by_user_by_app",
				"total_by_app" => "user_engagement/total_by_app",
				"total_by_app_for_objects" => "total_by_app_for_objects",
				"detail_no_text" => "detail_no_text",
				"detail_without_filter" => "detail_by_context",
			    "detail_by_context" => "detail_by_context",
			    "detail_by_context_for_objects" => "detail_by_context_for_objects",
				"detail_by_context_by_user" => "detail_by_user",
				"detail_by_context_by_user_for_objects" => "detail_by_user",
				"detail_by_context_by_app" => "detail_by_app",
				"detail_by_context_by_app_for_objects" => "detail_by_app_for_objects",
				"detail_by_context_by_user_by_app" => "detail_by_user_by_app",
				"detail_by_context_by_user_by_app_for_objects" => "detail_by_user_by_app",
				"detail_by_user" => "detail_by_user",
				"detail_by_user_for_objects" => "detail_by_user",
				"detail_by_user_by_app" => "detail_by_user_by_app",
				"detail_by_user_by_app_for_objects" => "detail_by_user_by_app",
				"detail_by_app" => "detail_by_app",
				"detail_by_app_for_objects" => "detail_by_app_for_objects",
				
				"count_no_text" => "count_no_text",
				"count_without_filter" => "count_by_context",
			    "count_by_context" => "count_by_context",
			    "count_by_context_for_objects" => "count_by_context_for_objects",
				"count_by_context_by_user" => "count_by_user",
				"count_by_context_by_user_for_objects" => "count_by_user",
				"count_by_context_by_app" => "count_by_app",
				"count_by_context_by_app_for_objects" => "count_by_app_for_objects",
				"count_by_context_by_user_by_app" => "count_by_user_by_app",
				"count_by_context_by_user_by_app_for_objects" => "count_by_user_by_app",
				"count_by_user" => "count_by_user",
				"count_by_user_for_objects" => "count_by_user",
				"count_by_user_by_app" => "count_by_user_by_app",
				"count_by_user_by_app_for_objects" => "count_by_user_by_app",
				"count_by_app" => "count_by_app",
				"count_by_app_for_objects" => "count_by_app_for_objects",
			),
			"vpaas_usage" => array (
				"detail_no_text" => "detail_no_text",
				"count_no_text" => "count_no_text",
			),
			
		);

		if ( isset ( $map[$type_str] ) )
			$report_type_mapping =  $map[$type_str] ;
		else
			throw new Exception ( "Cannot find mapping for [$type_str]" );
			
		if (!($has_filter) && isset($report_type_mapping[$flavor_str . "_" . self::NO_FILTER_SUFFIX])) {
			return $report_type_mapping[$flavor_str . "_" . self::NO_FILTER_SUFFIX];					
		}
		
		if ($filter_by) {
			$flavor_str = $flavor_str . $filter_by;
		} else {
			if ( $no_text )
				$flavor_str = $flavor_str . $no_text;
		}
		if ( $for_objects )	
				$flavor_str = $flavor_str . $for_objects;
		
		
		if ( isset ( $report_type_mapping[$flavor_str ]))
			return $report_type_mapping[$flavor_str];
		else
			return null; 

	}
}
?>
