<?php
/**
 * Will run the KAsyncValidateLiveMediaServers 
 *
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */
require_once (__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncWowme();
$instance->run();
$instance->done();
