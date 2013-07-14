<?php
/**
 * Will run KAsyncBulkUpload
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncBulkUpload();
$instance->run(); 
$instance->done();
