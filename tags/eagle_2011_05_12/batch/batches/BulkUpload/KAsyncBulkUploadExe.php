<?php
/**
 * Will run KAsyncBulkUpload
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once("bootstrap.php");

$instance = new KAsyncBulkUpload();
$instance->run(); 
$instance->done();
