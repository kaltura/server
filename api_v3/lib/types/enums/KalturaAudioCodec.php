<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAudioCodec extends KalturaStringEnum
{
	const NONE = "";
	
	const MP3 = "mp3";
	const AAC = "aac";
	const VORBIS = "vorbis";
	const WMA = "wma";
	const WMAPRO = "wmapro";
	const AMRNB = "amrnb";
	const MPEG2 = "mpeg2";
	const AC3 = "ac3";
	const PCMS16LE = "pcm_s16le";
	const COPY = "copy";
}