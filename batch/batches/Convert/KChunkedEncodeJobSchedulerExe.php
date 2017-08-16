<?php
/**
 * Will run KChunkedEncodeJobScheduler
 *
 * @package Scheduler
 * @subpackage ChunkedEncode
 */
//require_once(__DIR__ . "/../../bootstrap.php");
require_once("/opt/kaltura/app/batch/bootstrap.php");

				/********************************************************
				 * The bellow includes to be removed for production
				 ********************************************************
				 */
	if (class_exists('KChunkedEncodeJobScheduler')) {
		require_once "/opt/kaltura/app/batch/client/KalturaTypes.php";

		require_once "KChunkedEncodeUtils.php";
		require_once "KChunkedEncode.php";
		require_once "KBaseChunkedEncodeSessionManager.php";
		require_once "KChunkedEncodeSessionManager.php";

		require_once "KChunkedEncodeDistrExecInterface.php";
		require_once "KChunkedEncodeMemcacheWrap.php";
	}

$instance = new KChunkedEncodeJobScheduler();
$instance->run();
$instance->done();

