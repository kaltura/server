<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDropFolderHandler
 * 
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */

$instance = new KAsyncDropFolderHandler();
$instance->run(); 
$instance->done();
