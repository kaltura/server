<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncBulkUpload
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */

$instance = new KAsyncBulkUpload();
$instance->run(); 
$instance->done();
?>