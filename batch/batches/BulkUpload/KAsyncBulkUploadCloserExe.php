<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncBulkUploadCloser
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */

$instance = new KAsyncBulkUploadCloser();
$instance->run(); 
$instance->done();
?>