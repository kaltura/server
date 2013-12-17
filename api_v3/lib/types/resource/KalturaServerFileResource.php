<?php
/**
 * Used to ingest media file that is already accessible on the shared disc.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaServerFileResource extends KalturaDataCenterContentResource
{
	/**
	 * Full path to the local file 
	 * @var string
	 * @requiresPermission all
	 */
	public $localFilePath;
	
	private static $map_between_objects = array('localFilePath');
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaDataCenterContentResource::validateForUsage()
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('localFilePath');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
		
		$object_to_fill->setKeepOriginalFile(true);
		$ret = parent::toObject($object_to_fill, $props_to_skip);
		/* @var $ret kLocalFileResource */
		
		if(!file_exists($ret->getLocalFilePath()))
			throw new KalturaAPIException(KalturaErrors::LOCAL_FILE_NOT_FOUND, $ret->getLocalFilePath());
		
		return $ret;
	}
}