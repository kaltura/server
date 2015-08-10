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
		$cmdLine=KConversionEngineFfmpeg::experimentalFixing($cmdLine, $this->data->flavorParamsOutput, $this->cmd, $this->inFilePath, $this->outFilePath);
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

		$wmCmdLine = null;
		if(isset($wmData)){
			$wmCmdLine = KConversionEngineFfmpeg::buildWatermarkedCommandLine($wmData, $this->data->destFileSyncLocalPath, $cmdLine,
					KBatchBase::$taskConfig->params->ffmpegCmd, KBatchBase::$taskConfig->params->mediaInfoCmd);
		}
				/*
				 * 'watermark_pair_' tag for NGS digital signature watermarking flow
				 */
		if(isset($this->data->flavorParamsOutput->tags) && strstr($this->data->flavorParamsOutput->tags,'watermark_pair_')!=false){
			$wmCmdLine = KConversionEngineFfmpeg::buildNGSPairedDigitalWatermarkingCommandLine((isset($wmCmdLine)?$wmCmdLine:$cmdLine), $this->data);
		}

		// un-impersonite
		KBatchBase::unimpersonate();

		if(isset($wmCmdLine))
			$cmdLine = $wmCmdLine;
		
		return $cmdLine;
	}
}
