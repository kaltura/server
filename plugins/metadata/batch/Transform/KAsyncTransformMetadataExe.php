<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncTransformMetadata
 * 
 * @package plugins.metadata
 * @subpackage Scheduler.Transform
 */

$instance = new KAsyncTransformMetadata();
$instance->run(); 
$instance->done();
?>