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
	const COPY = "copy";
}