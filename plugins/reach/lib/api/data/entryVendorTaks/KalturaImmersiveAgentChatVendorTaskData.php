<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaImmersiveAgentChatVendorTaskData extends KalturaVendorTaskData
{
	private static $map_between_objects = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kImmersiveAgentChatVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
