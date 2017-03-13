<?php

ini_set ( "memory_limit", "256M" );

require_once(__DIR__ . "/../../../../batch/bootstrap.php");

function sigint()
{
	global $instance;
	$instance->preKill();
	exit;
}
pcntl_signal(SIGINT, 'sigint');
pcntl_signal(SIGTERM, 'sigint');

/**
 * Executes the KAsyncDropFolderWatcher
 * 
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */

$instance = new KAsyncDropFolderWatcher();
$instance->run(); 
$instance->done();
