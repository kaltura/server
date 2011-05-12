<?php
require_once( 'myConfigWrapper.class.php');
ini_set( "memory_limit" , "256M" );

$TRACE_INFO = false;
$g_context = "";
function SET_CONTEXT ( $str )
{
	global $g_context;
	$g_context = $str;
}

function TRACE ( $str )
{
	global $g_context;
	if ( $g_context === null ) return ;
	$time = ( microtime(true) );
	$milliseconds = (int)(($time - (int)$time) * 1000);  
	if ( function_exists('memory_get_usage') )
		$mem_usage = "{". memory_get_usage() . "}";
	else
		$mem_usage = ""; 
	echo strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds . " " . $mem_usage . " " .$g_context . ": " . $str ."\n";
}

function INFO ( $str )
{
	global $TRACE_INFO;
	if ( $TRACE_INFO ) TRACE ( $str );
}

abstract class myBatchBase
{
	const SEPARATOR =" \r\n";
	const MAX_FAILURES = 5;
	
	const REGISTERED_BATCHS = "_registered";
	const BATCHWATCH_DIR = "batchwatch"; 
	
	const SECONDS_TO_FORCE_DIE = 300;
	
	protected static $s_pending_tasks = 0;
	
	protected static $s_force_die_time = null;
	
	private static $s_databaseManager;
	private static $s_failure_count = 0;

	protected $m_config;
	
	protected $script_name;
	
	protected static $batch_script_name;
	
	public static function getBatchwatchPath ()
	{
		return myContentStorage::getFSContentRootPath() ."/batchwatch/";
	}
	
	public static function getBatchStatus( $args )	{	return null; }
	
	// will register each batch with all the relevant parameters so will be able to query it for statistics
	protected function register ( $script_name )
	{
		$args = func_get_args() ;
		array_shift ( $args ); // ignore the first arg - always the script_name
		
		$cls = get_class ( $this );
		$script_name = realpath( $script_name);
		
		self::$batch_script_name = $script_name;
		/* easeier process management */
		$batch_script = substr(self::$batch_script_name, strrpos(str_replace("\\", "/", self::$batch_script_name), "/")+1);
		if(kConf::get('kaltura_installation_type') == 'CE')
		{
			/* on CE verify that other batches are not running */
			$stub_status = new batchStatus();
			$indicators= $stub_status->getIndicatorFilesForBatch(str_replace('.php', '', $batch_script));
			foreach($indicators as $ind)
			{
				$parts = explode(".", $ind);
				if ($stub_status->getProcessStatusByPid($parts[2]))
				{
					die("another batch of type $batch_script is already running... exiting.");
				}
			}
		}
		$running_filename = str_replace('.php', '', $batch_script).'.running.'.getmypid();
		$file_path = self::getBatchwatchPath() . "/" . $running_filename;
		file_put_contents( $file_path, date('Y-m-d H:i:s')); // sync - OK

		
		$line = $cls . "," . $script_name . "," . implode ( "," , $args );

		$file_path = self::getBatchwatchPath() . "/" . self::REGISTERED_BATCHS;
		if ( ! file_exists( $file_path ) )
		{
			file_put_contents( $file_path , $line ); // sync - OK
			return;
		}
		
		$content = @file_get_contents( $file_path );
		if ( strpos ( $content , $line ) !== FALSE ) return;

		// add to file only of does not already exists
		$content .= self::SEPARATOR  . $line;
		file_put_contents( $file_path , $content ); // sync - OK
	}
	
	// read from file all registered batchs
	public static function getAllRegistered ( )
	{
		$file_path = self::getBatchwatchPath() . "/" . self::REGISTERED_BATCHS;
		
		if ( ! file_exists( $file_path ) )
		{
			return array();
		}
				
		$all = array();
		$content = @file_get_contents( $file_path );
		$batch_job_info_list = explode ( self::SEPARATOR , $content ); 
		
		foreach ( $batch_job_info_list as $batch_job_info_str )
		{
			if ( trim ( $batch_job_info_str ) == "" ) continue;
			$batch_job_info = explode ( "," , $batch_job_info_str );
			
			$batch_class_name = $batch_job_info[0];
			
//print_r ( $batch_job_info )	;
//print ( "<br>");		
			try
			{
				if ( $batch_class_name )
				{
					//$batch_stats = call_user_func_array ( array ( $batch_class_name , "getBatchStatus" ) , array_shift ( $batch_job_info ) );
					array_shift ( $batch_job_info );
					$fixed_arr = array();
					foreach ( $batch_job_info as $info )
					{
						 $fixed_arr[] = str_replace ( ".php" , "" , $info );
					}
					
					$batch_stats = call_user_func ( array ( $batch_class_name , "getBatchStatus" ) , $fixed_arr );
					$all[] = $batch_stats;
				}
				else
				{
					// very stange - cannot create this class
				}
			}
			catch ( Exception $ex )
			{
				
			}
		}
		
		return $all;
	}
	
