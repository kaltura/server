<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncBulkDownloadCloser
 *
 * @package Scheduler
 * @subpackage Bulk-Download
 */

$instance = new KAsyncBulkDownloadCloser();
$instance->run(); 
$instance->done();
?>