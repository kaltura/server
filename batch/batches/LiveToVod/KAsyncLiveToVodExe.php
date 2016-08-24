<?php
/**
 * Executes the KAsyncCopyCuePointFromLiveToVod
 * 
 * @package Scheduler
 * @subpackage Copy
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncLiveToVod();
$instance->run(); 
$instance->done();
