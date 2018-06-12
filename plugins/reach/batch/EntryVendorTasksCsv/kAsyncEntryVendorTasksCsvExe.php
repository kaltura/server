<?php

require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncEntryVendorTasksCsv
 *
 * @package plugins.reach
 * @subpackage EntryVendorTasks-Csv
 */

$instance = new KAsyncEntryVendorTasksCsv();
$instance->run();
$instance->done();