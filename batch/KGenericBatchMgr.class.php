<?php
/**
 *  
 * @package Scheduler
 */

if(strtoupper(PHP_SAPI) != 'CLI' && strtoupper(PHP_SAPI) != 'CGI-FCGI')
{
	echo 'This script must be executed using CLI';
	exit;
}

$phpPath = 'php';
if(isset($argc) && $argc > 1)
{
	$phpPath = $argv[1];
}
else if(isset($_SERVER['PHP_PEAR_PHP_BIN']))
{
	$phpPath = $_SERVER['PHP_PEAR_PHP_BIN'];
}
	
$iniFile = dirname ( __FILE__ ) . "/batch_config.ini";		// should be the full file path

if(isset($argc) && $argc > 2)
{
	$iniFile = $argv[2];
}

if(!file_exists($iniFile))
{
	die("Configuration file [$iniFile] not found.");
}
	
require_once("bootstrap.php");

$kscheduler = new KGenericScheduler($phpPath, $iniFile);
$kscheduler->run();
