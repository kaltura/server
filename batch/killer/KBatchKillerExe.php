<?php
require_once("bootstrap.php");

/**
 * Will run KBatchKiller
 * 
 * @package Scheduler
 * @subpackage Monitor
 */

$config = unserialize(base64_decode($argv[1]));

$instance = new KBatchKiller($config);
$instance->run(); 

