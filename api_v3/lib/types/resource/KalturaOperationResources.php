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

	/**
	 * @var KalturaChapterNamePolicy
	 */
	public $chapterNamePolicy;

	private static $map_between_objects = array
	(
		'resources',
		'chapterNamePolicy',
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

		$maxResourcesCount = kConf::get("maxOperationResourcesCount", kConfMapNames::RUNTIME_CONFIG, 5);
		if (count($this->resources) > $maxResourcesCount)
		{
			throw new KalturaAPIException(KalturaErrors::RESOURCES_COUNT_EXCEEDED_MAX_ALLOWED_COUNT, $maxResourcesCount);
		}

		$overallDuration = 0 ;
		foreach ($this->resources as $resource)
		{
			if(!($resource instanceof KalturaOperationResource))
			{
				throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource));
			}
			$resource->validateForUsage($sourceObject, $propertiesToSkip);

			foreach ($resource->operationAttributes as $operationAttribute)
			{
				if (!($operationAttribute instanceof KalturaClipAttributes))
				{
					throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($resource->operationAttributes));
				}
				if (!$operationAttribute->duration)
				{
					throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, 'duration');
				}
				$overallDuration += $operationAttribute->duration;
			}
		}
		$maxDurationSeconds = kConf::get("maxMultiClipsDurationSeconds", kConfMapNames::RUNTIME_CONFIG, 5 * 60 * 60);
		if ($overallDuration / 1000 > $maxDurationSeconds)
		{
			throw new KalturaAPIException(KalturaErrors::CLIPS_DURATIONS_EXCEEDED_MAX_ALLOWED_DURATION, $maxDurationSeconds);
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