<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversionEngineFfmpegVp8  extends KJobConversionEngine
{
	const FFMPEG_VP8 = "ffmpeg_vp8";
	
	public function getName()
	{
		return self::FFMPEG_VP8;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::FFMPEG_VP8;
	}
	
	public function getCmd ()
	{
		return $this->engine_config->params->ffmpegVp8Cmd;
	}
}
