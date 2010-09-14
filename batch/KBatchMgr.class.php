<?php
/**
 *  
 * @package Scheduler
 */

require_once("bootstrap.php");

$iniFile = dirname ( __FILE__ ) . "/batch_config.ini";		// should be the full file path
KalturaLog::info("Running KBatchMgr");

$phpPath  = $argv[1];
if ( ! $phpPath )
{
	die ( "First argument shoulb be the full path of the php executable" );
}

$kscheduler = new KScheduler( $phpPath , $iniFile , 30 );
$kscheduler->run();
?>