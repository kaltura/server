<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */

chdir(dirname( __FILE__ ) . "/../../");

require_once("bootstrap.php");

$iniDir = dirname ( __FILE__ ) . "/../configurations/batch";
 
$kdebuger = new KGenericDebuger($iniDir);
$kdebuger->run('KAsyncStorageDelete');
