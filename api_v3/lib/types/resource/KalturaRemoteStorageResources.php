<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, the media file won't be downloaded but a file sync object of URL type will point to the media URL.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaRemoteStorageResources extends KalturaContentResource
{
	/**
	 * Array of remote stoage resources 
	 * @var KalturaRemoteStorageResourceArray
	 */
	public $resources;

	private static $map_between_objects = array
	(
		'resources',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$this->validatePropertyNotNull('resources');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kRemoteStorageResources();
		
		$resources = array();
		if($this->resources)
		{
			foreach($this->resources as $resource)
			{
				/* @var $resource KalturaRemoteStorageResource */
				$resources[] = $resource->toObject();
			}
		}
		$object_to_fill->setResources($resources);
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}