<?php
/**
 * Will run the KAsyncPartnerLoadCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once("bootstrap.php");

$instance = new KAsyncPartnerLoadCleanup ( );
$instance->run(); 
$instance->done();
