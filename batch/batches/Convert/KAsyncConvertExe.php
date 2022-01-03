<?php
/**
 * Will run KAsyncConvert
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

if (function_exists('pcntl_signal'))
{
	pcntl_signal(SIGTERM, 'shutDown');
}

function shutDown($signal)
{
	// Do nothing
	// We need this so that 'system' function which is called to execute the ffmpeg "$return_value" be SIGTERM (15)
}

$instance = new KAsyncConvert();
$instance->run(); 
$instance->done();
