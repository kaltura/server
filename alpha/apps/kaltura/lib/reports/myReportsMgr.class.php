<?php
class myReportsMgr
{
	const REPORT_FLAVOR_GRAPH= 1;
	const REPORT_FLAVOR_TOTAL= 2;
	const REPORT_FLAVOR_TABLE= 3;
	const REPORT_FLAVOR_COUNT = 4;
	
	const REPORT_TYPE_TOP_CONTENT = 1;
	const REPORT_TYPE_CONTENT_DROPOFF = 2;
	const REPORT_TYPE_CONTENT_INTERACTIONS = 3;
	const REPORT_TYPE_MAP_OVERLAY = 4;
	const REPORT_TYPE_TOP_CONTRIBUTORS = 5;
	const REPORT_TYPE_TOP_SYNDICATION = 6;
	const REPORT_TYPE_CONTENT_CONTRIBUTIONS = 7;
	const REPORT_TYPE_ADMIN_CONSOLE = 10;
	const REPORT_TYPE_SYSTEM_GENERIC_PARTNER = 100;
	const REPORT_TYPE_SYSTEM_GENERIC_PARTNER_TYPE = 101;

	const REPORTS_COUNT_CACHE = 60;
	

	public static function runQuery ( $query_file , $map , $debug = false )
	{
		if ( strpos ($query_file,".") === 0 || strpos ($query_file,"/") === 0 || strpos ($query_file,"http") === 0 )
		{
			die ( "Will not search for invalid report_type [$query_file" );
		}
		$file_path = dirname(__FILE__)."/". $query_file . ".sql";
		
		$sql_raw_content = file_get_contents( $file_path );
		if ( ! $sql_raw_content )
		{
			die ( "Cannot find sql for [$query_file] at [$file_path]" );
		}	

		// replace all params in $sql_raw_content with map

		foreach ( $map as $name => $value )
		{
			$sql_raw_content = str_replace ( "{" . strtoupper( $name ) . "}" , $value , $sql_raw_content );
		}
		
		$query = $sql_raw_content;
		
		$header = null;
		
		if ( !$debug )
		{
			$res = self::executeQuery ( $query );	
			if ( $res )
			{
				$row = $res[0];
				$header = array();
				foreach ( $row as $name => $value )
				{
					$header[]= $name;
					$data[] = $value;
				}				
			}
		}
		else
		{
			$res = null;
		}
		
		return array ( $query , $res , $header );
	}
	
	
	public static function getGraph ( $partner_id , $report_type , reportsInputFilter $input_filter , $dimension = null , $object_ids = null )
	{
		$start = microtime(true);
//		$partner_id = "1";
		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_GRAPH , $input_filter , null , null , null , $object_ids );
//print_r ( $result );		
		//		plays.event_date_id,count_plays,distinct_plays,sum_time_viewed,avg_time_viewed,count_loads

		// assume each row has the same number of columns

		if ( $report_type == self::REPORT_TYPE_CONTENT_DROPOFF )
		{
			$res = self::getGraphsByColumnName ( $result , $report_type);
		}
		else
		{
			$res = self::getGraphsByDateId ( $result , $report_type );
		}
		
		$end = microtime(true);
		KalturaLog::log( "getGraph took [" . ( $end - $start ) . "]" );
		
