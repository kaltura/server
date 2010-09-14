<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncTransformMetadata
 * 
 * @package Scheduler
 * @subpackage Metadata.Transform
 */

$instance = new KAsyncTransformMetadata();
$instance->run(); 
$instance->done();
?>