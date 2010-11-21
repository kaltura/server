<?php

/**
 * Subclass for representing a row from the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParams extends assetParams
{
	const SOURCE_FLAVOR_ID = 0;
	
	const TAG_THUMB_SOURCE = "thumb_source";
		
	const VIDEO_CODEC_NONE = "";
	const VIDEO_CODEC_VP6 = "vp6";
	const VIDEO_CODEC_H263 = "h263";
	const VIDEO_CODEC_H264 = "h264";
	const VIDEO_CODEC_H264B = "h264b";
	const VIDEO_CODEC_H264M = "h264m";
	const VIDEO_CODEC_H264H = "h264h";
	const VIDEO_CODEC_FLV = "flv";
	const VIDEO_CODEC_MPEG4 = "mpeg4";
	const VIDEO_CODEC_THEORA = "theora";
	const VIDEO_CODEC_WMV2 = "wmv2";
	const VIDEO_CODEC_WMV3 = "wmv3";
	const VIDEO_CODEC_WVC1A = "wvc1a";
	const VIDEO_CODEC_VP8 = "vp8";
	const VIDEO_CODEC_COPY = "copy";
	
	const AUDIO_CODEC_NONE = "";
	const AUDIO_CODEC_MP3 = "mp3";
	const AUDIO_CODEC_AAC = "aac";
	const AUDIO_CODEC_VORBIS = "vorbis";
	const AUDIO_CODEC_WMA = "wma";
	const AUDIO_CODEC_COPY = "copy";
	
	public function getClipOffset()		{return $this->getFromCustomData('ClipOffset');}
	public function getClipDuration()	{return $this->getFromCustomData('ClipDuration');}
}
