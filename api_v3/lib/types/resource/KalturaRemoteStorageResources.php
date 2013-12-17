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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject($srcObj)
	 */
	public function fromObject($source_object)
	{
		/* @var $source_object kRemoteStorageResources */
		parent::fromObject($source_object);
		
		$this->resources = KalturaRemoteStorageResourceArray::fromObjectArray($source_object->getResources());
	}
}