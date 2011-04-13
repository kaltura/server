<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileHandlerConfig extends KalturaObject
{
	/**
	 * @var int
	 */
	public $handlerType;
	
	/**
	 * @var string
	 */
	public $filePattern;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'handlerType',
		'filePattern',
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
}