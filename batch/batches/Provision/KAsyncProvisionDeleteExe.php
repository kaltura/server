<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncProvisionDelete
 *
 * @package Scheduler
 * @subpackage Provision
 */

$instance = new KAsyncProvisionDelete();
$instance->run(); 
$instance->done();
?>