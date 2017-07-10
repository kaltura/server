."\n<?php
class myReportsMgr
{
	const REPORT_FLAVOR_GRAPH= 1;
	const REPORT_FLAVOR_TOTAL= 2;
	const REPORT_FLAVOR_TABLE= 3;
	const REPORT_FLAVOR_COUNT = 4;
	const REPORT_FLAVOR_BASE_TOTAL = 5;

	const REPORT_TYPE_TOP_CONTENT = 1;
	const REPORT_TYPE_CONTENT_DROPOFF = 2;
	const REPORT_TYPE_CONTENT_INTERACTIONS = 3;
	const REPORT_TYPE_MAP_OVERLAY = 4;
	const REPORT_TYPE_TOP_CONTRIBUTORS = 5;
	const REPORT_TYPE_TOP_SYNDICATION = 6;
	const REPORT_TYPE_CONTENT_CONTRIBUTIONS = 7;
	const REPORT_TYPE_ADMIN_CONSOLE = 10;
	const REPORT_TYPE_USER_ENGAGEMENT = 11;
	const REPORT_TYPE_USER_ENGAGEMENT_TOTAL_UNIQUE = 110;
	const REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT = 12;
	const REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT_TOTAL_UNIQUE = 120;
	const REPORT_TYPE_USER_TOP_CONTENT = 13;
	const REPORT_TYPE_USER_TOP_CONTENT_TOTAL_UNIQUE = 130;
	const REPORT_TYPE_USER_CONTENT_DROPOFF = 14;
	const REPORT_TYPE_USER_CONTENT_DROPOFF_TOTAL_UNIQUE = 140;
	const REPORT_TYPE_USER_CONTENT_INTERACTIONS = 15;
	const REPORT_TYPE_USER_CONTENT_INTERACTIONS_TOTAL_UNIQUE = 150;
	const REPORT_TYPE_SYSTEM_GENERIC_PARTNER = 100;
	const REPORT_TYPE_SYSTEM_GENERIC_PARTNER_TYPE = 101;
	const REPORT_TYPE_PARTNER_BANDWIDTH_USAGE = 200;
	const REPORT_TYPE_PARTNER_USAGE = 201;
	const REPORT_TYPE_PARTNER_USAGE_DASHBOARD = 202;
	const REPORT_TYPE_PEAK_STORAGE = 300;
	const REPORT_TYPE_APPLICATIONS = 16;
	const REPORT_TYPE_USER_USAGE = 17;
	const REPORT_TYPE_SPECIFIC_USER_USAGE = 18;
	const REPORT_TYPE_VAR_USAGE = 19;
	const REPORT_TYPE_TOP_CREATORS = 20;
	const REPORT_TYPE_PLATFORMS = 21;
	const REPORT_TYPE_OPERATION_SYSTEM = 22;
	const REPORT_TYPE_BROWSERS = 23;
	const REPORT_TYPE_LIVE = 24;
	const REPORT_TYPE_TOP_PLAYBACK_CONTEXT = 25;
	const REPORT_TYPE_VPAAS_USAGE = 26;

	const REPORTS_TABLE_MAX_QUERY_SIZE = 20000;
	const REPORTS_CSV_MAX_QUERY_SIZE = 130000;
	const REPORTS_TABLE_RESULTS_SINGLE_ITERATION_SIZE = 10000;
	const REPORTS_COUNT_CACHE = 60;
	
	const COUNT_PLAYS_HEADER = "count_plays";
	const UNIQUE_USERS = "unique_known_users";
	const UNIQUE_VIDEOS = "unique_videos";
	
	const OBJECT_IDS_PLACE_HOLDER = "{OBJ_ID_CLAUSE}";
	const APPLICATION_NAME_PLACE_HOLDER = "{APPLICATION_NAME}";
	const PUSERS_PLACE_HOLDER = "{PUSER_ID}";
	const UNKNOWN_PUSER_ID_CLAUSE = "'0'";
	const UNKNOWN_NAME_CLAUSE = "'Unknown'";

	static $unique_total_reports = array (self::REPORT_TYPE_USER_ENGAGEMENT,
										self::REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT, 
										self::REPORT_TYPE_USER_TOP_CONTENT,
										self::REPORT_TYPE_USER_CONTENT_DROPOFF,
										self::REPORT_TYPE_USER_CONTENT_INTERACTIONS);
										
	static $end_user_filter_get_count_reports = array (self::REPORT_TYPE_PLATFORMS,
										self::REPORT_TYPE_OPERATION_SYSTEM, 
										self::REPORT_TYPE_BROWSERS,
										self::REPORT_TYPE_TOP_CONTENT,
										self::REPORT_TYPE_TOP_PLAYBACK_CONTEXT);
										
	static $escaped_params = array(self::OBJECT_IDS_PLACE_HOLDER,
								   self::APPLICATION_NAME_PLACE_HOLDER,
								   self::PUSERS_PLACE_HOLDER);
								   
	static $reports_without_graph = array(self::REPORT_TYPE_VPAAS_USAGE);
	
	static $reports_without_totals = array(self::REPORT_TYPE_VPAAS_USAGE);
	
