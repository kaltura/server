<?php
require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true );

ini_set( "memory_limit" , "256M" );

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// will execute another php script several times
$prog_to_execute=$argv[1];
$log_file=$argv[2];
@$action=$argv[3];
$g_context = "batchRunner";

//$batch_path = "/web/kaltura/alpha/batch/";
$batch_path = "";

$prog_to_execute = $batch_path . $prog_to_execute;
// write the output to the log file
$exec_cmd = $log_file ? "$prog_to_execute >> $log_file" : $prog_to_execute ;

if ( $action == "stop" )
{
	TRACE ( "------------------------ Removing indicator for [$prog_to_execute] ------------------------");
	$path = batchStatus::batchEnd( $prog_to_execute );
	TRACE ( "------------------------ Removed [$path] ------------------------" );
	die();
}

TRACE ( "------------------------ Executing [$exec_cmd] ------------------------");
$return_value = "";

$count = 1;
$max_count = 50;
while ( true )
{
	if ( $count > $max_count ) 
	{
		TRACE ( "Exceeded number of executions [$max_count]. die!" );
		die();
	}
	TRACE ( "------------------------ Running ($count) [$prog_to_execute] ------------------------" );
$path = batchStatus::batchStart( $prog_to_execute );
TRACE ( "Set batch indicator in [$path]");
	exec ( $exec_cmd , $output , $return_value );
$path = batchStatus::batchEnd( $prog_to_execute );
TRACE ( "Removed batch indicator in [$path]");
	TRACE ( "($count) [$prog_to_execute]\n" . print_r ( $output , true ) . print_r ( $return_value , true ) );
	TRACE ( "------------------------ Ended ($count) [$prog_to_execute] ------------------------" );
	$count++;
}

function TRACE ( $str )
{
	global $g_context;
	$time = ( microtime(true) );
	$milliseconds = (int)(($time - (int)$time) * 1000);  
	if ( function_exists('memory_get_usage') )
		$mem_usage = "{". memory_get_usage(true) . "}";
	else
		$mem_usage = ""; 
	echo strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds . " " . $mem_usage . " " .$g_context . ": " . $str ."\n";
}
?>