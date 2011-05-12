<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncImportMetadata
 * 
 * @package plugins.metadata
 * @subpackage Scheduler.Import
 */

$instance = new KAsyncImportMetadata();
$instance->run(); 
$instance->done();
?>