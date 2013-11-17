<?php
//ob_implicit_flush();

if(strtoupper(PHP_SAPI) != 'CLI' && strtoupper(PHP_SAPI) != 'CGI-FCGI')
{
	echo 'This script must be executed using CLI.';
	exit (1);
}

$phpPath = 'php';
if(isset($argv[2]))
{
	$phpPath = $argv[2];
}
else if(isset($_SERVER['PHP_PEAR_PHP_BIN']))
{
	$phpPath = $_SERVER['PHP_PEAR_PHP_BIN'];
}

$iniDir = realpath(__DIR__ . '\\..\\..\\configurations\\batch');		// should be the full file path

if(isset($argv[3]))
{
	$iniDir = $argv[3];
}

if(!file_exists($iniDir))
{
	die("Configuration file [$iniDir] not found.");
}

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap_scheduler.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'win-service-constants.php');

$serviceName = 'kaltura_batch';

//Windows Service Control 
$serviceAction = 'status';

if(isset($argv) and isset($argv[1]) and strlen($argv[1]))
{
	$serviceAction = $argv[1];
}

$systemConfig = parse_ini_file(realpath(__DIR__ . '\\..\\..\\configurations\\system.ini'));
$pid = $systemConfig['LOG_DIR'] . '\\batch\\batch.pid';

switch($serviceAction)
{
	case 'status':
		$ServiceStatus = win32_query_service_status($serviceName);
		if($ServiceStatus['CurrentState'] == WIN32_SERVICE_STOPPED)
		{
			KalturaLog::info('Service Stopped');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_START_PENDING)
		{
			KalturaLog::info('Service Start Pending');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_STOP_PENDING)
		{
			KalturaLog::info('Service Stop Pending');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_RUNNING)
		{
			KalturaLog::info('Service Running');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_CONTINUE_PENDING)
		{
			KalturaLog::info('Service Continue Pending');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_PAUSE_PENDING)
		{
			KalturaLog::info('Service Pause Pending');
		}
		else if($ServiceStatus['CurrentState'] == WIN32_SERVICE_PAUSED)
		{
			KalturaLog::info('Service Paused');
		}
		else
		{
			KalturaLog::info('Service Status Unknown');
		}
		exit(0);
		
	case 'install':
		win32_create_service(array(
			'service' => $serviceName, 
			'display' => 'Kaltura asynchronous batch jobs scheduler',
			'description' => 'Kaltura asynchronous batch jobs scheduler', 
			'params' => __FILE__ . " run $phpPath $iniDir", 
			'path' => $phpPath,
			'start_type' => WIN32_SERVICE_AUTO_START,
			'error_control' => WIN32_SERVER_ERROR_NORMAL,
		));
		KalturaLog::info('Service Installed');
		exit(0);
		
	case 'uninstall':
		win32_delete_service($serviceName);
		KalturaLog::info('Service Removed');
		exit(0);
		
	case 'start': 
		win32_start_service($serviceName);
		KalturaLog::info('Service Started');
		exit(0);
		
	case 'stop': 
		win32_stop_service($serviceName);
		KalturaLog::info('Service Stopped');
		exit(0);
		
	case 'run':
		win32_start_service_ctrl_dispatcher($serviceName);
		win32_set_service_status(WIN32_SERVICE_RUNNING);
		break;
		
	case 'debug': 
		set_time_limit(10);
		break;
		
	default:
		KalturaLog::info('Unkown action');
		exit(-1);
}

$kscheduler = new KGenericScheduler($phpPath, $iniDir);
while(1)
{
	//Handle Windows Service Request 
	if($serviceAction == 'run')
	{
		switch(win32_get_last_control_message())
		{
			case WIN32_SERVICE_CONTROL_CONTINUE:
				break;
				
			case WIN32_SERVICE_CONTROL_INTERROGATE:
				win32_set_service_status(WIN32_NO_ERROR);
				break;
				
			case WIN32_SERVICE_CONTROL_STOP:
				KalturaLog::info('Service stopped gracefully');
				if(file_exists($pid))
					unlink($pid);
					
				win32_set_service_status(WIN32_SERVICE_STOPPED);
				exit(0);
				
			default:
				win32_set_service_status(WIN32_ERROR_CALL_NOT_IMPLEMENTED);
		}
	}
	
	$kscheduler->loop();
}

//Exit 
if($serviceAction == 'run')
{		
	win32_set_service_status(WIN32_SERVICE_STOPPED);
}

exit(0);
