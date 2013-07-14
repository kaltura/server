<?php
/**
 * Will run KAsyncBulkDownloadCloser
 *
 * @package Scheduler
 * @subpackage Bulk-Download
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncBulkDownloadCloser();
$instance->run(); 
$instance->done();
