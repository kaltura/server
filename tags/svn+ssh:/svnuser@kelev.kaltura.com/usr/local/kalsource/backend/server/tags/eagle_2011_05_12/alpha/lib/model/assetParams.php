<?php

/**
 * Subclass for representing a row from the 'flavor_params' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class assetParams extends BaseassetParams
{
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
	const CONTAINER_FORMAT_WEBM = "webm";
	const CONTAINER_FORMAT_MPEG = "mpeg";
	const CONTAINER_FORMAT_MPEGTS = "mpegts";
	const CONTAINER_FORMAT_APPLEHTTP = "applehttp";
	
	const CONTAINER_FORMAT_PDF = 'pdf';
	const CONTAINER_FORMAT_SWF = 'swf';
	
	const TAG_SOURCE = "source";
	const TAG_WEB = "web";
	const TAG_MBR = "mbr";
	const TAG_MOBILE = "mobile";
	const TAG_IPHONE = "iphone";
	const TAG_EDIT = "edit";
	const TAG_ISM = "ism";
	const TAG_SLWEB = "slweb";
	const TAG_APPLEMBR = "applembr";
	
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
	 * @see lib/model/om/BaseflavorParams#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(flavorParamsPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
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
	
	public function setRequiredPermissions($permissionNames)
	{
		$this->putInCustomData('requiredPermissions', $permissionNames);
	}
	
	public function getRequiredPermissions()
	{
		return $this->getFromCustomData('requiredPermissions');
	}
}
