<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationEngineFfmpeg  extends KSingleOutputOperationEngine
{
	protected function getCmdLine()
	{
		$this->inFilePath = kFile::checkFileExists($this->inFilePath) ? '"' . kFile::realPath($this->inFilePath) . '"' : $this->inFilePath;
		
		$cmdLine = parent::getCmdLine();
		if(get_class($this)=='KOperationEngineFfmpegVp8'){
			$cmdLine=KConversionEngineFfmpeg::experimentalFixing($cmdLine, $this->data->flavorParamsOutput, $this->cmd, $this->inFilePath, $this->outFilePath);
		}
		$cmdLine=KDLOperatorFfmpeg::ExpandForcedKeyframesParams($cmdLine);
		
		// impersonite
		KBatchBase::impersonate($this->data->flavorParamsOutput->partnerId); // !!!!!!!!!!!$this->job->partnerId);

				/*
				 * Fetch watermark 
				 */
		if(isset($this->data->flavorParamsOutput->watermarkData)){
				$wmStr = $this->data->flavorParamsOutput->watermarkData;
				KalturaLog::log("watermarks:$wmStr");
				$wmData = json_decode($wmStr);
				if(isset($wmData)){
					KalturaLog::log("Watermark data:\n".print_r($wmData,1));
					$fixedCmdLine = KConversionEngineFfmpeg::buildWatermarkedCommandLine($wmData, $this->data->destFileSyncLocalPath, $cmdLine,
							KBatchBase::$taskConfig->params->ffmpegCmd, KBatchBase::$taskConfig->params->mediaInfoCmd);
					if(isset($fixedCmdLine)) $cmdLine = $fixedCmdLine;
				}
				else
					KalturaLog::err("Bad watermark JSON string($wmStr), carry on without watermark");
		}
		
				/*
				 * Fetch subtitles 
				 */
		if(isset($this->data->flavorParamsOutput->subtitlesData)){
			$subsStr = $this->data->flavorParamsOutput->subtitlesData;
			KalturaLog::log("subtitles:$subsStr");
			$subsData = json_decode($subsStr);
			if(isset($subsData)){
				$jobMsg = null;
				$fixedCmdLine = KConversionEngineFfmpeg::buildSubtitlesCommandLine($subsData, $this->data, $cmdLine, $jobMsg);
				if(isset($jobMsg)) $this->message = $jobMsg;
				if(isset($fixedCmdLine)) $cmdLine = $fixedCmdLine;
			}
			else {
				KalturaLog::err("Bad subtitles JSON string($subsStr), carry on without subtitles");
			}
		}

				/*
				 * 'watermark_pair_' tag for NGS digital signature watermarking flow
				 */
		if(isset($this->data->flavorParamsOutput->tags) && strstr($this->data->flavorParamsOutput->tags,'watermark_pair_')!=false){
			$fixedCmdLine = KConversionEngineFfmpeg::buildNGSPairedDigitalWatermarkingCommandLine($cmdLine, $this->data);
			if(isset($fixedCmdLine)) $cmdLine = $fixedCmdLine;
		}

		// un-impersonite
		KBatchBase::unimpersonate();

	
		return $cmdLine;
	}
}
