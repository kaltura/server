<?php

/**
 * Executes the KAsyncCopyCaptions
 *
 * @package plugins.caption
 * @subpackage Scheduler
 */
require_once(__DIR__ . "/../../../../../../batch/bootstrap.php");

$instance = new KAsyncCopyCaptions();
$instance->run();
$instance->done();
