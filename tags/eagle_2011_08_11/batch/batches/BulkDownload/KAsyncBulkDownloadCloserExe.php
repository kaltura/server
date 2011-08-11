<?php
/**
 * Will run KAsyncBulkDownloadCloser
 *
 * @package Scheduler
 * @subpackage Bulk-Download
 */
require_once("bootstrap.php");

$instance = new KAsyncBulkDownloadCloser();
$instance->run(); 
$instance->done();
