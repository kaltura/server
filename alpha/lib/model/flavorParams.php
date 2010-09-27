<?php

/**
 * Subclass for representing a row from the 'flavor_params' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParams extends BaseflavorParams
{
	const SOURCE_FLAVOR_ID = 0;
	
	const CONTAINER_FORMAT_FLV = "flv";
	const CONTAINER_FORMAT_MP4 = "mp4";
	const CONTAINER_FORMAT_AVI = "avi";
	const CONTAINER_FORMAT_MOV = "mov";
	const CONTAINER_FORMAT_MP3 = "mp3";
	const CONTAINER_FORMAT_3GP = "3gp";
	const CONTAINER_FORMAT_OGG = "ogg";
	const CONTAINER_FORMAT_WMV = "wmv";
	const CONTAINER_FORMAT_WMA = "wma";
	const CONTAINER_FORMAT_ISMV = "ismv";
	const CONTAINER_FORMAT_MKV = "mkv";
	
	const CONTAINER_FORMAT_PDF = 'pdf';
	const CONTAINER_FORMAT_SWF = 'swf';
		
	const VIDEO_CODEC_NONE = "";
	const VIDEO_CODEC_VP6 = "vp6";
	const VIDEO_CODEC_H263 = "h263";
	const VIDEO_CODEC_H264 = "h264";
	const VIDEO_CODEC_H264B = "h264b";
	const VIDEO_CODEC_H264M = "h264m";
	const VIDEO_CODEC_H264h = "h264h";
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
	
	const TAG_SOURCE = "source";
	const TAG_WEB = "web";
	const TAG_MBR = "mbr";
	const TAG_MOBILE = "mobile";
	const TAG_IPHONE = "iphone";
	const TAG_EDIT = "edit";
	const TAG_ISM = "ism";
	const TAG_SLWEB = "slweb";
	
	public static $COLLECTION_TAGS = array(flavorParams::TAG_ISM); 
	
	const SYSTEM_DEFAULT = 1; 
	
	const FLAVOR_PARAMS_CREATION_MODE_MANUAL = 1;
	const FLAVOR_PARAMS_CREATION_MODE_KMC = 2;
	const FLAVOR_PARAMS_CREATION_MODE_AUTOMATIC = 3;
	
	private static $validTags = array(
		self::TAG_SOURCE,
		self::TAG_WEB,
		self::TAG_MBR,
		self::TAG_MOBILE,
		self::TAG_IPHONE,
		self::TAG_EDIT,
	);

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseflavorParams#preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(flavorParamsPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return parent::preUpdate($con);
	}
	
	public function setTags($v)
	{
		parent::setTags(strtolower($v));
	}
	
	public function getTagsArray()
	{
		return explode(',', $this->getTags());
	}
	
	static function isValidTag($tag)
	{
		return array_key_exists($tag, self::$validTags);
	}
	
	static function getValidTags()
	{
		return self::$validTags;
	}
	
	public function hasTag($v)
	{
		$tags = explode(',', $this->getTags());
		return in_array($v, $tags);
	}
	
	public function addTag($v)
	{
		$tags = explode(',', $this->getTags());
		$tags[] = $v;
		$this->setTags(implode(',', $tags));
	}
	
	public function removeTag($v)
	{
		$tags = explode(',', $this->getTags());
		
		$finalTags = array();
		foreach($tags as $tag)
			if($tag != $v)
				$finalTags[] = $tag;
				
		$this->setTags(implode(',', $finalTags));
	}	
	
	public function setDynamicAttribute($attributeName, $v)
	{
		$this->putInCustomData($attributeName, $v);
	}
	
	public function getClipOffset()		{return $this->getFromCustomData('ClipOffset');}
	public function getClipDuration()	{return $this->getFromCustomData('ClipDuration');}
}
