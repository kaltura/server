<?php
/**
 * Will run KChunkedEncodeJobScheduler
 *
 * @package Scheduler
 * @subpackage ChunkedEncode
 */
//require_once(__DIR__ . "/../../bootstrap.php");
require_once("/opt/kaltura/app/batch/bootstrap.php");

$instance = new KChunkedEncodeJobScheduler();
$instance->run();
$instance->done();

