<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineFfmpegVp8  extends KConversionEngineFfmpeg
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
		return KBatchBase::$taskConfig->params->ffmpegVp8Cmd;
	}
}
