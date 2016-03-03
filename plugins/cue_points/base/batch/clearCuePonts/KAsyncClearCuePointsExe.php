<?php
/**
 * Will run the KAsyncClearCuePoints 
 *
 * @package Scheduler
 * @subpackage ClearCuePoints
 */
require_once(__DIR__ . "/../../../../../batch/bootstrap.php");

$instance = new KAsyncClearCuePoints();
$instance->run();
$instance->done();
