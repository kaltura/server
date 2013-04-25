<?php
/**
 * Will run KAsyncWidevineRepositorySyncCloser
 *
 * @package plugins.widevine
 * @subpackage Scheduler
 */
require_once("bootstrap.php");

$instance = new KAsyncWidevineRepositorySyncCloser();
$instance->run(); 
$instance->done();
