<?php
/**
 * Will run the KAsyncValidateLiveMediaServers 
 *
 * @package Scheduler
 * @subpackage ValidateLiveMediaServers
 */
require_once (__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncValidateLiveMediaServers();
$instance->run();
$instance->done();