	static $reports_without_table = array();
										
										
	
	/**
	 * @param int $partner_id
	 * @param int $report_type myReportsMgr::REPORT_TYPE_*
	 * @param reportsInputFilter $input_filter
	 * @param string $dimension returns single column from the graphs array
	 * @param string $object_ids comma seperated ids
	 * @return array <date|type, array <columnName, value>>
	 */
	public static function getGraph ( $partner_id , $report_type , reportsInputFilter $input_filter , $dimension = null , $object_ids = null )
	{
		$start = microtime(true);
		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_GRAPH , $input_filter , null , null , null , $object_ids );

		if ( $report_type == self::REPORT_TYPE_PLATFORMS)
		{
			if ($object_ids != NULL && count($object_ids) > 0)
				$res = self::getGraphsByDateId ( $result , $report_type);
			else
				$res = self::getMultiGraphsByDateId ( $result , "device", $report_type); 
		}
		else if ( $report_type == self::REPORT_TYPE_CONTENT_DROPOFF || $report_type == self::REPORT_TYPE_USER_CONTENT_DROPOFF)
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
	
	private static function getMultiGraphsByDateId ( $result , $multiline_column, $report_type )
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
					if ($column != $multiline_column)
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
				elseif ($column === $multiline_column)
				{
					$multiline_val = $val;
				}
				else
				{
					$graph = $graphs[$column];
					if ($graph[$date] != null)
						$graph[$date] =  $graph[$date] . "," . $multiline_val . ":" . $val; // the value for graph 1 will be column #1 in the row 
					else
						 $graph[$date] = $multiline_val . ":" . $val;
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
			if ($input_filter instanceof endUserReportsInputFilter && in_array($report_type, self::$unique_total_reports)) 
			{
				foreach ( $row as $name => $value )
				{
					if ($name == self::COUNT_PLAYS_HEADER)
					{
						$count_plays = $value;
						break;
					}
				}
				if (count($res[0]) == 1) {
					$header = array();
					$data = array();
				}
				$count_plays_limit = kConf::get('plays_limit');
				if ($count_plays > $count_plays_limit) {
					$unique_header[]= self::UNIQUE_USERS;
					$unique_data[] = "-";
					$unique_header[]= self::UNIQUE_VIDEOS;
					$unique_data[] = "-";
					$header = array_merge($unique_header, $header);
					$data = array_merge($unique_data, $data);						
				} else {
					$result  = self::executeQueryByType( $partner_id , $report_type * 10 , self::REPORT_FLAVOR_TOTAL , $input_filter , null , null , null , $object_ids );
					$row = $result[0];
			
					foreach ( $row as $name => $value )
					{
						$unique_header[]= $name;
						$unique_data[] = $value;
					}			
					$header = array_merge($unique_header, $header);
					$data = array_merge($unique_data, $data);
				}
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
		$page_size , $page_index , $order_by , $object_ids = null , $offset = null)

	{
		$start = microtime ( true );
		$total_count = 0;
		if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, self::$end_user_filter_get_count_reports) )
		{
			$total_count = self::getTotalTableCount( $partner_id , $report_type , $input_filter  ,
				$page_size , $page_index , $order_by , $object_ids );	
			
			if ( $total_count <= 0 )
			{
				$end = microtime(true);
				KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );			
				return array ( array() , array() , 0 );
			}
		}
		if ( ! $page_size || $page_size < 0 ) $page_size = 10;
		$page_size = min($page_size , self::REPORTS_TABLE_MAX_QUERY_SIZE);
		
		if ( ! $page_index || $page_index < 0 ) $page_index = 0;

		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_TABLE , $input_filter ,$page_size , $page_index , $order_by , $object_ids, $offset );
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
			$res =  array ( array() , array() , 0 );
		}
		
		$end = microtime(true);
		KalturaLog::log( "getTable took [" . ( $end - $start ) . "]" );

		return $res;
	}

	/**
	 * @param int $partner_id
	 * @param int $report_type myReportsMgr::REPORT_TYPE_*
	 * @param reportsInputFilter $input_filter
	 * @param string $object_ids comma seperated ids
	 * @return array <columnName, value>
	 */
	public static function getBaseTotal ( $partner_id , $report_type , reportsInputFilter $input_filter , $object_ids = null )
	{
		$start = microtime(true);
		$result  = self::executeQueryByType( $partner_id , $report_type , self::REPORT_FLAVOR_BASE_TOTAL , $input_filter , null , null , null , $object_ids );
		$res = null;
		if ( count($result) > 0 )
		{
			$res = $result[0];
			
		}
		
		$end = microtime(true);
		KalturaLog::log( "getSubTotal took [" . ( $end - $start ) . "]" );
		
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
		
		list ( $file_path , $file_name ) = self::createFileName ( $partner_id , $report_type , $input_filter , $dimension , $object_ids ,$page_size , $page_index , $order_by );
		// TODO - remove comment and read from disk
/*		
		if ( file_exists ( $file_path ) )
		{
			return $url;
		}
	*/	
		$csv = new myCsvWrapper ();
		
		$arr = array();
		
		if (!in_array($report_type, self::$reports_without_graph))
		{
			$arr = self::getGraph( $partner_id , 
				$report_type , 
				$input_filter ,
				$dimension , 
				$object_ids );
		}
		
		
		if (!in_array($report_type, self::$reports_without_totals))
			list ( $total_header , $total_data ) = self::getTotal( $partner_id , 
				$report_type , 
				$input_filter , $object_ids );			
		
	
		if ($page_size < self::REPORTS_TABLE_RESULTS_SINGLE_ITERATION_SIZE)
		{
			if (!in_array($report_type, self::$reports_without_table))
			{
				list ( $table_header , $table_data , $table_total_count ) = self::getTable( $partner_id ,
					$report_type ,
					$input_filter ,
					$page_size , $page_index ,
					$order_by ,  $object_ids );
					
					if ($input_filter instanceof endUserReportsInputFilter)
					{
						$table_total_count =  self::getTotalTableCount($partner_id, $report_type, $input_filter, $page_size, $page_index, $order_by, $object_ids);
					}
					
			} 
			
			$csv = myCsvReport::createReport( $report_title , $report_text , $headers ,
				$report_type , $input_filter , $dimension ,
				$arr , $total_header , $total_data , $table_header , $table_data , $table_total_count, $csv);
	
			$data = $csv->getData();
	
			// return URLwq
			if ( ! file_exists (dirname ( $file_path ) ))
					kFile::fullMkfileDir( dirname ( $file_path ) , 0777 );
				//adding BOM for fixing problem in open .csv file with special chars using excel.
				$BOM = "\xEF\xBB\xBF";
				file_put_contents ( $file_path, $BOM . $data );
		}
		else
		{
			$tempCsv = new myCsvWrapper();
	
			if ( ! $page_size || $page_size < 0 ) $page_size = 10;
			if ( ! $page_index || $page_index < 1 ) $page_index = 1;
	
			//checking if query is too big
			$table_amount =  self::getTotalTableCount($partner_id, $report_type, $input_filter, $page_size, $page_index, $order_by, $object_ids);
			
			if ($table_amount > self::REPORTS_CSV_MAX_QUERY_SIZE && $page_size > self::REPORTS_CSV_MAX_QUERY_SIZE)
				throw new kCoreException("Exceeded max query size: " . self::REPORTS_CSV_MAX_QUERY_SIZE ,kCoreException::SEARCH_TOO_GENERAL);
			
			$start_offest = ($page_index - 1) * $page_size;
			$end_offset = $start_offest + $page_size;
			$iteration_page_size = self::REPORTS_TABLE_RESULTS_SINGLE_ITERATION_SIZE;
	
			for ($current_offset = $start_offest ; $current_offset < $end_offset  ; $current_offset = $current_offset + $iteration_page_size )
			{
				$offset_difference = $end_offset - $current_offset;
				if ($offset_difference < self::REPORTS_TABLE_RESULTS_SINGLE_ITERATION_SIZE)
					$iteration_page_size = $offset_difference;
	
				//here page_index is redundant
				list ( $table_header , $table_data , $table_total_count ) = self::getTable( $partner_id ,
					$report_type ,
					$input_filter ,
					$iteration_page_size , $page_index ,
					$order_by ,  $object_ids , $current_offset);
	
				if (!$table_data)
					break;
				
				//first iteration - create the beginning of the report
				if ($current_offset == $start_offest)
				{	
					$csv = myCsvReport::createReport( $report_title , $report_text , $headers ,
						$report_type , $input_filter , $dimension ,
						$arr , $total_header , $total_data , $table_header , $table_data , $table_amount , $csv);
	
					$data = $csv->getData();
	
					// return URL
					if ( ! file_exists (dirname ( $file_path ) ))
						kFile::fullMkfileDir( dirname ( $file_path ) , 0777 );
					
					//adding BOM for fixing problem in open .csv file with special chars using excel.
					$BOM = "\xEF\xBB\xBF";
					file_put_contents ( $file_path, $BOM . $data );
				}
				//not first iteration - append data to the created file
				else
				{
					//append data from query to file
					$tempCsv->clearData();
					$tempCsv = myCsvReport::appendLines($tempCsv , $table_data);
					$data = $tempCsv->getData();
	
					file_put_contents ( $file_path, $data  , FILE_APPEND);
				}
				
			}
	
		}

		$url = self::createUrl($partner_id, $file_name);
	return $url;
	}

