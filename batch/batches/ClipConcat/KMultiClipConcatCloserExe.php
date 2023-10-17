<?php
/**
 * Will run KMultiClipConcatCloser
 *
 * @package Scheduler
 * @subpackage ClipConcat
 */

require_once(__DIR__ . "/../../bootstrap.php");
$instance = new KMultiClipConcatCloser();
$instance->run();
$instance->done();