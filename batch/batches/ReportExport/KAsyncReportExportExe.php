<?php
/**
 * Will run KAsyncReportExport
 *
 * @package Scheduler
 * @subpackage ReportExport
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncReportExport();
$instance->run();
$instance->done();