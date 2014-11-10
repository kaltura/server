<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationEngineFfmpeg  extends KSingleOutputOperationEngine
{
	protected function getCmdLine()
	{
		$cmdLine=parent::getCmdLine();
		$cmdLine=KConversionEngineFfmpeg::expandForcedKeyframesParams($cmdLine);

		$wmStr = strstr($this->operator->params, "watermark:");
		if($wmStr==false)
			return $cmdLine;

		$wmStr = trim(substr($this->operator->params, strlen("watermark:")));

			/*
			 * If no watermarkData, carry on 
			 */
		if($wmStr==null) {
			return $cmdLine;
		}

		KalturaLog::log("Watermark string($wmStr)");
		$wmData = json_decode($wmStr);
		if(!isset($wmData)){
			KalturaLog::err("Bad watermark JSON string($wmStr), carry on without watermark");
		}
		KalturaLog::log("Watermark data:\n".print_r($wmData,1));

		// impersonite
		KBatchBase::impersonate($this->data->flavorParamsOutput->partnerId); // !!!!!!!!!!!$this->job->partnerId);

		// Apply theads configuration parameter, or default to 4
		$threads = isset( KBatchBase::$taskConfig->params->threads ) ? KBatchBase::$taskConfig->params->threads : 4;
		$ffmpegCmd = KBatchBase::$taskConfig->params->ffmpegCmd . ' -threads '. $threads;

		$wmCmdLine = KConversionEngineFfmpeg::buildWatermarkedCommandLine($wmData, $this->data->destFileSyncLocalPath, $cmdLine,
			$ffmpegCmd, KBatchBase::$taskConfig->params->mediaInfoCmd);
		// un-impersonite
		KBatchBase::unimpersonate();

		if(isset($wmCmdLine))
			$cmdLine = $wmCmdLine;
		
		return $cmdLine;
	}
}
