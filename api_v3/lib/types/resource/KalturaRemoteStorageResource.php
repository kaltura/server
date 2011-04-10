<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, the media file won’t be downloaded but a file sync object of URL type will point to the media URL.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaRemoteStorageResource extends KalturaUrlResource 
{
	/**
	 * ID of storage profile to be associated with the created file sync, used for file serving URL composing, keep null to use the default. 
	 * @var int
	 */
	public $storageProfileId;

	private static $map_between_objects = array
	(
		'storageProfileId',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('storageProfileId');
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kRemoteStorageResource();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}