		return $res;
	}
	
	
	private static function getGraphsByDateId ( $result , $report_type )
	{
		$graphs = array();
		$should_create_graphs = true;		
		foreach ( $result as $row )
		{
			$row_size = count($row);
//print_r ( $row );			
			if ( $should_create_graphs )
			{
				$first = true;
				foreach ( $row as $column => $val )
				{
					if ( $first )
					{
						$first = false;
						continue;
					}
					$graphs[$column] = array();
				}
				$should_create_graphs = false;
			}
			// index 0 is always the date
			// the rest of the indexes are the dimensions
			$first = true;
			foreach ( $row as $column => $val )
			{
				if ( $first )
				{
					$date = $val;
/*	no formatting should be done on the server side
 * 				if ( $val )
					{
						$date = self::formatDateFromDateId ( $val );
					}
	*/				
					$first = false;
				}
				else
				{
					$graph = $graphs[$column];
					$graph[$date] = $val; // the value for graph 1 will be column #1 in the row 
					$graphs[$column] = $graph;
				}
			}
		}
//echo "<br>";		
//print_r ( $graphs );
//die();		
		return $graphs;		
	}
	
	
	private static function getGraphsByColumnName ( $result , $report_type )
	{
		$graphs = array();
		$should_create_graphs = true;	
		$graph = array();
 	
		foreach ( $result as $row )
		{
			foreach ( $row as $column => $val )
			{
				$graph[$column] = $val;
			}
		}
		
		$type_str = self::$type_map[$report_type];
		$graphs[$type_str] = $graph;
		return $graphs;		
	}	
	
	
	
	/**
	 * Will return 2 arrays 
	 * 	headers
	 * 	data
	 *
	 * @param unknown_type $partner_id
	 * @param unknown_type $report_type
	 * @param reportsInputFilter $input_filter
	 * @return unknown
	 */
	public static function getTotal ( $partner_id , $report_type , reportsInputFilter $input_filter , $object_ids = null  )
	{
		$start = microtime ( true );
//		$partner_id = "23456";
		
		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_TOTAL , $input_filter , null , null , null , $object_ids );
		if ( count($result) > 0 )
		{
			$row = $result[0];
			$header = array();
			$data = array();
			foreach ( $row as $name => $value )
			{
				$header[]= $name;
				$data[] = $value;
			}
			$res = array ( $header , $data );
		}
		else
		{
//			return $result[0]; // for total - there is only a single record
			$res = array ( null , null );
		}	
		$end = microtime(true);
		KalturaLog::log( "getTotal took [" . ( $end - $start ) . "]" );
		
		return $res;
	}
	
	
	public static function getTable ( $partner_id , $report_type , reportsInputFilter $input_filter  ,
		$page_size , $page_index , $order_by , $object_ids = null )
	{
		$start = microtime ( true );
		$total_count = self::getTotalTableCount( $partner_id , $report_type , $input_filter  ,
			$page_size , $page_index , $order_by , $object_ids );
			
		if ( ! $page_size || $page_size < 0 ) $page_size = 10;
		if ( ! $page_index || $page_index < 0 ) $page_index = 0;
		
		if ( $total_count <= 0 )
		{
			$end = microtime(true);
			KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );			
			return array ( null , null , 0 );
		}
		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_TABLE , $input_filter ,$page_size , $page_index , $order_by , $object_ids );

		if ( count($result) > 0 )
		{
			$row = $result[0];
			$header = array();
			$data = array();
			$first = true;
			foreach ( $result as $row )
			{
				$rowData = array();
				foreach ( $row as $name => $value )
				{
					if ( $first )
					{
						$header[]= $name;
					}
					$rowData[] = $value;
				}
				
				$data[] = $rowData;//= $value;				
				$first = false;				
			}
//print_r ( $header );
//die();			
			$res = array ( $header , $data , $total_count );
		}
		else
		{
			$res =  array ( null , null , 0 );
		}
		
		$end = microtime(true);
		KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );

		return $res;
	}

	
	
	/**
	 * will store the content of the report on disk and return the Url for the file
	 *
	 * @param string $partner_id
	 * @param string $report_title
	 * @param string $report_text
	 * @param string $headers
	 * @param int $report_type
	 * @param reportsInputFilter $input_filter
	 * @param string  $dimension
	 * @param string $object_ids
	 * @param int $page_size
	 * @param int $page_index
	 * @param string $order_by
	 */
	public static function getUrlForReportAsCsv ( $partner_id , 
			$report_title , $report_text , $headers , 
			$report_type , 
			reportsInputFilter $input_filter , 
			$dimension = null , 
			$object_ids = null ,
			$page_size =10, $page_index =0, $order_by )
	{
		// create file_name
		// TODO - check if file already exists - if so - serve it if not expired
		
		list ( $file_name , $url ) = self::createFileName ( $partner_id , $report_type , $input_filter , $dimension , $object_ids ,$page_size , $page_index , $order_by );
		// TODO - remove comment and read from disk
/*		
		if ( file_exists ( $file_name ) )
		{
			return $url;
		}
	*/	
		$arr = self::getGraph( $partner_id , 
			$report_type , 
			$input_filter ,
			$dimension , 
			$object_ids );

		list ( $total_header , $total_data ) = self::getTotal( $partner_id , 
			$report_type , 
			$input_filter , $object_ids );			
		
		list ( $table_header , $table_data , $table_total_count ) = self::getTable( $partner_id , 
			$report_type , 
			$input_filter ,
			$page_size , $page_index ,
			$order_by ,  $object_ids );		

		$data = myCsvReport::createReport( $report_title , $report_text , $headers ,
			$report_type , $input_filter , $dimension , 
			$arr , $total_header , $total_data , $table_header , $table_data , $table_total_count);
		
		// return URL
		if ( ! file_exists (dirname ( $file_name ) ))
			@mkdir( dirname ( $file_name ) , 0777 );
		file_put_contents( $file_name , $data );
		
		return $url;
	}

