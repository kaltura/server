<?php
/**
 * Will run KAsyncBulkUploadCloser
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once("bootstrap.php");

$instance = new KAsyncBulkUploadCloser();
$instance->run(); 
$instance->done();
