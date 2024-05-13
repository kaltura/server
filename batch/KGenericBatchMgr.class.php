<?php
/**
 *
 * @package Scheduler
 */

if(strtoupper(PHP_SAPI) != 'CLI' && strtoupper(PHP_SAPI) != 'CGI-FCGI')
{
	echo 'This script must be executed using CLI.';
	exit (1);
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

$iniDir = dirname ( __FILE__ ) . "/../configurations/batch";		// should be the full file path

if(isset($argc) && $argc > 2)
{
	$iniDir = $argv[2];
}

if(!file_exists($iniDir))
{
	die("Configuration file [$iniDir] not found.");
}

require_once(__DIR__ . "/bootstrap_scheduler.php");
$kscheduler = new KGenericScheduler($phpPath, $iniDir);

function gracefulShutDown($signal)
{
	global $kscheduler;
	echo "Got signal [$signal] from terminal";
	$kscheduler->preKill($signal);
}

function shutDown($signal)
{
	global $kscheduler;
	echo "Got signal [$signal] from terminal";
	exit(0);
}

function deRegisterHost($signal)
{
	global $kscheduler;
	echo "Got signal [$signal] from terminal";
	$kscheduler->deRegisterHost($signal);
}

//Windows machines by default do not have the pcntl installed, so check if function exists before calling it
if(function_exists("pcntl_signal"))
{
	pcntl_signal(SIGINT, 'gracefulShutDown');
	pcntl_signal(SIGTERM, 'shutDown');
	pcntl_signal(SIGUSR1, 'deRegisterHost');
}

$kscheduler->run();
