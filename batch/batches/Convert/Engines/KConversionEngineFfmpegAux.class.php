<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineFfmpegAux  extends KJobConversionEngine
{
	const FFMPEG_AUX = "ffmpeg_aux";
	
	public function getName()
	{
		return self::FFMPEG_AUX;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::FFMPEG_AUX;
	}
	
	public function getCmd ()
	{
		return KBatchBase::$taskConfig->params->ffmpegAuxCmd;
	}
}
