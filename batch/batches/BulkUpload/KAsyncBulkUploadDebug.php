<?php

/**
 * @package Scheduler
 * @subpackage Debug
 */
if(strtoupper(PHP_SAPI) != 'CGI-FCGI')
{
	echo 'This script must be executed using CLI';
	exit;
}

chdir(dirname( __FILE__ ) . "/../../");

require_once("bootstrap.php");

$iniFile = "batch_config.ini";		// should be the full file path

$kdebuger = new KGenericDebuger($iniFile);
$kdebuger->run('KAsyncBulkUpload');

?>