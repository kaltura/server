<?php
/**
 * Will run the KAsyncSimuLiveAddScheduleEvents
 *
 * @package Scheduler
 * @subpackage SimuLiveAddScheduleEvents
 */
require_once (__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncSimuLiveAddScheduleEvents();
$instance->run();
$instance->done();

