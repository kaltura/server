<?php
/**
 * Executes the KAsyncCopyCuePointFromLiveToVOD
 * 
 * @package Scheduler
 * @subpackage Copy
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncLiveToVOD();
$instance->run(); 
$instance->done();
