<?php

/**
 * Subclass for representing a row from the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
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
		$this->setType(assetType::FLAVOR);
	}
	
	const SOURCE_FLAVOR_ID = 0;
	const DYNAMIC_ATTRIBUTES_ALL_FLAVORS_INDEX = -2; // "flavor params id" that will identify dynamic attributes that should apply for all flavors except for source
	
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
	const VIDEO_CODEC_MPEG2 = "mpeg2";
	const VIDEO_CODEC_APCO = "apco";
	const VIDEO_CODEC_APCS = "apcs";
	const VIDEO_CODEC_APCN = "apcn";
	const VIDEO_CODEC_APCH = "apch";
	const VIDEO_CODEC_DNXHD = "dnxhd";
	const VIDEO_CODEC_DV = "dv";
	const VIDEO_CODEC_VP9 = "vp9";
	const VIDEO_CODEC_H265 = "h265";
	const VIDEO_CODEC_COPY = "copy";
	
	const AUDIO_CODEC_NONE = "";
	const AUDIO_CODEC_MP3 = "mp3";
	const AUDIO_CODEC_AAC = "aac";
	const AUDIO_CODEC_AACHE = "aache";
	const AUDIO_CODEC_VORBIS = "vorbis";
	const AUDIO_CODEC_WMA = "wma";
	const AUDIO_CODEC_WMAPRO = "wmapro";
	const AUDIO_CODEC_AMRNB = "amrnb";
	const AUDIO_CODEC_MPEG2 = "mpeg2";
	const AUDIO_CODEC_AC3 = "ac3";
	const AUDIO_CODEC_EAC3 = "eac3";
	const AUDIO_CODEC_PCM = "pcm";
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

	public function setAspectRatioProcessingMode($v)	{$this->putInCustomData('AspectRatioProcessingMode', $v);}
	public function getAspectRatioProcessingMode()	{return $this->getFromCustomData('AspectRatioProcessingMode', null, 0);}
	
	public function setForceFrameToMultiplication16($v)	{$this->putInCustomData('ForceFrameToMultiplication16', $v);}
	public function getForceFrameToMultiplication16()	{return $this->getFromCustomData('ForceFrameToMultiplication16', null, 1);}
	
	public function setVideoConstantBitrate($v)	{$this->putInCustomData('VideoConstantBitrate', $v);}
	public function getVideoConstantBitrate()	{return $this->getFromCustomData('VideoConstantBitrate', null, 0);}
	
	public function setVideoBitrateTolerance($v)	{$this->putInCustomData('VideoBitrateTolerance', $v);}
	public function getVideoBitrateTolerance()	{return $this->getFromCustomData('VideoBitrateTolerance', null, 0);}
	
	public function setIsGopInSec($v)	{$this->putInCustomData('IsGopInSec', $v);}
	public function getIsGopInSec()	{return $this->getFromCustomData('IsGopInSec', null, 0);}
	
	public function setIsAvoidVideoShrinkFramesizeToSource($v) 	{$this->putInCustomData('IsAvoidVideoShrinkFramesizeToSource', $v);}
	public function getIsAvoidVideoShrinkFramesizeToSource()	{return $this->getFromCustomData('IsAvoidVideoShrinkFramesizeToSource', null, 0);}

	public function setIsAvoidVideoShrinkBitrateToSource($v) 	{$this->putInCustomData('IsAvoidVideoShrinkBitrateToSource', $v);}
	public function getIsAvoidVideoShrinkBitrateToSource()		{return $this->getFromCustomData('IsAvoidVideoShrinkBitrateToSource', null, 0);}

	public function setIsVideoFrameRateForLowBrAppleHls($v) 	{$this->putInCustomData('IsVideoFrameRateForLowBrAppleHls', $v);}
	public function getIsVideoFrameRateForLowBrAppleHls()		{return $this->getFromCustomData('IsVideoFrameRateForLowBrAppleHls', null, 0);}

	public function setMultiStream($v){ $this->putInCustomData('MultiStream', $v);}
	public function getMultiStream(){return $this->getFromCustomData('MultiStream', null, null);}
	
	public function setAnamorphicPixels($v){ $this->putInCustomData('AnamorphicPixels', $v);}
	public function getAnamorphicPixels(){return $this->getFromCustomData('AnamorphicPixels', null, 0);}
	
	public function setIsAvoidForcedKeyFrames($v){ $this->putInCustomData('IsAvoidForcedKeyFrames', $v);}
	public function getIsAvoidForcedKeyFrames(){return $this->getFromCustomData('IsAvoidForcedKeyFrames', null, 0);}
	
		/*
		 * Bitwise flags:
		 * 1: bitrate oriented optimization
		 * 2: frame size oriented optimization
		 * Should be '3' to turn both on
		 */
	public function setOptimizationPolicy($v){ $this->putInCustomData('OptimizationPolicy', $v);}
	public function getOptimizationPolicy(){return $this->getFromCustomData('OptimizationPolicy', null, 1);}
		/*
		 * When set, IMX sources (mxf/mpeg2/720x608) the top 32 lines will be cropped
		 */
	public function setIsCropIMX($v){ $this->putInCustomData('IsCropIMX', $v);}
	public function getIsCropIMX(){return $this->getFromCustomData('IsCropIMX', null, 1);}
	
	public function setMaxFrameRate($v){ $this->putInCustomData('MaxFrameRate', $v);}
	public function getMaxFrameRate(){return $this->getFromCustomData('MaxFrameRate', null, 0);}
	
	public function setWatermarkData($v){ $this->putInCustomData('WatermarkData', $v);}
	public function getWatermarkData(){return $this->getFromCustomData('WatermarkData', null, null);}

	public function setSubtitlesData($v){ $this->putInCustomData('SubtitlesData', $v);}
	public function getSubtitlesData(){return $this->getFromCustomData('SubtitlesData', null, null);}

	public function setIsEncrypted($v){ $this->putInCustomData('IsEncrypted', $v);}
	public function getIsEncrypted(){return $this->getFromCustomData('IsEncrypted', null, 0);}

	public function setContentAwareness($v){ $this->putInCustomData('ContentAwareness', $v);}
	public function getContentAwareness(){return $this->getFromCustomData('ContentAwareness', null, 0.5);}

	public function setForcedKeyFramesMode($v){ $this->putInCustomData('ForcedKeyFramesMode', $v);}
	public function getForcedKeyFramesMode(){return $this->getFromCustomData('ForcedKeyFramesMode', null, 1);}

	public function setChunkedEncodeMode($v){ $this->putInCustomData('ChunkedEncodeMode', $v);}
	public function getChunkedEncodeMode(){return $this->getFromCustomData('ChunkedEncodeMode', null, 0);}
}
