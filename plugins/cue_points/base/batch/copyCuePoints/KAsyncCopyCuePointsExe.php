<?php
/**
 * Will run the KAsyncCopyCuePoints
 *
 * @package Scheduler
 * @subpackage copyCuePoints
 */
require_once(__DIR__ . "/../../../../../batch/bootstrap.php");

$instance = new KAsyncCopyCuePoints();
$instance->run();
$instance->done();
