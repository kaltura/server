<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncDispatchEventNotification
 * 
 * @package plugins.eventNotification
 * @subpackage Scheduler
 */

$instance = new KAsyncDispatchEventNotification();
$instance->run(); 
$instance->done();
