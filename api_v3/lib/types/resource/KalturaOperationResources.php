<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaOperationResources extends KalturaContentResource
{
	/**
	 * @var KalturaOperationResourceArray
	 */
	public $resources;

	private static $map_between_objects = array
	(
		'resources',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaResource::validateEntry()
	 */
	public function validateEntry(entry $dbEntry, $validateLocalExist = false)
	{
		parent::validateEntry($dbEntry,$validateLocalExist);
		$this->validatePropertyNotNull('resources');

		foreach($this->resources as $resource)
		{
			if(!($resource instanceof KalturaOperationResource))
			{
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource));
			}
			$resource->validateEntry($dbEntry,$validateLocalExist);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		$this->validatePropertyNotNull('resources');

		foreach($this->resources as $resource)
		{
			if(!($resource instanceof KalturaOperationResource))
			{
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource));
			}
			$resource->validateForUsage($sourceObject, $propertiesToSkip);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaResource::entryHandled()
	 */
	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);

		foreach($this->resources as $resource)
		{
			$resource->entryHandled($dbEntry);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
		{
			$object_to_fill = new kOperationResources();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}