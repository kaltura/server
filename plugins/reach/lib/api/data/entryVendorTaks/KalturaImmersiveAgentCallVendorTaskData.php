<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaImmersiveAgentCallVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * The unique identifier for the immersive agent call
	 *
	 * @var string
	 */
	public $callId;

	private static $map_between_objects = array
	(
		'callId',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kImmersiveAgentCallVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		parent::validatePropertyNotNull('callId');
	}
}
