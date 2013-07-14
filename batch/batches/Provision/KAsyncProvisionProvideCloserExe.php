<?php
/**
 * Will run KAsyncProvisionProvide
 *
 * @package Scheduler
 * @subpackage Provision
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncProvisionProvideCloser();
$instance->run(); 
$instance->done();
