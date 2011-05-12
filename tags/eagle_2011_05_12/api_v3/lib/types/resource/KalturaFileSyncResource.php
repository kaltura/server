<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different file in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaFileSyncResource extends KalturaContentResource 
{
	/**
	 * The object type of the file sync object 
	 * @var int
	 */
	public $fileSyncObjectType;
	
	/**
	 * The object sub-type of the file sync object 
	 * @var int
	 */
	public $objectSubType;
	
	/**
	 * The object id of the file sync object 
	 * @var string
	 */
	public $objectId;
	
	/**
	 * The version of the file sync object 
	 * @var string
	 */
	public $version;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('fileSyncObjectType');
    	$this->validatePropertyNotNull('objectSubType');
    	$this->validatePropertyNotNull('objectId');
	}

	private static $map_between_objects = array
	(
		'fileSyncObjectType',
		'objectSubType',
		'objectId',
		'version',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kFileSyncResource();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}