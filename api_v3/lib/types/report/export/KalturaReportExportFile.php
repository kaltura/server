<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportExportFile extends KalturaObject
{
	/**
	 * @var string
	 */
	public $fileId;

	/**
	 * @var string
	 */
	public $fileName;

	private static $map_between_objects = array
	(
		"fileId",
		"fileName",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kReportExportFile();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
