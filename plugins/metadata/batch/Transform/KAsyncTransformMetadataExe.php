<?php
require_once("bootstrap.php");

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