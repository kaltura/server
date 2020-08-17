<?php
/**
 * Will run the KAsyncReachJobCleaner
 *
 * @package Scheduler
 * @subpackage ReachJobCleaner
 */
require_once(__DIR__ . '/../../bootstrap.php');

$instance = new KAsyncReachJobCleaner();
$instance->run();
$instance->done();
