<?php

/**
 * Will run the KAsyncSimuLiveClearScheduleEvents
 *
 * @package Scheduler
 * @subpackage SimuLiveClearScheduleEvents
 */
require_once (__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncSimuLiveClearScheduleEvents();
$instance->run();
$instance->done();
