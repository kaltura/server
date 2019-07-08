<?php
require_once(__DIR__ . "/../../../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncConvertCaptionAssetExe
 * 
 * @package plugins.caption
 * @subpackage Scheduler
 */

$instance = new KAsyncConvertCaptionAsset();
$instance->run(); 
$instance->done();
