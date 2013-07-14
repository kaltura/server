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
	 * Regular expression that defines valid file names to be handled.
	 * The following might be extracted from the file name and used if defined:
	 * 	- (?P<referenceId>\w+) - will be used as the drop folder file's parsed slug.
	 *  - (?P<flavorName>\w+)  - will be used as the drop folder file's parsed flavor.
	 * 
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
	

	public function validateForInsert($propertiesToSkip = array())
	{
		if (is_null($this->contentMatchPolicy)) {
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, get_class($this).'::contentMatchPolicy');
		}
		
		return parent::validateForInsert($propertiesToSkip);
	}
	
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if (is_null($this->slugRegex)) {
			$this->slugRegex = kDropFolderEventsConsumer::DEFAULT_SLUG_REGEX;
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}	
	
}