<?php

/**
 * @package Scheduler
 */

require_once("bootstrap.php");
$g_context = null;

function TRACE ( $obj )
{
	global $g_context;
	
	if ( is_string( $obj ))
		$str = $obj;
	else
		$str = print_r ( $obj ,  true );
	
	$time = ( microtime(true) );
	$milliseconds = (int)(($time - (int)$time) * 1000);  
	if ( function_exists('memory_get_usage') )
		$mem_usage = "{". memory_get_usage(true) . "}";
	else
		$mem_usage = ""; 
	echo $g_context . ":" . strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds . " " . $mem_usage . ": " . $str ."\n";
}

/**
 * 
 * @package Scheduler
 * @abstract
 */
abstract class KRunableClass
{
	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig;
	
	/**
	 * @var string
	 */
	protected $sessionKey;
	
	/**
	 * @var int timestamp
	 */
	private $start;
	
	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct(KSchedularTaskConfig $taskConfig = null) 
	{
		/*
		 *  argv[0] - the script name
		 *  argv[1] - serialized KSchedulerConfig config
		 */
		global $argv, $g_context;

		$this->sessionKey = uniqid('sess');		
		$this->start = microtime(true);
		
		if(is_null($taskConfig))
		{
			$this->taskConfig = unserialize(base64_decode($argv[1]));
		}
		else
		{
			$this->taskConfig = $taskConfig;
		}
		
		if(!$this->taskConfig)
			die("Task config not supplied");
		
		date_default_timezone_set($this->taskConfig->getTimezone());
		
		// clear seperator between executions
		KalturaLog::debug('___________________________________________________________________________________');
		KalturaLog::info(file_get_contents(dirname( __FILE__ ) . "/../VERSION.txt"));
		
		if(! ($this->taskConfig instanceof KSchedularTaskConfig))
		{
			KalturaLog::err('config is not a KSchedularTaskConfig');
			die;
		}
		 
		KalturaLog::debug("set_time_limit({$this->taskConfig->maximumExecutionTime})");
		set_time_limit($this->taskConfig->maximumExecutionTime);
	}
	
	protected function getParams ( $name  )
	{
		return  $this->taskConfig->$name;
	}
	
	protected function getAdditionalParams ( $name  )
	{
		return  $this->taskConfig->params->$name;
	}
	
	/**
	 * @abstract
	 */
	abstract public function run() ; 
	
	public function done()
	{
		KalturaLog::info("Done after [" . (microtime ( true ) - $this->start ) . "] seconds");
	}
	
	
	public function read_data() {
		$in = fopen("php://stdin", "r");
//		set_timeout();
		$in_string = fgets($in, 255);
//		clear_timeout();
		fclose($in);
		return $in_string;
	}
	
	protected function shouldDie()
	{
		return $this->read_data();
//		return false;
	}	
}


?>