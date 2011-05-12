<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../alpha'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

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

function INFO ( $str )
{
	global $TRACE_INFO;
	if ( $TRACE_INFO ) TRACE ( $str );
}

$global_kaltura_memory_limit = "256M";
ini_set("memory_limit",$global_kaltura_memory_limit);

?>