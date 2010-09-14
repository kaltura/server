<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

class batchwatchAction extends kalturaSystemAction
{
	private $log_info;
	
	/**
	 * Will give a good view of the batch processes in the system
	 */
	public function execute()
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		$this->forceSystemAuthentication();

		if ( $this->getP ( "mode" , null ) == "restart" )
		{
			$batch_name = $this->getP ( "batch" );
			$path = "/web/batchwatch/$batch_name";
			echo $path;
			@file_put_contents( $path , "restart" ); // sync - OK
			die();	
		}
		
		$this->setLogInfo();
		
		$this->include_old = $this->getP ( "old" , false );
		// import    - DB
		// old convert client (in) + client errors - DISK  
		// old convert server + server errors - DISK
		// old convert client (out) - DISK
		// handled- DISK
		// emails - DB
		// flatten - DB
		// new convert client (in) - DISK
		// new convert server - DISK
		// new convert client (out)- DISK
		// commercial encoder (in) - DISK
		// download video (in) - DB
		// download video (out) - DISK
		// notifications - DB

		// check logs - to see that jobs are running - make sure that was written not too long ago
		// green | yellow | red for each section depending on thresholds
		
		// TODO - read from paramter to understand the time frame of which to query the db 
		$hours_back  = $this->getP ( "h" , 24 );
		
		$this->hours_back = $hours_back;
		// search in DB for pending / errornouse jobs
		$db_job_stats = $this->getJobStats ( $hours_back );
		
		$content = myContentStorage::getFSContentRootPath();
		// saerch on disk in varios directories
		
		$this->import = $this->getDbStats ( "batchImportServer" , $db_job_stats , BatchJob::BATCHJOB_TYPE_IMPORT );
		$this->flatten = $this->getDbStats ( "batchFlattenServer" , $db_job_stats , BatchJob::BATCHJOB_TYPE_FLATTEN );
		$this->bulk = $this->getDbStats ( "batchBulkUpload" , $db_job_stats , BatchJob::BATCHJOB_TYPE_BULKUPLOAD );
		
		$db_convert_stats = $this->getConversionStats ( $hours_back );

		if ( $this->include_old )
		{
			$this->old_convert_client_in = $this->getDiskStats ( "batchConvertClient" , $content . "/content/preconvert/files" );
			$this->old_convert_client_out = $this->getDiskStats ( "batchConvertClient" , $content . "/conversions/postconvert/files" );
			$this->old_convert_client_out_db = $this->getDbStats ( "batchConvertClient" , $db_convert_stats , 
				BatchJob::BATCHJOB_TYPE_CONVERT  );
			
			$this->old_convert_client_out["full_stats"] = $this->old_convert_client_out_db["full_stats"];
			$this->old_convert_client_out["successful_stats"] = $this->old_convert_client_out_db["successful_stats"];
			$this->old_convert_client_out["count"] = $this->old_convert_client_out["count"] + 
			$this->old_convert_client_out_db["full_stats"][BatchJob::BATCHJOB_STATUS_PROCESSING]["count"];  
	//		$this->old_convert_client_out = $this->mergeStats ( )
			$this->old_convert_client_errors = $this->getDiskStats ( "__reconert"  ,$content . "/conversions/CLIENT_ERRORS/files" );
			$this->old_convert_server = $this->getDiskStats ( "batchConvertServer" ,$content . "/conversions/preconvert/files" );
			$this->old_convert_server_in_proc = $this->getDiskStats ( null ,$content . "/conversions/preconvert/inprocess_files" ); // see how many files are in the in_process dir - indicating process of the old server
			// merge the 2 results from the old server directory
			$this->old_convert_server  = $this->mergeStats ( 0 , $this->old_convert_server , 2 , $this->old_convert_server_in_proc );
			
			$this->old_convert_server_errors = $this->getDiskStats ( null , $content . "/conversions/SERVER_ERRORS/files" );
		}
		
		$this->new_convert_client_in = $this->getDiskStats ( "newBatchConvertClient" , $content . "/content/new_preconvert" , "*.in*" ) ; //dicator" );
		$this->new_convert_client_out = $this->getDiskStats ( "newBatchConvertClient" , $content . "/conversions/postconvert_res" , "*.in*" ) ; //dicator" );
		$this->new_convert_server = $this->getDiskStats ( "newBatchConvertServer" , $content . "/conversions/preconvert_cmd" , "*.in*" ) ; //dicator" );
		$this->new_commercial_convert_server = $this->getDiskStats ( "newBatchCommercialConvertServer" , $content . "/conversions/preconvert_commercial_cmd" , "*.in*" ) ; //dicator" );
		
