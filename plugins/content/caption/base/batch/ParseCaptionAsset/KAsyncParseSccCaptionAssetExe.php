<?php
require_once(__DIR__ . "/../../../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncParseSccCaptionAssetExe
 * 
 * @package plugins.caption
 * @subpackage Scheduler
 */

$instance = new KAsyncParseSccCaptionAsset();
$instance->run(); 
$instance->done();
