<?php
//set_error_handler('errorHandler');

function errorHandler( $errno, $errstr, $errfile, $errline, $errcontext)
{
  echo 'Into '.__FUNCTION__.'() at line '.__LINE__.
  "\n\n---ERRNO---\n". print_r( $errno, true).
  "\n\n---ERRSTR---\n". print_r( $errstr, true).
  "\n\n---ERRFILE---\n". print_r( $errfile, true).
  "\n\n---ERRLINE---\n". print_r( $errline, true).
  "\n\n---ERRCONTEXT---\n".print_r( $errcontext, true).
  "\n\nBacktrace of errorHandler()\n".
  print_r( debug_backtrace(), true);
}
/*
 * Created on Nov 25, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/batch/myBatchFileConverter.class.php');

//if ($argc != 2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
if ( $argc > 1 )
{
	$server_id = $argv[1];
}
else
{
	$server_id = 1;
}

echo ("Starting BatchFileConverterServer with id [$server_id]\n" );
$start_time = microtime(true);
myBatchFileConverterServer::convert( $server_id );
$end_time = microtime ( true );
$diff = (int)(( $end_time - $start_time ) * 1000);
echo ( "****************\nEndes after " . $diff . " millisecond \n****************" );
?>