// -------------------------------------------- private -----------------------------------------------// 	

	private static function createUrl ($partner_id, $file_name)
	{
		$ksStr = "";
		$partner = PartnerPeer::retrieveByPK ( $partner_id );
		$secret = $partner->getSecret ();
		$privilege = ks::PRIVILEGE_DOWNLOAD . ":" . $file_name;
		
		$maxExpiry = 86400;
		$expiry = $partner->getKsMaxExpiryInSeconds();
		if(!$expiry || ($expiry > $maxExpiry))
			$expiry = $maxExpiry;
		
		$result = kSessionUtils::startKSession ( $partner_id, $secret, null, $ksStr, $expiry, false, "", $privilege );
		
		if ($result < 0)
			throw new Exception ( "Failed to generate session for asset [" . $this->getId () . "] of type " . $this->getType () );
			
		//url is built with DC url in order to be directed to the same DC of the saved file
		$url = kDataCenterMgr::getCurrentDcUrl() . "/api_v3/index.php/service/report/action/serve/ks/$ksStr/id/$file_name/report.csv";
		return $url;
	}
	
	private static function createFileName ( $partner_id )
	{
	$args = func_get_args();
		$file_name = uniqid();
		$time_suffix = date ( "Y-m-D-H" , ((int)(time() / 43200))*  43200 ) ; // calculate for intervlas of half days (86400/2)  
		
		$folderPath = "/content/reports/$partner_id";
		$fullPath = myContentStorage::getFSContentRootPath() .  $folderPath;
		if(!file_exists($fullPath))
			kFile::fullMkfileDir($fullPath, 0777, true);
			
		$fileName = "{$file_name}_{$time_suffix}";
		$file_path = "$fullPath/$fileName";
		
//		$path = "/content/reports/$partner_id/{$file_name}_{$time_suffix}";
//		$file_path = myContentStorage::getFSContentRootPath() .  $path;
//		$url = requestUtils::getHost() . $path;
		return array ( $file_path , $fileName );
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
			
		$key = 	$partner_id_str . "|" . $report_type . "|" . 
			$input_filter->from_date . $input_filter->to_date . $input_filter->keywords . $input_filter->search_in_admin_tags . $input_filter->search_in_tags . $input_filter->interval .
			$object_ids . $input_filter->categories;
		if ($input_filter instanceof endUserReportsInputFilter)
			$key = $key .  $input_filter->application . $input_filter->userIds . $input_filter->playbackContext . $input_filter->ancestorPlaybackContext;
		return $key;
	}
	
	public static function formatDateFromDateId ( $val )
	{
		// the $val is the date_id -> YYYYMMDD
		//$date = $val;
		$h = 0;
		$y = (int)substr ( $val , 0 , 4 );
		$m = (int)substr ( $val , 4 , 2 );
		$d = (int)substr ( $val , 6 , 2 );
        
        if (strlen($val) == 10) 
        	$h = (int)substr ( $val, 8, 2);
         
		$date = mktime  ( $h, 0, 0 , $m , $d , $y  ) ;	
      	
		return $date;	
	}


	
	private static function executeQueryByType ( $partner_id , $report_type , $report_flavor , reportsInputFilter $input_filter  ,
		$page_size , $page_index , $order_by , $object_ids = null , $offset = null)
	{
		$start = microtime(true);
		try
		{
			$link = self::getConnection();
			$add_search_text = false;
			
                        $str_object_ids = $object_ids;
			if ($input_filter instanceof endUserReportsInputFilter) 
                                $str_object_ids .=  $input_filter->categories;
   	                
   	        $use_index = "USE INDEX (PRIMARY)";        
			if ( is_numeric( $report_type ))
			{
				$file_path = myReportsSqlFileMgr::getSqlFilePath( 
					self::$type_map[$report_type] ,  
					self::$flavor_map[$report_flavor] , 
					$add_search_text , 
					$str_object_ids ,
					$input_filter);
			}
			else
			{
				if ( strpos ($report_type,".") === 0 || strpos ($report_type,"/") === 0 || strpos ($report_type,"http") === 0 )
				{
					throw new kCoreException("Will not search for invalid report_type [$report_type", kCoreException::INVALID_QUERY);
				}
				$file_path = dirname(__FILE__)."/". $report_type . ".sql";
			}

			$sql_raw_content = file_get_contents( $file_path );
			if ( ! $sql_raw_content )
			{
				$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaReportProvider');
				foreach ($pluginInstances as $pluginInstance)
				{

					$res = $pluginInstance->getReportResult($partner_id, $report_type, $report_flavor, $object_ids, $input_filter, $page_size, $page_index,  $order_by);
					if (!is_null($res))
					{
						return $res;
					}
				}
				throw new kCoreException("Cannot find sql for [$report_type] [$report_flavor] at [$file_path]", kCoreException::QUERY_NOT_FOUND);
			}
			
			$entryFilter = new entryFilter();
			$entryFilter->setPartnerSearchScope($partner_id);
			$shouldSelectFromSearchEngine = false;
			
			$category_ids_clause = "1=1"; 
			if ($input_filter instanceof endUserReportsInputFilter)
			{
				if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
				{
					$categoryFilter = new categoryFilter();
					if ($input_filter->playbackContext && $input_filter->ancestorPlaybackContext)
						$categoryIds = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
					else {
						if ($input_filter->playbackContext)
							$categoryFilter->set("_in_full_name", $input_filter->playbackContext);
						if ($input_filter->ancestorPlaybackContext)
							$categoryFilter->set("_matchor_likex_full_name", $input_filter->ancestorPlaybackContext);
						
						$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
						$categoryFilter->attachToCriteria($c);
						$c->applyFilters();
					
						$categoryIdsFromDB = $c->getFetchedIds();
					
						if (count($categoryIdsFromDB))
							$categoryIds = implode(",", $categoryIdsFromDB);
						else
							$categoryIds = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
					}
							
					$category_ids_clause = "ev.context_id in ( $categoryIds )";
				}
				
				if ($input_filter->application) {
					$input_filter->extra_map[self::APPLICATION_NAME_PLACE_HOLDER] = "'" . mysqli_real_escape_string($link, $input_filter->application) . "'";
				} 
				if ($input_filter->userIds != null) {
					$escapedobjectIds = self::explodeAndEscape($input_filter->userIds, $link);
					$puserIds = "('" . implode("','", $escapedobjectIds) . "')";
					// replace puser_id '0' with 'Unknown' as it saved on dwh pusers table
					$puserIds = str_replace(self::UNKNOWN_PUSER_ID_CLAUSE, self::UNKNOWN_NAME_CLAUSE, $puserIds);
					$input_filter->extra_map[self::PUSERS_PLACE_HOLDER] = $puserIds;
				}
			}
			
			if ($input_filter->categories) 
			{ 
				$entryFilter->set("_matchor_categories", $input_filter->categories);
				$shouldSelectFromSearchEngine = true;
			}
			
			if ($input_filter->keywords)
			{
				if($input_filter->search_in_tags)
					$entryFilter->set("_free_text", $input_filter->keywords);
				else
					$entryFilter->set("_like_admin_tags", $input_filter->keywords);
				
				$shouldSelectFromSearchEngine = true;
			}
			
			$entryIdsFromDB = array();
			
			if ($shouldSelectFromSearchEngine)
			{
				$c = KalturaCriteria::create(entryPeer::OM_CLASS);
				$entryFilter->attachToCriteria($c);
				$c->applyFilters();
				
				$entryIdsFromDB = $c->getFetchedIds();
				
				if ($c->getRecordsCount() > count($entryIdsFromDB))
					throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL );
				
				if (!count($entryIdsFromDB))
					$entryIdsFromDB[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;
			}
						
			$obj_ids_clause = null;
			
			if($object_ids)
			{
				$escapedobjectIds = self::explodeAndEscape($object_ids, $link);
				$object_ids_str = "'" . implode("','" , $escapedobjectIds) . "'";
				
				if ( $report_type == self::REPORT_TYPE_CONTENT_CONTRIBUTIONS )
				{
					$obj_ids_clause = "ev.entry_media_source_id in ( $object_ids_str)";
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
				else if ( $report_type == self::REPORT_TYPE_PARTNER_USAGE || $report_type == self::REPORT_TYPE_VAR_USAGE || $report_type == self::REPORT_TYPE_PEAK_STORAGE)
				{
					$obj_ids_clause = "partner_id in ($object_ids_str)";
				}		
				else if ( $report_type == self::REPORT_TYPE_PLATFORMS)
				{
					$obj_ids_clause = "device in ($object_ids_str)";
				}
				else
				{
					$entryIds = "'" . implode("','", array_merge($escapedobjectIds, $entryIdsFromDB)) . "'";
					$obj_ids_clause = "ev.entry_id in ( $entryIds )";
					
				}
			}
			elseif (count($entryIdsFromDB))
			{
				$entryIds = "'" . implode("','", $entryIdsFromDB) . "'";
				$obj_ids_clause = "ev.entry_id in ( $entryIds )";
			}

			if ($entryIds && substr_count($entryIds, ",") < 10) 
				$use_index = " ";

			
			if ($input_filter instanceof endUserReportsInputFilter && ($input_filter->userIds != null) && ($report_type == self::REPORT_TYPE_USER_USAGE || $report_type == self::REPORT_TYPE_SPECIFIC_USER_USAGE) ) {
					$userFilter = new kuserFilter();
					$userFilter->set("_in_puser_id", $input_filter->userIds);
					$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
					$userFilter->attachToCriteria($c);
					$c->applyFilters();
				
					$userIdsFromDB = $c->getFetchedIds();
				
					if (count($userIdsFromDB))
						$kuserIds = implode(",", $userIdsFromDB);
					else
						$kuserIds = kuser::KUSER_ID_THAT_DOES_NOT_EXIST;
							
					$obj_ids_clause = "u.kuser_id in ( $kuserIds )";
			} 
			
			if ( is_numeric( $report_type ))
				$order_by = self::getOrderBy( self::$type_map[$report_type] , $order_by );
			
			$query = self::getReplacedSql($link, $sql_raw_content , $partner_id , $input_filter , $page_size , $page_index , $order_by , $obj_ids_clause, $category_ids_clause , $offset, $use_index);
			if ( is_numeric( $report_type ))
				$query_header = "/* -- " . self::$type_map[$report_type] . " " . self::$flavor_map[$report_flavor] . " -- */\n";
			else 
				$query_header = "/* -- " . $report_type . " -- */\n";
			KalturaLog::log( "\n{$query_header}{$query}" );
			
			$res = self::executeQuery ( $query, $link );
			
			$end = microtime(true);
			KalturaLog::log( "Query took [" . ( $end - $start ) . "]" );
			return $res;
		}
		catch ( Exception $ex )
		{
			KalturaLog::log( $ex->getMessage() );
			// TODO - write proeper error
			if ($ex->getCode() == kCoreException::SEARCH_TOO_GENERAL);
				throw $ex;
			
			throw new Exception ( "Error while processing report for [$partner_id , $report_type , $report_flavor]" );
		}
	}
		
	private static $flavor_map = array ( 
		self::REPORT_FLAVOR_GRAPH => "graph" ,
		self::REPORT_FLAVOR_TOTAL => "total" ,
		self::REPORT_FLAVOR_TABLE => "detail" ,
		self::REPORT_FLAVOR_COUNT => "count" , 
		self::REPORT_FLAVOR_BASE_TOTAL =>"base_total",
	);
	
	private static $type_map = array ( 
		self::REPORT_TYPE_TOP_CONTENT => "top_content" ,
		self::REPORT_TYPE_CONTENT_DROPOFF => "content_dropoff" ,
		self::REPORT_TYPE_CONTENT_INTERACTIONS => "content_interactions" ,
		self::REPORT_TYPE_MAP_OVERLAY => "map_overlay" ,
		self::REPORT_TYPE_TOP_CONTRIBUTORS => "top_contributors" ,
		self::REPORT_TYPE_TOP_CREATORS => "top_creators" ,
		self::REPORT_TYPE_TOP_SYNDICATION => "top_syndication" ,
		self::REPORT_TYPE_CONTENT_CONTRIBUTIONS => "content_contributions" ,
		self::REPORT_TYPE_ADMIN_CONSOLE => "admin_console" ,
		self::REPORT_TYPE_USER_ENGAGEMENT => "user_engagement",
		self::REPORT_TYPE_USER_ENGAGEMENT_TOTAL_UNIQUE => "user_engagement_unique",
		self::REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT => "specific_user_engagement",
		self::REPORT_TYPE_SPEFICIC_USER_ENGAGEMENT_TOTAL_UNIQUE => "user_engagement_unique",
		self::REPORT_TYPE_USER_TOP_CONTENT => "user_top_content",
		self::REPORT_TYPE_USER_TOP_CONTENT_TOTAL_UNIQUE => "user_engagement_unique",
		self::REPORT_TYPE_USER_CONTENT_DROPOFF => "user_content_dropoff", 
		self::REPORT_TYPE_USER_CONTENT_DROPOFF_TOTAL_UNIQUE => "user_content_dropoff_unique",
	    self::REPORT_TYPE_USER_CONTENT_INTERACTIONS => "user_content_interactions",
	    self::REPORT_TYPE_USER_CONTENT_INTERACTIONS_TOTAL_UNIQUE => "user_content_interactions_unique",
		self::REPORT_TYPE_SYSTEM_GENERIC_PARTNER => "system_generic_partner" ,
		self::REPORT_TYPE_SYSTEM_GENERIC_PARTNER_TYPE => "system_generic_partner_type" ,
		self::REPORT_TYPE_PARTNER_BANDWIDTH_USAGE => "partner_bandwidth_usage" ,
		self::REPORT_TYPE_PARTNER_USAGE => "partner_usage" ,
		self::REPORT_TYPE_PARTNER_USAGE_DASHBOARD => "partner_usage_dashboard",
		self::REPORT_TYPE_PEAK_STORAGE => "peak_storage" ,
		self::REPORT_TYPE_APPLICATIONS => 'applications',
		self::REPORT_TYPE_USER_USAGE => 'user_usage',
		self::REPORT_TYPE_SPECIFIC_USER_USAGE => 'specific_user_usage',
		self::REPORT_TYPE_VAR_USAGE => 'var_usage',
		self::REPORT_TYPE_PLATFORMS => 'platforms',
		self::REPORT_TYPE_OPERATION_SYSTEM => 'os',
		self::REPORT_TYPE_BROWSERS => 'browsers',
		self::REPORT_TYPE_LIVE => "live",
		self::REPORT_TYPE_TOP_PLAYBACK_CONTEXT => "top_playback_context",
		self::REPORT_TYPE_VPAAS_USAGE => "vpaas_usage",
	
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
				"avg_view_drop_off",
			),
			"top_contributors" => array (
#				"screen_name",			
				"count_total" ,	
				"count_video" ,
				"count_audio" ,
				"count_image" ,
				"count_mix" ,
			),		
			"top_creators" => array (
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
			"user_engagement" => array (
				"unique_videos",
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"avg_view_drop_off",
				"load_play_ratio" ,		
			),
			"specific_user_engagement" => array (
				"unique_videos",
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"avg_view_drop_off",
				"load_play_ratio" ,		
			),
			"user_top_content" => array (
				"unique_videos",				
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"avg_view_drop_off",
				"load_play_ratio" ,	
			),
			"user_content_dropoff" => array (
				"count_plays" ,	
				"count_plays_25" ,
				"count_plays_50" ,
				"count_plays_75" ,
				"count_plays_100" ,
				"play_through_ratio" ,
			) ,	
			"user_content_interactions" => array (	
				"count_plays" ,	
				"count_edit" ,
				"count_viral" ,
				"count_download" ,
				"count_report" ,
			),
			"var_usage" => array (
				"month_id",
				"date_id",
				"bandwidth_consumption",
				"average_storage",
				"peak_storage",
				"added_storage",
				"deleted_storage",
				"combined_bandwidth_storage",
				"transcoding_usage",
			    "month_id,partner_id",
			    "date_id,partner_id",
			),
			"partner_usage" => array (
				"month_id",
				"date_id",
				"bandwidth_consumption",
				"average_storage",
				"peak_storage",
				"added_storage",
				"deleted_storage",
				"combined_bandwidth_storage",
				"transcoding_consumption"
			),
			"user_usage" => array (
				"added_entries",
				"deleted_entries",
	 			"total_entries",
				"added_storage_mb",
				"deleted_storage_mb",
				"total_storage_mb",
				"added_msecs",
				"deleted_msecs",
				"total_msecs",
			),
			"specific_user_usage" => array (
				"date_id",
				"month_id",
				"added_entries",
				"deleted_entries",
				"added_storage_mb",
				"deleted_storage_mb",
				"added_msecs",
				"deleted_msecs",
			),
			"platforms" => array (	
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"load_play_ratio" ,	
				"avg_view_drop_off",
			),
			"os" => array (	
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"load_play_ratio" ,	
				"avg_view_drop_off",
			),
			"browsers" => array (	
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"load_play_ratio" ,	
				"avg_view_drop_off",
			),
			"live" => array (
				"count_plays"
			),
			"top_playback_context" => array (
				"count_plays" ,	
				"sum_time_viewed" ,
				"avg_time_viewed" ,
				"count_loads" ,
				"avg_view_drop_off",
				"load_play_ratio" ,		
			),
			"vpaas_usage" => array (
				"month_id",
				"total_plays",
				"bandwidth_gb",
				"avg_storage_gb",
				"transcoding_gb",
				"total_media_entries",
				"total_end_users", 
			)
		);
		
		$valid_field  = false;

		// if the order by is not explicitly allowed - don't allow it !
		if ( isset ( $map[$report_type] ) )
		{
			$section = $map[$report_type];
			$order_by_without_direction = str_replace("+", "", $order_by);
			$order_by_without_direction = str_replace("-", "", $order_by_without_direction);
			
			if ( in_array ( trim($order_by_without_direction) , $section ) )
			{
				$valid_field = true;
			}
		}
		
		if ( $valid_field ) {
			$order_by_fields = explode(',', $order_by);
			$order_by_str = "";
			foreach($order_by_fields as $curr_order_by)
			{
				if ( $curr_order_by[0] == '-' )
				{
					$order_by_field =  substr($curr_order_by,1);
					$order_by_dir = "DESC";
				}
				elseif ( $curr_order_by[0] == '+' )
				{
					$order_by_field =  substr($curr_order_by,1);
					$order_by_dir = "ASC";
				}
				else
				{
					$order_by_field =  $curr_order_by;
					$order_by_dir = "DESC";
				}
			    $order_by_str = "$order_by_str $order_by_field $order_by_dir ,";
			}
			$order_by_str = substr($order_by_str,0,-1);
		}
		else 
		{
			$order_by_str = "1=1 /* [$report_type][$valid_field]: BAD order field [" . 
				str_replace ( array ( "/" , "*" ) , array ( "" , "" ) , $order_by_field ) . 
				"] */";  
		}
		
		return $order_by_str;
	}
	
	private static function getReplacedSql ( $link, $sql_content , $partner_id , reportsInputFilter $input_filter , 
		$page_size , $page_index  , $order_by , $obj_ids_clause = null, $cat_ids_clause = null , $offset = null, $use_index = null)
	{
		// TODO - format the search_text according to the the $input_filter
		$search_text_match_clause = "1=1"; //self::setSearchFieldsAndText ( $input_filter );

		if ($offset)
			$pagination_first = $offset;
		else
		{
		$pagination_first = ( $page_index - 1 ) * $page_size;
		if ( $pagination_first < 0 ) $pagination_first = 0;
		}

		if ( $order_by )
		{
			$order_by_str = $order_by;
		}
		else
		{
			$order_by_str = "1=1";
		}

		$obj_ids_str = $obj_ids_clause ? $obj_ids_clause : "1=1";
		$cat_ids_str = $cat_ids_clause ? $cat_ids_clause : "1=1";
		// the diff between user and server timezones 
		$time_shift = round($input_filter->timeZoneOffset / 60);
		
		// add time zone offset to the time shift
		$dateTimeZoneServer = new DateTimeZone(kConf::get('date_default_timezone'));
		$dateTimeZoneUTC = new DateTimeZone("UTC");
		$dateTimeUTC = new DateTime("now", $dateTimeZoneUTC);
		$timeOffsetSeconds = $dateTimeZoneServer->getOffset($dateTimeUTC);
		$timeOffset = round($timeOffsetSeconds / 3600); // convert to hours
		$time_shift += $timeOffset;
		
		$time_shift *= -1; // Don't ask me why but it works that way
		
		$origTimeZone = date_default_timezone_get ();
		date_default_timezone_set('UTC');
				
		// removing hours, minutes and seconds from the date  
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
				"{TIME_SHIFT}" , 
				"{CAT_ID_CLAUSE}" ,
				"{GROUP_COLUMN}" ,
				"{USE_INDEX}"
			);
			
		$values = 
			array (
				intval($partner_id) ,
				self::intToDateTime($input_filter->from_date), 
				self::intToDateTime($input_filter->to_date ),
				intval($input_filter->from_day),
				intval($input_filter->to_day),
				self::intToDateId($input_filter->to_date , -7 ),
				self::intToDateId($input_filter->to_date , -30 ),
				self::intToDateId($input_filter->to_date , -180 ),
				$search_text_match_clause ,
				$order_by_str ,
				$pagination_first ,
				$page_size ,
				$obj_ids_str , 
				$time_shift,
				$cat_ids_str,
				($input_filter->interval == reportInterval::MONTHS ? "month_id" : "date_id"),
				$use_index
			);
				
		if ( $input_filter->extra_map )
		{
			foreach ( $input_filter->extra_map as $name => $value  )	
			{
				$names[] = $name;
				$values[] = $value;				
			}
		}
		
		foreach ($values as $key => &$value) {
			if (!in_array($names[$key], self::$escaped_params))
				$value = mysqli_real_escape_string($link, $value);
		}

		
		$replaced_sql = str_replace ( $names , $values , $sql_content );	

		date_default_timezone_set($origTimeZone);
		
		return $replaced_sql;
	}
	
	private static function executeQuery ( $query, $link )
	{
		kApiCache::disableConditionalCache();
		$mysql_function = 'mysqli';
	
		$db_config = kConf::get( "reports_db_config" );
		
		if($mysql_function == 'mysql') $db_selected =  mysql_select_db ( $db_config["db_name"] , $link );
		else $db_selected =  mysqli_select_db ( $link , $db_config["db_name"] );
		
		$error_function = $mysql_function.'_error';
		if (!$db_selected) {
			throw new kCoreException('mysqli_select_db('. $db_config["db_name"].') failed, check settings in the reports_db_config section of configurations/local.ini', kCoreException::INVALID_QUERY);
		}

		if($mysql_function == 'mysql') $result = mysql_query($query);
		else $result = mysqli_query($link, $query);
		
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) 
		{
		
		    KalturaLog::err('Invalid query: ' . $error_function($link));
		    $message = 'Invalid query';
		    throw new kCoreException($message, kCoreException::INVALID_QUERY);
		}
			
		$res = array();
	
		$fetch_function = $mysql_function.'_fetch_assoc';
		while ($row = $fetch_function($result)) 
		{			
			$res[] = $row;
		}
		
		$free_result_func = $mysql_function.'_free_result';
		$free_result_func($result);
		$close_function = $mysql_function.'_close';
		$close_function($link);
		
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
	
	private static function getConnection() 
	{
		$mysql_function = 'mysqli';
		$db_config = kConf::get( "reports_db_config" );
		if (!isset($db_config["port"])) {
		    if(ini_get("mysqli.default_port")!==null){
			$db_config["port"]=ini_get("mysqli.default_port");
		    }else{
			$db_config["port"]=3306;
		    }
		}	    
		$timeout = isset ( $db_config["timeout"] ) ? $db_config["timeout"] : 40;
		
		ini_set('mysql.connect_timeout', $timeout );
		$host = $db_config["host"];
		if ( isset ( $db_config["port"] ) && $db_config["port"]  && $mysql_function != 'mysqli' ) $host .= ":" . $db_config["port"];
		
		$connect_function = $mysql_function.'_connect';
		$link  = $connect_function( $host , $db_config["user"] , $db_config["password"] , null, $db_config["port"] );
		if (mysqli_connect_errno()) {
		        throw new kCoreException('DB connection failed: '. mysqli_connect_error()."\ncheck settings in the reports_db_config section of configurations/local.ini", kCoreException::INVALID_QUERY);
		}
		KalturaLog::log( "Reports query using database host: [$host] user [" . $db_config["user"] . "]" );
		
		return $link;
	}	
	
	private static function explodeAndEscape($ids, $link) {
		$escapedobjectIds = array();
		$objectIds = explode(',', $ids);
		foreach ($objectIds as $objectId) {
			$escapedobjectId = trim($objectId, "'");
			$escapedobjectId = mysqli_real_escape_string($link, $escapedobjectId);
			$escapedobjectIds[] = $escapedobjectId;
		}
		return $escapedobjectIds;
	}

}


class reportsInputFilter
{
	public $from_date;
	public $to_date;
	public $from_day;
	public $to_day;
	public $keywords;
	public $search_in_tags;
	public $search_in_admin_tags;
	public $extra_map;
	public $categories;
	public $timeZoneOffset;
	public $interval;
	
	public function getFilterBy() {
		return "";
			
	}
}

class endUserReportsInputFilter extends reportsInputFilter
{
	public $application;
	public $userIds;
	public $playbackContext;
	public $ancestorPlaybackContext;
	
	public function getFilterBy() {
		$filterBy = ""; 
		if ($this->playbackContext != null || $this->ancestorPlaybackContext != null) 
			$filterBy = "_by_context";
		if ($this->userIds != null) 
			$filterBy = $filterBy . "_by_user";
		if ($this->application != null)
			$filterBy = $filterBy . "_by_app";

		return $filterBy;
			
	}
}
