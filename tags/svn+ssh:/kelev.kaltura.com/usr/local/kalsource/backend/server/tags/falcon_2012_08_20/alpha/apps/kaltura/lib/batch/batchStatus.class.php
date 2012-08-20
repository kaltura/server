<?php
class batchStatus
{
	public $batch_name = null;
	public $in_path = null;
	public $pending = 0;
	public $in_proc = 0;
	public $errors = null;
	public $last_log_time = null;
	public $oldest = null;
	public $newest = null; 
	public $succeedded_in_period;
	public $failed_in_period;
	
	private $running_os = '';
	
	private $pending_data = array();
	private $in_proc_data = array();
	
	private $db_job_stats = null;
	
	private static $proc_res = null;
	
	private static $batch_names = array();
	
	function __construct()
	{
		$this->running_os = $this->getPlatform();
	}
	
	private static function getBatchNameFromCommandLine ( $batch_cmd_line )
	{
		$batch_name = $batch_cmd_line;
		// crack and find the name of the process from the command line
		$tokens = explode ( " " , $batch_cmd_line );
		foreach ( $tokens as $token )
		{
			// if found the word batch - use it
			if ( stripos ( $token , "batch" ) !== FALSE ) 
			{
				$batch_name = $token;
				break;
			}
		}		
		
		return pathinfo( $batch_name , PATHINFO_FILENAME );
	}
	
	public static function cleanup ( )
	{
		foreach ( self::$batch_names as $batch_name )
		{
			self::batchEnd ( $batch_cmd_line );	
		}
	}
	
	public static function batchStart( $batch_cmd_line ) 
	{
		$batch_name = self::getBatchNameFromCommandLine( $batch_cmd_line );
		$path = myContentStorage::getFSContentRootPath() . "/batchwatch/_$batch_name";
		touch ( $path );
		
		self::$batch_names[] = $batch_name;
		register_shutdown_function(array("batchStatus", "cleanup"));
		return $path;
	}

	
	public static function batchEnd( $batch_cmd_line ) 
	{
		$batch_name = self::getBatchNameFromCommandLine( $batch_cmd_line );
		$path = myContentStorage::getFSContentRootPath() . "/batchwatch/_$batch_name";
		@unlink ( $path );
		return $path;
	}
	
