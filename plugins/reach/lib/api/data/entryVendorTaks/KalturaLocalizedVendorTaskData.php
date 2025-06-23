<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaLocalizedVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * Language code
	 *
	 * @var KalturaLanguage
	 */
	public $outputLanguage;

	/**
	 * result as JSON string.
	 *
	 * @var string
	 */
	public $outputJson;

	private static $map_between_objects = array(
		'outputLanguage',
		'outputJson',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kLocalizedVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}
