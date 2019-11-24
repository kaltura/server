<?php
/**
 * Will run KClipConcatCloser
 *
 * @package Scheduler
 * @subpackage ClipConcat
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KClipConcatCloser();
$instance->run();
$instance->done();
