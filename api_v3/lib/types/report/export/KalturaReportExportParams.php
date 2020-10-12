<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportParams extends KalturaObject
{
	/**
	 * @var string
	 */
	public $recipientEmail;

	/**
	 * Time zone offset in minutes (between client to UTC)
	 * @var int
	 */
	public $timeZoneOffset = 0;

	/**
	 *  @var KalturaReportExportItemArray
	 */
	public $reportItems;

	/**
	 * @var string
	 */
	public $reportsItemsGroup;

	private static $map_between_objects = array
	(
		"recipientEmail",
		"reportItems",
		"reportsItemsGroup",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kReportExportParams();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
