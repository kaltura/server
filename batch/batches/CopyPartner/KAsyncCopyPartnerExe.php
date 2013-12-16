<?php
/**
 * Executes the KAsyncCopyPartner
 * 
 * @package Scheduler
 * @subpackage CopyPartner
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncCopyPartner();
$instance->run(); 
$instance->done();
