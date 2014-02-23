<?php
/**
 * Executes the KAsyncSyncCategoryPrivacyContext
 * 
 * @package Scheduler
 * @subpackage SyncCategoryPrivacyContext
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncSyncCategoryPrivacyContext();
$instance->run(); 
$instance->done();
