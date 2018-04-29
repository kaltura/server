<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */

chdir(dirname(__FILE__) . "/../../");

require_once(__DIR__ . "/../../../../../batch/bootstrap.php");

$iniDir = dirname(__FILE__) . "/../../../../../configurations/batch"; // should be the full file path


$kdebuger = new KGenericDebuger($iniDir);
$kdebuger->run('KAsyncCopyCuePoints');
