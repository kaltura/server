<?php

require_once("bootstrap.php");

/**
 * Executes the KAsyncWidevineRepositorySync
 * 
 * @package plugins.widevine
 * @subpackage Scheduler
 */

$instance = new KAsyncWidevineRepositorySync();
$instance->run(); 
$instance->done();
