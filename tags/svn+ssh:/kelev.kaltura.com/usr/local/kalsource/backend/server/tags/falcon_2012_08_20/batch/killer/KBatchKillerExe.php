<?php
/**
 * Will run KBatchKiller
 * 
 * @package Scheduler
 * @subpackage Monitor
 */
require_once("bootstrap.php");

$config = unserialize(base64_decode($argv[1]));

$instance = new KBatchKiller($config);
$instance->run(); 