	public static function getPlatform()
	{
		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			$os = (substr_count($_SERVER['SERVER_SOFTWARE'], 'Win32'))? 'win': 'nix';
		}
		else
		{
			$os = (substr_count(strtolower(@$_SERVER['OS']), 'windows'))? 'win': 'nix';
		}
		return $os;
	}
	
	public function getProcessStatusByPid($pid)
	{
		if ($this->running_os == 'win')
		{
		    $command = 'tasklist /FI "PID eq '.$pid.'"';
		}
		if ($this->running_os == 'nix')
		{
		    $command = 'ps aux | grep '.$pid.' |grep -v grep';
		}
		exec($command, $output, $error);
		$output = trim(implode("\n",$output));
		if ($error || !$output)
		{
			return 0;
		}
		else
		{
			return 1;
		}
		
	}
	
	public static function getIndicatorFilesForBatch($batch_name)
	{
		$files_in_dir = scandir(myBatchBase::getBatchwatchPath());
		$batch_indicators = array();
		foreach($files_in_dir as $ind)
		{
		    if (substr_count($ind, $batch_name.'.running'))
		    {
			$batch_indicators[] = $ind;
		    }
		}
		return $batch_indicators;
	}
	
	public function getStatus()
	{
		/* status check according to running.pid filename and platform relevant command */
		$batch_name = self::getBatchNameFromCommandLine( $this->batch_name );
		$running = 0;
		$batch_indicators = self::getIndicatorFilesForBatch($batch_name);
		foreach($batch_indicators as $ind)
		{
			$filename_parts = explode('.', $ind);
			if ($this->getProcessStatusByPid($filename_parts[2]))
			{
				$running++;
			}
			else
			{
				// indicator exists, process doesn't
				//echo "i should unlink now: ".$ind;
				unlink(myBatchBase::getBatchwatchPath().$ind);
			}
		}
		return $running;
	}
	
	public function getName( $name_only = true )
	{
		if ( $name_only )  
			return self::getBatchNameFromCommandLine( $this->batch_name );
		return $this->batch_name;
	}

	public static function getPhpPath()
	{
		$kaltura_env_path = myContentStorage::getFSContentRootPath() .  '/kaltura/alpha/batch/kaltura_env.sh';
		$kaltura_env = file_get_contents($kaltura_env_path);
		$kaltura_env_lines = explode("\n", $kaltura_env);
		foreach($kaltura_env_lines as $line)
		{
			// find PHP_PATH and return the path
			if(substr_count($line, 'PHP_PATH'))
			{
				$parts = explode('=', $line);
				return $parts[1];
			}
		}
		throw("could not find php path");
	}
	public static function executeRunBatch ( $action , $batch_name )
	{
		$output = array();
		$error = "";
		$batch_runner_log = myContentStorage::getFSContentRootPath().'/logs/'.php_uname('n').'-batchRunner.log';
		//$command = myContentStorage::getFSContentRootPath() .  '/kaltura/alpha/batch/runBatch.sh ' . $action . " " . $batch_name;
		$runbatch_path = myContentStorage::getFSContentRootPath() .'/kaltura/alpha/batch/runBatch.php';
		if (self::getPlatform() == 'win')
		{
			$args = '"'.$runbatch_path.'" "'. $action .'" "'. $batch_name .'"';
			pclose(popen("start \"kaltura_batchrunner\" \"" . self::getPhpPath() . "\" " . $args, "r"));
		}
		else
		{
			$args = $runbatch_path.' '. $action .' '. $batch_name.' >> '.$batch_runner_log.' &';
			pclose(popen(self::getPhpPath() . " " . $args, "r"));
		}
//		exec($command, $output, $error);
	}
	
	/*
	 * When creating a batchWatch command do 2 things:
	 * 	1. create the batchwatch/ file for the batchWatch to handle
	 *  2. execute the same command line the batchwatch would only in the scope of the web
	 * 
	 * the 2 together will make sure the processes will really stop/start/restart
	 */
	private function createBatchwatchCmd ( $cmd )
	{
		// use this with no prefix
		$batch_name = self::getBatchNameFromCommandLine( $this->batch_name );
		$path = myContentStorage::getFSContentRootPath() . "/batchwatch/$batch_name";

		self::executeRunBatch ( $cmd , $batch_name );

		if ( $cmd == "stop")
		{
			$path = batchStatus::batchEnd( $batch_name );
		}
						
		return $path;		
	}
	
	public function start()
	{
		if (!$this->getStatus())
		{
			return $this->createBatchwatchCmd( "start" );
		}
		return 1;
	}
	
	public function stop()
	{
		if($this->getStatus())
		{
			return $this->createBatchwatchCmd( "stop" );
		}
		return 1;
	}
	
	public function restart()
	{
		return $this->createBatchwatchCmd( "restart" );
	}
	
	public function addToPending ( $title , $count )
	{
		if ( ! $count ) $count = 0;
		$this->pending_data[$title] = $count ;
		$this->pending += $count ;
	}

	public function getPendingData()
	{
		if ( $this->pending_data )
			return $this->pending_data;
		return null;	
	}
	
	public function addToInProc ( $title , $count )
	{
		if ( ! $count ) $count = 0;
		$this->in_proc_data[$title] = $count ;
		$this->in_proc += $count ;
	}
	
	public function getInProcData()
	{
		if ( $this->in_proc_data ) 
			return $this->in_proc_data;
		return null;
	}
	
	public function getLogData ( $log_name )
	{
		$file_name = null;
		$log_timestamp = "";
		$log_size = "0";
	
		if ( $log_name )
		{
			$log_dir = myContentStorage::getFSContentRootPath() . "/logs/";
			$pat = $log_dir . "/*$log_name*";
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
	
	public function getDiskStatsCount ( $log_name , $dir , $pattern = null )
	{
//print ( "getDiskStatsCount ( $log_name , $dir , $pattern = null )<br>")	;	
		$oldest = null;
		$newest = 0;
		$path = realpath ( $dir  ) . ( $pattern ? "/$pattern" : "/*" );
		$files = glob($path);
		$count = count ( $files );
		return $count;
	}
	
	public function getDiskStats ( $log_name , $dir , $pattern = null )
	{
		$oldest = null;
		$newest = 0;
		$path = realpath ( $dir  ) . ( $pattern ? "/$pattern" : "/*" );
		$files = glob($path);
		$count = count ( $files );
		foreach ( $files as $file )
		{
			$timestamp =  filemtime ( $file );
			if ( $oldest == null || $timestamp < $oldest  ) $oldest = $timestamp;
			if ( $timestamp > $newest ) $newest = $timestamp;
		}

		if ( $oldest > $newest ) $oldest = $newest;
		return array ( 
			"service_name" => $log_name ,  // all log_names are called after their services
			"path" => $path , 
			"count" => $count , 
			"oldest" => $oldest , 
			"newest" => $newest , 
		 );
	}	

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
		
	public function getDbStats ( $log_name , $job_type , $successful_stats_enum = BatchJob::BATCHJOB_STATUS_FINISHED , 
		$failed_stats_enum = BatchJob::BATCHJOB_STATUS_FAILED )
	{
		if ( $this->db_job_stats == null )
		{
			  $this->db_job_stats = $this->getJobStats ( 2400 );
		}
		
		$oldest = null;
		$newest = 0;
		$count = 0;
		$stats_for_job = @$this->db_job_stats[$job_type];
		$successful_stats = null;
		$failed_stats = null;
		if ( $stats_for_job )
		{
			foreach ( $stats_for_job as $status => $stats_for_job_per_status )
			{
				if ( $status == $successful_stats_enum )
				{
					 $successful_stats = $stats_for_job_per_status;
				}
				elseif ( $status == $failed_stats_enum )
				{
					$failed_stats = $stats_for_job_per_status;
				}
				else
				{
					if ( $oldest == null || $stats_for_job_per_status["oldest"] < $oldest  ) 
						$oldest = $stats_for_job_per_status["oldest"];
					if ( $stats_for_job_per_status["newest"] > $newest ) $newest = $stats_for_job_per_status["newest"];
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
			"failed_stats" => $failed_stats , 
			"log_name" => $file_name  , 
			"log_timestamp" => $log_timestamp , 
			"log_size" => $log_size ); 		
	}
		
}
?>