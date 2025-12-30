<?php
/**
 * Run KAsyncBulkUpdateUserEntry
 *
 * @package Scheduler
 * @subpackage BulkUpdateUserEntry
 */

require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncBulkUpdateUserEntry();
$instance->run();
$instance->done();
