<?php

require_once(__DIR__ . "/../../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncWidevineRepositorySync
 * 
 * @package plugins.widevine
 * @subpackage Scheduler
 */

$instance = new KAsyncWidevineRepositorySync();
$instance->run(); 
$instance->done();