	protected static function getConfig ( $prefix )
	{
		return new myConfigWrapper ( $prefix );
	}
	
	protected static function getSleepParams ( $prefix )
	{
		$config =  new myConfigWrapper ( $prefix );
		$sleep_between_cycles = $config->get ( "sleep_between_cycles_sec" , 10) ;
		$number_of_times_to_skip_writing_sleeping = $config->get ( "skip_writing_sleep" ) ;
		return array ($sleep_between_cycles , $number_of_times_to_skip_writing_sleeping );
		
	}	
	
	protected static function initDb( $should_perform_shutdown = false )
	{
		TRACE ( "----------------- Initializing DB ------------------- ");
		if ( self::$s_databaseManager == NULL )
		{
			$dbConf = kConf::getDB();
			DbManager::setConfig($dbConf);
			//self::$s_databaseManager = new sfDatabaseManager();
		}
		if ( $should_perform_shutdown )
		{
			TRACE ( "Attempting shutdown of DB due to errors" );
			// All of this brutal shutdown & init is to release all DB connections and restart as clean as possible
			//
			//self::$s_databaseManager->shutdown();
			//propel::close();
			//propel::initialize();
			DbManager::shutdown();
			DbManager::initialize();
		}
		DbManager::initialize();
		//self::$s_databaseManager->initialize();

	}	
	
	protected static function failed ()
	{
		self::$s_failure_count++;

		TRACE ( "Failed [" . self::$s_failure_count . "] times out of the allowed [" . self::MAX_FAILURES . "]" );
		if ( self::$s_failure_count >= self::MAX_FAILURES )
		{
			TRACE ( "Fatal error, failed too many times [" . self::$s_failure_count . "]" );
			exit ( );
		}
	}

	protected static function succeeded ()
	{
		self::$s_failure_count = 0;
	}
	
	
	protected static function shouldProceed ()
	{
		if ( ! function_exists('memory_get_usage') )
			return true;
		$mem_usage =  memory_get_usage() ;
		$limit =  self::parseMemorySize ( ini_get( "memory_limit") );
//		TRACE ( "shouldProceed: $mem_usage / $limit [" . ( $mem_usage < 0.8 * $limit ) . "]" );
		if  ( $mem_usage > ( 0.8 * $limit ) )
		{
	//		TRACE ( "shouldProceed - NO! memory: [$mem_usage] limit [$limit]" );
			return false;
		}
		
		return true;
		
	} 
	
	private static function parseMemorySize ( $size_str )
	{
		$fixed_str = strtolower( $size_str );
		if ( kString::endsWith( $fixed_str , "m" ) )
			return $size_str * 1024 * 1024;
		if ( kString::endsWith( $fixed_str , "k" ) )
			return $size_str * 1024;
		return $size_str;
	}
	
	protected static function exitIfDone()
	{
		if ( ! self::shouldProceed() ) 
		{
			if ( self::$s_pending_tasks == 0  )
			{
				TRACE ( "Gracefully exiting..." );
				die();
			}
			else 
			{
				if ( self::$s_force_die_time == null )
				{
					// set the force_die_time 
					self::$s_force_die_time = time() + self::SECONDS_TO_FORCE_DIE;
					TRACE ( "Should exis but still exists [" . self::$s_pending_tasks . "] pending tasts ... Will FORCE DIE in [" . 
						self::SECONDS_TO_FORCE_DIE . "] seconds");
				}
				elseif ( time() > self::$s_force_die_time )
				{
					TRACE ( "FORCE DIE !!. There are still [" . self::$s_pending_tasks . "] pending tasts but their time has come !" );
					die();
				}
				else
				{
					TRACE ( "Should exit but still exists [" . self::$s_pending_tasks . "] pending tasts ... Will FORCE DIE in [" . 
						(time() - self::$s_force_die_time ) . "] seconds");					
				}
			}
		}
		/* easeier process management */
		$batch_script = substr(self::$batch_script_name, strrpos(str_replace("\\", "/", self::$batch_script_name), "/")+1);
		$running_filename = str_replace('.php', '', $batch_script).'.running.'.getmypid();
		$file_path = self::getBatchwatchPath() . "/" . $running_filename;
		file_put_contents( $file_path, date('Y-m-d H:i:s')); // sync - OK
	}
}
?>