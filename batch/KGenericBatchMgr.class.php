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

// Make sure no other scheduler is running on this machine
$lock_socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create lock socket\n");
socket_bind($lock_socket, "127.0.0.1", 31212) or die("Could not bind to lock socket\n");

$phpPath = 'php';
if(isset($argc) && $argc > 1)
{
	$phpPath = $argv[1];
}
else if(isset($_SERVER['PHP_PEAR_PHP_BIN']))
{
	$phpPath = $_SERVER['PHP_PEAR_PHP_BIN'];
}
	
$iniFile = dirname ( __FILE__ ) . "/../configurations/batch.ini";		// should be the full file path

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