		$this->download_video_in = $this->getDbStats ( null , $db_job_stats , BatchJob::BATCHJOB_TYPE_DOWNLOAD );
		$this->download_video_out = $this->getDiskStats ( "batchDownloadVideoServer" , $content . "/conversions/download_res" , "*.in*" ) ; //dicator" );
		
		
		
	}
	
	
	private function setLogInfo ()
	{
		$log_info_file = myContentStorage::getFSContentRootPath() . "/logs/logsize.log";
		
		// this is the new way to read the log data on production - due to logs being created on other mchines and other disks
		if ( file_exists( $log_info_file ) )
		{
			$content = file_get_contents( $log_info_file );
			$lines = explode ( "\n" , $content );
			
			$this->log_info =  $lines;
		}
	}
	/*
		will return :
		file count in dir
		oldest file timestamp
		newest file timestamp
		date & size of log file
	 * 
	 */
	private function getDiskStats ( $log_name , $dir , $pattern = null )
	{
		$oldest = null;
		$newest = 0;
		$path = realpath ( $dir  ) . ( $pattern ? "/$pattern" : "/*" );
		$files = glob($path);
		$count = count ( $files );
		foreach ( $files as $file )
		{
//echo "$file" . "<br>";			
			$timestamp =  filemtime ( $file );
			if ( $oldest == null || $timestamp < $oldest  ) $oldest = $timestamp;
			if ( $timestamp > $newest ) $newest = $timestamp;
		}

		list ( $file_name , $log_timestamp , $log_size ) = $this->getLogData( $log_name );
		
		if ( $oldest > $newest ) $oldest = $newest;
		return array ( 
			"service_name" => $log_name ,  // all log_names are called after their services
			"path" => $path , 
			"count" => $count , 
			"oldest" => $oldest , 
			"newest" => $newest , 
			"log_name" => $file_name  , 
			"log_timestamp" => $log_timestamp , 
			"log_size" => $log_size );
	}
	
	private function getDbStats ( $log_name , $db_job_stats , $job_type , $successful_stats_enum = BatchJob::BATCHJOB_STATUS_FINISHED ,
		$failed_stats_enum = BatchJob::BATCHJOB_STATUS_FAILED )
	{
		$oldest = null;
		$newest = 0;
		$count = 0;
		$stats_for_job = @$db_job_stats[$job_type];
		$successful_stats = null;
		if ( $stats_for_job )
		{
			
			foreach ( $stats_for_job as $status => $stats_for_job_per_status )
			{
				if ( $status == $successful_stats_enum )
				{
					 $successful_stats = $stats_for_job_per_status;
				}
				else
				{
					if ( $oldest == null || $stats_for_job_per_status["oldest"] < $oldest  ) $oldest = $stats_for_job_per_status["oldest"];
					if ( $stats_for_job_per_status["newest"] > $newest ) $newest = $stats_for_job_per_status["newest"];
					if ( $status != $failed_stats_enum ) // don't count the failed enums in the total count  
						$count += $stats_for_job_per_status["count"];
				}
			}
		}

		list ( $file_name , $log_timestamp , $log_size ) = $this->getLogData( $log_name );
		
		if ( $oldest > $newest ) $oldest = $newest;
		return array ( 
			"service_name" => $log_name ,  // all log_names are called after their services
			"path" => null , 
			"count" => $count , 
			"oldest" => $oldest , 
			"newest" => $newest , 
			"full_stats" => $stats_for_job , 
			"successful_stats" => $successful_stats ,
			"log_name" => $file_name  , 
			"log_timestamp" => $log_timestamp , 
			"log_size" => $log_size ); 		
	}
	
	private function mergeStats ( $job_status1 , $stats1 , $job_status2 , $stats2 )
	{
		return array ( 
			"service_name" => $stats1["service_name"] ,  // all log_names are called after their services
			"path" => $stats1["path"] , 
			"count" => $stats1["count"] + $stats2["count"] , 
			"oldest" => min ( $stats1["oldest"] , $stats2["oldest"] ), 
			"newest" => max ( $stats1["newest"] , $stats2["newest"] ), 
			"full_stats" => array ( $job_status1 => $stats1 , $job_status2 => $stats2 	) , 
			"log_name" => $stats1["log_name"]  , 
			"log_timestamp" => $stats1["log_timestamp"] , 
			"log_size" => $stats1["log_size"] );			
	}
	
	// TODO - gets all stats from db and organizes them in a matrix 
	private function getJobStats ( $hours_ago = 24 )
	{
		$connection = Propel::getConnection();
		$from_date = date("Y-m-d H:i:s", time()- $hours_ago * 3600 ); 
		// don't fetch status 7=aborted
		// need type 1=import, 3=flatten, 4=bulkupload and 6=download
		// select status = 5 as well , but don't include in problematic count
	    $query = "select job_type , status , UNIX_TIMESTAMP(min(created_at)) as oldest , UNIX_TIMESTAMP(max(created_at)) as newest , count(1) as cnt " .
	    	"from batch_job where status in (0,1,2,3,4,6 , 5) and job_type in (1,3,4,6) and created_at>\"$from_date\" " . 
	    	"group by job_type,status";
		
		
		$statement = $connection->prepareStatement($query);
		$resultset = $statement->executeQuery();	

		$db_job_stats = array();
		while ($resultset->next())
	    {
	    	$job_type = $resultset->getInt('job_type');
	    	$status = $resultset->getInt('status');
//	    	$oldest = $resultset->getTimestamp('oldest');
//	    	$newest = $resultset->getTimestamp('newest');
			$oldest = $resultset->getInt('oldest');
	    	$newest = $resultset->getInt('newest');
	    	$count = $resultset->getInt('cnt');

	    	if ( !isset ( $db_job_stats[$job_type] ) )
	    	{
	    		$job_type_data = array();
	    	}
	    	else
	    	{
	    		$job_type_data =  $db_job_stats[$job_type];
	    	}
	    	
	    	// foreach job_type - creaet an array per status
	    	$job_type_data [$status] = array ( "oldest" => $oldest , "newest" => $newest , "count" => $count ) ;
	    	$db_job_stats[$job_type] = $job_type_data;
	    }
		return $db_job_stats;
				
	}
	
	// TODO - gets all stats from db and organizes them in a matrix 
	private function getConversionStats ( $hours_ago = 24 )
	{
		$connection = Propel::getConnection();
		$from_date = date("Y-m-d H:i:s", time()- $hours_ago * 3600 ); 
		// don't fetch status 7=aborted
		// need type 1=import, 3=flatten, 4=bulkupload and 6=download
		// select status = 5 as well , but don't include in problematic count
	    $query = "select status , UNIX_TIMESTAMP(min(created_at)) as oldest , UNIX_TIMESTAMP(max(created_at)) as newest , count(1) as cnt " .
	    	"from conversion where status in (-1,1,2) and created_at>\"$from_date\" " . 
	    	"group by status";
		
		
		$statement = $connection->prepareStatement($query);
		$resultset = $statement->executeQuery();	

		$db_convert_stats = array();
		while ($resultset->next())
	    {
	    	$job_type = BatchJob::BATCHJOB_TYPE_CONVERT;
	    	$status = $resultset->getInt('status');
			$oldest = $resultset->getInt('oldest');
	    	$newest = $resultset->getInt('newest');
	    	$count = $resultset->getInt('cnt');

	    	if ( !isset ( $db_convert_stats[$job_type] ) )
	    	{
	    		$conversion_data = array();
	    	}
	    	else
	    	{
	    		$conversion_data =  $db_convert_stats[$job_type];
	    	}
	    	
	    	switch ($status)
	    	{
		    	case conversion::CONVERSION_STATUS_ERROR: 
		    		$status = BatchJob::BATCHJOB_STATUS_FAILED;
		    		break;
		    	case conversion::CONVERSION_STATUS_PRECONVERT: 
		    		$status = BatchJob::BATCHJOB_STATUS_PROCESSING;
		    		break;
		    	case conversion::CONVERSION_STATUS_COMPLETED: 
		    		$status = BatchJob::BATCHJOB_STATUS_FINISHED;
		    		break;
	    	}	
	    	
	    	// foreach job_type - creaet an array per status
	    	$conversion_data [$status] = array ( "oldest" => $oldest , "newest" => $newest , "count" => $count ) ;
	    	$db_convert_stats[$job_type] = $conversion_data;
	    }
		return $db_convert_stats;
	}
	
	
	
	private function getLogData ( $log_name )
	{
		if ( $this->log_info )
		{
			foreach ( $this->log_info as $line )
			{	
				if ( ! $line ) continue;
//				echo "[$line]<br>";
				list ( $log_size , $log_timestamp , $file_name ) = explode ( " " , $line );
				if ( strpos ( $file_name , $log_name ) > 0 )
					return array ( "*" . $file_name , (int)$log_timestamp , $log_size );
			}
		}
		
		
		$file_name = null;
		$log_timestamp = "";
		$log_size = "0";
	
		if ( $log_name )
		{
			$log_dir = myContentStorage::getFSContentRootPath() . "/logs/";
			$pat = $log_dir . "/*$log_name*";
//echo "[$pat]<br>";			
			$possible_log_files = glob ( $pat );

			// iterate through all the files - the last (and most updated)  will be taken
			// TODO - the above didn't work - stopped at the first log - SHOUD FIX
			foreach ( $possible_log_files as $file )
			{
				$file_name = realpath ( $file );
				clearstatcache();//true , $file_name );				
				$log_timestamp = filemtime ( $file_name );
				$log_size = filesize( $file_name );
				break;
			}
		}	
		
		if ( ! $file_name ) $file_name =  $log_name;
		
		return array ( $file_name , $log_timestamp , $log_size );
	}
}
?>