<?php

/**
 * Subclass for representing a row from the 'flavor_params_output' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParamsOutput extends assetParamsOutput
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
	
	public function getCollectionTag()
	{
		$tags = explode(',', $this->getTags());
		foreach(flavorParams::$COLLECTION_TAGS as $tag)
		{
			if(in_array($tag, $tags))
				return $tag;
		}
		return null;
	}
	
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
	
	public function setClipOffset($v)	{$this->putInCustomData('ClipOffset', $v);}
	public function getClipOffset()		{return $this->getFromCustomData('ClipOffset');}

	public function setClipDuration($v)	{$this->putInCustomData('ClipDuration', $v);}
	public function getClipDuration()	{return $this->getFromCustomData('ClipDuration');}
}
