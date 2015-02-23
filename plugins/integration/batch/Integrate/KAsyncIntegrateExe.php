<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncIntegrate
 * 
 * @package plugins.integration
 * @subpackage Scheduler
 */

$instance = new KAsyncIntegrate();
$instance->run(); 
$instance->done();
