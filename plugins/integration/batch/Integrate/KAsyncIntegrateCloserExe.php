<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncIntegrateCloser
 * 
 * @package plugins.integration
 * @subpackage Scheduler
 */

$instance = new KAsyncIntegrateCloser();
$instance->run(); 
$instance->done();
