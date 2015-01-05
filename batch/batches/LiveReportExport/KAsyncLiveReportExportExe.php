<?php
/**
 * Will run KAsyncLiveReportExport
 *
 * @package Scheduler
 * @subpackage LiveReportExport
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncLiveReportExport();
$instance->run(); 
$instance->done();
