<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileHandlerConfig extends KalturaObject
{	
	/**
	 * @var KalturaDropFolderFileHandlerType
	 * @insertonly
	 */
	public $handlerType;

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'handlerType',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DropFolderFileHandlerConfig();
			
		parent::toObject($dbObject, $skip);
		
		return $dbObject;
	}
	
	public function fromObject ($source_object)
	{
		parent::fromObject($source_object);
	}
	

	public function validateForInsert()
	{
		if (is_null($this->handlerType)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, get_class($this).'::handlerType');
		}
		return parent::validateForInsert();
	}
	
	
}