<?php
/*****************************
	Change log:
 */
 
/*****************************
 * Includes & Globals
 */
ini_set("memory_limit","512M");

require_once(__DIR__ . "/../../batch/bootstrap.php");
//require_once "/opt/kaltura/app/alpha/scripts/bootstrap.php";

if (!class_exists('KChunkedEncodeSessionManager')) {
require_once "/opt/kaltura/app/batch/client/KalturaTypes.php";
require_once("/opt/kaltura/app/infra/chunkedEncode/KChunkedEncodeUtils.php");
require_once("/opt/kaltura/app/infra/chunkedEncode/KChunkedEncode.php");
require_once("/opt/kaltura/app/infra/chunkedEncode/KChunkedEncodeSessionManager.php");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/********************
	 *
	 */
	function main($argv)
	{
		KalturaLog::log("Started at:".date("Y-m-d H:i:s"));
		KalturaLog::log("args:".print_r($argv,1));

		if(count($argv)<2){
			KalturaLog::log("ERROR: NOT ENOUGH ARGS!");
			exit(-1);
		}

		if(($idx=array_search("-sessionFile", $argv))!==false){
			$sessionFilename = $argv[$idx+1];
			KalturaLog::log("Input session file name:$sessionFilename");
			$sessionManager = KChunkedEncodeSessionManager::LoadFromSessionFile($sessionFilename);
		}
		else {
			$sessionManager = KChunkedEncodeSessionManager::LoadFromCmdLineArgs($argv);
			if($sessionManager->Initialize()!=true){
				$sessionManager->Report();
				KalturaLog::log("Finished at:".date("Y-m-d H:i:s"));
				exit(-1);
			}
		}
		if($sessionManager->Generate()==true){
			copy($sessionManager->chunker->getSessionName(), $sessionManager->chunker->params->output);
			$rv = 0;
		}
		else
			$rv = -1;
		$sessionManager->Report();
		KalturaLog::log("Finished at:".date("Y-m-d H:i:s"));
		exit($rv);
	}
	
/********************
 *
 */

	main($argv);
