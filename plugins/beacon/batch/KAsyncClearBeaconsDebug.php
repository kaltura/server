<?php

/**
 * @package Scheduler
 * @subpackage Debug
 */

chdir(dirname(__FILE__) . "/../../../batch");

require_once(__DIR__ . "/../../../batch/bootstrap.php");

$iniFile = realpath(dirname(__FILE__) . "/../../../configurations/batch");

$kdebuger = new KGenericDebuger($iniFile);
$kdebuger->run('KAsyncClearBeacons');