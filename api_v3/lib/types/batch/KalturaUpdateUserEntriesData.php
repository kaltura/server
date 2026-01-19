<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUpdateUserEntriesData extends KalturaJobData
{
	/**
	 * @var KalturaUserEntryStatus
	 */
	public $oldStatus;

	/**
	 * @var KalturaUserEntryStatus
	 */
	public $newStatus;

	private static $map_between_objects = array
	(
		'oldStatus',
		'newStatus',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kUpdateUserEntriesData();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
