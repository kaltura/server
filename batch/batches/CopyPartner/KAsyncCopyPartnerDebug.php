<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
chdir(dirname( __FILE__ ) . "/../../");
require_once(__DIR__ . "/../../bootstrap.php");

$iniDir = "../configurations/batch";		// should be the full file path

try
{
	$kdebuger = new KGenericDebuger($iniDir);
	$kdebuger->run('KAsyncCopyPartner');
}
catch ( Exception $e )
{
	echo $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString(); 
}