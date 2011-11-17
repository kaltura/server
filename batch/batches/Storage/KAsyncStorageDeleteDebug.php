<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */

chdir(dirname( __FILE__ ) . "/../../");

require_once("bootstrap.php");

$iniFile = dirname ( __FILE__ ) . "/../configurations/batch.ini";		// should be the full file path
 
$kdebuger = new KGenericDebuger($iniFile);
$kdebuger->run('KAsyncStorageDelete');
