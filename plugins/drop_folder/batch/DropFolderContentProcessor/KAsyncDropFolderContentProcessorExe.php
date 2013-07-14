<?php

require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncDropFolderContentProcessor
 * 
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */

$instance = new KAsyncDropFolderContentProcessor();
$instance->run(); 
$instance->done();
