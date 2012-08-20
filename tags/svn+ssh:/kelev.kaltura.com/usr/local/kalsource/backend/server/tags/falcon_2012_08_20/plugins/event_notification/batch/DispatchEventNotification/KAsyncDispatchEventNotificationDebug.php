<?php
/**
 * @package plugins.eventNotification
 * @subpackage Scheduler.Debug
 */

// /opt/kaltura/app/batch
chdir(dirname(__FILE__) . "/../../../../batch");

require_once ("bootstrap.php");

$iniFile = realpath(dirname(__FILE__) . "/../../../../configurations/batch.ini");

$kdebuger = new KGenericDebuger($iniFile);
$kdebuger->run('KAsyncDispatchEventNotification');
