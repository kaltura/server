<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncImportMetadata
 * 
 * @package Scheduler
 * @subpackage Metadata.Import
 */

$instance = new KAsyncImportMetadata();
$instance->run(); 
$instance->done();
?>