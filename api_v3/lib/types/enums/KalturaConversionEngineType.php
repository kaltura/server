<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaConversionEngineType extends KalturaEnum
{
	const KALTURA_COM = 0;
	const ON2 = 1;
	const FFMPEG = 2;
	const MENCODER = 3;
	const ENCODING_COM = 4;
	const EXPRESSION_ENCODER3 = 5;
	const QUICK_TIME_PLAYER_TOOLS = 6;
	const FAST_START = 7;
	
	const FFMPEG_VP8 = 98;
	const FFMPEG_AUX = 99;
	
	// document conversion engines
	const PDF2SWF = 201;
	const PDF_CREATOR = 202;
	const OPENOFFICE_UCONV = 203;
	
}
?>