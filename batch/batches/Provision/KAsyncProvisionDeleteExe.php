<?php
/**
 * Will run KAsyncProvisionDelete
 *
 * @package Scheduler
 * @subpackage Provision
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncProvisionDelete();
$instance->run(); 
$instance->done();
