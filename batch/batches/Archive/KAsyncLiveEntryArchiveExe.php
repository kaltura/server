<?php

/**
 * Executes the KAsyncCopyCuePointFromLiveToVod
 *
 * @package Scheduler
 * @subpackage Copy
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncLiveEntryArchive();
$instance->run();
$instance->done();