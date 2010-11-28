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
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->type = assetType::FLAVOR;
	}
	
	const SOURCE_FLAVOR_ID = 0;
	
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
	
	const CUSTOM_DATA_FIELD_VIDEO_CODEC = "FlavorVideoCodec";
	const CUSTOM_DATA_FIELD_VIDEO_BITRATE = "FlavorVideoBitrate";
	const CUSTOM_DATA_FIELD_AUDIO_CODEC = "FlavorAudioCodec";
	const CUSTOM_DATA_FIELD_AUDIO_BITRATE = "FlavorAudioBitrate";
	const CUSTOM_DATA_FIELD_AUDIO_CHANNELS = "FlavorAudioChannels";
	const CUSTOM_DATA_FIELD_AUDIO_SAMPLE_RATE = "FlavorAudioSampleRate";
	const CUSTOM_DATA_FIELD_AUDIO_RESOLUTION = "FlavorAudioResolution";
	const CUSTOM_DATA_FIELD_FRAME_RATE = "FlavorFrameRate";
	const CUSTOM_DATA_FIELD_GOP_SIZE = "FlavorGopSize";
	const CUSTOM_DATA_FIELD_TWO_PASS = "FlavorTwoPass";
	const CUSTOM_DATA_FIELD_DEINTERLICE = "FlavorDeinterlice";
	const CUSTOM_DATA_FIELD_ROTATE = "FlavorRotate";
	
//	Should be uncommented after migration script executed
//	public function getVideoCodec()			{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_CODEC);}
//	public function getVideoBitrate()		{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_BITRATE);}
//	public function getAudioCodec()			{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CODEC);}
//	public function getAudioBitrate()		{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_BITRATE);}
//	public function getAudioChannels()		{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CHANNELS);}
//	public function getAudioSampleRate()	{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_SAMPLE_RATE);}
//	public function getAudioResolution()	{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_RESOLUTION);}
//	public function getFrameRate()			{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_FRAME_RATE);}
//	public function getGopSize()			{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_GOP_SIZE);}
//	public function getTwoPass()			{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_TWO_PASS);}
//	public function getDeinterlice()		{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_DEINTERLICE);}
//	public function getRotate()				{return $this->getFromCustomData(flavorParams::CUSTOM_DATA_FIELD_ROTATE);}
	
	public function setVideoCodec($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_CODEC, $v); return parent::setVideoCodec($v);}
	public function setVideoBitrate($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_VIDEO_BITRATE, $v); return parent::setVideoBitrate($v);}
	public function setAudioCodec($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CODEC, $v); return parent::setAudioCodec($v);}
	public function setAudioBitrate($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_BITRATE, $v); return parent::setAudioBitrate($v);}
	public function setAudioChannels($v)	{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_CHANNELS, $v); return parent::setAudioChannels($v);}
	public function setAudioSampleRate($v)	{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_SAMPLE_RATE, $v); return parent::setAudioSampleRate($v);}
	public function setAudioResolution($v)	{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_AUDIO_RESOLUTION, $v); return parent::setAudioResolution($v);}
	public function setFrameRate($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_FRAME_RATE, $v); return parent::setFrameRate($v);}
	public function setGopSize($v)			{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_GOP_SIZE, $v); return parent::setGopSize($v);}
	public function setTwoPass($v)			{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_TWO_PASS, $v); return parent::setTwoPass($v);}
	public function setDeinterlice($v)		{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_DEINTERLICE, $v); return parent::setDeinterlice($v);}
	public function setRotate($v)			{$this->putInCustomData(flavorParams::CUSTOM_DATA_FIELD_ROTATE, $v); return parent::setRotate($v);}

	public function getClipOffset()		{return $this->getFromCustomData('ClipOffset');}
	public function getClipDuration()	{return $this->getFromCustomData('ClipDuration');}
}
