<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */

if(strtoupper(PHP_SAPI) != 'CGI-FCGI' && strtoupper(PHP_SAPI) != 'CLI')
{
	echo 'This script must be executed using CLI';
	exit;
}

chdir(dirname( __FILE__ ) . "/../");

require_once(__DIR__ . "/../bootstrap.php");

$iniDir = "batch_config.ini";		// should be the full file path

$kdebuger = new KGenericDebuger($iniDir);
$kdebuger->run('KScheduleHelper');
