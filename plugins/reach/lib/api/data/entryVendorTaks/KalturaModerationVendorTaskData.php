<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaModerationVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * A comma seperated string of rule IDs.
	 *
	 * @var string
	 */
	public $ruleIds;

	/**
	 * A comma seperated string of policy IDs.
	 *
	 * @var string
	 */
	public $policyIds;

	/**
	 * JSON string containing the moderation output.
	 *
	 * @var string
	 */
	public $moderationOutputJson;


	private static $map_between_objects = array
	(
		'ruleIds',
		'policyIds',
		'moderationOutputJson'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject) {
			$dbObject = new kModerationVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
