<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

/**
 * Will convert a single flavor and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	get the flavor 
 * 		convert using the right method
 * 		save recovery file in case of crash
 * 		move the file to the archive
 * 		set the entry's new status and file details 
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncDivertConvert extends KAsyncConvert
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT;
	}
	
	/* (non-PHPdoc)
	 * @see batches/Convert/KAsyncConvert#getFilter()
	 */
	protected function getFilter()
	{
		$filter = parent::getFilter();
		$filter->onStressDivertToIn = $this->getSupportedDiversions();
			
		return $filter;
	}
	
	protected function getMaxJobsEachRun()
	{
		return $this->taskConfig->maxJobsEachRun;
	}
	
	protected function convert(KalturaBatchJob $job, KalturaConvartableJobData $data)
	{
		$job = $this->kClient->batch->updateExclusiveConvertJobSubType($job->id, $this->getExclusiveLockKey(), $job->onStressDivertTo);
		
		return parent::convert($job, $data);
	}
	
	
	/*
	 * @return string
	 */
	private function getSupportedDiversions()
	{
		$supported_engines_arr = array();
		if  ( $this->taskConfig->params->divertOn2 ) $supported_engines_arr[] = KalturaConversionEngineType::ON2;
		if  ( $this->taskConfig->params->divertFFMpeg ) $supported_engines_arr[] = KalturaConversionEngineType::FFMPEG;
		if  ( $this->taskConfig->params->divertMEncoder ) $supported_engines_arr[] = KalturaConversionEngineType::MENCODER;
		if  ( $this->taskConfig->params->divertEncodingCom ) $supported_engines_arr[] = KalturaConversionEngineType::ENCODING_COM;
		if  ( $this->taskConfig->params->divertKalturaCom ) $supported_engines_arr[] = KalturaConversionEngineType::KALTURA_COM;
		if  ( $this->taskConfig->params->divertFFMpegAux ) $supported_engines_arr[] = KalturaConversionEngineType::FFMPEG_AUX;
		if  ( $this->taskConfig->params->divertFFMpegVp8 ) $supported_engines_arr[] = KalturaConversionEngineType::FFMPEG_VP8;
		
		return join(',', $supported_engines_arr);
	}
}
?>