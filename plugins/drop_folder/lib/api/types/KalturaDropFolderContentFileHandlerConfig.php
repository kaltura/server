<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderContentFileHandlerConfig extends KalturaDropFolderFileHandlerConfig
{	
	/**
	 * @var KalturaDropFolderContentFileHandlerMatchPolicy
	 */
	public $contentMatchPolicy;
	
	/**
	 * @var string
	 */
	public $slugRegex;
		
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'contentMatchPolicy',
		'slugRegex',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DropFolderContentFileHandlerConfig();
			
		parent::toObject($dbObject, $skip);
		$dbObject->setHandlerType(DropFolderFileHandlerType::CONTENT);
		
		return $dbObject;
	}
	

	public function validateForInsert()
	{
		if (is_null($this->contentMatchPolicy)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, get_class($this).'::contentMatchPolicy');
		}
		if (is_null($this->slugRegex)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, get_class($this).'::slugRegex');
		}
		return parent::validateForInsert();
	}
	
}