// -------------------------------------------- private -----------------------------------------------// 	

	private static function createFileName ( $partner_id )
	{
		$args = func_get_args();
		$file_name = "";
		foreach ( $args as $arg )
		{
//			if ( $file_name ) $file_name .= "_";
			if ( $arg instanceof reportsInputFilter )
				$file_name .= $arg->toShortString();
			else 
				$file_name .= "{$arg}";
		}
		$time_suffix = date ( "Y-m-D-H" , ((int)(time() / 43200))*  43200 ) ; // calculate for intervlas of half days (86400/2)  
		
		$path = "/content/reports/$partner_id/{$file_name}_{$time_suffix}";
		$file_path = myContentStorage::getFSContentRootPath() .  $path;
		$url = requestUtils::getHost() . $path;
		return array ( $file_path , $url );
	}
	/**
	 * @var myCache
	 */
	private static $count_cache ;
	private static function getTotalTableCount( $partner_id , $report_type , reportsInputFilter $input_filter  ,
		$page_size , $page_index , $order_by , $object_ids = null )
	{
		$cache_key = self::createCacheKey ( $partner_id , $report_type , $input_filter , $object_ids );
		if ( ! self::$count_cache )
		{
			self::$count_cache = new myCache( "reportscount" , self::REPORTS_COUNT_CACHE ); // store the cache for 
		}
		
		$total_count = self::$count_cache->get( $cache_key );
		if ( $total_count )
		{
			KalturaLog::log( "count from cache: [$total_count]" );
			return $total_count;
		}
		
		$total_count_arr = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_COUNT , $input_filter ,null , null , null , $object_ids );
		if ( $total_count_arr && isset ($total_count_arr[0]["count_all"] ) )
		{
			$total_count = $total_count_arr[0]["count_all"];
		}
		else
		{
			$total_count = 0;
		}
	KalturaLog::log( "count: [$total_count]" );
	
		self::$count_cache->put( $cache_key , $total_count ); // store in the cache for next time
		return $total_count;				
	}
		
	private static function createCacheKey ( $partner_id , $report_type , reportsInputFilter  $input_filter , $object_ids )
	{
		if ( strlen( $partner_id ) > 40 )
			$partner_id_str = md5($partner_id);
		else
			$partner_id_str = $partner_id;
		return $partner_id_str . "|" . $report_type . "|" . 
			$input_filter->from_date . $input_filter->to_date . $input_filter->keywords . $input_filter->search_in_admin_tags . $input_filter->search_in_tags .
			$object_ids;
	}
	
	public static function formatDateFromDateId ( $val )
	{
		// the $val is the date_id -> YYYYMMDD
		//$date = $val;
		$y = (int)substr ( $val , 0 , 4 );
		$m = (int)substr ( $val , 4 , 2 );
		$d = (int)substr ( $val , 6 , 2 );
		
		$date = mktime  ( 0,0,0 , $m , $d , $y  ) ;	
		return $date;	
	}
	
	private static function executeQueryByType ( $partner_id , $report_type , $report_flavor , reportsInputFilter $input_filter  ,
		$page_size , $page_index , $order_by , $object_ids = null )
	{
		$start = microtime(true);
		try
		{
	//		require_once ( dirname(__FILE__)  . "/StubReports.php" );
	//		return StubReports::STUBexecuteQueryByType ( $partner_id , $report_type , $report_flavor , $input_filter  ,		$page_size , $page_index , $order_by );
			
			// if the keywords or the categories are not empty - use the text version of the query
			$add_search_text = ( $input_filter->keywords != "" || $input_filter->categories != "");
			
			if ( is_numeric( $report_type ))
			{
				$file_path = myReportsSqlFileMgr::getSqlFilePath( 
					self::$type_map[$report_type] ,  
					self::$flavor_map[$report_flavor] , 
					$add_search_text , 
					$object_ids ? true : false );
			}
			else
			{
				if ( strpos ($report_type,".") === 0 || strpos ($report_type,"/") === 0 || strpos ($report_type,"http") === 0 )
				{
					die ( "Will not search for invalid report_type [$report_type" );
				}
				$file_path = dirname(__FILE__)."/". $report_type . ".sql";
			}
			
			$sql_raw_content = file_get_contents( $file_path );
			if ( ! $sql_raw_content )
			{
				die ( "Cannot find sql for [$report_type] [$report_flavor] at [$file_path]" );
			}
			
			if ( $object_ids )
			{
				//the object ids are not supposed to include single quotes - if they do hhave them - escape them
				$object_ids = str_replace ( "'" , "\'" , $object_ids ) ; 
				// quote all the objects with SINGLE-QUOTES			
				$object_ids_str = "'" . str_replace ( "," , "','" , $object_ids ) . "'";
	
				if ( $report_type == self::REPORT_TYPE_CONTENT_CONTRIBUTIONS )
				{
					$obj_ids_clause = "entry_media_source_id in ( $object_ids_str)";
				}
				else if ( $report_type == self::REPORT_TYPE_TOP_SYNDICATION )
				{
					$obj_ids_clause = "ev.domain_id in ( $object_ids_str)";
				}
				else if ( $report_type == self::REPORT_TYPE_MAP_OVERLAY )
				{
					$obj_ids_clause = "ev.country_id in ( $object_ids_str)";
				}	
				else if ( $report_type == self::REPORT_TYPE_ADMIN_CONSOLE )
				{
					$obj_ids_clause = "dim_partner.partner_id in ( $object_ids_str)";
				}		
				else
				{
					$obj_ids_clause = "ev.entry_id in ( $object_ids_str )";
				}
			}
			else
			{
				$obj_ids_clause = null;
			}
			
			if ( is_numeric( $report_type ))
				$order_by = self::getOrderBy( self::$type_map[$report_type] , $order_by );
			
			$query = self::getReplacedSql( $sql_raw_content , $partner_id , $input_filter , $page_size , $page_index , $order_by , $obj_ids_clause );
			if ( is_numeric( $report_type ))
				$query_header = "/* -- " . self::$type_map[$report_type] . " " . self::$flavor_map[$report_flavor] . " -- */\n";
			else 
				$query_header = "/* -- " . $report_type . " -- */\n";
			KalturaLog::log( "\n{$query_header}{$query}" );
			
			$res = self::executeQuery ( $query );
			
			$end = microtime(true);
			KalturaLog::log( "Query took [" . ( $end - $start ) . "]" );
			return $res;
		}
		catch ( Exception $ex )
		{
			KalturaLog::log( $ex->getMessage() );
			// TODO - write proeper error
			throw new Exception ( "Error while processing report for [$partner_id , $report_type , $report_flavor]" );
		}
	}
		
	private static $flavor_map = array ( 
		self::REPORT_FLAVOR_GRAPH => "graph" ,
		self::REPORT_FLAVOR_TOTAL => "total" ,
		self::REPORT_FLAVOR_TABLE => "detail" ,
		self::REPORT_FLAVOR_COUNT => "count" , 
	);
	
	private static $type_map = array ( 
		self::REPORT_TYPE_TOP_CONTENT => "top_content" ,
		self::REPORT_TYPE_CONTENT_DROPOFF => "content_dropoff" ,
		self::REPORT_TYPE_CONTENT_INTERACTIONS => "content_interactions" ,
		self::REPORT_TYPE_MAP_OVERLAY => "map_overlay" ,
		self::REPORT_TYPE_TOP_CONTRIBUTORS => "top_contributors" ,
		self::REPORT_TYPE_TOP_SYNDICATION => "top_syndication" ,
		self::REPORT_TYPE_CONTENT_CONTRIBUTIONS => "content_contributions" ,
		self::REPORT_TYPE_ADMIN_CONSOLE => "admin_console" ,
		self::REPORT_TYPE_SYSTEM_GENERIC_PARTNER => "system_generic_partner" ,
		self::REPORT_TYPE_SYSTEM_GENERIC_PARTNER_TYPE => "system_generic_partner_type" ,
		
	);
	
	
	/*
	 * Will map what fields can be part of the ORDER BY clause
	 */
	private static function getOrderBy ( $report_type , $order_by )
	{
		if ( ! $order_by ) return null;
		
		$map = array (
			"content_contributions" => array (
#				"entry_media_source_name" => "es.entry_media_source_name" ,
				"count_total" ,
				"count_video" ,
				"count_audio" ,
				"count_image" ,
				"count_mix" ,
				"count_ugc" ,
				"count_admin" ,
			) ,
			"content_dropoff" => array (
#				"entry_name" => "en.entry_name",
				"count_plays" ,	
				"count_plays_25" ,
				"count_plays_50" ,
				"count_plays_75" ,
				"count_plays_100" ,
				"play_through_ratio" ,
			) ,	
			"content_interactions" => array (	
#				"entry_name" => "en.entry_name",
				"count_plays" ,	
				"count_edit" ,
				"count_viral" ,
				"count_download" ,
				"count_report" ,
			),	
			"map_overlay" => array (
#				"entry_name" => "en.entry_name",		
				"country",
				"location_name",		
				"count_plays" ,	
				"count_plays_25" ,
				"count_plays_50" ,
				"count_plays_75" ,
				"count_plays_100" ,
				"play_through_ratio" ,	
			),
			"top_content" => array (	
#				"entry_name" => "en.entry_name",			
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"load_play_ratio" ,	
			),
			"top_contributors" => array (
#				"screen_name",			
				"count_total" ,	
				"count_video" ,
				"count_audio" ,
				"count_image" ,
				"count_mix" ,
			),				
			"top_syndication" => array (
#				"domain_name" => "dom.domain_name",			
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"load_play_ratio" ,	
			),
		);
			
		if ( $order_by[0] == '-' )
		{
			$order_by_field =  substr($order_by,1);
			$order_by_dir = "DESC";
		}
		elseif ( $order_by[0] == '+' )
		{
			$order_by_field =  substr($order_by,1);
			$order_by_dir = "ASC";
		}
		else
		{
			$order_by_field =  $order_by;
			$order_by_dir = "DESC";
		}
		
		// if the order by is not explicitly allowed - don't allow it !
		
		$valid_field  = false;
		
		if ( isset ( $map[$report_type] ) )
		{
			$section = $map[$report_type];

			if ( in_array ( $order_by_field , $section ) )
			{
				$order_by_str = "$order_by_field $order_by_dir";
				$valid_field = true;
			}
		}
		
		
		if ( ! $valid_field )
		{
			$order_by_str = "1=1 /* [$report_type][$valid_field]: BAD order field [" . 
				str_replace ( array ( "/" , "*" ) , array ( "" , "" ) , $order_by_field ) . 
				"] */";  
		}
		
		return $order_by_str;
	}
	
	private static function getReplacedSql ( $sql_content , $partner_id , reportsInputFilter $input_filter , 
		$page_size , $page_index  , $order_by , $obj_ids_clause = null )
	{
		// TODO - format the search_text according to the the $input_filter
		$search_text_match_clause = self::setSearchFieldsAndText ( $input_filter );
		
		$categories_match_clause = self::setCategoriesMatchClause( $input_filter );
		
		$pagination_first = ( $page_index - 1 ) * $page_size;
		if ( $pagination_first < 0 ) $pagination_first = 0;
		
		if ( $order_by )
		{
			$order_by_str = $order_by;
		}
		else
		{
			$order_by_str = "1=1";
		}

// TODO - remove when timezone is correct on the client's side
date_default_timezone_set ('UTC' );
		
		if(!preg_match('/^[0-9_a-z,]+$/', $obj_ids_clause))
			$obj_ids_clause = null;
			
		$obj_ids_str = $obj_ids_clause ? $obj_ids_clause : "1=1";
		
// TODO - remove ! nasty hack until client will suply rounded dates that don't depend on the timezone  
		$delta_in_seconds = $input_filter->to_date - $input_filter->from_date;
		$input_filter->from_date = floor($input_filter->from_date/86400)*86400;  // round down the from_date to the beginning of the day
		$input_filter->to_date = $input_filter->from_date + $delta_in_seconds;	 // add the delta to the to_date
		
		$names = 
			array ( 
				"{PARTNER_ID}" , 
				"{FROM_TIME}" ,
				"{TO_TIME}" , 
				"{FROM_DATE_ID}" ,			// added for aggregation SQLs
				"{TO_DATE_ID}" ,			// added for aggregation SQLs
				"{TIME_SLOT_7}" ,
				"{TIME_SLOT_30}" ,
				"{TIME_SLOT_180}" ,
				"{SEARCH_TEXT_MATCH}" ,
				"{SORT_FIELD}" , 
				"{PAGINATION_FIRST}" ,
				"{PAGINATION_SIZE}" ,
				"{OBJ_ID_CLAUSE}" , 
				"{CATEGORIES_MATCH}" , );
		$values = 
			array (
				$partner_id ,
				self::intToDateTime($input_filter->from_date), 
				self::intToDateTime($input_filter->to_date ),
				self::intToDateId($input_filter->from_date), 
				self::intToDateId($input_filter->to_date ),
				self::intToDateId($input_filter->to_date , -7 ),
				self::intToDateId($input_filter->to_date , -30 ),
				self::intToDateId($input_filter->to_date , -180 ),
				$search_text_match_clause ,
				$order_by_str ,
				$pagination_first ,
				$page_size ,
				$obj_ids_str , 
				$categories_match_clause);
				
		if ( $input_filter->extra_map )
		{
			foreach ( $input_filter->extra_map as $name => $value  )	
			{
				$names[] = $name;
				$values[] = $value;				
			}
		}
		$replaced_sql = str_replace ( $names , $values , $sql_content );	
			
		return $replaced_sql;
	}
	
	private static function executeQuery ( $query )
	{
		$db_config = kConf::get( "reports_db_config" );
		$timeout = isset ( $db_config["timeout"] ) ? $db_config["timeout"] : 40;
		
		ini_set('mysql.connect_timeout', $timeout );
		$host = $db_config["host"];
		if ( isset ( $db_config["port"] ) && $db_config["port"] ) $host .= ":" . $db_config["port"];
		$link  = mysql_connect ( $host , $db_config["user"] , $db_config["password"] , null );
			
KalturaLog::log( "Reports query using database host: [$host] user [" . $db_config["user"] . "]" );
		
		$db_selected =  mysql_select_db ( $db_config["db_name"] , $link );
		
		if (!$db_selected) {
    		die ('Can\'t use foo : ' . mysql_error());
		}

		$result = mysql_query($query);
		
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) 
		{
		
		    $message  = 'Invalid query: ' . mysql_error() . "\n";
		    $message .= 'Whole query: ' . $query;
		    die($message);
		}
			
		$res = array();
	
		while ($row = mysql_fetch_assoc($result)) 
		{			
			$res[] = $row;
		}
		
		mysql_free_result($result);
		mysql_close($link);
		
		return $res;
	}
	
	/**
	 * Will set the fields in which to search and format the saerch string
	 * @param $input_filter
	 * @return array ( string , string )(
	 */
	private static function setSearchFieldsAndText ( reportsInputFilter $input_filter )
	{
		// TODO - should enforce the maximum length of 31 characters ?
		
		// remove leading and tailing spaces and set to lower case
		$search_text =  strtolower( trim($input_filter->keywords ) );
		
		if ( empty ( $search_text ) )
		{
			// no search caluse
			return "1=1";
		}
		// escape the ' character to prevent SQL injection
		$search_text = str_replace( "'" , "\'" , $search_text );
		$quote  = strpos ( $search_text , "\""  );
		if ( strpos ( $search_text , '"'  ) === false )
		{
			if ( strpos ( $search_text , ' or ' ) > 0 )
			{
				// in OR mode - take the words literally - it will cause the full text search to allow one or more of the keywords to appear
				$search_text = str_replace( " or " , " " , $search_text);
			}
			else
			{
				// use AND mode - have the '+' character before each word
				$word_arr = explode ( " " , $search_text );
				$search_text = "+" . implode ( " +" , $word_arr );
			}
		}
		else
		{
			// leave $search_text as is - the user is attempting to quote an exact phrase 
		}
		
		if ( $input_filter->search_in_tags ) //== "true" )
		{
			$search_fields = "en.entry_name,en.description,en.tags,en.admin_tags" ;// use index for all searchable fields
		}
		else
		{
			$search_fields = "en.admin_tags" ;// use index for admin_tags only			
		}

		return "MATCH($search_fields) AGAINST('$search_text'/*SEARCH_STRING*/ IN BOOLEAN MODE )";
	}
	
	/**
	 * will set the match-against cluase depending on the input_filter->categories
	 * 
	 * If emtpy, will return the valid "1=1" query element so the queries will stay correct
	 *
	 * @param reportsInputFilter $input_filter
	 */
	private static function setCategoriesMatchClause ( reportsInputFilter $input_filter )
	{
		$categories_ids_str = null;
		if ( $input_filter->categories )
		{
			$categories_ids_str = entryFilter::categoryNamesToIndexedIds ( $input_filter->categories );
			if ( $categories_ids_str )
			{
				$categories_match = " MATCH (search_text_discrete) AGAINST ( '$categories_ids_str' IN BOOLEAN MODE ) ";
				return  $categories_match;
			}
		}
		return "1=1";
	}
	
	private static function intToDateTime ( $timestamp )
	{
		return date ( "Y-m-d H:i:s" , $timestamp );	 
	}

	private static function intToDateId ( $timestamp , $day_offset=null)
	{
		if ( $day_offset )
			$timestamp = $timestamp + $day_offset * 60 * 60 * 24;
		return date ( "Ymd" , $timestamp );	 
	}

}


class reportsInputFilter
{
	public $from_date;
	public $to_date;
	public $keywords;
	public $search_in_tags;
	public $search_in_admin_tags;
	public $extra_map;
	public $categories;
	
	public function toShortString()
	{
		return $this->from_date ."_".$this->to_date."_".$this->keywords."_".$this->search_in_tags."_".$this->search_in_admin_tags.
		"_".$this->categories;
	}
}
?>